<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 25-04-2015
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
Entry From 		 : 361
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.conversions.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------



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
if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
		
		function js_set_value( str_data,tr_id ) {
			toggle( tr_id, '#FFFFCC');
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			//alert(str_all[2]);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;	
			}
				document.getElementById('job_no').value=str_all[2];
				
				if( jQuery.inArray( str , selected_id ) == -1 ) {
					selected_id.push( str );
					selected_name.push( str_po );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == str ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = '' ; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
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
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
    
         
				<table width="1100"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="1090" class="rpt_table" align="center" rules="all">
                                <thead>                	 
                                    <th width="150">Company Name</th>
                                    <th width="140">Buyer Name</th>
                                    <th width="100">Job No</th>
                                    <th width="60">Ref No</th>
                                    <th width="100">Order No</th>
                                    <th width="60">Style No</th>
                                    <th width="60">File No</th>
                                    <th width="150">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_aop_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                        ?>
                                    </td>
                                <td id="buyer_td">
									
                                 <?
								 if(str_replace("'","",$cbo_company_name)!=0)
								 {
								 	echo create_drop_down( "cbo_buyer_name", 150,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$cbo_company_name)."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" ); 
								 }
								 else
								 {
								   echo create_drop_down( "cbo_buyer_name", 150, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
								 }
                                ?>	
                                </td>
                                 <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                 <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px"></td>
                                 <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="<? echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="<? echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'service_booking_aop_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
                            </tr>
                            <tr>
                                <td  align="center"  valign="top" colspan="4">
                                    <? //echo load_month_buttons();  ?>
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="9" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number"></td>
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
	
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond="";
	//new development 
	if (str_replace("'","",$data[7])!="") $ref_cond=" and b.grouping='$data[7]' "; else  $ref_cond="";
	if (str_replace("'","",$data[8])!="") $style_ref_cond=" and a.style_ref_no='$data[8]' "; else  $style_ref_cond="";
	if (str_replace("'","",$data[9])!="") $file_no_cond=" and b.file_no='$data[9]' "; else  $file_no_cond="";
	
	if($db_type==0)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$arr=array (2=>$comp,3=>$buyer_arr);
	
	if ($data[2]==0)
	{
		 $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.approved=1 $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond order by a.job_no";  
		// echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,File No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,60,120,60,90,120,70,80","1020","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,1,0,1,3','','');
	}
	else
	{
		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0  $company $buyer order by a.job_no";
		
		echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
	}
	
} 


if ($action=="populate_order_data_from_search_popup")
{
	 
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "load_drop_down( 'requires/service_booking_aop_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=6 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_aop_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if ($action=="load_drop_down_fabric_description")
{

	$data=explode("_",$data);
	$fabric_description_array=array();
	if($data[1] =="")
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and cons_process=35 ");
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  
		where job_no='$data[0]' and status_active=1 and is_deleted=0 and cons_process=35  ");
	}
	
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			
		}
		
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  job_no='$data[0]'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected,
	"set_process(this.value,'set_process')" );
} 


 
 if($action=="set_process")
 {
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 echo $process; die;
	 
 }
 
 
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );


if($action=="lapdip_approval_list_view_edit")
{
	
	$data=explode("**",$data);
	$job_no=$data[0];
	$type=$data[1];
	$process=$data[3];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
	$fabric_description_array_empty=array();
	$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$job_no'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  job_no='$job_no'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	
	if($rate_from_library==1)
	{
		$rate_disable="disabled";	
	}
	else
	{
		$fab_mapping_disable="disabled";
	}
	
	if($type==0)
	{
	 $sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	       sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
	       a.amount,b.size_number_id,b.color_number_id,a.dia_width,a.lib_composition,a.lib_supplier_rate_id
		   from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and 
		   a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id and a.job_no='$job_no' and a.booking_type=3 and a.process=35 and 
		   a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and   a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 ";
	//echo $sql;//die;
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row)
	{
		$sensitivity=$row[csf("sensitivity")];
		$fabric_description_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		if(in_array($fabric_description_id,$fabric_description_array_empty))
		{
			$print_cond_header=0;
			$print_cond_footer=0;
        }
		else
		{
			$print_cond_header=1;
			$i=1;
			if($z==1) $print_cond_footer=0; else $print_cond_footer=1;
			$fabric_description_array_empty[]=$fabric_description_id;
		}
	
		if($print_cond_footer==1)
		{
        ?>
                </table>
            </div>
		<?
		}
		if($print_cond_header==1)
		{
		?>
          
			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close">
				<table class="rpt_table" border="1" width="1300" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Gmts.Size</th>
						<th>Item Size</th>
                        <th>Fab. Mapping</th>
                        <th>UOM</th>
                        <th>Fin Dia</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
                        <th></th>
					</thead>
		<?
		}
        ?>
                <tbody>
                   <tr align="center">
							<td>
								<?
									echo create_drop_down("po_no_".$fabric_description_id."_".$i, 110, $po_number,"", 1,'', $row[csf("po_break_down_id")],"",1);
								?>
								<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:80px;" class="text_boxes" disabled="disabled">
							</td>
                            
                            <td>
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("artwork_no")]; ?>" style="width:70px;" class="text_boxes">
							</td>
							
							
							<td>
                             <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
								<input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" disabled="disabled" />
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>" disabled="disabled" />
							</td>
							<td>
								<input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("fabric_color_id")]];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("fabric_color_id")];} else { echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";}?>">
                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";}?>" disabled="disabled" />
								<input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="<?  echo $row[csf("id")]; ?>">
							</td>
                            <td>
								<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?php echo $row[csf('lib_composition')]; ?>" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
                              
								<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="<?php echo $row[csf('lib_supplier_rate_id')]; ?>">
							</td>
                            <td>
								<?
									echo create_drop_down("uom_".$fabric_description_id."_".$i, 70, $unit_of_measurement,"", 1, "--Select--",$row[csf("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","","$uom_item");
								?>
							</td>
                             <td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("dia_width")]; ?>" style="width:100px;" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-'); ?>" style="width:70px;" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_end_date")],'dd-mm-yyyy','-'); ?>" style="width:70px;" class="datepicker">
							</td>
                            <td>
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty'); calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $row[csf("wo_qnty")]; ?>"/>
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $row[csf("rate")]; ?>" <?php echo $rate_disable; ?>>
							</td>
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $row[csf("amount")]; ?>" disabled="disabled">
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td></td>
						</tr>
                </tbody>
		<?
		$i++;
		$z++;
	}
	if($z>1)
	{
	?>
			</table>
		</div>
	<?
	}
	}
	if($type==1)
	{
		
		$fabric_description_id=$data[2];
		$process=$data[3];
		$sensitivity=$data[4];
		$txt_order_no_id=$data[5];
		
		if($sensitivity==0)
		{
			$groupby="group by b.id,b.po_number";
		    $sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnt ,
			d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per ,
			CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN 
			round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
			
			from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls
			
			e,wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no 
			and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and 
			c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and 
			f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no' and e.id in($fabric_description_id) and b.id 
			in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
			c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 
			
			group by b.id,b.po_number,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,
			f.costing_per";
			
			$sql2="select b.id as po_break_down_id, min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  from wo_po_break_down b,
			wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='$job_no' and b.id 
			in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $groupby";


		}
		
		
		else if($sensitivity==1 || $sensitivity==3)
		{
			$groupby="group by b.id,b.po_number,c.color_number_id";
			
			 $sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty,
			 d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per,
			 CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN
			 round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			 round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
			 
			 from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,
			 wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g 
			  
			 where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no
			 and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes 
		 	 and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no'
			 and e.id in($fabric_description_id) and b.id in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1
		     and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 
			 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  
			
		     group by b.id,b.po_number,c.color_number_id,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,
			 e.color_break_down,f.body_part_id,f.costing_per";
			 
			 $sql2="select b.id as po_break_down_id, c.color_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  
			 from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and
			 b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
			 c.is_deleted=0 $groupby";
		}
		else if($sensitivity==2)
		{
			$groupby="group by b.id,b.po_number,c.size_number_id";
			$sql1="select b.id as po_break_down_id,b.po_number,min(c.id) as color_size_table_id,c.size_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty,
			d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per ,
			g.gmts_sizes,g.item_size,CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN
			round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
			
			from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls 
			e,wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and 
			a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id 
			and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and 
			f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no' and e.id in($fabric_description_id) and 
			b.id in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1
			and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 
			and f.is_deleted=0 
			group by b.id,b.po_number,c.size_number_id,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,
			e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per , g.gmts_sizes,g.item_size";
			
		    $sql2="select b.id as po_break_down_id, c.size_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty 
			from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and 
			b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			$groupby";
//echo $sql1;
		}
		else if($sensitivity==4)

		{
			
		 	$groupby="group by b.id,b.po_number,c.color_number_id,c.size_number_id";
			$sql1="select b.id as po_break_down_id,b.po_number,min(c.id) as color_size_table_id,c.size_number_id,c.color_number_id,
			sum(c.plan_cut_qnty) as plan_cut_qnty ,
			d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per,
			g.gmts_sizes,g.item_size,CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN 
			round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty
			
			from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,
			wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g 
			
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no
			and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and 
			c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and 
			f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no' and e.id in($fabric_description_id) and b.id 
			in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and
			c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		    group by b.id,b.po_number,c.color_number_id,c.size_number_id,e.fabric_description,d.costing_per,e.cons_process,e.req_qnty,e.charge_unit,
			e.amount,e.color_break_down,f.body_part_id,f.costing_per ,g.gmts_sizes,g.item_size";
			
		 	$sql2="select b.id as po_break_down_id, c.color_number_id,c.size_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty
			from wo_po_break_down b, wo_po_color_size_breakdown c 
			
			where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 
			and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $groupby";
		}
		
		
		?>
			
            
			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close">
            
				<table class="rpt_table" border="1" width="1200" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Gmts.Size</th>
						<th>Item Size</th>
                        <th>Fab. Mapping</th>
                        <th>UOM</th>
                        <th>Fin Dia</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
						<th></th>
					</thead>
					<tbody>
					<?
					// echo "document.getElementById('hide_fabric_description').value = '".$fabric_description_id."';\n";
					 
					
					$dataArray=sql_select($sql1);
					if(count($dataArray)==0)
					{
						$dataArray=sql_select($sql2);
					}
					$i=1;
					
					foreach($dataArray as $row)
					{
						
						
						$woqnty="";
						if($row[csf("body_part_id")]==3)
						{
							$woqnty=$row[csf("plan_cut_qnty")]*2;
							$uom_item="1,2";
							$selected_uom="1";
						}
						else if($row[csf("body_part_id")]==2)
						{
							$woqnty=$row[csf("plan_cut_qnty")]*1;
							$uom_item="1,2";
							$selected_uom="1";
						}
						else if($row[csf("body_part_id")]!=2 || $row[csf("body_part_id")]!=3 )
						{
							$woqnty=$row[csf("wo_req_qnty")];
							$selected_uom="12";
						}
						
						if($row[csf("body_part_id")]==2 || $row[csf("body_part_id")]==3)
						{
						    $rate="";
							$amount="";	
						}
						else
						{
							
							$rate=$row[csf("charge_unit")];
							$amount=$rate*$woqnty;
						}
						$sql=sql_select("select a.gmts_color_id,a.contrast_color_id from wo_pre_cos_fab_co_color_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.fabric_description and b.id=$fabric_description_id");
						foreach($sql as $gcid)
						{
							$item_color_arr[$fabric_description_id][$gcid[csf("gmts_color_id")]]=$gcid[csf("contrast_color_id")];
						}
						$itemColor=$item_color_arr[$fabric_description_id][$row[csf("color_number_id")]];
						if($woqnty>0){
							?>
							<tr align="center">
								<td>
									<?
										echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number,"", 1,'', $row[csf("po_break_down_id")],"",1);
									?>
									<input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
								</td>
								<td>
									<?
										echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
									?>
									<input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
								</td>
								
								<td>
									<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" class="text_boxes">
								</td>
								<td>
	                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
	                            
									<input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" disabled="disabled"/>
	                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>"disabled="disabled"/>
								</td>
								<td>
									<input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor ];} else { echo "";}?>"/>
	                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
								</td>
								<td>
									<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled" />
	                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								</td>
								<td>
									<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>">
	                                <input type="hidden" name="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
									<input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
								</td>
	                             <td>
									<input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
	                              
									<input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
								</td>
	                            <td>
									<?
									echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$selected_uom,"copy_value(".$fabric_description_id.",".$i.",'uom')","","$uom_item");
									?>
								</td>
	                            <td>
									<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:100px;" class="text_boxes">
								</td>
	                            <td>
									<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" class="datepicker">
								</td>
	                            <td>
									<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" class="datepicker">
								</td>
	                            <td>
									<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $woqnty; ?>"/>
								</td>
	                            <td>
									<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" <?php echo $rate_disable; ?>>
								</td>
	                            <td>
									<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								</td>
	                            <td>
									<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
								</td>
								<td></td>
							</tr>
							<?	
							$i++;
						}
					}
					?>
					</tbody>
				</table>
			</div>
		<?
		
	}
}

if ($action=="fabric_detls_list_view")
{
	$data=explode("**",$data);
	
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', 
			'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  job_no='$job_no'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	
	if($db_type==0) { $group_concat="group_concat(b.po_break_down_id) as order_id"; $group_concat.=",group_concat(b.id) as dtls_id";}
	if($db_type==2)
	 { $group_concat="listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) as order_id";
	   $group_concat.=",listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as dtls_id";
	}
	$sql="select a.id, a.job_no,b.booking_no,$group_concat,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,b.process,b.sensitivity,
	sum(b.wo_qnty) as wo_qnty,b.insert_date from wo_booking_dtls b, wo_booking_mst a
  	where b.booking_no=a.booking_no and a.booking_no='$data[1]'and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	and b.process=35
  	group by a.job_no,a.id,b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date";
	//echo $sql;
		?>
    <div id="" style="" class="accord_close">
    
        <table class="rpt_table" border="1" width="1100" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="50px">Sl</th>
                <th width="300px">Fabric Description</th>
                <th width="100px">Job No</th>
                <th width="100px">Booking No</th>
                <th width="200px">Po Number</th>
                <th width="100px">Process </th>
                <th width="120px">Sensitivity</th>
                <th width="80px">WO. Qnty</th>
                <th width="80px">Amount</th>
                <th></th>
            </thead>
            <tbody>
            <?
            $dataArray=sql_select($sql);
        
            $i=1;
            foreach($dataArray as $row)
            {
				$allorder='';
				$all_po_number=explode(",",$row[csf('order_id')]);
				foreach($all_po_number as $po_id)
				{
				if($allorder!="") 	$allorder.=",".$po_number[$po_id];
				else 				$allorder=$po_number[$po_id];
					
				}
                
                
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")];?>","child_form_input_data","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
                    <td> <? echo $i; ?>
                        
                        <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><p><? echo  $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p> </td>
                    
                    <td>	<? echo  $row[csf('job_no')]; ?></td>
                    <td>	<? echo  $row[csf('booking_no')]; ?></td>
                    <td>	<p><? echo  implode(",",array_unique(explode(",",$allorder))); ?></p></td>
                    <td>	<? echo  $conversion_cost_head_array[$row[csf('process')]]; ?></td>
                    <td>	<? echo  $size_color_sensitive[$row[csf('sensitivity')]]; ?></td>
                    <td>	<? echo  $row[csf('wo_qnty')]; ?></td>
                    <td>	<? echo  $row[csf('amount')]; ?></td>
                    <td></td>
                </tr>
            <?	
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
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
			if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		    $response_booking_no="";
			if($db_type==0)
			{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SB', date("Y",time()), 5, 
			"select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 
			and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
			}
			if($db_type==2)
			{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SB', date("Y",time()), 5,"select booking_no_prefix,
			booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and 
			to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
			}
			
			$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
			$field_array="id,booking_type,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,
			job_no,po_break_down_id,item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,
			pay_mode,source,attention,process,inserted_by,insert_date";
			$data_array ="(".$id.",3,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$response_booking_no=$new_booking_no[0];
			// echo "insert into wo_booking_mst($field_array)values".$data_array;die;
		    $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
			check_table_status( $_SESSION['menu_id'],0); 
			
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$response_booking_no;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_booking_no;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$response_booking_no;
			}
			else{
				oci_rollback($con);  
				echo "10**".$response_booking_no;
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
		 $field_array_up="booking_type*booking_month*booking_year*booking_no*buyer_id*job_no*po_break_down_id*
		 item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*updated_by*update_date";
		 $data_array_up ="3*".$cbo_booking_month."*".$cbo_booking_year."*".$txt_booking_no."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //=======================================================================================================
		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		if($db_type==0)
		{
			if($rID){
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
			if($rID){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
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
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID){
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
			if($rID){
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



if ($action=="save_update_delete_dtls")
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
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,pre_cost_fabric_cost_dtls_id,artwork_no,po_break_down_id,color_size_table_id,job_no,booking_no,booking_type,fabric_color_id,
         gmts_color_id,item_size,gmts_size,description,uom,process,sensitivity,wo_qnty,rate,amount,delivery_date,delivery_end_date,dia_width,lib_composition, lib_supplier_rate_id,inserted_by,
		 insert_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;			 
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $findia="findia_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name");

			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","361");  
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }
			 else $color_id = 0;
			 
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$$fabric_description_id.",".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$txt_job_no.",".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$lib_composition.",".$$lib_supplier_rateId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		     $id_dtls=$id_dtls+1;
		 }
		
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);   
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
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
		 
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id
*gmts_color_id*item_size*gmts_size*description*uom*process*sensitivity*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width* lib_composition* lib_supplier_rate_id*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;			 
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $gmts_size_id="gmts_size_id_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $findia="findia_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","361");  
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }
			 else $color_id = 0;

			//$color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name");  
			if(str_replace("'",'',$$updateid)!="")
			{
			$id_arr[]=str_replace("'",'',$$updateid);
			$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$po_id."*".$$color_size_table_id."*".$txt_job_no."*".$txt_booking_no."*3*".$color_id."*".$$gmts_color_id."*".$$item_size."*".$$gmts_size_id."*".$$fabric_description_id."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$findia."*".$$lib_composition."*".$$lib_supplier_rateId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		
		// print_r($id_arr);die;
		//echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		 
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
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
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$txt_all_update_id=str_replace("*",",",str_replace("'","",$txt_all_update_id));
		$rID=sql_multirow_update("wo_booking_dtls",$field_array,$data_array,"id","".$txt_all_update_id."",1);
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;
		 
		
	}
}
 

if ($action=="service_booking_popup")
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
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                 	<thead>
                        	<th  colspan="6">
                              <?
                              echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                    </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Job  No</th>
                        <th width="100">Booking No</th>
                        <th width="200">Date Range</th>
                        <th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_aop_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     	<? 
						echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
						?>
                    </td>
                     <td>
					  <input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px">
					 </td> 
                      <td>
					  <input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px">
					 </td> 
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_booking_search_list_view', 'search_div', 'service_booking_aop_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
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
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num='$data[5]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
	}
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	
	if($data[6]==2)
	{
	 	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
	 	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	
	if($data[6]==3)
	{
	 	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond="";
	 	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond=""; 
	} 
	 
	 
	 
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_no,6=>$item_category,7=>$fabric_source,8=>$suplier);
	 $sql= "select a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,c.job_no_prefix_num,
		a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id from wo_booking_mst a ,wo_po_details_master c 
		where $company $buyer $booking_date and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.process=35
		and a.job_no=c.job_no    $booking_cond $job_cond 
		group by a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
		a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,c.job_no_prefix_num,a.supplier_id
		order by booking_no_prefix_num desc"; 
		
	//echo $sql;
		
/*	$sql= "select a.process,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
		a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id from wo_booking_mst a 
		where $company $buyer $booking_date and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.process=1 ";*/	
		
		//echo $sql;
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier", "100,80,100,100,90,200,80,80","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,item_category,fabric_source,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,po_break_down_id,item_category,fabric_source,supplier_id", '','','0,3,0,0,0,0,0,0,0','','');
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
		//freeze_window(operation);
		http.open("POST","trims_booking_controller.php",true);
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
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            
            
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
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	?>
	<div style="width:1150px" align="left">       
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="90%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
								//echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                            

							 $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> 
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                           <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <?php echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                            <? echo $result[csf('website')];
                            }
							?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking For AOP</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id" > 
               
               </td>      
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$nameArray_job=sql_select( "select distinct c.job_no, c.style_ref_no  from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$style_ref_no.=$result_job[csf('style_ref_no')].",";
		}
		$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                 <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Style No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo chop($style_ref_no,',');
				?> 
                </td>
                 <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $all_job_arr=rtrim($job_no,',');
				?> 
                </td> 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  style="font-size:12px">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
        </table>  
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=35 and status_active=1 and is_deleted=0 and wo_qnty>0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1  and process=35 and status_active=1 and is_deleted=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
			//echo "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." <br>";
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty>0 and rate>0 and status_active=1 and is_deleted=0 "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and status_active=1 and is_deleted=0 and fabric_color_id=".$result_color[csf('fabric_color_id')]."");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total+=$result_color_size_qnty[csf('cons')] ;
                if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]=$result_color_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[fabric_color_id]] !='')
                {
                echo number_format($color_tatal[$result_color[fabric_color_id]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1"); 
		
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2  and process=35 and status_active=1 and is_deleted=0<br>";
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2  and process=35 and status_active=1 and is_deleted=0 and wo_qnty>0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2  and process=35 and status_active=1 and is_deleted=0");
		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and rate>0 and status_active=1 and is_deleted=0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>

                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and status_active=1 and is_deleted=0");  
	
					foreach($nameArray_size_size_qnty as $result_size_size_qnty)
					{
					?>
					<td style="border:1px solid black; text-align:right">
					<? 
					if($result_size_size_qnty[csf('cons')]!= "")
					{
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
					$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')]; 
					}
					}
					else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[gmts_sizes]] !='')
                {
                echo number_format($color_tatal[$result_size[gmts_sizes]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3  and process=35 and status_active=1 and is_deleted=0 and wo_qnty>0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3  and process=35 and status_active=1 and is_deleted=0 "); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and rate>0 and status_active=1 and is_deleted=0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0 ");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3"); 
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0 and wo_qnty>0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty>0 and rate>0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>
                    <?
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>
                
                <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0");                          
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0  and process=35 and status_active=1 and is_deleted=0 and wo_qnty>0"); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty>0 and rate>0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and status_active=1 and is_deleted=0");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>

                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
        <?
        $mcurrency="";
	   $dcurrency="";
	   if($result[csf('currency_id')]==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($result[csf('currency_id')]==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($result[csf('currency_id')]==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
		?>
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);//echo number_to_words(number_format($booking_grand_total,2),"USD", "CENTS");?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    <br><br>
    <?
    if($show_comment==1)
	{
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery();
	//$convQty=$conversion->getQtyArray_by_orderAndProcess();
	$convAmt=$conversion->getAmountArray_by_orderAndProcess();
	//print_r($convAmt);
	?>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>
                   
                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width=""> Comments </th>
                </tr>
       <tbody>
       <?
					$pre_cost_currency_arr=return_library_array( "select job_no,currency_id from  wo_po_details_master", "job_no", "currency_id"  );
					$pre_cost_exchange_rate_arr=return_library_array( "select job_no,exchange_rate from   wo_pre_cost_mst", "job_no", "exchange_rate"  );
					
					$pre_cost_item_id_arr=return_library_array( "select id,item_number_id from wo_pre_cost_fabric_cost_dtls", "id", "item_number_id"  );
					$ship_date_arr=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date"  );
					$gmtsitem_ratio_array=array();
					$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  ");// where job_no ='FAL-14-01157'
					foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
					{
					$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
					}
					$po_qty_arr=array();$aop_data_arr=array();
					 $aop_booking_array=array();$aop_booking_data=array();
					  $sql_wo=sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.item_category=12 and 
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
						foreach($sql_wo as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
							$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
						}
						
						if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
					else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
					$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
					 booking_type=3 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
					foreach($wo_book as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$aop_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
						
					//$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.plan_cut) as order_quantity,(sum(b.plan_cut)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					$sql_po_qty=sql_select("select a.job_no_mst as job, a.item_number_id, sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a  where     a.is_deleted=0  and a.status_active=1 group by a.job_no_mst,a.item_number_id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("job")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
					}
					$pre_cost=sql_select("select job_no,sum(charge_unit) as charge_unit,sum(amount) AS aop_cost, sum(avg_req_qnty) as avg_req_qnty from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
						$aop_data_arr[$row[csf('job_no')]]['avg_req_qnty']=$row[csf('avg_req_qnty')];
						$aop_data_arr[$row[csf('job_no')]]['unit']=$row[csf('charge_unit')];	
					}
					
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
					if($db_type==0)
					{
						$group_concat="group_concat(c.fabric_description ) as  pre_cost_fabric_cost_dtls_id,group_concat(c.id ) as  conv_cost_dtls_id ";	
					}
					else
					{
						$group_concat="listagg(c.fabric_description ,',') within group (order by c.fabric_description) AS pre_cost_fabric_cost_dtls_id,
  listagg(c.id ,',') within group (order by c.id) AS conv_cost_dtls_id";
					}
				
				
				$sql_aop="select $group_concat,b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c    where a.job_no=b.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and b.amount>0 and  a.status_active=1  and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0  and b.status_active=1  and b.is_deleted=0   group by b.po_break_down_id,a.job_no  order by b.po_break_down_id";
				
				//echo $sql_aop;
					
                    $nameArray=sql_select( $sql_aop );
					
					//print_r($nameArray);
                    foreach ($nameArray as $selectResult)
                    {
						
						//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						
						$pre_cost_item=array_unique(explode(",",$selectResult[csf('pre_cost_fabric_cost_dtls_id')]));
						//print_r($pre_cost_item);
						$po_qty=0;$booking_data='';
						foreach($pre_cost_item as $item)
						{
							$set_ratio=$gmtsitem_ratio_array[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]];
							$po_qty+=$po_qty_arr[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]]['order_quantity'];
						}
						$conv_cost_dtls_id=array_unique(explode(",",$selectResult[csf('conv_cost_dtls_id')]));
						//print_r($pre_cost_item);
						$booking_data='';
						foreach($conv_cost_dtls_id as $ids)
						{
								
								if($booking_data!='' ) $booking_data.=",".$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];else $booking_data=$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
						}
						$booking_amount=0;
						$exchaned_rate=0;
						$booking_data=array_unique(explode(",",$booking_data));
						foreach($booking_data as $book_no)  //Cumulative value ---Aziz--
						{
							if($book_no!=str_replace("'","",$txt_booking_no))
							{
								$booking_amount=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['amount'];
								$exchaned_rate=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['exchange_rate'];
							}
						}
						
						
						//echo $booking_amount.'='.$exchaned_rate;
						//print_r($booking_data);
						
						$pre_cost_currence_id=$pre_cost_currency_arr[$selectResult[csf('job_no')]];
						$pre_cost_exchange_rate=$pre_cost_exchange_rate_arr[$selectResult[csf('job_no')]];
						$wo_currence_id=$result[csf('currency_id')];
						if($pre_cost_currence_id==$wo_currence_id)//USD=2,TK=1
						{
							 $aop_charge=($selectResult[csf("amount")]/1)+($booking_amount/1);
						}
						/*else if($wo_currence_id==2 && $pre_cost_currence_id==1 ) 
						{
							 $aop_charge=$selectResult[csf("amount")]*$result[csf('exchange_rate')];
						}*/
						else if($wo_currence_id==1 && $pre_cost_currence_id==2 ) 
						{
							 $aop_charge=($selectResult[csf("amount")]/$pre_cost_exchange_rate)+($booking_amount/$pre_cost_exchange_rate);
						}
						
						
						$tot_per_ratio=$costing_per_qty*$set_ratio;
						
						//$pre_cost_aop=(($aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']/$tot_per_ratio)*$po_qty)*$aop_data_arr[$selectResult[csf('job_no')]]['unit'];
						$pre_cost_aop=$convAmt[$selectResult[csf('po_id')]][35];

						//$aop_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$ship_date_arr[$selectResult[csf("po_id")]];
						
						//echo $aop_data_arr[$selectResult[csf('job_no')]]['aop'].'=>>'.$tot_per_ratio.'=='.$po_qty;
						//$all_job_arr[]=$selectResult[csf('job_no')];
						//echo "Jahid";
						
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?> 
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?> 
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?> 
                    
                    </td>
                     <td style="border:1px solid black;" width="80" align="right" title="<? echo $aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']."##".$tot_per_ratio ."##".$po_qty."##".$aop_data_arr[$selectResult[csf('job_no')]]['unit'];  ?> ">
                     <? echo number_format($pre_cost_aop,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($aop_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_aop>$aop_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_aop<$aop_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_aop==$aop_charge) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_aop;
	  	 $tot_aop_cost+=$aop_charge;
		 $total_balance_aop+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
    <?
	}
	?>
         <br/>
		 <?
            echo signature_table(79, $cbo_company_name, "1113px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,implode(',',$all_job_arr));
         ?>
    </div>
    
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
<?
exit();
}


if($action=="show_trim_booking_report1")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                           // echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                           
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> 
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                           <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <?php echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                            <? echo $result[csf('website')];
                            }
							?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking For AOP</strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
		
	    $buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		/*$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
		}*/
		
		$po_no=""; $po_id='';
		
		$nameArray_job=sql_select( "select b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.id, b.po_number"); 
		
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('id')].",";
		}
		
		// PO ID Different But Po No Same Then Following Code (Added By Fuad)
		//$po_no=implode(",",array_unique(explode(",",substr($po_no,0,-1)))); 
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")";
		//$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")", "po_break_down_id", "article_number"  );
		
		$po_id=substr($po_id,0,-1);//(Added By Fuad)
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".$po_id.")", "po_break_down_id", "article_number");
		//print_r($article_number_arr);
		
        foreach ($nameArray as $result)
        {
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>
                	
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>	
                <td  width="110" rowspan="6" align="center">
                
                <? 
			$nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
			?>
            
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
                       <? 
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
                </td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                
            </tr> 
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo rtrim($job_no,',');
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo $buyer_name_arr[$buyer_name];
				?> 
                </td>
            </tr> 
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
        </table> 
        <br/> 
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 "); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Gmts Color</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description='".$result_item[csf('description')]."' and wo_qnty !=0 and sensitivity=1 and status_active=1 and is_deleted=0  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0 and process=35");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0 and process=35"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Constrast Color</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_constrast_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0 group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_constrast_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_constrast_color,4);
                $total_constrast_color+=$amount_constrast_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_constrast_color,4);
                $booking_grand_total+=$total_constrast_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Constrast COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0 and process=35 ");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0 and process=35"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0 group by po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_size_sensitive,4);
                $total_amount_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_size_sensitive,4);
                $booking_grand_total+=$total_amount_size_sensitive;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->
        
         <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0 and process=35 ");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0 and process=35"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Color & Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$total_amount_color_and_size_sensitive=0;
            $nameArray_item_description=sql_select( "select po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,
			dia_width,sum(wo_qnty) as cons 
			from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." 
			and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0
			group by po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_color_and_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_color_and_size_sensitive,4);
                $total_amount_color_and_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_color_and_size_sensitive,4);
                $booking_grand_total+=$total_amount_color_and_size_sensitive;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->
        
        <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 
		and wo_qnty !=0 and status_active=1 and is_deleted=0 and process=35" ); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong> As Per No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 and is_deleted=0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
			
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and status_active=1 and is_deleted=0 ");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
        <?
        $mcurrency="";
	   $dcurrency="";
	   if($result[csf('currency_id')]==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($result[csf('currency_id')]==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($result[csf('currency_id')]==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
		?>
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);//number_to_words(number_format($booking_grand_total,2),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
       <br><br>
        <?
    if($show_comment==1)
	{
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery();
	//$convQty=$conversion->getQtyArray_by_orderAndProcess();
	$convAmt=$conversion->getAmountArray_by_orderAndProcess();
	//print_r($convAmt);
	?>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>
                   
                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width=""> Comments </th>
                </tr>
       <tbody>
       <?
					$pre_cost_currency_arr=return_library_array( "select job_no,currency_id from  wo_po_details_master", "job_no", "currency_id");
					$pre_cost_exchange_rate_arr=return_library_array( "select job_no,exchange_rate from   wo_pre_cost_mst", "job_no", "exchange_rate"  );
					
					$pre_cost_item_id_arr=return_library_array( "select id,item_number_id from wo_pre_cost_fabric_cost_dtls", "id", "item_number_id"  );
					$ship_date_arr=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date"  );
					
					
					$gmtsitem_ratio_array=array();
					$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  ");// where job_no ='FAL-14-01157'
					foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
					{
					$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
					}
					
					
					$po_qty_arr=array();$aop_data_arr=array();
					 $aop_booking_array=array();$aop_booking_data=array();
					  $sql_wo=sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.item_category=12 and 
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
						foreach($sql_wo as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
							$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
						}
						
						if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
					else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
					$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
					 booking_type=3 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
					foreach($wo_book as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$aop_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
						
					//$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.plan_cut) as order_quantity,(sum(b.plan_cut)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					$sql_po_qty=sql_select("select a.job_no_mst as job, a.item_number_id,sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a  where     a.is_deleted=0  and a.status_active=1 group by a.job_no_mst,a.item_number_id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("job")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
					}
					$pre_cost=sql_select("select job_no,sum(charge_unit) as charge_unit,sum(amount) AS aop_cost, sum(avg_req_qnty) as avg_req_qnty from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
						$aop_data_arr[$row[csf('job_no')]]['avg_req_qnty']=$row[csf('avg_req_qnty')];
						$aop_data_arr[$row[csf('job_no')]]['unit']=$row[csf('charge_unit')];	
					}
					
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
					if($db_type==0)
					{
						$group_concat="group_concat(c.fabric_description ) as  pre_cost_fabric_cost_dtls_id,group_concat(c.id ) as  conv_cost_dtls_id ";	
					}
					else
					{
						$group_concat="listagg(c.fabric_description ,',') within group (order by c.fabric_description) AS pre_cost_fabric_cost_dtls_id,
  listagg(c.id ,',') within group (order by c.id) AS conv_cost_dtls_id";
					}
					
					$sql_po_qty=sql_select("select a.job_no_mst as job, a.item_number_id,sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a  where     a.is_deleted=0  and a.status_active=1 group by a.job_no_mst,a.item_number_id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("job")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
					}
					
					$pre_cost=sql_select("select job_no,sum(charge_unit) as charge_unit,sum(amount) AS aop_cost, sum(avg_req_qnty) as avg_req_qnty  from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
						$aop_data_arr[$row[csf('job_no')]]['avg_req_qnty']=$row[csf('avg_req_qnty')];
						$aop_data_arr[$row[csf('job_no')]]['unit']=$row[csf('charge_unit')];		
					}
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
				
					 $sql_aop="select $group_concat,b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c    where a.job_no=b.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12  and b.amount>0 and  a.status_active=1  and a.is_deleted=0  and c.status_active=1  and c.is_deleted=0  and b.status_active=1  and b.is_deleted=0   group by b.po_break_down_id,a.job_no  order by b.po_break_down_id";
					
                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						$pre_cost_item=array_unique(explode(",",$selectResult[csf('pre_cost_fabric_cost_dtls_id')]));
						$po_qty=0;
						foreach($pre_cost_item as $item)
						{
							$set_ratio=$gmtsitem_ratio_array[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]];
							$po_qty+=$po_qty_arr[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]]['order_quantity'];
						}
						$conv_cost_dtls_id=array_unique(explode(",",$selectResult[csf('conv_cost_dtls_id')]));
						//print_r($pre_cost_item);
						$booking_data='';
						foreach($conv_cost_dtls_id as $ids)
						{
								
								if($booking_data!='' ) $booking_data.=",".$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];else $booking_data=$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
						}
						$booking_amount=0;
						$exchaned_rate=0;
						$booking_data=array_unique(explode(",",$booking_data));
						foreach($booking_data as $book_no) //Cumulative value
						{
							if($book_no!=str_replace("'","",$txt_booking_no))
							{
							$booking_amount=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['amount'];
							$exchaned_rate=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['exchange_rate'];
							}
						}
						
						$pre_cost_exchange_rate=$pre_cost_exchange_rate_arr[$selectResult[csf('job_no')]];
						$pre_cost_currence_id=$pre_cost_currency_arr[$selectResult[csf('job_no')]];
						$wo_currence_id=$result[csf('currency_id')];
						if($pre_cost_currence_id==$wo_currence_id)//USD=2,TK=1
						{
							 
							 $aop_charge=($selectResult[csf("amount")]/1)+($booking_amount/1);
						}
						else if($wo_currence_id==1 && $pre_cost_currence_id==2 ) 
						{
							 
							 $aop_charge=($selectResult[csf("amount")]/$pre_cost_exchange_rate)+($booking_amount/$pre_cost_exchange_rate);
						}
						$tot_per_ratio=$costing_per_qty*$set_ratio;
						//$pre_cost_aop=(($aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']/$tot_per_ratio)*$po_qty)*$aop_data_arr[$selectResult[csf('job_no')]]['unit'];
						$pre_cost_aop=$convAmt[$selectResult[csf('po_id')]][35];
						$ship_date=$ship_date_arr[$selectResult[csf("po_id")]];

	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?> 
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?> 
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?> 
                    
                    </td>
                     <td style="border:1px solid black;" width="80" align="right" title="<? echo $aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']."##".$tot_per_ratio."##".$po_qty."##".$aop_data_arr[$selectResult[csf('job_no')]]['unit'];   ?>">
                     <? 
					 echo number_format($pre_cost_aop,2);
					 ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($aop_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_aop>$aop_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_aop<$aop_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_aop==$aop_charge) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_aop;
	  	 $tot_aop_cost+=$aop_charge;
		 $total_balance_aop+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
          <?
	}
		  ?>
         <br/>
        
		 <?
            echo signature_table(79, $cbo_company_name, "1313px");
         ?>
    </div>
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
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

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year from wo_booking_mst  where booking_no='$data'"; 
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_process').value = '35';\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		echo "load_drop_down( 'requires/service_booking_aop_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		//echo "load_drop_down( 'requires/service_booking_aop_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";


	 }
}


if ($action=="Supplier_workorder_popup")
{
	echo load_html_head_contents("Production Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?> 
	<script>
		var permission='<? echo $permission; ?>';
		
		function js_set_value(id,rate,cons_compo)
		{
			document.getElementById('hide_charge_id').value=id;
			document.getElementById('hide_supplier_rate').value=rate;
			document.getElementById('hide_construction_compo').value=cons_compo;
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
            <input type="hidden" name="hide_supplier_rate" id="hide_supplier_rate" class="text_boxes" value="">
            <input type="hidden" name="hide_charge_id" id="hide_charge_id" class="text_boxes" value="">
            <input type="hidden" name="hide_construction_compo" id="hide_construction_compo" class="text_boxes" value="">
            <div style="width:720px;max-height:450px;" align="center">
                <table cellspacing="0" width="700" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="35">SL</th>
                        <th width="200">Construction & Composition </th>
                        <th width="100">Process Type </th>
                        <th width="150">Process Name</th>
                        <th width="100">Color</th>
                        <th width="50">UOM</th>
                        <th width="">Rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?
						$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.process_type_id as PROCESS_TYPE_ID,d.const_comp as CONST_COMP,d.process_id AS PROCESS_ID,d.gsm as GSM,d.color_id as COLOR_ID,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=25 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=6 and d.comapny_id=$cbo_company_name and a.id=$cbo_supplier_name");
						
						$i=1;
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rate=$row['RATE']/($txt_exchange_rate*1);
							if($hidden_supplier_rate_id==$row['ID'])  $bgcolor="#FFFF00";
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25" onClick="js_set_value('<? echo $row['ID']; ?>','<? echo $rate; ?>','<? echo $row['CONST_COMP']; ?>')" style="cursor:pointer"> 
								<td><?php echo $i; ?></td>
                                <td align="left"><? echo $row['CONST_COMP']; ?></td>
                                <td align="left"><? echo $process_type[$row['PROCESS_TYPE_ID']]; ?></td>
                                <td align="left"><? echo $conversion_cost_head_array[$row['PROCESS_ID']]; ?></td>
                                <td align="left"><? echo $color_library_arr[$row['COLOR_ID']]; ?></td>
                                <td align="left"><? echo $unit_of_measurement[$row['UOM_ID']]; ?></td>
								<td><? echo number_format($rate,4,".",""); ?>
                                    <input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $row['ID']; ?>">
								</td>
							</tr>
							<? 
							$i++;
						}
                        ?>
                    </tbody>
                </table>
               
            </div>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


?>