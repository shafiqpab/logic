<? 
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Fabric Booking
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	27-12-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
-----------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class3/class.conditions.php');
include('../../../includes/class3/class.reports.php');
//include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class3/class.yarns.php');
include('../../../includes/class3/class.trims.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","","" );
}

if ($action=="load_drop_down_suplier")
{
	if($data==5){
	echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{
	echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );

	}
}

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
if($action=="check_month_maintain")
{ 
	//echo $data;
	 $sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1)
	{
		echo "1"."_";
	}
	else
	{
		echo "0"."_";
	}
	
	exit();	
}


if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
     
	<script>
	 
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1100" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                      <thead>
                        	<th  colspan="7">
                              <?
                               echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                         <th width="100">Job No</th>
                         <th width="100">File No</th>
                         <th width="100">Internal Ref.</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking"> 
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
					?>	</td>
                     <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                      <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                       <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                      <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                      <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_booking_search_list_view', 'search_div', 'fabric_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
    
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if ($action=="create_booking_search_list_view")
{
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.`insert_date`, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	
	if($data[7]==1)
		{
		 if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'  "; else  $booking_cond="";
	     if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
		}
	if($data[7]==4 || $data[7]==0)
		{
		 if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
	     if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
		}
	
	if($data[7]==2)
		{
		 if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
	     if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
		}
	
	if($data[7]==3)
		{
		 if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
	     if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond=""; 
		}
		
		$file_no = str_replace("'","",$data[8]);
		$internal_ref = str_replace("'","",$data[9]);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' "; 
	
  if($db_type==0)
	{
	  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	/*if($file_no!="" || $internal_ref!="")
	{
	$sql_po=sql_select("select b.job_no_mst from  wo_po_break_down b where   b.status_active=1 and b.is_deleted=0  $file_no_cond  $internal_ref_cond");
	 $job_data=$sql_po[0][csf('job_no_mst')];
	}
	if($job_data!="" || $job_data!=0) $job_data_cond=" and a.job_no='$job_data' "; else $job_data_cond=""; 
	*/
	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no,a.po_break_down_id,a.job_no from wo_booking_mst  a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}
	 $approved=array(0=>"No",1=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$po_array,9=>$item_category,10=>$fabric_source,11=>$suplier,12=>$approved,13=>$is_ready);
	
	$sql= "select a.booking_no_prefix_num,a.booking_date,c.file_no,c.grouping,a.company_id,a.buyer_id,a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_no,a.ready_to_approved,b.style_ref_no  from wo_booking_mst a, wo_po_details_master b,wo_po_break_down c   where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst  and a.booking_type=1 and a.is_short=2 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no"; 
	
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Style Ref.,PO number,Internal Ref,File No,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,100,200,100,100,80,80,50,50","1320","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,job_no,0,po_break_down_id,0,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,style_ref_no,po_break_down_id,grouping,file_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0,0,0','','');
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,booking_percent,delivery_date,source,booking_year,colar_excess_percent,cuff_excess_percent,is_approved,ready_to_approved,is_apply_last_update,rmg_process_breakdown,fabric_composition from wo_booking_mst  where booking_no='$data'"; 
	
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_booking_percent').value = '".$row[csf("booking_percent")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_colar_excess_percent').value = '".$row[csf("colar_excess_percent")]."';\n";
		echo "document.getElementById('txt_cuff_excess_percent').value = '".$row[csf("cuff_excess_percent")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('processloss_breck_down').value = '".$row[csf("rmg_process_breakdown")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		$group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";
		$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$row[csf("po_break_down_id")].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
		foreach($data_array3 as $inv)
		{
		$grouping=implode(",",array_unique(explode(",",$inv[csf("grouping")])));
		$file_no=implode(",",array_unique(explode(",",$inv[csf("file_no")])));
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		}

		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
		
		

		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		$po_m="";
		$sql_m= "select dealing_marchant from   wo_po_details_master  where job_no='".$row[csf("job_no")]."'"; 
		$data_array_m=sql_select($sql_m);
		foreach ($data_array_m as $row_m)
		{
			$po_m=$row_m[csf('dealing_marchant')];
		}
		if($row[csf("is_apply_last_update")]==2)
		{
			echo "document.getElementById('app_sms3').innerHTML = 'Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$po_m]."';\n";
		}
		else
		{
			echo "document.getElementById('app_sms3').innerHTML = '';\n";
		}
		
		$colar_culff_percent=return_field_value("colar_culff_percent", "variable_order_tracking", "company_name='".$row[csf("company_id")]."'  and variable_list=40 and status_active=1 and is_deleted=0");
		if($colar_culff_percent==1)
		{
			echo "$('#txt_colar_excess_percent').removeAttr('disabled')".";\n";
			echo "$('#txt_cuff_excess_percent').removeAttr('disabled')".";\n";
		}
		if($colar_culff_percent==2)
		{
			echo "$('#txt_colar_excess_percent').attr('disabled','true')".";\n";
		    echo "$('#txt_cuff_excess_percent').attr('disabled','true')".";\n";
		}
		$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$row[csf("po_break_down_id")].") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary)
		{
		   echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n"; 
		   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
		}
	 }
}


if ($action=="order_search_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();	
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			//alert(x)
			var newColor = 'yellow';
			//if ( x.style ) 
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}
		
		function js_set_value( str_data,tr_id ) 
		{
			
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;	
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];
			
			if( jQuery.inArray( str , selected_id ) == -1 ) 
			{
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == str ) break;
				}
			
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
					//alert(selected_id.length)
				if(selected_id.length==0)
				{
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
			<?
				$booking_month=0;
				if(str_replace("'","",$cbo_booking_month)<10)
				{
					$booking_month.=str_replace("'","",$cbo_booking_month);
				}
				else
				{
					$booking_month=str_replace("'","",$cbo_booking_month); 
				}
				$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
				$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
				
				if($booking_month!=0)
				{
				 $start_date=$start_date;
				 $end_date=$end_date;
				
				}
				else
				{
				$start_date='';
				$end_date='';
				}
            ?>
            <form name="searchpofrm_1" id="searchpofrm_1">
                <table width="1200"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="1200" class="rpt_table" align="center" rules="all">
                            
                                <thead> 
                                <tr>
                                 <th width="150" colspan="4"> </th>
                                        <th>
                                          <?
                                           echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                                          ?>
                                        </th>
                                      <th width="150" colspan="4"> </th>
                                </tr> 
                                <tr>               	 
                                    <th width="150">Company Name</th>
                                    <th width="150">Buyer Name</th>
                                    <th width="100">Job No</th>
                                    <th width="100">Internal Ref</th>
                                    <th width="100">File No</th>
                                     <th width="100">Style Ref </th>
                                    <th width="150">Order No</th>
                                    <th width="200">Date Range</th>
                                    <th></th>
                                    </tr>           
                                </thead>
                                <tr>
                                    <td> 
                                    <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'fabric_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                    </td>
                                    <td id="buyer_td">
                                    <?
                                    echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
                                    ?>	
                                    </td>
                                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                     <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                                     <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                                     <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                                    <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:85px" value="<? //echo $start_date; ?>"/>
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:85px" value="<? //echo $end_date; ?>"/>
                                    </td> 
                                    <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td  align="center"  valign="top" colspan="7">
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" align="center">
                                    <strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes" readonly style="width:550px" id="po_number">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" >
                        <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /> 
                        </td>
                    </tr>
                    <tr>
                        <td id="search_div" align="center">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }
	
	
	//if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
	//if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; else  $order_cond=""; 
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	if($data[7]==1)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond=""; 
	}
	if($data[7]==2)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond=""; 
	}
	if($data[7]==3)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond=""; 
	}
	if($data[7]==4 || $data[7]==0)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond=""; 
	}
	
	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	/*if($file_no!="" || $internal_ref!="")
	{
	$sql_po=sql_select("select b.id from  wo_po_break_down b where   b.status_active=1 and b.is_deleted=0  $file_no_cond  $internal_ref_cond");
	 $po_id_data=$sql_po[0][csf('id')];
	}
	if($po_id_data!="" || $po_id_data!=0) $po_data_cond=" and b.id='$po_id_data' "; else $po_data_cond="";*/
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id,b.grouping,b.file_no, b.po_number,b.po_quantity,b.shipment_date,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no"; 
	
	echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No,Internal Ref,File No,Job Qty.,PO number,PO Qty,Shipment Date", "60,60,50,100,100,100,70,150,80,80","950","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,grouping,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,1,0,1,3','','');
} 

if ($action=="populate_order_data_from_search_popup")
{
	$group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
	foreach ($data_array as $row)
	{
		$grouping=implode(",",array_unique(explode(",",$row[csf("grouping")])));
		$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		
		$booking_no="";
		$sql= sql_select("select booking_no from wo_booking_mst  where job_no='".$row[csf("job_no")]."' and booking_type=1");
		foreach($sql as $sql_row)
		{
			$booking_no.=$sql_row[csf('booking_no')].", ";
		}
		
		if($booking_no=="")
		{
			 echo "document.getElementById('app_sms3').innerHTML = '';\n";
		}
		else
		{
			echo "document.getElementById('app_sms3').innerHTML = 'Booking No ".rtrim($booking_no ,", ")." is found against  this Job No';\n";
		}
		
		$colar_culff_percent=return_field_value("colar_culff_percent", "variable_order_tracking", "company_name='".$row[csf("company_name")]."'  and variable_list=40 and status_active=1 and is_deleted=0");
		if($colar_culff_percent==1)
		{
			echo "$('#txt_colar_excess_percent').removeAttr('disabled')".";\n";
			echo "$('#txt_cuff_excess_percent').removeAttr('disabled')".";\n";
		}
		if($colar_culff_percent==2)
		{
			echo "$('#txt_colar_excess_percent').attr('disabled','true')".";\n";
		    echo "$('#txt_cuff_excess_percent').attr('disabled','true')".";\n";
		}
		
		$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$data.") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary)
		{
		   echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";  
		   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
		}
	}
}
 

if ($action=="generate_fabric_booking")
{
	extract($_REQUEST);
	?>
    <table width="2020" class="rpt_table" border="0" rules="all">
        	<thead>
        	<tr>
                <th width="50">Sl</th>
            	<th width="120">PO Number</th>
                <th width="140">Gmts Item</th>
                <th width="120">Body Part</th>
                <th width="100">Color Type</th>   
                <th width="100">Construction</th>
                <th width="100">Composition</th>
                <th width="50">GSM</th>
                <th width="80">Dia/Width</th> 
                <th width="80">Proces Loss</th> 
                <th width="50">Item Size</th>
                <th width="150">Col. Sensivity</th>
                <th width="100">Gmts.Color</th>
                <th width="100">Fab.Color</th>
                <th width="100">Gmts. Sizes</th> 
                <th width="100">Gmts. Quantity (Plan Cut)</th>  
                <th width="100">Fin Fab Qnty</th>
                <th width="100">Gray Qnty</th>
                <th width="50">Rate</th>
                <th width="100">Amount</th> 
                <th width="60">Colar/ Cuff percent</th>   
                <th></th>          
           </tr>
       </thead>
       </table>
        <div style=" max-height:200px; overflow-y:scroll; width:2020px"  align="left">
        <table width="2003" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
       <tbody>
    <?
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$txt_booking_percent=str_replace("'","",$txt_booking_percent);
	
	$company2=return_field_value("company_name","wo_po_details_master a, wo_po_break_down b","b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no  and b.is_deleted=0 and b.status_active=1");
	$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name='".$company2."'  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids2);
	$finish_fabric_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty ,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "fin_fab_qnty");
	
	$grey_fabric_qnty_array=return_library_array( "select sum(a.grey_fab_qnty) as grey_fab_qnty,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "grey_fab_qnty");
	
	/*$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id,po_break_down_id", "id", "plan_cut_qnty");*/
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id, item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	
	$nameArray=sql_select("
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.job_no,
	a.item_number_id,
	a.body_part_id,
	a.fab_nature_id,
	a.fabric_source,
	a.color_type_id,
	a.gsm_weight,
	a.construction,
	a.composition,
	a.color_size_sensitive,
	a.costing_per,
	a.color,
	a.color_break_down,
	a.rate,
	b.id,
	b.po_break_down_id,
	b.color_size_table_id,
	b.color_number_id,
	b.gmts_sizes as size_number_id,
	b.dia_width,
	b.item_size,
	b.cons,
	b.process_loss_percent,
	b.requirment,
	b.pcs

FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b
WHERE
	a.job_no=b.job_no and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
			$constrast_color_arr=array();
			if($result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
			 if ($count%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
					
				
	$bala_fin_fab_qnty=0;
	$bala_grey_fab_qnty=0;
	if($result[csf("costing_per")]==1)
	{
		
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==2)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==3)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==4)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==5)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	$bala_fin_fab_qnty=$bala_fin_fab_qnty-$finish_fabric_qnty_array[$result[csf("id")]];
	$bala_grey_fab_qnty=$bala_grey_fab_qnty-$grey_fabric_qnty_array[$result[csf("id")]];
					if($bala_fin_fab_qnty>0  || $bala_grey_fab_qnty> 0)
					{
						 $count++;
			?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $count; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $count; ?>">
                        <td width="50"> <? echo $count;?></td>
                        <td width="120"><? echo $po_number[$result[csf("po_break_down_id")]];?> <input type="hidden" id="pobreakdownid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("po_break_down_id")]; ?>"/></td>
                        <td width="140"><? echo $garments_item[$result[csf("item_number_id")]];?></td>
                        <td width="120">
						<? echo $body_part[$result[csf("body_part_id")]];?>
                        <input  type="hidden" id="bodypartid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("body_part_id")]; ?>"/>
                        </td>
                        <td width="100"><? echo $color_type[$result[csf("color_type_id")]];?> <input type="hidden" id="colortype_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_type_id")]; ?>"/></td>   
                        <td width="100"><? echo $result[csf("construction")]; ?> 
                        <input type="hidden" id="construction_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("construction")]; ?>"/></td>

                        <input type="hidden" id="precostfabriccostdtlsid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("pre_cost_fabric_cost_dtls_id")]; ?>"/></td>
                        <td width="100"><? echo $result[csf("composition")]; ?> 
                        <input type="hidden" id="composition_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("composition")]; ?>"/>
                        <input type="hidden" id="cotaid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_size_table_id")]; ?>"/>
                        </td>
                        <td width="50"><?  echo $result[csf("gsm_weight")]; ?> <input type="hidden" id="gsmweight_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("gsm_weight")]; ?>"/></td>
</td>
                        <td width="80"><?  echo $result[csf("dia_width")]; ?><input type="hidden" id="diawidth_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("dia_width")]; ?>"/></td>  
                        <td width="80"><?  echo $result[csf("process_loss_percent")]; ?> <input type="hidden" id="processlosspercent_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("process_loss_percent")]; ?>"/></td>
                        <td width="50"><p><?  echo $result[csf("item_size")];?></p></td> 
                        <td width="150">
						<?
						echo $size_color_sensitive[$result[csf("color_size_sensitive")]]; 
						?>
                        </td>  
                        <td width="100">
						<?
						if($result[csf("color_size_sensitive")]!=0)
						{
						echo $color_library[$result[csf("color_number_id")]]; 
						}
						?>
                        </td>
                        <td width="100">
						<?
						$color_id="";
						if($result[csf("color_size_sensitive")]==3)
						{
						echo $constrast_color_arr[$result[csf("color_number_id")]]; 
						$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$result[csf('pre_cost_fabric_cost_dtls_id')]." and gmts_color_id=".$result[csf('color_number_id')]."");
						}
						else if($result[csf("color_size_sensitive")]==0)
						{
						echo $color_library[$result[csf("color")]]; 
						$color_id=$result[csf("color")];
						}
						else
						{
						echo $color_library[$result[csf("color_number_id")]]; 
						$color_id=$result[csf("color_number_id")];
						}
						
						?>
                        
                        <input  type="hidden" id="colorid_<? echo $count; ?>" style="width:20px;" value="<? echo $color_id; ?>"/>
						
                        </td>
                        <td width="100">
						<? echo $size_library[$result[csf("size_number_id")]]; ?>
                         <input  type="hidden" id="gmtssizeid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("size_number_id")]; ?>"/>
                        </td>
                        <td align="right" width="100"> <? echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]]; ?></td>
                        <td align="right" width="100">
                        <input type="text"   id="finscons_<? echo $count; ?>" name="finscons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'finish')" value="<?  echo $bala_fin_fab_qnty; ?>" placeholder="<?  echo $bala_fin_fab_qnty; $tot_bala_fin_fab_qnty+=$bala_fin_fab_qnty; ?>"/>
                        </td>
                        <td align="right" width="100">
                        <input type="text"   id="greycons_<? echo $count; ?>" name="greycons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'grey')" value="<?   echo $bala_grey_fab_qnty;?>" placeholder="<?   echo $bala_grey_fab_qnty; $tot_bala_grey_fab_qnty+=$bala_grey_fab_qnty;?>"/>
                        </td>
                        <td align="right" width="50"><input type="text" id="rate_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px" onChange="validate_value( <? echo $count ;?>,'rate')"  value="<? echo $result[csf("rate")]; ?>" <? if ($cbo_fabric_source==1){ echo "readonly";} else { echo "";}  ?>/> </td>   
                        <td align="right" width="100"> <input type="text" id="amount_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo def_number_format(($result[csf("rate")]*$bala_grey_fab_qnty),2,''); ?>" readonly/></td>
                        <? 
						$colarculfpercentreadonly="";
						if($result[csf("body_part_id")]==2 || $result[csf("body_part_id")]==3){
							$colarculfpercentreadonly="";
						}
						else 
						{
							$colarculfpercentreadonly="readonly";
						}
						?>
                        <td align="right" width="60"><input type="text" id="colarculfpercent_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? //echo $result[csf("id")]; ?>" <? echo $colarculfpercentreadonly; ?>  onChange="copy_colarculfpercent(<? echo $count; ?>)" /></td> 
                        <td align="right"><input type="text" id="updateid_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? //echo $result[csf("id")]; ?>" readonly/></td> 
                        <td align="right"><?  ?></td> 
                    </tr>
                <?
					}
		} // if count namearray end
	} // for each name arra
	?>
	</tbody>
    </table>
    
    </div>
    <?
    if($count==0)
	{
		$value ="Full Booked";
		$display="";
	}
	else
	{
		$value ="";
		$display="none";
	}
	?>
    <table width="2020" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	            <td width="50" align="center" colspan="21" id="full_booked" style="font-size:50; font-weight:bold; display:<? $display; ?>"><? echo $value; ?></td>
   </tr>
    <tr>
	            <th width="50"></th>
            	<th width="120"></th>
                <th width="140"></th>
                <th width="120"></th>
                <th width="100"></th>   
                <th width="100"></th>
                <th width="100"></th>
                <th width="50"></th>
                <th width="80"></th> 
                 <th width="80"></th> 
                <th width="50"></th>
                <th width="150"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th> 
                <th width="100"></th>   
                <th width="100"><? echo number_format($tot_bala_fin_fab_qnty,4);?></th>
                <th width="100"><? echo number_format($tot_bala_grey_fab_qnty,4);?></th>
                <th width="50"></th>
                <th width="100"></th>
                <th width="60"></th>  
                <th></th>          
   </tr>
   </tfoot>
   </table>
       <table width="2020" class="rpt_table" border="0" rules="all">
    <tr>
           	 <td align="center" width="100%" colspan="18" class="button_container">
				<?
                echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls",0,0,"",2) ;
				
				foreach($format_ids as $row_id)
				{
				
					if($row_id==1)
					{ 
						?>
						 <input type="button" value="Fabric Booking GR" onClick="generate_fabric_report_gr('show_fabric_booking_report_gr')"  style="width:150px;" name="print_gr" id="print_gr" class="formbutton" />
						 <input type="hidden" id="booking_option" name="booking_option" /><input type="hidden" id="booking_option_no" name="booking_option_no" />
						 <input type="hidden" id="booking_option_id" name="booking_option_id" /> 
						<?
					}
					if($row_id==2)
					{ 
						?>
				   <input type="button" value="Print Booking F1" onClick="generate_fabric_report('show_fabric_booking_report')"  style="width:130px;" name="print" id="print" class="formbutton" />
					   <? 
				   }
				   if($row_id==3)
					{ 
						?>
						 <input type="button" value="Print Booking F2" onClick="generate_fabric_report('show_fabric_booking_report3')"  style="width:130px;" name="print_booking3" id="print_booking3" class="formbutton" /> 
					    <? 
				    }
				   
				    if($row_id==4)
					{ 
						?>
						 <input type="button" value="Print For Cut F1" onClick="generate_fabric_report('show_fabric_booking_report1')"  style="width:130px;" name="print_booking1" id="print_booking1" class="formbutton" />
						
						<? 
				    }
				   
				   if($row_id==5)
					{ 
						?>
						  <input type="button" value="Print For Cut F2" onClick="generate_fabric_report('show_fabric_booking_report2')"  style="width:130px;" name="print_booking2" id="print_booking2" class="formbutton" /> 
						
						<?
					}
					if($row_id==6)
					{ 
						?>
						  <input type="button" value="Fabric Booking F1" onClick="generate_fabric_report('show_fabric_booking_report4')"  style="width:130px;" name="print_booking4" id="print_booking4" class="formbutton" />
						<? 
					}
				   
				   if($row_id==7)
					{ 
						?>
						 <input type="button" value="Fabric booking F2" onClick="generate_fabric_report('show_fabric_booking_report5')"  style="width:130px;" name="print_booking5" id="print_booking5" class="formbutton" />
						<? 
					}
				   if($row_id==28)
					{ 
						?>
						 <input type="button" value="Fabric booking AKH" onClick="generate_fabric_report('show_fabric_booking_report_akh')"  style="width:130px;" name="print_booking_akh" id="print_booking_akh" class="formbutton" />
						<? 
					}
				   if($row_id==45)
					{ 
						?>
						<input type="button" value="Fabric booking Urmi" onClick="generate_fabric_report('show_fabric_booking_report_urmi')"  style="width:130px;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
						<? 
					}
					if($row_id==53)
					{ 
						?>
						<input type="button" value="Fabric booking JK" onClick="generate_fabric_report('show_fabric_booking_report_jk')"  style="width:130px;" name="print_booking_jk" id="print_booking_jk" class="formbutton" />
						<? 
					}
				   
				}
                ?>  
               <!--<input type="button" value="Print Booking F1" onClick="generate_fabric_report('show_fabric_booking_report')"  style="width:130px" name="print" id="print" class="formbutton" />
                    <input type="button" value="Print Booking F2" onClick="generate_fabric_report('show_fabric_booking_report3')"  style="width:130px" name="print_booking3" id="print_booking3" class="formbutton" /> 
                    <input type="button" value="Print For Cut F1" onClick="generate_fabric_report('show_fabric_booking_report1')"  style="width:130px" name="print_booking1" id="print_booking1" class="formbutton" />
                    <input type="button" value="Print For Cut F2" onClick="generate_fabric_report('show_fabric_booking_report2')"  style="width:130px" name="print_booking2" id="print_booking2" class="formbutton" /> 
                    <input type="button" value="Fabric Booking F1" onClick="generate_fabric_report('show_fabric_booking_report4')"  style="width:130px" name="print_booking4" id="print_booking4" class="formbutton" />
                    
                    <input type="button" value="Fabric Booking F2" onClick="generate_fabric_report('show_fabric_booking_report5')"  style="width:130px" name="print_booking5" id="print_booking5" class="formbutton" />-->
           </td>        
   </tr>
   </table>
<?
}

if ($action=="show_fabric_booking")
{
	extract($_REQUEST);
	?>
    <table width="2020" class="rpt_table" border="0" rules="all">
        	<thead>
        	<tr>
                <th width="50">Sl</th>
            	<th width="120">PO Number</th>
                <th width="140">Gmts Item</th>
                <th width="120">Body Part</th>
                <th width="100">Color Type</th>   
                <th width="100">Construction</th>
                <th width="100">Composition</th>
                <th width="50">GSM</th>
                <th width="80">Dia/Width</th>  
                <th width="80">Proces Loss</th>
                <th width="50">Item Size</th>
                <th width="150">Col. Sensivity</th>
                <th width="100">Gmts.Color</th>
                <th width="100">Fab.Color</th>
                <th width="100">Gmts. Sizes</th> 
                <th width="100">Gmts. Quantity (Plan Cut)</th>  
                <th width="100">Fin Fab Qnty</th>
                <th width="100">Gray Qnty</th>
                <th width="50">Rate</th>
                <th width="100">Amount</th>
                 <th width="60">Colar/ Cuff percent</th>      
                <th></th>          
           </tr>
       </thead>
       </table>
        <div style=" max-height:200px; overflow-y:scroll; width:2020px"  align="left">
        <table width="2003" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
       <tbody>
	<?
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$tot_finish_fab_qnty=0;
	$tot_grey_fab_qnty=0;
	if($type==1)
	{
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$company2=return_field_value("company_name","wo_po_details_master a, wo_po_break_down b","b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no  and b.is_deleted=0 and b.status_active=1");
	$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name='".$company2."'  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids2);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$company2=return_field_value("company_name","wo_po_details_master a, wo_po_break_down b","b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no  and b.is_deleted=0 and b.status_active=1");
	$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name='".$company2."'  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids2);
	
	/*$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id,po_break_down_id", "id", "plan_cut_qnty");*/
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	
	$booking_dtls_id_array=return_library_array( "select a.id as booking_dtls_id,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "booking_dtls_id");
	
	$fabric_color_array=return_library_array( "select a.id as booking_dtls_id,a.fabric_color_id,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "fabric_color_id");
	
	$finish_fabric_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "fin_fab_qnty");
	
	$grey_fabric_qnty_array=return_library_array( "select sum(a.grey_fab_qnty) as grey_fab_qnty,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "grey_fab_qnty");
	
	$grey_fabric_amount_array=return_library_array( "select a.id as booking_dtls_id,a.amount,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "amount");
	
		$grey_fabric_rate_array=return_library_array( "select a.id as booking_dtls_id,a.rate,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "rate");

		$colar_cuff_percent_array=return_library_array( "select a.id as booking_dtls_id,a.colar_cuff_per,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "colar_cuff_per");

	
	$nameArray=sql_select("
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.job_no,
	a.item_number_id,
	a.body_part_id,
	a.fab_nature_id,
	a.fabric_source,
	a.color_type_id,
	a.gsm_weight,
	a.construction,
	a.composition,
	a.color_size_sensitive,
	a.costing_per,
	a.color,
	a.color_break_down,
	a.rate,
	b.id,
	b.po_break_down_id,
	b.color_size_table_id,
	b.color_number_id,
	b.gmts_sizes as size_number_id,
	b.dia_width,
	b.item_size,
	b.cons,
	b.process_loss_percent,
	b.requirment,
	b.pcs

FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b
WHERE
	a.job_no=b.job_no and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
        //}
        
        $count=0;
        foreach ($nameArray as $result)
        {
            if (count($nameArray)>0 )
            {
                $constrast_color_arr=array();
                if($result[csf("color_size_sensitive")]==3)
                {
                    $constrast_color=explode('__',$result[csf("color_break_down")]);
                    for($i=0;$i<count($constrast_color);$i++)
                    {
                        $constrast_color2=explode('_',$constrast_color[$i]);
                        $constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
                    }
                }
                
			    if ($count%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
						
					if($finish_fabric_qnty_array[$result[csf("id")]]>0  || $grey_fabric_qnty_array[$result[csf("id")]]> 0  )
					{
						 $count++;
			?>     
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $count; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $count; ?>">
                        <td width="50" onClick="select_id_for_delete_item(<? echo $count; ?>)"> <? echo $count;   ?></td>
                        <td width="120"><? echo $po_number[$result[csf("po_break_down_id")]];?> <input type="hidden" id="pobreakdownid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("po_break_down_id")]; ?>"/></td>
                        <td width="140"><? echo $garments_item[$result[csf("item_number_id")]];?></td>
                        <td width="120">
						<? echo $body_part[$result[csf("body_part_id")]];?>
                        <input  type="hidden" id="bodypartid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("body_part_id")]; ?>"/>
                        </td>
                        <td width="100"><? echo $color_type[$result[csf("color_type_id")]];?> <input type="hidden" id="colortype_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_type_id")]; ?>"/></td>   
                        <td width="100"><? echo $result[csf("construction")]; ?> 
                        <input type="hidden" id="construction_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("construction")]; ?>"/>
                        <input type="hidden" id="precostfabriccostdtlsid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("pre_cost_fabric_cost_dtls_id")]; ?>"/></td>
                        <td width="100"><? echo $result[csf("composition")]; ?>
                        <input type="hidden" id="composition_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("composition")]; ?>"/> 
                        <input type="hidden" id="cotaid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_size_table_id")]; ?>"/></td>
                        <td width="50"><?  echo $result[csf("gsm_weight")]; ?> <input type="hidden" id="gsmweight_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("gsm_weight")]; ?>"/></td>
                        <td width="80"><?  echo $result[csf("dia_width")]; ?><input type="hidden" id="diawidth_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("dia_width")]; ?>"/></td> 
                         <td width="80"><?  echo $result[csf("process_loss_percent")]; ?> <input type="hidden" id="processlosspercent_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("process_loss_percent")]; ?>"/></td> 
                        <td width="50"><p><?  echo $result[csf("item_size")];?></p></td> 
                        <td width="150"><? echo $size_color_sensitive[$result[csf("color_size_sensitive")]];  ?></td>  
                        <td width="100">
						<?
						if($result[csf("color_size_sensitive")]!=0)
						{
						echo $color_library[$result[csf("color_number_id")]]; 
						//echo $result[csf("color_number_id")];
						}
						?>
                        </td>
                        <td width="100">
						<? 
						$color_id="";
						if($type==1)
						{
							echo $color_library[$fabric_color_array[$result[csf("id")]]]; 
							$color_id=$fabric_color_array[$result[csf("id")]];
						}
						
						//========
						else if($type==2)
						{
							
							if($result[csf("color_size_sensitive")]==3)
							{
								//print_r($constrast_color_arr);
							echo $constrast_color_arr[$result[csf("color_number_id")]]; 
							$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result['job_no']."' and pre_cost_fabric_cost_dtls_id=".$result['pre_cost_fabric_cost_dtls_id']." and gmts_color_id=".$result['color_number_id']."");
							//echo $color_id;
							//echo "select contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='".$result['job_no']."' and pre_cost_fabric_cost_dtls_id=".$result['pre_cost_fabric_cost_dtls_id']." and gmts_color_id=".$result['color_number_id']."";
							}
							else if($result[csf("color_size_sensitive")]==0)
							{
							echo $color_library[$result[csf("color")]]; 
							$color_id=$result[csf("color")];
							}
							else
							{
							echo $color_library[$result[csf("color_number_id")]]; 
							$color_id=$result[csf("color_number_id")];
							}
						}
						?>
                         <input  type="hidden" id="colorid_<? echo $count; ?>" style="width:20px;" value="<? echo $color_id; ?>"/>
                        </td>
                        <td width="100">
						<? echo $size_library[$result[csf("size_number_id")]]; ?>
                        <input  type="hidden" id="gmtssizeid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("size_number_id")]; ?>"/>
                        </td>
                        <td align="right" width="100"> <? echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]]; ?></td>
                        <td align="right" width="100">
                        <input type="text" title="<? echo $result[csf("cons")]; ?>"   id="finscons_<? echo $count; ?>" name="finscons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'finish')" value="<?  echo $finish_fabric_qnty_array[$result[csf("id")]]; $tot_finish_fab_qnty+=$finish_fabric_qnty_array[$result[csf("id")]];  ?>" placeholder="<? // echo def_number_format($result[csf("fin_fab_qnty")]-$result[csf("efin_fab_qnty")],2,'');?>"/>
                        </td>
                        <td align="right" width="100">
                        <input type="text"   id="greycons_<? echo $count; ?>" name="greycons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'grey')" value="<?   echo  $grey_fabric_qnty_array[$result[csf("id")]]; $tot_grey_fab_qnty+=$grey_fabric_qnty_array[$result[csf("id")]];?>" placeholder="<?  // echo def_number_format($result[csf("grey_fab_qnty")],2,'');?>"/>
                        </td>
                        <td align="right" width="50"><input type="text" id="rate_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px" onChange="validate_value( <? echo $count ;?>,'rate')"  value="<? echo $grey_fabric_rate_array[$result[csf("id")]]; ?>" <? if ($cbo_fabric_source==1){ echo "readonly";} else { echo "";}  ?>/></td>   
                        <td align="right" width="100"> <input type="text" id="amount_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo $grey_fabric_amount_array[$result[csf("id")]]; $tot_grey_fab_amount+=$grey_fabric_amount_array[$result[csf("id")]]; ?>" readonly/></td>
                        
                        <? 
						$colarculfpercentreadonly="";
						if($result[csf("body_part_id")]==2 || $result[csf("body_part_id")]==3){
							$colarculfpercentreadonly="";
						}
						else 
						{
							$colarculfpercentreadonly="readonly";
						}
						?>
                        <td align="right" width="60"><input type="text" id="colarculfpercent_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo $colar_cuff_percent_array[$result[csf("id")]]; ?>" <? echo $colarculfpercentreadonly; ?> onChange="copy_colarculfpercent(<? echo $count; ?>)" /> </td> 
                        
                        <td align="right"> 
                        <input type="text" id="updateid_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo $booking_dtls_id_array [$result[csf("id")]]; ?>" readonly /> 
                        </td> 
                    </tr>
                <?
			}
		} // if count namearray end
	} // for each name arra
}
if($type==2)
   {
	   $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	execute_query( "update wo_booking_mst set is_apply_last_update=1  where  booking_no ='".$txt_booking_no."' and status_active=1 and is_deleted=0",0);
   $rID= execute_query( "update wo_booking_dtls set fin_fab_qnty=0, grey_fab_qnty=0   where  booking_no ='".$txt_booking_no."' and status_active=1 and is_deleted=0",0);
	if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				//echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				//echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				//echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				//echo "10**".$new_booking_no[0];
			}
		}
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$txt_booking_percent=str_replace("'","",$txt_booking_percent);
	$company2=return_field_value("company_name","wo_po_details_master a, wo_po_break_down b","b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no  and b.is_deleted=0 and b.status_active=1");
	$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name='".$company2."'  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids2);

	/*$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id,po_break_down_id", "id", "plan_cut_qnty");*/
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$booking_dtls_id_array=return_library_array( "select a.id as booking_dtls_id,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "booking_dtls_id");
	
	
	$finish_fabric_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "fin_fab_qnty");
	
	$grey_fabric_qnty_array=return_library_array( "select sum(a.grey_fab_qnty) as grey_fab_qnty,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.id", "id", "grey_fab_qnty");
		$colar_cuff_percent_array=return_library_array( "select a.id as booking_dtls_id,a.colar_cuff_per,b.id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".$txt_order_no_id.") and a.booking_no='$txt_booking_no' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0", "id", "colar_cuff_per");

	
	$nameArray=sql_select("
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.job_no,
	a.item_number_id,
	a.body_part_id,
	a.fab_nature_id,
	a.fabric_source,
	a.color_type_id,
	a.gsm_weight,
	a.construction,
	a.composition,
	a.color_size_sensitive,
	a.costing_per,
	a.color,
	a.color_break_down,
	a.rate,
	b.id,
	b.po_break_down_id,
	b.color_size_table_id,
	b.color_number_id,
	b.gmts_sizes as size_number_id,
	b.dia_width,
	b.item_size,
	b.cons,
	b.process_loss_percent,
	b.requirment,
	b.pcs

FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b
WHERE
	a.job_no=b.job_no and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
		$count=0;
        foreach ($nameArray as $result)
        {
            if (count($nameArray)>0 )
            {
                $constrast_color_arr=array();
                if($result[csf("color_size_sensitive")]==3)
                {
                    $constrast_color=explode('__',$result[csf("color_break_down")]);
                    for($i=0;$i<count($constrast_color);$i++)
                    {
                        $constrast_color2=explode('_',$constrast_color[$i]);
                        $constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
                    }
          }
		   
	$bala_fin_fab_qnty=0;
	$bala_grey_fab_qnty=0;
	if($result[csf("costing_per")]==1)
	{
		
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");

	}
	if($result[csf("costing_per")]==2)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==3)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==4)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
	if($result[csf("costing_per")]==5)
	{
		$bala_fin_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("cons")])*$txt_booking_percent)/100),5,"");
		$bala_grey_fab_qnty =def_number_format((((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")])*$txt_booking_percent)/100),5,"");
	}
                
			    if ($count%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";
	 $bala_fin_fab_qnty=$bala_fin_fab_qnty-$finish_fabric_qnty_array[$result[csf("id")]];
	 $bala_grey_fab_qnty=$bala_grey_fab_qnty-$grey_fabric_qnty_array[$result[csf("id")]];
					 
					if($bala_fin_fab_qnty >0  || $bala_grey_fab_qnty > 0  )
					{
						
						 $count++;
		?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $count; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $count; ?>">
                        <td width="50" onClick="select_id_for_delete_item(<? echo $count; ?>)"> <? echo $count; ?></td>
                        <td width="120"><? echo $po_number[$result[csf("po_break_down_id")]];?> <input type="hidden" id="pobreakdownid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("po_break_down_id")]; ?>"/></td>
                        <td width="140"><? echo $garments_item[$result[csf("item_number_id")]];?></td>
                        <td width="120">
						<? echo $body_part[$result[csf("body_part_id")]];?>
                        <input  type="hidden" id="bodypartid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("body_part_id")]; ?>"/>
                        </td>
                        <td width="100"><? echo $color_type[$result[csf("color_type_id")]];?> <input type="hidden" id="colortype_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_type_id")]; ?>"/></td>   
                        <td width="100"><? echo $result[csf("construction")]; ?> 
                        <input type="hidden" id="construction_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("construction")]; ?>"/>
                        <input type="hidden" id="precostfabriccostdtlsid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("pre_cost_fabric_cost_dtls_id")]; ?>"/></td>
                        <td width="100"><? echo $result[csf("composition")]; ?>
                        <input type="hidden" id="composition_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("composition")]; ?>"/> 
                        <input type="hidden" id="cotaid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("color_size_table_id")]; ?>"/></td>
                        <td width="50"><?  echo $result[csf("gsm_weight")]; ?> <input type="hidden" id="gsmweight_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("gsm_weight")]; ?>"/></td>
                        <td width="80"><?  echo $result[csf("dia_width")]; ?><input type="hidden" id="diawidth_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("dia_width")]; ?>"/></td> 
                         <td width="80"><?  echo $result[csf("process_loss_percent")]; ?> <input type="hidden" id="processlosspercent_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("process_loss_percent")]; ?>"/></td> 
                        <td width="50"><p><?  echo $result[csf("item_size")];?></p></td> 
                        <td width="150"><? echo $size_color_sensitive[$result[csf("color_size_sensitive")]];  ?></td>  
                        <td width="100">
						<?
						if($result[csf("color_size_sensitive")]!=0)
						{
						echo $color_library[$result[csf("color_number_id")]]; 
						//echo $result[csf("color_number_id")];
						}
						?>
                        </td>
                        <td width="100">
						<? 
						$color_id="";
						if($type==1)
						{
							echo $color_library[$fabric_color_array[$result[csf("id")]]]; 
							$color_id=$fabric_color_array[$result[csf("id")]];
						}
						
						//========
						else if($type==2)
						{
							
							if($result[csf("color_size_sensitive")]==3)
							{
								//print_r($constrast_color_arr);
							echo $constrast_color_arr[$result[csf("color_number_id")]]; 
							$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$result[csf('pre_cost_fabric_cost_dtls_id')]." and gmts_color_id=".$result[csf('color_number_id')]."");
							//echo $color_id;
							//echo "select contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='".$result['job_no']."' and pre_cost_fabric_cost_dtls_id=".$result['pre_cost_fabric_cost_dtls_id']." and gmts_color_id=".$result['color_number_id']."";
							}
							else if($result[csf("color_size_sensitive")]==0)
							{
							echo $color_library[$result[csf("color")]]; 
							$color_id=$result[csf("color")];
							}
							else
							{
							echo $color_library[$result[csf("color_number_id")]]; 
							$color_id=$result[csf("color_number_id")];
							}
						}
						?>
                         <input  type="hidden" id="colorid_<? echo $count; ?>" style="width:20px;" value="<? echo $color_id; ?>"/>
                        </td>
                        <td width="100">
						<? echo $size_library[$result[csf("size_number_id")]]; ?>
                        <input  type="hidden" id="gmtssizeid_<? echo $count; ?>" style="width:20px;" value="<? echo $result[csf("size_number_id")]; ?>"/>
                        </td>
                        <td align="right" width="100"> <? echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]]; ?></td>
                        <td align="right" width="100">
                        <input type="text"   id="finscons_<? echo $count; ?>" name="finscons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'finish')" value="<?  echo $bala_fin_fab_qnty; $tot_finish_fab_qnty+=$bala_fin_fab_qnty;  ?>" placeholder="<? // echo def_number_format($result[csf("fin_fab_qnty")]-$result[csf("efin_fab_qnty")],2,'');?>"/>
	
                        </td>
                        <td align="right" width="100">
                        <input type="text"   id="greycons_<? echo $count; ?>" name="greycons_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  onChange="validate_value( <? echo $count ;?>,'grey')" value="<?   echo  $bala_grey_fab_qnty; $tot_grey_fab_qnty+=$bala_grey_fab_qnty;?>" placeholder="<?  // echo def_number_format($result[csf("grey_fab_qnty")],2,'');?>"/>
                        </td>
                        <td align="right" width="50"><input type="text" id="rate_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px" onChange="validate_value( <? echo $count ;?>,'rate')"  value="<? echo $result[csf("rate")]; ?>" <? if ($cbo_fabric_source==1){ echo "readonly";} else { echo "";}  ?>/></td>   
                        <td align="right" width="100"> <input type="text" id="amount_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo def_number_format(($result[csf("rate")]*$bala_grey_fab_qnty),2,''); ?>" readonly/></td>
                        <? 
						$colarculfpercentreadonly="";
						if($result[csf("body_part_id")]==2 || $result[csf("body_part_id")]==3){
							$colarculfpercentreadonly="";
						}
						else 
						{
							$colarculfpercentreadonly="readonly";
						}
						?>
                        <td align="right" width="60"><input type="text" id="colarculfpercent_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo $colar_cuff_percent_array[$result[csf("id")]]; ?>" <? echo $colarculfpercentreadonly; ?> onChange="copy_colarculfpercent(<? echo $count; ?>)" /></td> 
                        <td align="right"> 
                        <input type="text" id="updateid_<? echo $count; ?>" style=" width:100%; height:100%; border:none; text-align:right; background-color:<? echo $bgcolor; ?>;font-family:verdana; font-size:11px"  value="<? echo $booking_dtls_id_array [$result[csf("id")]]; ?>" readonly /> 
                        </td> 
                    </tr>
        <?
				}
			  }
		    }
    }
	?>
	</tbody>
    </table>
    </div>
     <?
    if($count==0)
	{
		$value ="Full Booked";
		$display="";
	}
	else
	{
		$value ="";
		$display="none";
	}
	?>
    <table width="2020" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	            <td width="50" align="center" colspan="21" id="full_booked" style="font-size:50; font-weight:bold; display:<? $display; ?>"><? echo $value; ?></td>
            	        
   </tr>
    <tr>
	            <th width="50"></th>
            	<th width="120"></th>
                <th width="140"></th>
                <th width="120"></th>
                <th width="100"></th>   
                <th width="100"></th>
                <th width="100"></th>
                <th width="50"></th>
                <th width="80"></th> 
                <th width="80"></th>  
                <th width="50"></th>
                <th width="150"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th> 
                <th width="100"></th>   
                <th width="100"><? echo number_format($tot_finish_fab_qnty,4);?></th>
                <th width="100"><? echo number_format($tot_grey_fab_qnty,4); ?></th>
                <th width="50"></th>
                <th width="100"><? echo number_format($tot_grey_fab_amount,2); ?></th> 
                <th width="60"></th> 
                <th></th>          
   </tr>
   </tfoot>
   </table>
       <table width="2020" class="rpt_table" border="0" rules="all">

    <tr>
           	 <td align="center" width="100%" colspan="19" class="button_container">
                    
                    <?
                    echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls",0,0,"",2) ;
				foreach($format_ids as $row_id)
				{
				
					if($row_id==1)
					{ 
						?>
						 <input type="button" value="Fabric Booking GR" onClick="generate_fabric_report_gr('show_fabric_booking_report_gr')"  style="width:150px;" name="print_gr" id="print_gr" class="formbutton" />
						 <input type="hidden" id="booking_option" name="booking_option" /><input type="hidden" id="booking_option_no" name="booking_option_no" />
						 <input type="hidden" id="booking_option_id" name="booking_option_id" /> 
						<?
					}
					if($row_id==2)
					{ 
						?>
				   <input type="button" value="Print Booking F1" onClick="generate_fabric_report('show_fabric_booking_report')"  style="width:130px;" name="print" id="print" class="formbutton" />
					   <? 
				   }
				   if($row_id==3)
					{ 
						?>
						 <input type="button" value="Print Booking F2" onClick="generate_fabric_report('show_fabric_booking_report3')"  style="width:130px;" name="print_booking3" id="print_booking3" class="formbutton" /> 
					    <? 
				    }
				   
				    if($row_id==4)
					{ 
						?>
						 <input type="button" value="Print For Cut F1" onClick="generate_fabric_report('show_fabric_booking_report1')"  style="width:130px;" name="print_booking1" id="print_booking1" class="formbutton" />
						
						<? 
				    }
				   
				   if($row_id==5)
					{ 
						?>
						  <input type="button" value="Print For Cut F2" onClick="generate_fabric_report('show_fabric_booking_report2')"  style="width:130px;" name="print_booking2" id="print_booking2" class="formbutton" /> 
						
						<?
					}
					if($row_id==6)
					{ 
						?>
						  <input type="button" value="Fabric Booking F1" onClick="generate_fabric_report('show_fabric_booking_report4')"  style="width:130px;" name="print_booking4" id="print_booking4" class="formbutton" />
						<? 
					}
				   
				   if($row_id==7)
					{ 
						?>
						 <input type="button" value="Fabric booking F2" onClick="generate_fabric_report('show_fabric_booking_report5')"  style="width:130px;" name="print_booking5" id="print_booking5" class="formbutton" />
						<? 
					}
				   if($row_id==28)
					{ 
						?>
						 <input type="button" value="Fabric booking AKH" onClick="generate_fabric_report('show_fabric_booking_report_akh')"  style="width:130px;" name="print_booking_akh" id="print_booking_akh" class="formbutton" />
						<? 
					}
				   if($row_id==45)
					{ 
						?>
						<input type="button" value="Fabric booking Urmi" onClick="generate_fabric_report('show_fabric_booking_report_urmi')"  style="width:130px;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
						<? 
					}
					if($row_id==53)
					{ 
						?>
						<input type="button" value="Fabric booking JK" onClick="generate_fabric_report('show_fabric_booking_report_jk')"  style="width:130px;" name="print_booking_jk" id="print_booking_jk" class="formbutton" />
						<? 
					}
				   
				}
                    ?>  
                    	
                    <!--<input type="button" value="Print Booking F1" onClick="generate_fabric_report('show_fabric_booking_report')"  style="width:130px" name="print" id="print" class="formbutton" />
                    <input type="button" value="Print Booking F2" onClick="generate_fabric_report('show_fabric_booking_report3')"  style="width:130px" name="print_booking3" id="print_booking3" class="formbutton" /> 
                    <input type="button" value="Print For Cut F1" onClick="generate_fabric_report('show_fabric_booking_report1')"  style="width:130px" name="print_booking1" id="print_booking1" class="formbutton" />
                    <input type="button" value="Print For Cut F2" onClick="generate_fabric_report('show_fabric_booking_report2')"  style="width:130px" name="print_booking2" id="print_booking2" class="formbutton" /> 
                    <input type="button" value="Fabric Booking F1" onClick="generate_fabric_report('show_fabric_booking_report4')"  style="width:130px" name="print_booking4" id="print_booking4" class="formbutton" />
                    
                    <input type="button" value="Fabric Booking F2" onClick="generate_fabric_report('show_fabric_booking_report5')"  style="width:130px" name="print_booking5" id="print_booking5" class="formbutton" />-->
                    <input type="button" class="formbutton" style="width:200px" value="Apply Last Update" onClick="fnc_show_booking(2)">
                    
 
           </td>        
   </tr>
   
   
   </table>
   
<?
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'Fb', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'Fb', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,booking_percent,colar_excess_percent,cuff_excess_percent,ready_to_approved,inserted_by,insert_date,rmg_process_breakdown,fabric_composition,entry_form"; 
		 $data_array ="(".$id.",1,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$txt_booking_percent.",".$txt_colar_excess_percent.",".$txt_cuff_excess_percent.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$processloss_breck_down.",".$txt_fabriccomposition.",86)";
		 $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
	
		/*if (is_duplicate_field( "sample_type_id", "wo_po_sample_approval_info", "job_no_mst=$txt_job_no and sample_type_id=$cbo_sample_type and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
			 
		
		
		$field_array="company_id*buyer_id*job_no*po_break_down_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*booking_percent*colar_excess_percent*cuff_excess_percent*ready_to_approved*updated_by*update_date*rmg_process_breakdown*fabric_composition"; 
		 $data_array ="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$txt_booking_percent."*".$txt_colar_excess_percent."*".$txt_cuff_excess_percent."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$processloss_breck_down."*".$txt_fabriccomposition."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);   
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_roolback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
		 
		
	}
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type, is_short,fabric_color_id,fin_fab_qnty,grey_fab_qnty,rate,amount,color_type,construction,copmposition,gsm_weight,dia_width,process_loss_percent,colar_cuff_per,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $pobreakdownid="pobreakdownid_".$i;
			 $precostfabriccostdtlsid="precostfabriccostdtlsid_".$i;
			 $colorid="colorid_".$i;
			 $cotaid="cotaid_".$i;
			 $finscons="finscons_".$i;
			 $greycons="greycons_".$i;
			 $rate="rate_".$i;
			 $amount="amount_".$i;
			 $colortype="colortype_".$i;
			 $construction="construction_".$i;
			 $composition="composition_".$i;
			 $gsmweight="gsmweight_".$i;
			 $diawidth="diawidth_".$i;
			 $processlosspercent="processlosspercent_".$i;
			 $colarculfpercent="colarculfpercent_".$i;
			 $updateid="updateid_".$i;
			 
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$$pobreakdownid.",".$$precostfabriccostdtlsid.",".$$cotaid.",".$txt_booking_no.",1,2,".$$colorid.",".$$finscons.",".$$greycons.",".$$rate.",".$$amount.",".$$colortype.",".$$construction.",".$$composition.",".$$gsmweight.",".$$diawidth.",".$$processlosspercent.",".$$colarculfpercent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		//echo  "INSERT INTO wo_booking_dtls (".$field_array.") VALUES ".$data_array.""; 
		  $rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1); 
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}		
		 $id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $add_comma=0;
		 $field_array="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type, is_short,fabric_color_id,fin_fab_qnty,grey_fab_qnty,rate,amount,color_type,construction,copmposition,gsm_weight,dia_width,process_loss_percent,colar_cuff_per,inserted_by,insert_date";
		 
		 $field_array_up="job_no*po_break_down_id*pre_cost_fabric_cost_dtls_id*color_size_table_id*booking_no*booking_type*is_short *fabric_color_id*fin_fab_qnty*grey_fab_qnty*rate*amount*color_type*construction*copmposition*gsm_weight*dia_width*process_loss_percent*colar_cuff_per*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $pobreakdownid="pobreakdownid_".$i;
			 $precostfabriccostdtlsid="precostfabriccostdtlsid_".$i;
			 $colorid="colorid_".$i;
			 $cotaid="cotaid_".$i;
			 $finscons="finscons_".$i;
			 $greycons="greycons_".$i;
			 $rate="rate_".$i;
			 $amount="amount_".$i;
			 $colortype="colortype_".$i;
			 $construction="construction_".$i;
			 $composition="composition_".$i;
			 $gsmweight="gsmweight_".$i;
			 $diawidth="diawidth_".$i;
			 $processlosspercent="processlosspercent_".$i;
			 $colarculfpercent="colarculfpercent_".$i;
			 $updateid="updateid_".$i;
			 if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$txt_job_no."*".$$pobreakdownid."*".$$precostfabriccostdtlsid."*".$$cotaid."*".$txt_booking_no."*1*2*".$$colorid."*".$$finscons."*".$$greycons."*".$$rate."*".$$amount."*".$$colortype."*".$$construction."*".$$composition."*".$$gsmweight."*".$$diawidth."*".$$processlosspercent."*".$$colarculfpercent."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 
			//if ($i!=1) $data_array .=",";
			//$data_array .="(".$id.",".$txt_job_no.",".$$pobreakdownid.",".$$precostfabriccostdtlsid.",".$$cotaid.",".$txt_booking_no.",1,".$$colorid.",".$$finscons.",".$$greycons.",".$$rate.",".$$amount.",".$$colortype.",".$$construction.",".$$composition.",".$$gsmweight.",".$$diawidth.",".$$processlosspercent.")";
			//$id=$id+1;
			}
			if(str_replace("'",'',$$updateid)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$$pobreakdownid.",".$$precostfabriccostdtlsid.",".$$cotaid.",".$txt_booking_no.",1,2,".$$colorid.",".$$finscons.",".$$greycons.",".$$rate.",".$$amount.",".$$colortype.",".$$construction.",".$$composition.",".$$gsmweight.",".$$diawidth.",".$$processlosspercent.",".$$colarculfpercent.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			    $id=$id+1;
				$add_comma++;
			}
		 }
		//echo  bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array !="")
		 {
		 $rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);
		 }

		 //$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace(",","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "1**".str_replace(",","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($selected_id_for_delete=="")
		{
			$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  booking_no =$txt_booking_no",0);	
		}
		else
		{
			$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  id in(".str_replace("'","",$selected_id_for_delete).")",0);
			//echo "update wo_booking_dtls set status_active=0,is_deleted =1 where  id in(".str_replace("'","",$selected_id_for_delete).")";
		}
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace(",","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);   
				echo "2**".str_replace(",","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="check_is_booking_used")
{
	$work_order_no=return_field_value("work_order_no","com_pi_item_details","work_order_no='$data' and status_active =1 and is_deleted=0");
	echo $work_order_no;
	die;
}

if($action=="delete_booking_item")
{
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
  $rID = execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1 where  booking_no ='$data'",0);	
   if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);   
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function add_break_down_tr(i) 
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
	}
		  
}

function fn_deletebreak_down_tr(rowNo) 
{   
	
	
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	
}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		freeze_window(operation);
		http.open("POST","fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            
            
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <? 
						}
					} 
					?>
                </tbody>
                </table>
                
                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="dtm_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
<script>
 
function trims_popup(page_link,title,i)
{
	var job_no=$('#txt_job_no').val();
	var booking_no=$('#txt_booking_no').val();
	var selected_no=$('#txt_order_no_id').val();
	var fabric=$('#fabric_'+i).val();
	var color=$('#color_'+i).val();
	var fabric_cost_id=$('#fabric_cost_id_'+i).val();
	
	
	if(booking_no=='')
	{
		alert('Booking  Not Found.');
		$('#txt_booking_no').focus();
		return;
	}
	
	page_link=page_link+'&job_no='+job_no+'&booking_no='+booking_no+'&selected_no='+selected_no+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id+'&index='+i;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../../')
	
}
</script>
<?
//echo $job_no."_".$booking_no."_".$selected_no;
//$sql=sql_select("select id,booking_id,booking_no,pre_cost_fabric_cost_id,fabric_color,precost_trim_cost_id,item_group,qty from wo_dye_to_match where booking_no='$booking_no'");
$dtm_arr=array();
$sql=sql_select("select pre_cost_fabric_cost_id,fabric_color,sum(qty) as qty  from wo_dye_to_match where booking_no='$booking_no' group by pre_cost_fabric_cost_id,fabric_color");
foreach($sql as $row){
	$dtm_arr[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]]=$row[csf('qty')];
}


$trims_matches_sql=sql_select("select a.id,a.body_part_id, a.composition, a.construction,a.gsm_weight,d.fabric_color_id,min(c.id) as cid,sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
			WHERE a.job_no=b.job_no and
			a.id=b.pre_cost_fabric_cost_dtls_id and
			c.job_no_mst=a.job_no and 
			c.id=b.color_size_table_id and
			b.po_break_down_id=d.po_break_down_id and 
			b.color_size_table_id=d.color_size_table_id and 
			b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
			d.booking_no ='$booking_no' and 
			d.status_active=1 and 
			d.is_deleted=0 
			group by a.id,a.body_part_id, a.composition, a.construction,a.gsm_weight,d.fabric_color_id order by a.id, cid ");
	
?>
	


</head>
<body>
<div align="center" style="width:100%;" >
<input type="hidden" id="txt_job_no" name="txt_job_no" value="<? echo $job_no;  ?>"/>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
<input type="hidden" id="txt_order_no_id" name="txt_order_no_id" value="<? echo $selected_no;  ?>"/>
	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match1" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="300">Fabric Driscription</th>
		<th width="150">Color</th>
		<th width="100">Fabric Qnty.</th>
		<th width="100">Trims</th>
	  </tr>
	</thead>
	<tbody>
	<? 
	
	$i=1;
	foreach($trims_matches_sql as $row)
	 { 
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="300"> 
        <input class="text_boxes" type="text" style="width:300px;"  name="fabric_<? echo $i; ?>" id="fabric_<? echo $i; ?>" value="<? echo $body_part[$row[csf('body_part_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')];?>" readonly/> 
         <input class="text_boxes" type="hidden" style="width:300px;"  name="fabric_cost_id_<? echo $i; ?>" id="fabric_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/> 
        </td>
	  	<td width="150">
        <? echo $color_library[$row[csf('fabric_color_id')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $row[csf('fabric_color_id')];?>" readonly/>
        </td>
	  	<td width="100" align="right">
		<? echo $row[csf('fin_fab_qnty')];?>
        </td>
	  	<td width="100"><input class="text_boxes" type="text" style="width:100px;"  name="trims_<? echo $i; ?>" id="trims_<? echo $i; ?>" value="<? echo $dtm_arr[$row[csf('id')]][$row[csf('fabric_color_id')]] ?>" onDblClick="trims_popup('fabric_booking_controller.php?action=trims_popup','Trims Item',<? echo $i ?>)" readonly/></td>
	  </tr>
	  <? $i++; 
	  
	  } ?>
	</tbody>
	</table>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="trims_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
<script>
function fnc_fabric_dye_to_match( operation )
{
	
	var job_no=$('#txt_job_no').val();
	var booking_no=$('#txt_booking_no').val();
	var selected_no=$('#txt_order_no_id').val();
	var fabric=$('#fabric').val();
	var color=$('#color').val();
	var fabric_cost_id=$('#fabric_cost_id').val();
	var index=$('#index').val();
	
	    var row_num=$('#tbl_trims_dyes_match tbody tr').length;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			data_all=data_all+get_submitted_data_string('trim_group_'+i+'*pre_cost_trim_cost_id_'+i+'*dyeqty_'+i+'*color_'+i,"../../../",i);
			
		}
		//alert(data_all); return;
		var data="action=save_update_delete_dye_to_match&operation="+operation+'&total_row='+row_num+data_all+'&booking_no='+booking_no+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id;
		freeze_window(operation);
		http.open("POST","fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{
	
	if(http.readyState == 4) 
	{
	        var reponse=trim(http.responseText).split('**');
			 if(trim(reponse[0])=='approved')
			 {
				 alert("This booking is approved");
				 release_freezing();
				 return;
			 }
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1)
			{
				var index=$('#index').val();
				parent.document.getElementById('trims_'+index).value=reponse[1];
				parent.emailwindow.hide();
			}
	}
}
</script>
<?
$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");

$dtm_arr=array();
$dtm_arr_item_color=array();
$sql=sql_select("select pre_cost_fabric_cost_id,fabric_color,precost_trim_cost_id,item_color,sum(qty) as qty from wo_dye_to_match where booking_no='$booking_no' and pre_cost_fabric_cost_id='$fabric_cost_id' group by pre_cost_fabric_cost_id,fabric_color,item_color,precost_trim_cost_id");

foreach($sql as $row){
	$dtm_arr[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('precost_trim_cost_id')]]=$row[csf('qty')];
	$dtm_arr_item_color[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('precost_trim_cost_id')]]=$row[csf('item_color')];
}
$trims_matches_sql=sql_select("select a.id,a.job_no,a.trim_group,a.description,a.cons_uom FROM wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,  wo_booking_dtls c   
			WHERE 
			a.job_no=b.job_no and
			a.job_no=c.job_no and
			a.id=b.wo_pre_cost_trim_cost_dtls_id and
			b.po_break_down_id=c.po_break_down_id and
			c.booking_no ='$booking_no' and
			b.po_break_down_id in($selected_no)
			group by a.id,a.job_no,a.trim_group,a.description,a.cons_uom
			");

$condition= new condition();
					if(str_replace("'","",$job_no) !=''){
					$condition->job_no("='$job_no'");
					}
					if(str_replace("'","",$selected_no) !=''){
						$condition->po_id("in($selected_no)");
					}
					$condition->init();
					$trim= new trims($condition);
					//echo $trim->getQuery();
					$totalqtyarray_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
?>
</head>
<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
<form id="dtm_1">
<input type="hidden" id="txt_job_no" name="txt_job_no" value="<? echo $job_no;  ?>"/>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
<input type="hidden" id="txt_order_no_id" name="txt_order_no_id" value="<? echo $selected_no;  ?>"/>
<input type="hidden" id="fabric" name="txt_order_no_id" value="<? echo $fabric;  ?>"/>
<input type="hidden" id="color" name="color" value="<? echo $color;  ?>"/>
<input type="hidden" id="fabric_cost_id" name="fabric_cost_id" value="<? echo $fabric_cost_id;  ?>"/>
<input type="hidden" id="index" name="index" value="<? echo $index;  ?>"/>
	<table width="700" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="150">Item Group</th>
		<th width="150">Item Color</th>
        <th width="150">Item Driscription</th>
		<th width="100">Req. Qty.</th>
		<th width="60">Uom</th>
        <th width="60">Dye Qnty</th>
	  </tr>
	</thead>
	<tbody>
	<? 
	
	$i=1;
	foreach($trims_matches_sql as $row)
	 { 
	 $item_color=$dtm_arr_item_color[$fabric_cost_id][$color][$row[csf('id')]];
	 if($item_color==""){
		 $item_color=$color;
	 }
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="150"> 
        <? echo $lib_item_group_arr[$row[csf('trim_group')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="trim_group_<? echo $i; ?>" id="trim_group_<? echo $i; ?>" value="<? echo $row[csf('trim_group')];?>" readonly/> 
         <input class="text_boxes" type="hidden" style="width:150px;"  name="pre_cost_trim_cost_id_<? echo $i; ?>" id="pre_cost_trim_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/> 
        </td>
	  	<td width="150">
        <? //echo $color_library[$color];?>
        <input class="text_boxes" type="text" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $color_library[$item_color];?>"/>
        </td>
	  	<td width="120">
		<? echo $row[csf('description')];?>
        </td>
	  	<td width="100">
        <input class="text_boxes_numeric" type="text" style="width:100px;"  name="reqqty_<? echo $i; ?>" id="reqqty_<? echo $i; ?>" value="<? echo $totalqtyarray_arr[$row[csf('job_no')]][$row[csf('id')]]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes" type="text" style="width:60px;"  name="uom_<? echo $i; ?>" id="uom_<? echo $i; ?>" value="<? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes_numeric" type="text" style="width:60px;"  name="dyeqty_<? echo $i; ?>" id="dyeqty_<? echo $i; ?>" value="<? echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]] ?>"/>
        </td>
	  </tr>
	  <? $i++; 
	  
	  } ?>
	</tbody>
	</table>
    </form>
    <table width="650" cellspacing="0" class="" border="0">
        <tr>
            <td align="center" height="15" width="100%"> </td>
        </tr>
        <tr>
            <td align="center" width="100%" class="button_container">
            <?
            echo load_submit_buttons( $permission, "fnc_fabric_dye_to_match", 0,0 ,"reset_form('dtm_1','','','','')",1) ; 
            ?>
            </td> 
        </tr>
    </table>
    </fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_dye_to_match")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$booking_id=return_field_value( "id", "wo_booking_mst","booking_no ='$booking_no'");
		
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no='$booking_no'");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
				//var data="action=save_update_delete_dye_to_match&operation="+operation+'&total_row='+row_num+data_all+'&booking_no='+booking_no+'&fabric='+fabric+'&color='+color;
				//data_all=data_all+get_submitted_data_string('trim_group_'+i+'*pre_cost_trim_cost_id_'+i+'*dyeqty_'+i,"../../../",i);

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		$id=return_next_id( "id", "wo_dye_to_match", 1 ) ;
		$field_array="id,booking_id,booking_no,pre_cost_fabric_cost_id,fabric_color,item_color,precost_trim_cost_id,item_group,qty";
		$total_dye_qty=0;
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++)
		{
			$trim_group="trim_group_".$i;
			$pre_cost_trim_cost_id="pre_cost_trim_cost_id_".$i;
			$dyeqty="dyeqty_".$i;
			$item_color="color_".$i;
			 
			if (!in_array(str_replace("'","",$$item_color),$new_array_color)){
				$color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name");  
				$new_array_color[$color_id]=str_replace("'","",$$item_color);
			}
			else{
				$color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			}
			if($color_id==""){
				$color_id=0;
			}
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$booking_id.",'".$booking_no."','".$fabric_cost_id."','".$color."','".$color_id."',".$$pre_cost_trim_cost_id.",".$$trim_group.",".$$dyeqty.")";
			$total_dye_qty+=str_replace("'"," ",$$dyeqty);
			$id=$id+1;
		}
		$rID_de3=execute_query( "delete from wo_dye_to_match where  pre_cost_fabric_cost_id ='".$fabric_cost_id."' and fabric_color= '".$color."'",0);
		$rID=sql_insert("wo_dye_to_match",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
			mysql_query("COMMIT");  
			echo "0**".$total_dye_qty;
			}
			else{
			mysql_query("ROLLBACK"); 
			echo "10**".$total_dye_qty;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
			oci_commit($con);
			echo "0**".$total_dye_qty;
			}
			else{
			oci_rollback($con);
			echo "10**".$total_dye_qty;
			}
		}
		disconnect($con);
		die;
	}	
}

if($action=="rmg_process_loss_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function js_set_value_set()
{
	
	  var cutting_per=$('#cutting_per').val(); 
	  if(cutting_per=="")
	  {
		cutting_per=0;  
	  }
	  
	  var embbroidery_per=$('#embbroidery_per').val(); 
	  if(embbroidery_per=="")
	  {
		embbroidery_per=0;  
	  }
	  
	  var printing_per=$('#printing_per').val(); 
	  if(printing_per=="")
	  {
		printing_per=0;  
	  }
	  
	  var wash_per=$('#wash_per').val(); 
	  if(wash_per=="")
	  {
		wash_per=0;  
	  }
	  
	  var sew_per=$('#sew_per').val(); 
	  if(sew_per=="")
	  {
		sew_per=0;  
	  }
	  
	  var fin_per=$('#fin_per').val(); 
	  if(fin_per=="")
	  {
		fin_per=0;  
	  }
	   
	var knitt_per=$('#knitt_per').val(); 
	  if(knitt_per=="")
	  {
		knitt_per=0;  
	  }
	  
	  var dying_per=$('#dying_per').val(); 
	  if(dying_per=="")
	  {
		dying_per=0;  
	  }
	  
	  var extracutt_per=$('#extracutt_per').val(); 
	  if(extracutt_per=="")
	  {
		extracutt_per=0;  
	  }
	  
	  var other_per=$('#other_per').val(); 
	  if(other_per=="")
	  {
		other_per=0;  
	  }
	  
	  var neck_sleev_printing_per=$('#neck_sleev_printing_per').val();
	  if(neck_sleev_printing_per=="")
	  {
		neck_sleev_printing_per=0;  
	  }
	  
	  var gmt_other_per=$('#gmt_other_per').val();
	  if(gmt_other_per=="")
	  {
		gmt_other_per=0;  
	  }
	  
	  
	  var yarn_dyeing_per=$('#yarn_dyeing_per').val();
	  if(yarn_dyeing_per=="")
	  {
		yarn_dyeing_per=0;  
	  }
	   
	  var all_over_print_per=$('#all_over_print_per').val();
	  if(all_over_print_per=="")
	  {
		all_over_print_per=0;  
	  }
	  
	  
	  var lay_wash_per=$('#lay_wash_per').val();
	  if(lay_wash_per=="")
	  {
		lay_wash_per=0;  
	  }
	  var gmtfinish_per=$('#gmtfinish_per').val();
	  if(gmtfinish_per=="")
	  {
		gmtfinish_per=0;  
	  }
	   
	 var processloss_breck_down=cutting_per+'_'+embbroidery_per+'_'+printing_per+'_'+wash_per+'_'+sew_per+'_'+fin_per+'_'+knitt_per+'_'+dying_per+'_'+extracutt_per+'_'+other_per+'_'+neck_sleev_printing_per+'_'+gmt_other_per+'_'+yarn_dyeing_per+'_'+all_over_print_per+'_'+lay_wash_per+'_'+gmtfinish_per;
	 document.getElementById('processloss_breck_down').value=processloss_breck_down;
	 parent.emailwindow.hide();
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
 <?
 $data=explode("_",$processloss_breck_down);
 ?>
<fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" /> 
    <table width="180" class="rpt_table" border="1" rules="all">
               <tr>
                <td width="130">
               Cut Panel rejection <!--  Extra Cutting %  breack Down 8-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="extracutt_per" id="extracutt_per" value="<? echo $data[8];  ?>"  /> 
                </td>
                </tr>
                <tr>
                <td width="130">
                 Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="printing_per" id="printing_per" value="<? echo $data[2];  ?>" /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                 Neck/Sleeve Printing <!-- new breack Down 10-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="neck_sleev_printing_per" id="neck_sleev_printing_per" value="<? echo $data[10];  ?>" /> 
                </td>
                </tr>
                
                
                <tr>
                <td width="130">
                Embroidery  <!-- Embroidery  % breack Down 1-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="embbroidery_per" id="embbroidery_per" value="<? echo $data[1];  ?>"  /> 
                </td>
                </tr>
                 
                
                <tr>
                <td width="130">
                Sewing/Input <!-- Sewing % breack Down 4-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="sew_per" id="sew_per" value="<? echo $data[4];  ?>" /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                Garments Wash  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="wash_per" id="wash_per"  value="<? echo $data[3];  ?>" /> 
                </td>
                
                
                </tr>
                <tr>
                <td width="130">
                Gmts Finishing  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmtfinish_per" id="gmtfinish_per"  value="<? echo $data[15];  ?>" /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmt_other_per" id="gmt_other_per" value="<? echo $data[11];  ?>"  /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                 Knitting   <!-- Knitting % breack Down 6-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="knitt_per" id="knitt_per" value="<? echo $data[6];  ?>"  /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                 Yarn Dyeing   <!-- New breack Down 12-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="yarn_dyeing_per" id="yarn_dyeing_per" value="<? echo $data[12];  ?>"  /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                Dyeing & Finishing   <!-- Finishing % breack Down 5-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="fin_per" id="fin_per" value="<? echo $data[5];  ?>"  /> 
                </td>
                </tr>
                
                
                <tr>
                <td width="130">
                All Over Print  <!-- New breack Down 13-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="all_over_print_per" id="all_over_print_per" value="<? echo $data[13];  ?>"  /> 
                </td>
                </tr>
                
                <tr>
                <td width="130">
                Lay Wash (Fabric)  <!-- New breack Down 14-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="lay_wash_per" id="lay_wash_per" value="<? echo $data[14];  ?>"  /> 
                </td>
                </tr>
                 
                
                <tr>
                <td width="130">
                 Dying   <!--breack Down 7-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="dying_per" id="dying_per" value="<? echo $data[7];  ?>"  /> 
                </td>
                </tr>
                <tr>
                <td width="130">
                 Cutting (Febric) <!-- Cutting % breack Down 0-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="cutting_per" id="cutting_per" value="<? echo $data[0];  ?>" /> 
                </td>
                </tr>
                <tr>
                <td width="130">
                 Others <!--breack Down 9-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="other_per" id="other_per" value="<? echo $data[9];  ?>"  /> 
                </td>
                </tr>
                
                <tr>
               <td align="center"  class="button_container" colspan="2">
			    <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/> 
                 </td> 
                </tr>
           </table>     
    </form>
</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}
if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<div style="width:1330px" align="center">       
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>									<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							if(str_replace("'","",$txt_job_no)!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}


                            foreach ($nameArray as $result)
                            { 
								echo $location_name_arr[$location];
                            ?>
                                <!--Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--> <br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No:  <? echo $result[csf('website')];
								
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					
					$group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";
					$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$result[csf('po_break_down_id')].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
					
					
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				/*$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}*/
				$booking_date=$result[csf('booking_date')];
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                
            </tr>
             <tr>
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr>  
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                <?
                if($db_type==0)
				{
					$last_update_date=return_field_value("max(date(update_date)) as update_date","wo_booking_dtls","status_active=1 and is_deleted=0 and booking_no=$txt_booking_no","update_date");
				}
				else
				{
					$last_update_date=return_field_value("max(to_char(update_date,'DD-MM-YYYY')) as update_date","wo_booking_dtls","status_active=1 and is_deleted=0 and booking_no=$txt_booking_no","update_date");
				}
				?>
                <td width="100" style="font-size:18px"><b>Last Updated</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? if($last_update_date!="" && $last_update_date!="0000-00-00") echo change_date_format($last_update_date); ?></b></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Internal Ref No</b></td>
               <td width="110"> :&nbsp;<? echo implode(",",array_unique(explode(",",$data_array3[0][csf("grouping")]))); ?></td>
                
               <td width="100" style="font-size:12px"><b>File no</b></td>
               <td width="110"> :&nbsp;<? echo  implode(",",array_unique(explode(",",$data_array3[0][csf("file_no")])));?></td>
            </tr> 
            
        </table>  
           <?
			}
			if($cbo_fabric_source==1)
			{
			//$nameArray_size=sql_select( "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id"); 
			$nameArray_size=sql_select( "select  size_number_id,min(id) as id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 group by size_number_id order by id");
		   ?>
            <table width="100%" >            
		    <tr>
            <td width="800">  
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown                </legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?  				
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>				
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					//$nameArray_color=sql_select( "select distinct color_number_id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 order by color_number_id"); 
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by id");
					?>
                    <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
                    
                    </tr>
                    <?
					foreach($nameArray_color as $result_color)
                    {						
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <? 
						$color_total=0;
						$color_total_order=0;
						
						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<? 
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
								}
								else echo "0";
							 ?>
							</td>
                           
                    <?   
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>
                          
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
                         </td>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>
                    
                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>  
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
                <?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				
				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>   
                </fieldset>
                
                 
                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				
				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>   
           </fieldset>
           </div>  
                </td>
            <td width="330" valign="top" align="left">
            <? 
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
					if($path=="")
                    {
                    $path='../../';
                    }
							
					?>
					<td>
                    
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
           </fieldset>
           </div>         	
          </td>
        </tr>
       </table>
        <?
			}// if($cbo_fabric_source==1) end
			
	  ?>
      <br/> 
     
      <!--  Here will be the main portion  -->
     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
				
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
				
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;
				
			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;
				
			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
				
			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

	 ?>
     
     <? 
	 
	/*$nameArray_fabric_description= sql_select("SELECT a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,a.process_loss_percent FROM view_wo_fabric_booking_data_park a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and b.status_active=1 and	
b.is_deleted=0  group by a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,process_loss_percent order by a.pre_cost_fabric_cost_dtls_id");*/
	if($cbo_fabric_source==1)
	{
	
$nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        <td  rowspan="9" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>  
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Grey</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id 
		  FROM 
		  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b 
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and 
		  a.job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
			
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
           <!-- <td  width="120" align="left">
			<?
			
			//echo $color_library[$color_wise_wo_result['fabric_color_id']]; 
			
			?></td>-->
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			

			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM 
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b 
				  WHERE 
				  b.booking_no =$txt_booking_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and 
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
				  a.construction='".$result_fabric_description['construction']."' and 
				  a.composition='".$result_fabric_description['composition']."' and 
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
				  a.dia_width='".$result_fabric_description['dia_width']."' and 
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and 
				  b.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				  b.status_active=1 and
				  b.is_deleted=0");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            
            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				
			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td><td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4); $grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right"><? echo number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right">
            <?
            if($process_loss_method==1)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
			}
			
			if($process_loss_method==2)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
			}
			echo number_format($totalprocess_percent_dzn,2);
			?>
            </td>
            </tr> 
    </table>
    <?
	}
	/*$nameArray_fabric_description= sql_select("SELECT a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,a.process_loss_percent FROM view_wo_fabric_booking_data_park a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and b.status_active=1 and	
b.is_deleted=0  group by a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,process_loss_percent order by a.pre_cost_fabric_cost_dtls_id");*/
	if($cbo_fabric_source==2)
	{
	
$nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type , b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="9" width="50"><p>Total Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        <td  rowspan="9" width="50"><p>Avg Rate <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Amount </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>  
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id 
		  FROM 
		  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b 
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and 
		  a.job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_amount=0;
			//$grand_totalcons_per_finish=0;
			//$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
           <!-- <td  width="120" align="left">
			<?
			
			//echo $color_library[$color_wise_wo_result['fabric_color_id']]; 
			
			?></td>-->
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			

			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_amount=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM 
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b 
				  WHERE 
				  b.booking_no =$txt_booking_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and 
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
				  a.construction='".$result_fabric_description['construction']."' and 
				  a.composition='".$result_fabric_description['composition']."' and 
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
				  a.dia_width='".$result_fabric_description['dia_width']."' and 
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and 
				  b.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				  b.status_active=1 and
				  b.is_deleted=0");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('rate')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],2); 
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty['grey_fab_qnty'];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<?
			$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,2); 
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            
            <td align="right">
            <?
			echo number_format($total_amount,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,

												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
            <?
			echo number_format($grand_total_amount,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				
			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<? 
			$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format($consumption_per_unit_fab,4); 
			//$grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right">
			<?
			$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
			//$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right">
            <?
			echo number_format($consumption_per_unit_amuont,2);
			?>
            </td>
            </tr> 
    </table>
    <?
	}
	?>
        <br/>
        <?
		if($cbo_fabric_source==1){
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		
		
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		
		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/ 
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	     
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id 
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
        </tr>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<? 
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");                          

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($plan_cut+$colar_excess_per,0); 
				$color_total_collar+=$plan_cut+$colar_excess_per; 
				$color_total_collar_order_qnty+=$plan_cut; 
				$grand_total_collar+=$plan_cut+$colar_excess_per; 
				$grand_total_collar_order_qnty+=$plan_cut; 
				?>
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>
        
        <?
		$nameArray_item_size=sql_select( "select  min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	       
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id 
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
        </tr>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result['color_number_id']]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
             
				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
				
				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut*2+$cuff_excess_per,0); 
				$color_total_cuff+=$plan_cut*2+$cuff_excess_per; 
				$color_total_cuff_order_qnty+=$plan_cut*2; 
				$grand_total_cuff+=$plan_cut*2+$cuff_excess_per; 
				$grand_total_cuff_order_qnty+=$plan_cut*2;
				
				/*$cuff_excess_per=(($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0); 
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2; 
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;*/ 
				?>
                
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");                          
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td>Rate</td>
                    <?
					}
					?>
                    <td>Cons for <? echo $costing_per; ?> Gmts</td>
                    <td>Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					if($row['copm_two_id'] !=0)
					{
						$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
					}
					$yarn_des.=$yarn_type[$row[csf('type_id')]];
					//echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']]; 
					echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                     <td><? echo number_format($row[csf('rate')],4); ?></td>
                     <?
					}
					 ?>
                    <td><? echo number_format($row[csf('yarn_required')],4); ?></td>
                   
                    <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                    <td align="right"><? //echo number_format($row['yarn_required'],2); $total_yarn+=$row['yarn_required']; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td></td>
                    <?
                    }
					?>
                    <td></td>
                    <td align="right"><? //echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                   
                   
                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					
					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?
					
					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?
					
					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    
                    
                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1"); 
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");

					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>
                    
                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>
                     
                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>
                   
                   
                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td></td>
                    
                    <td></td>
                   
                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>
        
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{

				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select po_break_down_id,min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by po_break_down_id,color_number_id,size_number_id,item_number_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					//$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type =1 and is_short=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<? 
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0) 
					{
						echo "Over Booking";
					} 
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>
                    
                    <tr>
                    <td colspan="3">Total:</td>
                    
                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                </td>
                
            </tr>
        </table>
        <?
		}// fabric Source End
		?>
        
         <!--<br><br><br><br>-->
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
         
       </div>
       <?
      
}
if($action=="show_fabric_booking_report1")
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value("sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value("sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">       
   <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>

                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}

							foreach ($nameArray as $result)
                            { 
							echo $location_name_arr[$location];

                            ?>
                               <!-- Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong>Fabric Dia & Garments Size Wise Finish Fabric Requirment<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time="";
                    if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}					
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>
                
                
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                
            </tr>
             <tr>
                
                	
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                
                
                
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr>  
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                
            </tr> 
            
        </table> 
        <br/>
           <?
			}
			
	$po_color_size_qnty_array=array();
	$po_size_qnty_array=array();
	$sql="SELECT b.gmts_item_id as item_number_id, c.id as po_id, d.size_number_id, d.color_number_id, d.order_quantity as order_quantity, d.plan_cut_qnty as plan_cut_qnty
	FROM wo_po_details_master a, wo_po_details_mas_set_details b , wo_po_break_down c, wo_po_color_size_breakdown d 
	WHERE 
	a.job_no = b.job_no and 
	a.job_no = c.job_no_mst and 
	a.job_no = d.job_no_mst and
	b.gmts_item_id=d.item_number_id and
    c.id = d.po_break_down_id and
	c.id in(".str_replace("'","",$txt_order_no_id).") and
	a.is_deleted =0 and
	a.status_active=1 and
	c.is_deleted =0 and
	c.status_active =1 and
	d.is_deleted =0 and
	d.status_active =1 
	";
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$po_color_size_qnty_array[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	$po_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
    
    $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.process_loss_percent) as  process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
a.body_part_id in(1,20) and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="6" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        
        
       </tr>
     <tr align="center"><th colspan="6" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        
       </tr>  
        <tr align="center"><th colspan="6" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>".$result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="6" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="6" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td  align='center' colspan='2'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="6" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td  align='center' colspan='2'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
       </tr>
       
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+6; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <th  width="50" align="left">Sl</th>
            <th  width="120" align="left">PO No</th>
            <th  width="120" align="left">Gmt Item</th>
            <th  width="120" align="left">Gmt Color</th>
            <th  width="120" align="left">Gmt Size</th>
            <th  width="100" align="left">Plan Cut Qty</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab.Color</th><th width='50'>Finish</th>";			
		}
		?>
       
       </tr>
       <?
	    $i=1;
		$total_plan_cut_qnty=0;
		$grand_total_fin_fab_qnty=0;
		$color_wise_wo_sql= sql_select("select b.po_break_down_id, c.item_number_id,c.color_number_id,c.size_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
		WHERE a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and 
		c.id=b.color_size_table_id and
		b.po_break_down_id=d.po_break_down_id and 
		b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
		d.booking_no =$txt_booking_no and 
		a.body_part_id in(1,20) and 
		d.status_active=1 and 
		d.is_deleted=0 
		group by b.po_break_down_id,c.item_number_id,c.color_number_id,c.size_number_id order by b.po_break_down_id, c.item_number_id,c.color_number_id,c.size_number_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td>
            <?
			echo $i;
			?>
            </td>
            <td>
            <?
			echo $po_number[$color_wise_wo_result[csf('po_break_down_id')]];
			?>
            </td>
            <td>
            <?
			echo $garments_item[$color_wise_wo_result[csf('item_number_id')]];
			?>
            </td>
            <td>
            <?
			echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
			?>
            </td>
            
            <td  width="120" align="left">
			<? 
			echo $size_library[$color_wise_wo_result[csf('size_number_id')]];
			?>
            </td>
            
            <td  width="100" align="right">
			<? 
			$plan_cut_qty=$po_color_size_qnty_array[$color_wise_wo_result[csf('po_break_down_id')]][$color_wise_wo_result[csf('item_number_id')]][$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]];
			echo $po_color_size_qnty_array[$color_wise_wo_result[csf('po_break_down_id')]][$color_wise_wo_result[csf('item_number_id')]][$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]];
			$total_plan_cut_qnty+=$po_color_size_qnty_array[$color_wise_wo_result[csf('po_break_down_id')]][$color_wise_wo_result[csf('item_number_id')]][$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]];
			//echo $color_wise_wo_result['plan_cut_qnty'];
			//$total_plan_cut_qnty+=$color_wise_wo_result['plan_cut_qnty'];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select avg(b.cons) as cons,min(d.fabric_color_id) as fabric_color_id, sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
				c.item_number_id=".$color_wise_wo_result[csf('item_number_id')]." and
				c.color_number_id=".$color_wise_wo_result[csf('color_number_id')]." and
				c.size_number_id=".$color_wise_wo_result[csf('size_number_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            <td width='50' align='right'>
			<? 
			

			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo $color_library[$color_wise_wo_result_qnty[csf('fabric_color_id')]] ;
			}
			?>
            </td>
            
			<td width='50' align='right'>
            <span style="border-bottom:1px solid; border-bottom-color:#000; width:100%">
            <? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </span>
            <br/>
            <span>
            <? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			//echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4);
			echo "<font style='font-size:10px'>Cons:".number_format($color_wise_wo_result_qnty[csf('cons')],4)."</font>";
			//$total_fin_fab_qnty+=$color_wise_wo_result_qnty['fin_fab_qnty'];
			}
			?>
            </span>
			
            </td>
            
            <?
			}
			?>
             <td align="right">
			 <? echo number_format($total_fin_fab_qnty,4); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?>
             </td>
            </tr>
         <?
		 $i++;
		}
		?>
        <tr style=" font-weight:bold">
        
        <td  width="120" align="left" colspan="5"><strong>Total</strong></td>
        <td  width="120" align="right"><? echo $total_plan_cut_qnty; ?></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4) ;?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,4);?></td>
          
            </tr> 
    </table>
    
         <!--<br><br><br><br>-->
         <br/>
         <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>
                    
                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>
                     
                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>
                   
                   
                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td></td>
                    
                    <td></td>
                   
                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
       </div>
       <?
}

if($action=="show_fabric_booking_report2")
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value("sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value("sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">       
   <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                             <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>

                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}

                            foreach ($nameArray as $result)
                            {
								echo $location_name_arr[$location];
 
                            ?>
                                <!--Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong>Fabric Dia & Garments Size Wise Finish Fabric Requirment<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no="";$file_no="";$int_ref_no="";
					$shipment_date="";
					$sql_po= "select po_number,file_no,grouping,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,file_no,grouping"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$file_no.=$row_po[csf('file_no')].", ";
						$int_ref_no.=$row_po[csf('grouping')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time="";
					
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					/*if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping, 
				listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
				else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
            	$data_array3=sql_select("select $group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".str_replace("'","",$txt_order_no_id).") and a.job_no=b.job_no_mst");*/
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                
            </tr>
             <tr>
                
                	
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                
                
                
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr> 
            <tr>
                <td style="font-size:18px"><b>Internal Ref No</b></td>
                <td style="font-size:18px"> :&nbsp;<b><? echo rtrim($int_ref_no,", ");//echo implode(",",array_unique(explode(",",$data_array3[0][csf("grouping")]))); ?></b></td>
                <td style="font-size:18px"><b>File no</b></td>
                <td style="font-size:18px" colspan="3"> :&nbsp;<b><?   echo rtrim($file_no,", ");//implode(",",array_unique(explode(",",$data_array3[0][csf("file_no")])));?></b></td>
            </tr>  
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                
            </tr> 
            
        </table> 
        <br/>
           <?
			}
			
	$po_color_size_qnty_array=array();
	$po_size_qnty_array=array();
	//$color_wise_row=array();
	/*$sql="SELECT d.size_number_id, d.color_number_id, sum(d.order_quantity) as order_quantity, sum(d.plan_cut_qnty) as plan_cut_qnty
	FROM wo_po_details_master a
	LEFT JOIN wo_po_details_mas_set_details b ON a.job_no = b.job_no
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE 
	a.is_deleted =0
	AND a.status_active=1
	AND c.id in(".str_replace("'","",$txt_order_no_id).")
	group by d.color_number_id,d.size_number_id";*/
	$sql="SELECT b.gmts_item_id as item_number_id, c.id as po_id, d.size_number_id, d.color_number_id, d.order_quantity as order_quantity, d.plan_cut_qnty as plan_cut_qnty
	FROM wo_po_details_master a, wo_po_details_mas_set_details b , wo_po_break_down c, wo_po_color_size_breakdown d 
	WHERE 
	a.job_no = b.job_no and 
	a.job_no = c.job_no_mst and 
	a.job_no = d.job_no_mst and
	b.gmts_item_id=d.item_number_id and
    c.id = d.po_break_down_id and
	c.id in(".str_replace("'","",$txt_order_no_id).") and
	a.is_deleted =0 and
	a.status_active=1 and
	c.is_deleted =0 and
	c.status_active =1 and
	d.is_deleted =0 and
	d.status_active =1 
	";
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$po_color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]][order_quantity]+=$row[csf('order_quantity')];
	$po_color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]][plan_cut_qnty]+=$row[csf('plan_cut_qnty')];
	$po_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$color_wise_row[$row[csf('color_number_id')]]+=1;
	}
	//print_r($color_wise_row);
    
    $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 

d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight order by a.body_part_id");
	
	
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="6" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='4'>&nbsp</td>";	
			else         		               echo "<td  colspan='4'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="6" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        
        
       </tr>
     <tr align="center"><th colspan="6" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td colspan='4'>&nbsp</td>";	
			else         		               echo "<td  colspan='4'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        
       </tr>  
        <tr align="center"><th colspan="6" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='4'>&nbsp</td>";	
			else         		               echo "<td  colspan='4'>".$result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="6" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td  colspan='4'>&nbsp</td>";
			else         		               echo "<td  colspan='4'>".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="6" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='4'>&nbsp</td>";
			else         		       echo "<td  align='center' colspan='4'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       
       
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*4+6; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <th  width="50" align="left">Sl</th>
            <th  width="120" align="left">Gmt Color</th>
            <th  width="120" align="left">Gmt Size</th>
            <th  width="120" align="left">Order Qty (Pcs)</th>
            <th  width="100" align="left">Excess Cut %</th>
            <th  width="120" align="left">Plan Cut Qty (Pcs)</th>
            
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab.Color</th><th width='50'>Dia/Width</th><th width='50'>Cons/pcs</th><th width='50'>Finish</th>";			
		}
		?>
       
       </tr>
       <?
	   $total_ord_cut_qnty=0;
	   $total_plan_cut_qnty=0;
	   //$total_plan_cut_qnty=0;
	   $grand_total_fin_fab_qnty=0;
	    $i=1;
	   foreach($color_wise_row as $key => $value)
	   {
	   
		$color_total_ord_cut_qnty=0;
	    $color_total_plan_cut_qnty=0;
		$color_grand_total_fin_fab_qnty=0;
		$color_wise_wo_sql= sql_select("select c.color_number_id,c.size_number_id, sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
		WHERE a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and 
		c.id=b.color_size_table_id and
		b.po_break_down_id=d.po_break_down_id and 
		b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
		d.booking_no =$txt_booking_no and
		c.color_number_id = $key and
		d.status_active=1 and 
		d.is_deleted=0 
		group by c.color_number_id,c.size_number_id order by c.color_number_id,c.size_number_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td>
            <?
			echo $i;
			?>
            </td>
            <td>
            <?
			echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
			?>
            </td>
            <td>
            <? 
			echo $size_library[$color_wise_wo_result[csf('size_number_id')]];
			?>
            </td>
            <td align="right">
            <?
			$ord_cut_qty=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][order_quantity];
			echo number_format($ord_cut_qty,2);
			$total_ord_cut_qnty+=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][order_quantity];
			$color_total_ord_cut_qnty+=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][order_quantity];
			?>
            </td>
            <td  width="100" align="right">
			<? 
			$excess_per=(($po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][plan_cut_qnty]-$ord_cut_qty)/$ord_cut_qty)*100;
			echo number_format($excess_per,2);
			?>
            </td>
            
            <td  width="120" align="right">
			<? 
			$plan_cut_qty=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][plan_cut_qnty];
			echo number_format($plan_cut_qty,2);
			$total_plan_cut_qnty+=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][plan_cut_qnty];
			$color_total_plan_cut_qnty+=$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][plan_cut_qnty];
			//echo $color_wise_wo_result['plan_cut_qnty'];
			//$total_plan_cut_qnty+=$color_wise_wo_result['plan_cut_qnty'];
			?>
            </td>
            
            
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty, min(a.width_dia_type) as width_dia_type, avg(b.cons) as cons ,min(b.dia_width) as dia_width , min(d.fabric_color_id) as fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				c.color_number_id=".$color_wise_wo_result[csf('color_number_id')]." and
				c.size_number_id=".$color_wise_wo_result[csf('size_number_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            <td width='50' align=''>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo $color_library[$color_wise_wo_result_qnty[csf('fabric_color_id')]] ;
			//$total_fin_fab_qnty+=$color_wise_wo_result_qnty['fin_fab_qnty'];
			}
			?>
            </td>
            <td width='50' align=''>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo $color_wise_wo_result_qnty[csf('dia_width')].",".$fabric_typee[$color_wise_wo_result_qnty[csf('width_dia_type')]] ;
			}
			?>
            </td>
            <td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[Csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')]/$po_color_size_qnty_array[$color_wise_wo_result[csf('color_number_id')]][$color_wise_wo_result[csf('size_number_id')]][plan_cut_qnty],4);
			//echo $po_color_size_qnty_array[$color_wise_wo_result['color_number_id']][$color_wise_wo_result['size_number_id']][plan_cut_qnty];
			}
			?>
            </td>
            
			<td width='50' align='right'>
           
            <? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
           
           
			
            </td>
            
            <?
			}
			?>
             <td align="right">
			 <? echo number_format($total_fin_fab_qnty,4); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty; $color_grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?>
             </td>
            </tr>
         <?
		 $i++;
		}
		?>
        <tr style=" font-weight:bold; background-color:#CCC">
        
        <td  width="120" align="left" colspan="3"><strong>Color Total</strong></td>
         <td  width="120" align="right"><? echo $color_total_ord_cut_qnty; ?></td>
         <td  width="120" align="right">
		 <?
		 $color_excess_per_tot=(($color_total_plan_cut_qnty-$color_total_ord_cut_qnty)/$color_total_ord_cut_qnty)*100;
		 echo number_format($color_excess_per_tot,2);
		 ?></td>
        <td  width="120" align="right"><? echo $color_total_plan_cut_qnty; ?></td>
        
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												c.color_number_id=".$color_wise_wo_result[csf('color_number_id')]." and
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4) ;?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($color_grand_total_fin_fab_qnty,4);?></td>
          
            </tr> 
        <?
	   }
		?>
        <tr style=" font-weight:bold">
        
        <td  width="120" align="left" colspan="3"><strong>Grand Total</strong></td>
         <td  width="120" align="right"><? echo $total_ord_cut_qnty; ?></td>
         <td  width="120" align="right">
		 <?
		 $excess_per_tot=(($total_plan_cut_qnty-$total_ord_cut_qnty)/$total_ord_cut_qnty)*100;
		 echo number_format($excess_per_tot,2);
		 ?></td>
        <td  width="120" align="right"><? echo $total_plan_cut_qnty; ?></td>
        
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
            <td width='50' align='right'><?  //echo number_format($color_wise_wo_result_qnty['fin_fab_qnty'],4) ;?></td>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4) ;?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,4);?></td>
          
            </tr> 
    </table>
         <!--<br><br><br><br>-->
         <br/>
         <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>
                    
                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>
                     
                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>
                   
                   
                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td></td>
                    
                    <td></td>
                   
                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
       </div>
       <?
}

if($action=="show_fabric_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	
	$po_number_arr=return_library_array( "select id,po_number from   wo_po_break_down",'id','po_number');
	$po_ship_date_arr=return_library_array( "select id,pub_shipment_date from   wo_po_break_down ",'id','pub_shipment_date');
	?>
	<div style="width:1330px" align="center">       
    <?php
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
?>										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}


                            foreach ($nameArray as $result)
                            { 
								echo $location_name_arr[$location];
                            ?>
                                <!--Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--> <br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time=="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                
            </tr>
             <tr>
                
                	
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                
                
                
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr>  
          <!-- <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><?// echo rtrim($po_no,", "); ?></b></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="5"> :&nbsp;<? //echo rtrim($shipment_date,", "); ?></td>
                
            </tr> -->
            
        </table>  
           <?
			}
			
			$nameArray_size=sql_select( "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id"); 
			//$nameArray_color=sql_select( "select distinct color_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).")"); 
		   ?>
            <table width="100%" >            
		    <tr>
            <td width="800">  
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown                </legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>PO Namber</strong></td>
                        <td style="border:1px solid black"><strong>Ship Date</strong></td>
                        <td style="border:1px solid black"><strong>Gmts Item</strong></td>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?  				
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>				
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					$order_id=explode(",",str_replace("'","",$txt_order_no_id));
					for($or=0;$or<count($order_id); $or++)
				    {
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select distinct color_number_id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id =$order_id[$or] and is_deleted=0 and status_active=1 order by color_number_id"); 
					?>
                   <!-- <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
                    
                    </tr>-->
                    <?
					foreach($nameArray_color as $result_color)
                    {						
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $po_number_arr[$order_id[$or]]; // echo $row_num_tr; ?></td>
                         <td align="center" style="border:1px solid black"><? echo change_date_format($po_ship_date_arr[$order_id[$or]],"dd-mm-yyyy","-"); // echo $row_num_tr; ?></td>
                          <td align="center" style="border:1px solid black"><? echo $garments_item[$gmts_item[$c]]; // echo $row_num_tr; ?></td>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <? 
						$color_total=0;
						$color_total_order=0;
						
						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id =$order_id[$or] and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
							
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<? 
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color['color_number_id']]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
								}
								else echo "0";
							 ?>
							</td>
                           
                    <?   
						}
                        }
                        ?>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total_order),0); ?></td>
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                         <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+4; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>  
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
            	<?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				
				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>   
                </fieldset>
                
                 
                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				
				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>   
           </fieldset>
           </div>  
                </td>
            <td width="330" valign="top" align="left">
            <? 
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no'");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{	
							
					?>
					<td>
						<img src="../../<? echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
           </fieldset>
           </div>         	
          </td>
        </tr>
       </table>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
	 .main_table tr th{
		 border:1px solid black;
		 font-size:13px;
		 outline: 0;
	 }
	  .main_table tr td{
		 border:1px solid black;
		 font-size:13px;
		 outline: 0;
	 }
	 </style>
     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
				
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
				
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;
				
			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;
				
			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
				
			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");

	 ?>
     
     <? 
	 
	/*$nameArray_fabric_description= sql_select("SELECT a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,a.process_loss_percent FROM view_wo_fabric_booking_data_park a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and b.status_active=1 and	
b.is_deleted=0  group by a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,process_loss_percent order by a.pre_cost_fabric_cost_dtls_id");*/
	
$nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent,avg(b.requirment) as  requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="5" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        <td  rowspan="9" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="5" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>  
        <tr align="center"><th colspan="5" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="5" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="5" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="5" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       <tr align="center"><th   colspan="5" align="left">Consumption For <? echo $costing_per; ?></th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'> Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+5; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <th  width="120" align="left">PO Number</th>
            <th  width="120" align="left">Ship Date</th>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];

		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,po_break_down_id
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by po_break_down_id,fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
          <td  width="120" align="left">
			<?
			echo $po_number_arr[$color_wise_wo_result[csf('po_break_down_id')]]; 
			?></td>
             <td  width="120" align="left">
			<?
			
			echo change_date_format($po_ship_date_arr[$color_wise_wo_result[csf('po_break_down_id')]],"dd-mm-yyyy","-"); 
			
			?></td>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			

			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM 
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b 
				  WHERE 
				  b.booking_no =$txt_booking_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and 
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
				  a.construction='".$result_fabric_description['construction']."' and 
				  a.composition='".$result_fabric_description['composition']."' and 
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
				  a.dia_width='".$result_fabric_description['dia_width']."' and 
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and 
				  b.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				  b.status_active=1 and
				  b.is_deleted=0");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 and 
				c.status_active=1 and 
				c.is_deleted=0 and 
				a.status_active=1 and 
				a.is_deleted=0
				");
				}
				
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
				b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 and 
				c.status_active=1 and 
				c.is_deleted=0 and 
				a.status_active=1 and 
				a.is_deleted=0
				");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            
            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 and 
												c.status_active=1 and 
												c.is_deleted=0 and 
												a.status_active=1 and 
												a.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
          <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0  and 
												c.status_active=1 and 
												c.is_deleted=0 and 
												a.status_active=1 and 
												a.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				
			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td><td width='50' align='right' > <? // echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4); $grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right"><? echo number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right">
            <?
            if($process_loss_method==1)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
			}
			
			if($process_loss_method==2)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
			}
			echo number_format($totalprocess_percent_dzn,2);
			?>
            </td>
            </tr> 
    </table>

        <br/>
        
        
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		
		
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		
		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/ 
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	     
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id 
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
        </tr>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<? 
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");                          

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$colar_excess_per=def_number_format(($plan_cut_qnty[csf('plan_cut_qnty')]*$colar_excess_percent)/100,6,0);
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per,0); 
				$color_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per; 
				$color_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')]; 
				$grand_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per; 
				$grand_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')]; 
				?>
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>
        
        <?
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	       
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id 
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
       </tr>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result['color_number_id']]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
             
				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
				
				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$cuff_excess_per=def_number_format((($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100,6,"");
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0); 
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2; 
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2; 
				/*echo $color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;*/
				?>
                
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");                          
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td>Rate</td>
                    <?
					}
					?>
                    <td>Cons for <? echo $costing_per; ?> Gmts</td>
                    <td>Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					if($row['copm_two_id'] !=0)
					{
						$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
					}
					$yarn_des.=$yarn_type[$row[csf('type_id')]];
					//echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']]; 
					echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                     <td><? echo number_format($row[csf('rate')],4); ?></td>
                     <?
					}
					 ?>
                    <td><? echo number_format($row[csf('yarn_required')],4); ?></td>
                   
                    <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                    <td align="right"><? //echo number_format($row['yarn_required'],2); $total_yarn+=$row['yarn_required']; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td></td>
                    <?
                    }
					?>
                    <td></td>
                    <td align="right"><? //echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                   
                   
                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					
					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?
					
					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?
					
					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    
                    
                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1"); 
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>
                    
                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>
                     
                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>
                   
                   
                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td></td>
                    
                    <td></td>
                   
                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by po_break_down_id,color_number_id,size_number_id,item_number_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					//$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type =1 and is_short=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<? 
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0) 
					{
						echo "Over Booking";
					} 
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>
                    
                    <tr>
                    <td colspan="3">Total:</td>
                    
                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                </td>
                
            </tr>
        </table>
        
         <!--<br><br><br><br>-->
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
         
       </div>
       <?
      
}

if($action=="show_fabric_booking_report4")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	
	$po_number_arr=return_library_array( "select id,po_number from   wo_po_break_down",'id','po_number');
	$po_ship_date_arr=return_library_array( "select id,pub_shipment_date from   wo_po_break_down ",'id','pub_shipment_date');
	$user_arr=return_library_array( "select id,user_name from   user_passwd ",'id','user_name');
	?>
	<div style="width:1330px" align="left">       
    	<?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date,approved_by from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
    ?>									<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span>
                               <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                                  
								  <?
								 }
							  	?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                           if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}

						    foreach ($nameArray as $result)
                            { 
								echo $location_name_arr[$location];

                            ?>
                               <!-- Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--> <br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
				$style_ref_no="";
				$inserted_by="";
				$currency_id="";
				
				
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.currency_id,a.rmg_process_breakdown,a.insert_date,a.inserted_by,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant,b.quotation_id from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$inserted_by=$result[csf('inserted_by')];
					$currency_id=$result[csf('currency_id')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time=="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$quot_date="";
					$sql_quot= "select quot_date from  wo_price_quotation  where id ='".$result[csf('quotation_id')]."'"; 
					$data_array_quot=sql_select($sql_quot);
					foreach ($data_array_quot as $row_quot){
						$quot_date=change_date_format($row_quot[csf('quot_date')],'dd-mm-yyyy','-');
					}
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;
                <b>
				<? 
				echo $result[csf('style_ref_no')];
				$style_ref_no=$result[csf('style_ref_no')];
				?> 
                </b>   
                </td>
                
            </tr>
             <tr>
                
                	
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                
                
                
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
               <!-- <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo //change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> -->
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                <td width="100" style="font-size:18px"><b>Quotation Id </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('quotation_id')];?></b><? echo "( Date :".$quot_date.")"?></td>
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr>  
        </table>  
           <?
			}
			//echo "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id";
			//$nameArray_size=sql_select( "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id"); 
			
			$nameArray_size=sql_select( "select  size_number_id,min(id) as id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  is_deleted=0 and status_active=1 group by size_number_id order by id"); 
			
		   ?>
            <table width="100%" >            
		    <tr>
            <td width="800">  
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown                </legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>PO Namber</strong></td>
                        <td style="border:1px solid black"><strong>Ship Date</strong></td>
                        <td style="border:1px solid black"><strong>Gmts Item</strong></td>
                        <td style="border:1px solid black"><strong>Style Ref</strong></td>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?  				
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>				
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					$order_id=explode(",",str_replace("'","",$txt_order_no_id));
					for($or=0;$or<count($order_id); $or++)
				    {
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id, min(id) as id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id =$order_id[$or] and is_deleted=0 and status_active=1 group by color_number_id  order by id"); 
					?>
                   <!-- <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
                    
                    </tr>-->
                    <?
					foreach($nameArray_color as $result_color)
                    {						
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $po_number_arr[$order_id[$or]]; // echo $row_num_tr; ?></td>
                         <td align="center" style="border:1px solid black"><? echo change_date_format($po_ship_date_arr[$order_id[$or]],"dd-mm-yyyy","-"); // echo $row_num_tr; ?></td>
                          <td align="center" style="border:1px solid black"><? echo $garments_item[$gmts_item[$c]]; // echo $row_num_tr; ?></td>
                          <td align="center" style="border:1px solid black"><? echo $style_ref_no; // echo $row_num_tr; ?></td>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <? 
						$color_total=0;
						$color_total_order=0;
						
						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id =$order_id[$or] and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
							
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<? 
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color['color_number_id']]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
								}
								else echo "0";
							 ?>
							</td>
                           
                    <?   
						}
                        }
                        ?>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total_order),0); ?></td>
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                         <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+8; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>  
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
            	<?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				
				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>   
                </fieldset>
                
                 
                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12--> 
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				
				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>   
           </fieldset>
           </div>  
                </td>
            <td width="330" valign="top" align="left">
            <? 
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no'");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{	
							
					?>
					<td>
						<img src="../../<? echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
           </fieldset>
           </div>         	
          </td>
        </tr>
       </table>
      <br/> 
      <?
	  if($cbo_fabric_source==1)
	  {
	  ?><!--  Here will be the main portion  -->  									 <!--  Here will be the main portion  -->
    <strong>Grey Fabric Details</strong>
    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td width="49%">
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
       <tr>
            <td  width="120" align="left">Fabric Color</td>
            <td  width="120" align="left">Fabric</td>
            <td  width="120" align="left">Composition</td>
            <td  width="120" align="left">GSM</td>
            <td  width="120" align="left">Process Loss</td>
			<? foreach($nameArray_size  as $result_size){?>
            <td align="center"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
            <? } ?>	
            <td   width="50">Total (Kg)</td>
       </tr>
       <?
	   
		$costing_per="";
		$costing_per_qnty=0;
		$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
		if($costing_per_id==1)
		{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
		
		}
		if($costing_per_id==2)
		{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
		
		}
		if($costing_per_id==3)
		{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
		
		}
		if($costing_per_id==4)
		{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
		
		}
		if($costing_per_id==5)
		{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
		
		}
		$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");

		   $wo_pre_cost_fabric_cost_dtls_id=array();
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			
				
			$color_wise_wo_sql=sql_select("select d.fabric_color_id, a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_type_id, b.dia_width,a.color_size_sensitive, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				a.body_part_id in(1,20) and
				d.booking_no =$txt_booking_no and 
				d.status_active=1 and 
				d.is_deleted=0 
				group by a.id,d.fabric_color_id order by c.id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$wo_pre_cost_fabric_cost_dtls_id[$color_wise_wo_result[csf('id')]]=$color_wise_wo_result[csf('id')];
			$fabric_color_id="";
			if($color_wise_wo_result[csf('color_size_sensitive')]==3)
			{
			$fabric_color_id=return_field_value( "gmts_color_id", "wo_pre_cos_fab_co_color_dtls","pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and contrast_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."'");	
			}
			else
			{
			$fabric_color_id=$color_wise_wo_result[csf('fabric_color_id')];	
			}
			$sql_dia_array=array();
		   $sql_dia=sql_select("Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'");
		   foreach($sql_dia as $sql_dia_row)
		   {
			$sql_dia_array[$sql_dia_row[csf("gmts_sizes")]][$sql_dia_row[csf("dia_width")]]= $sql_dia_row[csf("dia_width")];  
		   }
		?> 
			<tr>
            <td  width="120" align="left">
			<?
			echo $fabric_typee[$color_wise_wo_result[csf('width_dia_type')]];
			?>
            </td>
            <td  width="120" align="left">&nbsp;
			
            </td>
            <td>&nbsp;
           
            </td>
            <td  width="120" align="left">&nbsp;
			
            </td>
            
            <td  width="120" align="left">&nbsp;
			
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
			?>
		
            <td width='50' align='' >&nbsp; 
            <?
			$dia=implode(",", $sql_dia_array[$result_size[csf('size_number_id')]]);
			echo $dia;
			?>
            </td>
            <?
			}
			?>
            
            <td align="right">&nbsp;
            
            </td>
            </tr>
            
            <tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			//echo $color_library[$fabric_color_id];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('construction')].",".$color_type[$color_wise_wo_result[csf('color_type_id')]]; 
			?>
            </td>
            <td>
            <?
			echo $color_wise_wo_result[csf('composition')];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('gsm_weight')];
			?>
            </td>
            
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('process_loss_percent')];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.id='".$color_wise_wo_result[csf('id')]."' and
				c.size_number_id='".$result_size[csf('size_number_id')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
		
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            
            <td align="right"><? echo number_format($total_grey_fab_qnty,4); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <?
			}
			?>
           
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,4);?></td>
            
            </tr> 
    </table>
 </td>
 <td width="2%"></td>
 <td width="49%">
 <?
 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=implode(",", $wo_pre_cost_fabric_cost_dtls_id);
 if( $wo_pre_cost_fabric_cost_dtls_id_main_fabric !="")
 {
	 $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond="and pre_cost_fabric_cost_dtls_id not in($wo_pre_cost_fabric_cost_dtls_id_main_fabric)"; 
 }
     $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
a.body_part_id not in(1,20) and
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width,c.id");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]].",".$result_fabric_description[csf('construction')]."</td>";			
		}
		?>
       
       
        
       </tr>
     
        
        <tr align="center">
        <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')].",". $color_type[$result_fabric_description[csf('color_type_id')]].",". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       
       <tr align="center">
       <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$fabric_typee[$result_fabric_description[csf('width_dia_type')]].",". $result_fabric_description[csf('dia_width')].",".number_format($result_fabric_description[csf('requirment')],4)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th width='50'>Gmt. Color</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Color</th><th width='50' >Qty</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no 
										   $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td width='50' align='right'>
			<? 
			//if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			//{
				if($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]!="")
				{
				
				echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
				}
				else
				{
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
				}
			//}
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            
           
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td width='50' align='right'></td>
        
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <?
			}
			?>
            
            
           
            </tr> 
    </table>
 </td>
 </tr>
 </table>
    <br/>   									 <!--  Here will be the main portion  -->
    <strong>Finish Fabric Details</strong>
    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td width="49%">
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
       <tr>
            <td  width="120" align="left">Fabric Color</td>
            <td  width="120" align="left">Fabric</td>
            <td  width="120" align="left">Composition</td>
            <td  width="120" align="left">GSM</td>
            <td  width="120" align="left">Process Loss</td>
			<?  				
            foreach($nameArray_size  as $result_size)
            {?>
            <td align="center"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
            <? } ?>	
            <td   width="50">Total (Kg)</td>
       </tr>
       <?
	        $wo_pre_cost_fabric_cost_dtls_id=array();
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select d.fabric_color_id, a.id,a.color_size_sensitive,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_type_id, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				a.body_part_id in(1,20) and
				d.booking_no =$txt_booking_no and 
				d.status_active=1 and 
				d.is_deleted=0 
				group by a.id,d.fabric_color_id order by c.id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			
			$wo_pre_cost_fabric_cost_dtls_id[$color_wise_wo_result[csf('id')]]=$color_wise_wo_result[csf('id')];
			$fabric_color_id="";
			if($color_wise_wo_result[csf('color_size_sensitive')]==3)
			{
			$fabric_color_id=return_field_value( "gmts_color_id", "wo_pre_cos_fab_co_color_dtls","pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and contrast_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."'");	
			}
			else
			{
			$fabric_color_id=$color_wise_wo_result[csf('fabric_color_id')];	
			}
			
			$sql_dia_array=array();
		   $sql_dia=sql_select("Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'");
		   foreach($sql_dia as $sql_dia_row)
		   {
			$sql_dia_array[$sql_dia_row[csf("gmts_sizes")]][$sql_dia_row[csf("dia_width")]]= $sql_dia_row[csf("dia_width")];  
		   }
		?> 
			<tr>
            <td  width="120" align="left">
			<?
			echo $fabric_typee[$color_wise_wo_result[csf('width_dia_type')]];
			?>
            </td>
            <td  width="120" align="left">&nbsp;
			
            </td>
            <td>&nbsp;
           
            </td>
            <td  width="120" align="left">&nbsp;
			
            </td>
            
            <td  width="120" align="left">&nbsp;
			
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
			?>
		
            <td width='50' align='' >&nbsp; 
			<?
			$dia=implode(",", $sql_dia_array[$result_size[csf('size_number_id')]]);
			echo $dia;
			?>
            </td>
            <?
			}
			?>
            
            <td align="right">&nbsp;
            
            </td>
            
            
            </tr>
            
            
            
            <tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			//echo $color_library[$fabric_color_id];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			
			echo $color_wise_wo_result[csf('construction')].",".$color_type[$color_wise_wo_result[csf('color_type_id')]];
			?>
            </td>
            <td>
            <?
			echo $color_wise_wo_result[csf('composition')];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('gsm_weight')];
			?>
            </td>
            
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('process_loss_percent')];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.id='".$color_wise_wo_result[csf('id')]."' and
				c.size_number_id='".$result_size[csf('size_number_id')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
		
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4); 
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            
            <td align="right"><? echo number_format($total_fin_fab_qnty,4); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(b.cons) as avg_cons FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,4);?></td>
            
            </tr> 
            <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(b.cons) as avg_cons FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('avg_cons')],4);?></td>
            <?
			}
			?>
            <td align="right"><? //echo number_format($grand_total_fin_fab_qnty,2);?></td>
            
            </tr> 
            
            
    </table>
    </td>
    <td width="2%"></td>
    <td width="49%">
 <?
  $wo_pre_cost_fabric_cost_dtls_id_main_fabric=implode(",", $wo_pre_cost_fabric_cost_dtls_id);
  if( $wo_pre_cost_fabric_cost_dtls_id_main_fabric !="")
 {
	 $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond="and pre_cost_fabric_cost_dtls_id not in($wo_pre_cost_fabric_cost_dtls_id_main_fabric)"; 
 }
     $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
a.body_part_id not in(1,20) and
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width,c.id");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]].",".$result_fabric_description[csf('construction')]."</td>";			
		}
		?>
       
       
        
       </tr>
      
       <tr align="center">
        <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')].",". $color_type[$result_fabric_description[csf('color_type_id')]].",". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       
       <tr align="center">
       <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$fabric_typee[$result_fabric_description[csf('width_dia_type')]].",". $result_fabric_description[csf('dia_width')].",".number_format($result_fabric_description[csf('cons')],4)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th width='50'>Gmt Color</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Color</th><th width='50' >Qty</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;

			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no 
										  $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td width='50' align='right'>
			<? 
			//if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			//{
				if($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]!="")
				{
				
				echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
				}
				else
				{
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
				}
			//}
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4); 
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            
           
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td width='50' align='right'></td>
        
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);?></td>
            <?
			}
			?>
            
            
           
            </tr> 
    </table>
 </td>
    </tr>
    </table>
   <?
	  }
	  //=====================================================================Production End==============================================================
	?>
    
     <?
	 	  //=====================================================================Purchase Start==============================================================

	  if($cbo_fabric_source==2)
	  {
	$color_wise_wo_sql=sql_select("select d.fabric_color_id, a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_type_id,a.color_size_sensitive, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment ,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				a.body_part_id in(1,20) and
				d.booking_no =$txt_booking_no and 
				d.status_active=1 and 
				d.is_deleted=0 
				group by a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type,a.color_type_id,a.color_size_sensitive,d.fabric_color_id order by cid ");
	
	  ?><!--  Here will be the main portion  -->
    <strong>Fabric Details</strong>
    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
    <tr>
     <?
	if(count($color_wise_wo_sql)>0)
	{
	?>
    <td width="49%">
   
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
       <tr>
            <td  width="120" align="left">Fabric Color</td>
            <td  width="120" align="left">Fabric</td>
            <td  width="120" align="left">Composition</td>
            <td  width="120" align="left">GSM</td>
            <td  width="120" align="left">Process Loss</td>
			<? foreach($nameArray_size  as $result_size){?>
            <td align="center"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
            <? } ?>	
            <td   width="50">Total (Kg)</td>
            <td   width="50">Rate</td>
            <td   width="50">Amount</td>
       </tr>
       <?
	   
		$costing_per="";
		$costing_per_qnty=0;
		$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
		if($costing_per_id==1)
		{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
		
		}
		if($costing_per_id==2)
		{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
		
		}
		if($costing_per_id==3)
		{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
		
		}
		if($costing_per_id==4)
		{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
		
		}
		if($costing_per_id==5)
		{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
		
		}
		$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");

		    $wo_pre_cost_fabric_cost_dtls_id=array();
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$grand_total_amount=0;
			
				
			
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			
			$wo_pre_cost_fabric_cost_dtls_id[$color_wise_wo_result[csf('id')]]=$color_wise_wo_result[csf('id')];
			$fabric_color_id="";
			if($color_wise_wo_result[csf('color_size_sensitive')]==3)
			{
			$fabric_color_id=return_field_value( "gmts_color_id", "wo_pre_cos_fab_co_color_dtls","pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and contrast_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."'");	
			}
			else
			{
			$fabric_color_id=$color_wise_wo_result[csf('fabric_color_id')];	
			}
			
			
			$sql_dia_array=array();
		   $sql_dia=sql_select("Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'");
		   
		   foreach($sql_dia as $sql_dia_row)
		   {
			$sql_dia_array[$sql_dia_row[csf("gmts_sizes")]][$sql_dia_row[csf("dia_width")]]= $sql_dia_row[csf("dia_width")];  
		   }
		?> 
			<tr>
            <td  width="120" align="left">
			<?
			echo $fabric_typee[$color_wise_wo_result[csf('width_dia_type')]];
			?>
            </td>

            <td  width="120" align="left">&nbsp;
			
            </td>
            <td>&nbsp;
           
            </td>
            <td  width="120" align="left">&nbsp;
			
            </td>
            
            <td  width="120" align="left">&nbsp;
			
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
			?>
		
            <td width='50' align='' >&nbsp; 
            <?
			$dia=implode(",", $sql_dia_array[$result_size[csf('size_number_id')]]);
			echo $dia;
			?>
            </td>
            <?
			}
			?>
            
            <td align="right">&nbsp;
            
            </td>
            <td align="right">&nbsp;
            
            </td>
            <td align="right">&nbsp;
            
            </td>
            </tr>
            
            <tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			//echo $color_library[$fabric_color_id];
			
			?>
            </td>
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('construction')].",".$color_type[$color_wise_wo_result[csf('color_type_id')]];
			?>
            </td>
            <td>
            <?
			echo $color_wise_wo_result[csf('composition')];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('gsm_weight')];
			?>
            </td>
            
            <td  width="120" align="left">
			<? 
			echo $color_wise_wo_result[csf('process_loss_percent')];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.id='".$color_wise_wo_result[csf('id')]."' and
				c.size_number_id='".$result_size[csf('size_number_id')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
		
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            
            <td align="right"><? echo number_format($total_grey_fab_qnty,4); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            <td align="right">
			<? 
			echo number_format($color_wise_wo_result_qnty[csf('rate')],4); 
			//$grand_total_grey_fab_qnty+=$total_grey_fab_qnty;
			?>
            </td>
            <td align="right">
			<? 
			$amount=$total_grey_fab_qnty*$color_wise_wo_result_qnty[csf('rate')];
			echo number_format($amount,4); 
			$grand_total_amount+=$amount;?>
            </td>
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <?
			}
			?>
           
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,4);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty,4);?></td>
            <td align="right"><? echo number_format($grand_total_amount,4);?></td>
            
            </tr> 
    </table>
   
 </td>
 
 <td width="2%"></td>
  <?
	}
	?>
 <td width="49%" valign="top">
 <?
 //print_r($wo_pre_cost_fabric_cost_dtls_id);
 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=implode(",", $wo_pre_cost_fabric_cost_dtls_id);
 if( $wo_pre_cost_fabric_cost_dtls_id_main_fabric !="")
 {
	 $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond="and pre_cost_fabric_cost_dtls_id not in($wo_pre_cost_fabric_cost_dtls_id_main_fabric)"; 
 }
 $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
a.body_part_id not in(1,20) and
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width,cid");
	 ?>
    
      <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='4'>&nbsp</td>";	
			else         		               echo "<td  colspan='4'>". $body_part[$result_fabric_description[csf('body_part_id')]].",".$result_fabric_description[csf('construction')]."</td>";			
		}
		?>
       
       
        
       </tr>
     
        
        <tr align="center">
        <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='4' >&nbsp</td>";
			else         		               echo "<td colspan='4' >".$result_fabric_description[csf('composition')].",". $color_type[$result_fabric_description[csf('color_type_id')]].",". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       
       <tr align="center">
       <td width='50'></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='4'>&nbsp</td>";
			else         		              echo "<td colspan='4' align='center'>".$fabric_typee[$result_fabric_description[csf('width_dia_type')]].",". $result_fabric_description[csf('dia_width')].",".number_format($result_fabric_description[csf('requirment')],4)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th width='50'>Gmt Color</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Color</th><th width='50' >Qty</th><th width='50'>Rate</th><th width='50' >Amount</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no 
										  $wo_pre_cost_fabric_cost_dtls_id_main_fabric_cond and 
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td width='50' align='right'>
			<? 
			//if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			//{
				if($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]!="")
				{
					echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
				}
				else
				{
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
				}
			//}
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			$total_amount=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
            
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],4); 
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			echo number_format($amount,4); 
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            
           
            
           
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        
        <td width='50' align='right'></td>
        <?
		$other_fab_total=0;
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'></td>
            <td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('rate')],4);?></td>
            <td width='50' align='right' > 
			<? 
			$fabwise_amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			echo number_format($fabwise_amount,4);
			$other_fab_total+=$fabwise_amount;
			?>
            </td>
            <?
			}
			?>
            </tr> 
    </table>
 </td>
 </tr>
 </table>
 <br/>
 <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" border="1">
 <tr>
 <td>
  <?
	   $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
 <strong>Total Amount (In Words):</strong><? echo number_to_words(def_number_format($grand_total_amount+$total_amount,2,""),$mcurrency, $dcurrency);?>
 </td>
 </tr>
 </table>
    <?
	  }
	  //=====================================================================Purchase End==============================================================
	?>
    
   <br/>
   <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		
		$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by c.id");
	
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		
		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/ 
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	    
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id,c.id 
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<? 
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");                          

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$colar_excess_per=def_number_format(($plan_cut_qnty[csf('plan_cut_qnty')]*$colar_excess_percent)/100,6,0);
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per,0); 
				$color_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per; 
				$color_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')]; 
				$grand_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per; 
				$grand_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')]; 
				?>
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>
        
        <?
		$nameArray_item_size=sql_select( "select  b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by c.id");
		
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        
        <?  
		foreach($nameArray_item_size  as $result_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	
        }    
        ?>	
        <td rowspan="2" align="center"><strong>Total</strong></td> 
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>
        
        <?
        foreach($nameArray_item_size  as $result_item_size)
		{	     
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?	
        }    
        ?>	
         <?
	       
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id ,c.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?> 
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result['color_number_id']]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>
            
            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
             
				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
				
				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$cuff_excess_per=def_number_format((($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100,6,"");
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0); 
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2; 
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per; 
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2; 
				/*echo $color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;*/
				?>
                
                </td>
				<?	
			}    
			?>	
            
            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>
                
                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");                          
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
                                    </td>
                                </tr>
                            <?
						}
					}
					/*else
					{
				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                   
                   
                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					
					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?
					
					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?
					
					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    
                    
                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1"); 
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        
<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>DTM</b></td>
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Item</td>
                    <td>Body Color</td>
                    <td>Item Color</td>
                    <td>Dyeing Qty.</td>
                    <td>UOM</td>
                    </tr>
                    <?
					$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
//echo "select a.fabric_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,sum(qty) as qty   from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id a.booking_no=$txt_booking_no group by a.fabric_color,a.precost_trim_cost_id,b.item_group,b.cons_uom";
					$co=0;
					$sql_data=sql_select("select a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,sum(qty) as qty   from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id and a.booking_no=$txt_booking_no and a.qty>0 group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom order by a.fabric_color");
					foreach($sql_data  as $row)
                    {
					$co++;
					?>
                    <tr>
                    <td><? echo $co; ?></td>
                    <td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
                    <td><? echo $color_library[$row[csf('fabric_color')]];?></td>
                    <td><? echo $color_library[$row[csf('item_color')]];?></td>
                    <td align="right"><? echo $row[csf('qty')];?></td>
                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
                    </tr>
                    <?
					}
					?>
                    </table>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                
                
                <td width="49%" valign="top">
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by po_break_down_id,color_number_id,size_number_id,item_number_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by a.id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<? 
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0) 
					{
						echo "Over Booking";
					} 
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>
                    
                    <tr>
                    <td colspan="3">Total:</td>
                    
                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                </td>
                
            </tr>
        </table> 
    
    
    
    
    
    
    
    
    
    
    
    
    
     
        
         <!--<br><br><br><br>-->
         <?
		 	//echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
         
         <?
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=1 and company_id=$cbo_company_name order by sequence_no" );
	     $count=count($sql);

	$width=1330;
	$td_width=floor($width/$count);
	
	$standard_width=$count*150;
	
	if($standard_width>$width) $td_width=150;
	
	$no_coloumn_per_tr=floor($width/$td_width);
	$col=$count-2;
	$i=1;
	echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
	foreach($sql as $row)	
	{
		echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
		
		if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
		$i++;
	} 
	echo '</tr></table>';
		 ?>
       </div>
       <?
      
}

if($action=="show_fabric_booking_report5")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<div style="width:1330px" align="center">       
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
    ?>										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							//$location=return_field_value("location_name","lib_location","id=$data[3]" );
							//$nameArray=sql_select("select location_name from wo_po_details_master where job_no=$txt_job_no"); 
							if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";	
							}
							
							foreach ($nameArray as $result)
                            {
							 echo  $location_name_arr[$location]; 
                            ?>
                             
                               <!-- Plot No: <? //echo $result[csf('plot_no')]; ?> 
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?> 
                                Block No: <? //echo $result[csf('block_no')];?> 
                                City No: <? //echo $result[csf('city')];?> 
                                Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                                Province No: <?php //echo $result[csf('province')];?> 
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?> --><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')]; ?>
                             
                                <?
								
                            }
							
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                
            </tr>
             <tr>
                
                	
                
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                
                
                
            </tr>
             
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                
                
                
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                
                
                
            </tr>  
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                
            </tr> 
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                
            </tr> 
            
        </table>  
           <?
			}
	        ?>
     <br/>
      <!--  Here will be the main portion  -->
     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
				
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
				
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;
				
			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;
				
			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
				
			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

	 ?>
     
     <? 
	 
	/*$nameArray_fabric_description= sql_select("SELECT a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,a.process_loss_percent FROM view_wo_fabric_booking_data_park a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and b.status_active=1 and	
b.is_deleted=0  group by a.color_type_id,a.construction,a.composition,a.gsm_weight,a.dia_width,process_loss_percent order by a.pre_cost_fabric_cost_dtls_id");*/
if($cbo_fabric_source==1)
{
$nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 

c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="8" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>  
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
  
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id 
		  FROM 
		  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b 
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and 
		  a.job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
           <!-- <td  width="120" align="left">
			<?
			
			//echo $color_library[$color_wise_wo_result['fabric_color_id']]; 
			
			?></td>-->
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			

			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM 
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b 
				  WHERE 
				  b.booking_no =$txt_booking_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and 
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
				  a.construction='".$result_fabric_description['construction']."' and 
				  a.composition='".$result_fabric_description['composition']."' and 
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
				  a.dia_width='".$result_fabric_description['dia_width']."' and 
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and 
				  b.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				  b.status_active=1 and
				  b.is_deleted=0");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and

				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            
            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				
			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td><td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format(($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4); $grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right"><? echo number_format(($grand_total_grey_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right">
            <?
            if($process_loss_method==1)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
			}
			
			if($process_loss_method==2)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
			}
			echo number_format($totalprocess_percent_dzn,2);
			?>
            </td>
            </tr> 
    </table>
    <?
	}
	
	if($cbo_fabric_source==2)
	{
	
$nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type , b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and 
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and 
b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
d.booking_no =$txt_booking_no and 
d.status_active=1 and 
d.is_deleted=0 
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width");
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="8" width="50"><p>Total Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
        <td  rowspan="8" width="50"><p>Avg Rate <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="8" width="50"><p>Amount </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>  
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
        	
           
       </tr>       
        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
      
       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";			
		}
		?>
       
       </tr>
       <?
	      
		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id 
		  FROM 
		  wo_pre_cos_fab_co_color_dtls
		  WHERE 
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_amount=0;
			//$grand_totalcons_per_finish=0;
			//$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id 
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
           <!-- <td  width="120" align="left">
			<?
			
			//echo $color_library[$color_wise_wo_result['fabric_color_id']]; 
			
			?></td>-->
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			

			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_amount=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM 
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b 
				  WHERE 
				  b.booking_no =$txt_booking_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and 
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
				  a.construction='".$result_fabric_description['construction']."' and 
				  a.composition='".$result_fabric_description['composition']."' and 
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
				  a.dia_width='".$result_fabric_description['dia_width']."' and 
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and 
				  b.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				  b.status_active=1 and
				  b.is_deleted=0");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,d.rate avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				if($db_type==2)
				{
					
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and 
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
				d.booking_no =$txt_booking_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
				d.status_active=1 and 
				d.is_deleted=0 
				");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<? 
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<? 
			if($color_wise_wo_result_qnty[csf('rate')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],2); 
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty['grey_fab_qnty'];
			}
			?>
            </td>
            <td width='50' align='right' > 
			<?
			$amount=$color_wise_wo_result_qnty[csf('fin_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,2); 
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            
            <td align="right">
            <?
			echo number_format($total_amount,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,

												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
            <?
			echo number_format($grand_total_amount,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM 
												  view_wo_fabric_booking_data_park a,
												  wo_booking_dtls b 
												  WHERE 
												  b.booking_no =$txt_booking_no  and
												  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
												  a.po_break_down_id=b.po_break_down_id and 
												  a.color_size_table_id=b.color_size_table_id and
												  a.color_type_id='".$result_fabric_description['color_type_id']."' and 
												  a.construction='".$result_fabric_description['construction']."' and 
												  a.composition='".$result_fabric_description['composition']."' and 
												  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and 
												  a.dia_width='".$result_fabric_description['dia_width']."' and 
												  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
												  b.status_active=1 and
												  b.is_deleted=0
												  ");*/
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d  
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and 
												b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
												d.booking_no =$txt_booking_no and 
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												d.status_active=1 and 
												d.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				
			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<? 
			$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo "(".$grand_total_fin_fab_qnty."/".$po_qnty_tot.")*(".$total_set_qnty."*".$costing_per_qnty.")";
			echo number_format($consumption_per_unit_fab,4); 
			//$grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right">
			<?
			$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
			//$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right">
            <?
			echo number_format($consumption_per_unit_amuont,2);
			?>
            </td>
            </tr> 
    </table>
    <?
	}
	?>
        <br/>
        <?
		if($cbo_fabric_source==1){
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
                                    </td>
                                </tr>
                            <?
						}
					}
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                   
                </td>
                
            </tr>
        </table>
        <?
		}// fabric Source End
		?>
         
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
         
       </div>
       <?
      
}

if($action=="create_file")
{
	$content=$data;
	$file=fopen("welcome.html","w+");
	fwrite($file,$data);
	$file = 'welcome.html';
}
if($action=="download_file")
{
	extract($_REQUEST);
	set_time_limit(0);
	$file_path=$_REQUEST['filename'];
	download_start($file_path, ''.$_REQUEST['filename'].'', 'text/plain');
}

function download_start($file, $name, $mime_type='')
{
	if(file_exists($file))
	{
		echo "file found";
	}
	else
	{
    	die('File not found');
	}

	//Check the file exist or not
	if(!is_readable($file)) die('File not found or inaccessible!');
	$size = filesize($file);
	$name = rawurldecode($name);
	/* MIME type check*/
	$known_mime_types=array(
	  "pdf" => "application/pdf",
	  "txt" => "text/plain",
	  "html" => "text/html",
	  "htm" => "text/html",
	  "exe" => "application/octet-stream",
	  "zip" => "application/zip",
	  "doc" => "application/msword",
	  "xls" => "application/vnd.ms-excel",
	  "ppt" => "application/vnd.ms-powerpoint",
	  "gif" => "image/gif",
	  "png" => "image/png",
	  "jpeg"=> "image/jpg",
	  "jpg" =>  "image/jpg",
	  "php" => "text/plain"
	);
	
	if($mime_type=='')
	{
		$file_extension = strtolower(substr(strrchr($file,"."),1));
		if(array_key_exists($file_extension, $known_mime_types))
		{
		$mime_type=$known_mime_types[$file_extension];
	    } 
		else 
		{
			$mime_type="application/force-download";
		}
    }
	//turn off output buffering to decrease cpu usage
	@ob_end_clean(); 
	// required for IE Only
	if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off'); 
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes'); 
	/*non-cacheable */
	header("Cache-control: private");
	header('Pragma: private');
	header("Expires: Tue, 15 May 1984 12:00:00 GMT");
	
	// multipart-download and download resuming support
	if(isset($_SERVER['HTTP_RANGE']))
	{
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
		list($range) = explode(",",$range,2);
		list($range, $range_end) = explode("-", $range);
		$range=intval($range);
		if(!$range_end) {
		 $range_end=$size-1;
		} else {
		 $range_end=intval($range_end);
		}
		$new_length = $range_end-$range+1;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range-$range_end/$size");
	} else {
		$new_length=$size;
		header("Content-Length: ".$size);
	}
	
	/* Will output the file itself */
	$chunksize = 1*(1024*1024); //you may want to change this
	$bytes_send = 0;
	if ($file = fopen($file, 'r')){
	if(isset($_SERVER['HTTP_RANGE']))
	fseek($file, $range);
	
	while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length))
	{
		$buffer = fread($file, $chunksize);
		print($buffer); //echo($buffer); // can also possible
		flush();
		$bytes_send += strlen($buffer);
	}
	fclose($file);
	} else
	//If no permissiion
	die('Error - can not open file.');
	//die
	die();
}


if ($action=="unapp_request_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];
		
	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$booking_no=$data_all[0];
	$unapp_request=$data_all[1];
	
	$wo_id=return_field_value("id", "wo_booking_mst", "booking_no='$booking_no' and status_active=1 and is_deleted=0","id");
	
	if($unapp_request=="")
	{	
		$sql_request="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0","approval_cause");
		}
	}
	
	//echo $booking_no.'_'.$unapp_request;
	
	
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo $unapp_request; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();
			
			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{
				
				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				// alert(http.responseText);//return;
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				
				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}
		
		function fnc_close()
		{	
			unappv_request= $("#unappv_request").val();
			
			document.getElementById('hidden_appv_cause').value=unappv_request;
			
			parent.emailwindow.hide();
		}
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}



if ($action=="save_update_delete_unappv_request")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	//echo "shajjad_".$unappv_request.'_'.$wo_id.'_'.$page_id.'_'.$user_id; die;

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$approved_no=return_field_value("MAX(approved_no) approved_no","approval_history","entry_form=7 and mst_id=$wo_id","approved_no");
		
		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=7 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no","id");
		
		if($unapproved_request=="")
		{	
			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
		
			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,2,".$approved_no.",".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			//echo $rID; die;
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				echo "0**".$rID."**".$wo_id;
			}
			disconnect($con);
			die;	
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			
			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT"); 
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id); 
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				echo "1**".$rID."**".str_replace("'","",$wo_id);
			}
			disconnect($con);
			die;
		}
		
	}

	if ($operation==1)  // Update Here
	{	
		
	}
	
}


?>