<?
/*-------------------------------------------- Comments 
Version          : V2
Purpose			 : This form will create Service Booking For Dyeing V2
Functionality	 :	
JS Functions	 :
Created by		 : md mamun ahmed sagor
Creation date 	 : 05-06-2022
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
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=207 and is_deleted=0 and status_active=1");
    //echo $print_report_format; die;
    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
   // echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();	
}

if ($action=="load_drop_down_supplier")
{ 
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name",100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_dyeing_controller_v2');",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name",100, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0 and b.party_type in(21,25) group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_dyeing_controller_v2');","");
	}
	exit();
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	if($data[1]==5 || $data[1]==3 )
	{
			$supplier_name=return_field_value("contract_person","lib_company","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	else
	{
			$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
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
	selected_job_id = new Array(), selected_job_name = new Array();	;	
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
		
		function js_set_value( str_data,tr_id ) 
		{
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			
			var sp=1;
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed');return;	
			}
			if ( document.getElementById('booking_no').value!="" && document.getElementById('booking_no').value!=str_all[3] )
			{
				alert('No Booking Mix Allowed')
				return;
			}

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1; 

			var select_str=$('#booking_id_' + tr_id).val();
			
			var select_row=0;
			for(var i=1; i<=tbl_row_count; i++){

				var string=$('#booking_id_' + i).val();
				if(select_str==string)
				{
					//alert(select_str+'='+string);
					if(select_row==0)
					{
						select_row=i; sp=1;
					}
					else
					{
						select_row+=','+i; sp=2;
					}
					
					if( jQuery.inArray( $('#job_id_' + i).val() , selected_job_id ) == -1 ) {
							selected_job_id.push( $('#job_id_' + i).val() );
							selected_job_name.push( $('#job_no_' + i).val() );
					}
				}
			}
			
		
			var exrow = new Array();
				if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
				else countrow=1;

			//alert(exrow)
			for(var m=0; m<countrow; m++)
			{
				if(sp==2) exrow[m]=exrow[m];
				else exrow[m]=select_row;

				toggle( 'tr_'+exrow[m], '#FFFFCC');

				if( jQuery.inArray( $('#po_id_' + exrow[m]).val() , selected_id ) == -1 ) {
					selected_id.push( $('#po_id_' + exrow[m]).val() );
					selected_name.push( $('#po_number_id_' + exrow[m]).val() );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#po_id_' + exrow[m]).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
			}
			// document.getElementById('job_no').value=selected_job_name;
			// document.getElementById('booking_no').value=str_all[3];
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
			$('#booking_no').val( str_all[3] );
			$('#job_no').val( selected_job_name );
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<?

$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);

$job_no=return_field_value( "job_no", "wo_booking_mst","booking_no=$txt_fab_booking","job_no");
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
    
         
				<table width="1100"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="1090" class="rpt_table" align="center" rules="all">
                                <thead>                	 
                                    <th width="150">Company Name</th>
                                    <th width="140">Buyer Name</th>
									<th width="60">Year</th>
                                    <th width="100">Job No</th>
                                    <th width="60">Ref No</th>
									<th width="130" >
										<?php  if(str_replace("'","",$cbo_basis_on)==1){echo "Order No";}else{ echo "Booking No";}?>
									</th>
                                    <th width="60">Style No</th>
                                    <th width="60">File No</th>									
                                    <th width="150">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_dyeing_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
								<td>
								 <? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-Select-", date('Y'), "",0 ); ?>
								</td>
                                 <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                 <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
                                 <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
								
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="<? //echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="<? //echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $txt_booking_date ?>+'_'+<? echo "'$job_no'" ?>+'_'+<?="$cbo_basis_on";?>+'_'+document.getElementById('cbo_job_year').value, 'create_po_search_list_view', 'search_div', 'service_booking_dyeing_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
                            </tr>
                            <tr>
                                <td  align="center"  valign="top" colspan="4">
                                    <? //echo load_month_buttons();  ?>
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
									<input type="hidden" id="booking_no">
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="6" align="center"><strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number"></td>
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
	$booking_date=$data[10];
	$job_no=$data[11];
	$base_no=$data[12];
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[13]";
	if ($job_no!="") $job_no_cond=" and a.job_no='$job_no' "; else  $job_no_cond=""; 
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  $job_cond=""; 

	if($base_no==1){
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	}else{
		if (str_replace("'","",$data[6])!="") $order_cond=" and d.BOOKING_NO_PREFIX_NUM='$data[6]'  "; else  $order_cond=""; 
	}



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
	
	if($db_type==0)
	{ 
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date,'yyyy-mm-dd')."' and company_id='$data[0]')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($booking_date, "", "",1)."' and company_id='$data[0]')) and page_id=25 and status_active=1 and is_deleted=0";
	}
	$approval_status=sql_select($approval_status);
	$approval_need=$approval_status[0][csf('approval_need')];
	
	 if($approval_need==2 || $approval_need==0 || $approval_need=="") $approval_need_id=0;else $approval_need_id=$approval_need;
	 if($approval_need_id==1) $approval_cond=" and c.approved=$approval_need_id";else $approval_cond="";
	 //echo $approval_cond;die;


	 $booking_arr=array(118=>"Main",108=>"Partial",88=>"Short");

	$arr=array (2=>$comp,3=>$buyer_arr,12=>$booking_arr);
		

	if($base_no==1){

		if ($data[2]==0)
		{
			$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1  and b.shiping_status not in(3)  $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $job_no_cond $year_cond order by a.job_no";  
		//	echo $sql;

			echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,File No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,60,120,60,90,120,70,80","1020","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,1,0,1,3','','');
		}
		else
		{
			$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_no_cond order by a.job_no";
			
			echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
		}


	}else{

	
		$sql= "select a.job_no_prefix_num,to_char(a.insert_date,'YYYY') as year, a.job_no,a.id as job_id,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, 	b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no,d.booking_type,d.booking_no,d.entry_form  	from wo_po_details_master a, wo_po_break_down b, wo_booking_mst d,wo_booking_dtls e where a.job_no=b.job_no_mst  and d.booking_no=e.booking_no and e.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and b.shiping_status not in(3) $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $job_no_cond $year_cond and d.booking_type=1 and d.is_short in (1,2) and d.entry_form in(118,108,88) 	group by a.job_no_prefix_num,a.insert_date, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no ,d.booking_type,d.booking_no,d.entry_form,a.id  order by a.job_no ";  
	
		//	echo $sql;

		// echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,File No,Job Qty.,PO number,PO Qty,Shipment Date,Booking No,Booking Type", "90,60,60,100,60,120,60,90,120,70,80,100,80","1200","320",0, $sql , "js_set_value", "id,po_number,job_no,booking_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0,0,0,entry_form", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,file_no,job_quantity,po_number,po_quantity,shipment_date,booking_no,entry_form", '','','0,0,0,0,0,0,0,1,0,1,3,0,0','','');
		?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"  >

       				 <thead>
				    	<th width="30">SL</th>
                        <th width="90">Job No</th>
                        <th width="60">Year</th>
                        <th width="60">Company</th>
                        <th width="100">Buyer</th>
                        <th width="60">Ref No</th>
                        <th width="120">Style Ref. No</th>
                        <th width="60">File No</th>
                        <th width="90">Job Qty.</th>
						<th width="120">PO number</th>
						<th width="70">PO Qty</th>
						<th width="80">Shipment Date</th>
						<th width="100">Booking No</th>
						<th width="80">Booking Type</th>
        		</thead>
	</table>

	<div style="width:100%; overflow-y:scroll; max-height:340px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="list_view" >

				<?
					$dataArray=sql_select($sql);
				$i=1;
				foreach($dataArray as $row){

				$js_val=$row[csf('id')]."_".$row[csf('po_number')]."_".$row[csf('job_no')]."_".$row[csf('booking_no')];
				?>
				<tr id="tr_<?=$i;?>" onClick="js_set_value('<?=$js_val;?>',<?=$i;?>)">
					<td width="30"> <?=$i;?></td>
					<td width="90" style="word-break:break-all"> <?=$row[csf('job_no_prefix_num')];?></td>
					<td width="60" style="word-break:break-all"> <?=$row[csf('year')];?></td>
					<td width="60" style="word-break:break-all"> <?=$comp[$row[csf('company_name')]];?></td>
					<td width="100" style="word-break:break-all"> <?=$buyer_arr[$row[csf('buyer_name')]];?></td>
					<td width="60" style="word-break:break-all"> <?=$row[csf('grouping')];?></td>
					<td width="120" style="word-break:break-all"> <?=$row[csf('style_ref_no')];?></td>
					<td width="60" style="word-break:break-all"> <?=$row[csf('file_no')];?></td>
					<td width="90" style="word-break:break-all"> <?=$row[csf('job_quantity')];?></td>
					<td width="120" style="word-break:break-all"> <?=$row[csf('po_number')];?></td>
					<td width="70" style="word-break:break-all"> <?=$row[csf('po_quantity')];?></td>
					<td width="80" style="word-break:break-all"> <?=$row[csf('shipment_date')];?></td>
					<td width="100" style="word-break:break-all"> <?=$row[csf('booking_no')];?></td>
					<td width="80" style="word-break:break-all"> <?=$booking_arr[$row[csf('entry_form')]];?>
					<input type="hidden" name="booking_id[]" id="booking_id_<?php echo $i ?>" value="<?=$row[csf('booking_no')];?>"/>
					<input type="hidden" name="po_id[]" id="po_id_<?php echo $i ?>" value="<?=$row[csf('id')];?>"/>
					<input type="hidden" name="po_number_id[]" id="po_number_id_<?php echo $i ?>" value="<?=$row[csf('po_number')];?>"/>
					<input type="hidden" name="job_id[]" id="job_id_<?php echo $i ?>" value="<?=$row[csf('job_id')];?>"/>
					<input type="hidden" name="job_no[]" id="job_no_<?php echo $i ?>" value="<?=$row[csf('job_no')];?>"/>
				</td>
				</tr>
				<?
				$i++;}?>

		</table>
	</div>
		<?


	}



	
} 

if ($action=="fabric_booking_popup")
{
	//echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	//extract($_REQUEST);
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="11">
                        <input type="hidden" id="cbo_search_category">
                        </th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                        <input type="hidden" id="order_no_id" value="<? echo $order_no_id;?>">
                       
                        <? 
						//echo "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name";
						$sql="select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) and id=$company order by company_name";

						$sql_buyer="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$company' and buy.id=$buyer and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
						//echo $sql;
						echo create_drop_down( "cbo_company_mst", 150,$sql ,"id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_booking_urmi_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1); ?>
                    </td>
                    <td ><? echo create_drop_down( "cbo_buyer_name", 150, $sql_buyer,"id,buyer_name", 1, "-- Select Buyer --",$buyer,"",1 ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('order_no_id').value+'_'+<?="'$job_no'";?>, 'create_booking_search_list_view2', 'search_div', 'service_booking_dyeing_controller_v2','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11">

                    <?
						echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
						echo load_month_buttons();
                    ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company?>);
		load_drop_down( 'service_booking_knitting_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view2")
{
	$data=explode('_',$data);
	// echo $data[12];die;
	$po_ids=$data[12];
	
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no ='$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number = '$data[11]'  ";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '$data[11]%'  ";
	}

	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like'%$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]'  ";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '%$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]%'  ";
	}

	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);

	if ($po_ids==0) $po_ids_cond=""; else $po_ids_cond=" and d.id in($po_ids)";
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' ";

	if ($data[13]=="") $job_no_cond=""; else $job_no_cond=" and c.job_no='".trim($data[13])."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' ";

	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	//echo "select id,po_number from wo_po_break_down where id in($po_id)";
	/*$po_number=return_library_array( "select id,po_number from wo_po_break_down where id in($po_id)", "id", "po_number");
	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and   a.status_active=1 and a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row){
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value ){
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}*/

	$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$garments_item,7=>$po_array,10=>$item_category,11=>$fabric_source,12=>$suplier,13=>$approved,14=>$is_ready);

	//  $sql= "select a.id, a.booking_no_prefix_num, c.file_no, c.grouping, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.pay_mode, d.gmts_item_id, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.booking_no, a.ready_to_approved, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118 group by a.id, a.booking_no_prefix_num, c.file_no, c.grouping, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.pay_mode, d.gmts_item_id, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.booking_no, a.ready_to_approved, b.style_ref_no order by a.id DESC";

	$sql="select min(a.id) as id, a.booking_no_prefix_num, a.pay_mode,b.job_no, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond $job_no_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type in (1,4)  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $po_ids_cond group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no,b.job_no order by id DESC";
	?>
    <table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="60">Booking No</th>
                <th width="60">Booking Date</th>
                <th width="80">Buyer</th>
                <th width="60">Job No</th>
                <th width="90">Style Ref.</th>
                <th width="90">Gmts Item </th>
                <th width="100">PO number</th>
                <th width="80">Internal Ref</th>
                <th width="80">File No</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="50">Pay Mode</th>
                <th width="50">Supplier</th>
                <th width="50">Approved</th>
                <th>Ready to Approved</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1160px" >
        <table width="1140" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
            <tbody>
            <?
            $sl=1;
            $data=sql_select($sql);
            foreach($data as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>

				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer">
                    <td width="30"><? echo $sl; ?></td>
                    <td width="60"><? echo $row[csf("booking_no_prefix_num")];?></td>
                    <td width="60"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
                    <td width="80" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                    <td width="60"><? echo $row[csf("job_no")];?></td>
                    <td width="90" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="90" style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]];?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("po_number")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("file_no")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $item_category[$row[csf("item_category")]];?></td>
                    <td width="80" style="word-break:break-all"><? echo $fabric_source[$row[csf("fabric_source")]];?></td>
                    <td width="50" style="word-break:break-all"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                    <td width="50" style="word-break:break-all">
                    <?
                    if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]];
                    ?>
                    </td>
                    <td width="50"><? echo $approved[$row[csf("is_approved")]];?></td>
                    <td><? echo $is_ready[$row[csf("ready_to_approved")]];?></td>
				</tr>
				<?
				$sl++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <?
	exit();
}
if ($action=="populate_order_data_from_search_popup")
{
	 
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst");
	
	$job_no="";
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		// echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		// echo "load_drop_down( 'requires/service_booking_dyeing_controller_v2', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_dyeing_controller_v2', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
		$jobArr[$row[csf("job_no")]]=$row[csf("job_no")];
	
	}

	//======================================================load_drop_down_fabric_description=========================================================
	$fabric_desc_arr=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  
	where  status_active=1 and is_deleted=0 and cons_process in(31) ".where_con_using_array($jobArr,1,'job_no')." ");

	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			$fabric_desc_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")];
		}
		
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where status_active=1 ".where_con_using_array($jobArr,1,'job_no')." ");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			$fabric_desc_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")];

			}
		}
		
							
	}
	$fab_desc_id=implode(",",$fabric_desc_arr);
	echo "set_process('$fab_desc_id','set_process');\n";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if ($action=="load_drop_down_fabric_descriptions")
{
		$data=explode(",",$data);
		foreach($data as $v){
			$jobArr[$v]=$v;

		}
	$fabric_description_array=array();
	$process_id="31,25,26,31,32,33,34,36,37,38,39,40,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,77,80,81,82,83,84,85,86,87,88,89,90,92,93,94,135,136,137,138,140,141,142,143,144,146,147,148,149,150,155,156,158,159,160,161,162,163";

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  
		where  status_active=1 and is_deleted=0 and cons_process in($process_id) ".where_con_using_array($jobArr,1,'job_no')."  ");
	
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
			where  status_active=1 ".where_con_using_array($jobArr,1,'job_no')."");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
	}
	
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected,"set_process(this.value,'set_process')" );
} 

 
 if($action=="set_process")
 {
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 echo $process; die;
	 
 }
 if ($action == "check_fabric_process_data") 
{
	$data=explode("**",$data);
	
	$conv_fabric_des_id=$data[0];
	$job_no=$data[1];
	$booking_no=$data[2];
	$po_id=$data[4];
	 $condition= new condition();
	 if(str_replace("'","",$job_no) !=''){
		  $condition->job_no("='$job_no'");
	 }
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_req_qty_arr=$conversion->getQtyArray_by_orderAndConversionid();
	//print_r($conversion_req_qty_arr);
	 $sql_data_req="select c.job_no,c.id as conv_dtls_id,b.id as po_id from  wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c where   c.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and c.job_no='$job_no' and b.id in($po_id)  group by c.job_no,c.id,b.id";
	$dataResultPreReq=sql_select($sql_data_req);
	$budget_prev_wo_qty=0;
	foreach($dataResultPreReq as $row)
	{
		$budget_prev_wo_qty+=array_sum($conversion_req_qty_arr[$row[csf('po_id')]][$row[csf('conv_dtls_id')]]);
	}
	//echo $budget_prev_wo_qty.'_'.$tot_wo_qty;
	if($budget_prev_wo_qty>0)
	{
		echo $budget_prev_wo_qty.'_';
	}
	exit();
	
}
 
if($action=="lapdip_approval_list_view_edit")
{
	$data=explode("**",$data);
	
	$job_no=$data[0];
	$job_arr=explode(",",$job_no);
	foreach($job_arr as $v){
		$jobArr[$v]=$v;
	}
	$type=$data[1];
	$process=$data[3]; 
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[7];
	$base_no=$data[8];
	$fabric_description_array_empty=array();
	$fabric_description_array=array();
	$po_number=return_library_array( "select id,po_number from wo_po_break_down where status_active=1 ".where_con_using_array($jobArr,1,'job_no_mst')."", "id", "po_number"  );
	
	$fabric_description_array=array();// 
	$wo_pre_cost_fab_co_color_sql=sql_select("select gmts_color_id,contrast_color_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls  where job_no='$job_no'");
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where status_active=1 ".where_con_using_array($jobArr,1,'job_no')." ");
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
			where  status_active=1 ".where_con_using_array($jobArr,1,'job_no')."");
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
	$sql_wo=sql_select("select job_no,currency_id,booking_date,company_id from wo_booking_mst where  status_active=1 ".where_con_using_array($jobArr,1,'job_no')." and booking_no='$txt_booking_no'  and status_active=1" );
	foreach( $sql_wo as $row)
	{
		$currency_id=$row[csf("currency_id")];
		$booking_date=$row[csf("booking_date")];
		$company_id=$row[csf("company_id")];
	}
	$currency_rate=0;
	if($currency_id==1)
	{
	$currency_rate=set_conversion_rate( 2, $booking_date, $company_id );
	}
	
	 /*$sql_data_Priv="select c.color_number_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,b.po_break_down_id as po_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount from  wo_po_color_size_breakdown c,wo_booking_dtls b where  b.color_size_table_id=c.id and b.po_break_down_id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3  and b.job_no='$job_no' and b.process=31 group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,c.color_number_id";*/
		$i=1;
		foreach($jobArr as $jval){
			if($i==1){
				$jobNo .="'".$jval."'";
				$i++;
			}else{
				$jobNo .=",'".$jval."'";
			}
		}
	

	 $condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("in($jobNo)");
		}

		$condition->init();
		
		$conversion= new conversion($condition);
		
		 // echo $job_no.'ddd';
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		// print_r($conversion_knit_qty_arr);
		// echo $conversion->getQuery(); die;	
	/* $booking_no=str_replace("'","",$txt_booking_no);
	 if($booking_no!='') $booking_cond="and b.booking_no!='$booking_no'";
	 else $booking_cond="";*/
	 
	 if($type==1)
	{
	 	$booking_no=str_replace("'","",$txt_booking_no);
		 if($booking_no!='') $booking_cond="and b.booking_no!='$booking_no'";
		 else $booking_cond="";
	 }
	 else if($type==0 && $dtls_id!='')
	 {
	 	 $booking_no=str_replace("'","",$txt_booking_no);
		if($booking_no!='') $booking_cond="and b.booking_no!='$booking_no'";
		 else $booking_cond="";
	 }
	  $sql_data_charge_unit=sql_select("SELECT c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,c.charge_unit from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3  ".where_con_using_array($jobArr,1,'b.job_no')."  and b.process=31  and b.booking_no='$booking_no' group by b.job_no, c.id, b.po_break_down_id, b.sensitivity, b.uom, b.gmts_color_id, b.fabric_color_id, b.gmts_size, c.charge_unit"); 
	 
	   
	 foreach ($sql_data_charge_unit as $row) {
	 	//$po_fab_con_charge_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['rate']=$row[csf('charge_unit')];
		$po_fab_pre_cost_rate_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['pre_rate']=$row[csf('charge_unit')];
	 }
	 
	$sql_data_Priv="select c.id as conv_dtl_id, b.job_no, b.booking_type, b.po_break_down_id as po_id, b.sensitivity, b.uom, b.gmts_color_id, b.fabric_color_id, b.gmts_size, sum(b.wo_qnty) as wo_qnty, sum(b.amount) as amount, c.charge_unit from  wo_pre_cost_fab_conv_cost_dtls c, wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type in (1,3)  ".where_con_using_array($jobArr,1,'b.job_no')."  and b.process=31 $booking_cond group by b.job_no, b.booking_type, c.id, c.charge_unit, b.po_break_down_id, b.sensitivity, b.uom, b.gmts_color_id, b.fabric_color_id, b.gmts_size";
	 
	//  echo 	$sql_data_Priv;
	$dataResultPre=sql_select($sql_data_Priv);
	$po_fab_prev_booking_arr=array();
	foreach($dataResultPre as $row)
	{
		if($row[csf('booking_type')]==3)
		{
			$po_fab_prev_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['wo_qty']+=$row[csf('wo_qnty')];
			 
			$po_fab_prev_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['amount']=$row[csf('amount')];
			if($type==1)
			{
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
		}
		else
		{
			if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==2 || $row[csf('sensitivity')]==0)// AS Per Size
			{
				$po_fab_prev_size_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_size')]]['wo_qnty']+=$row[csf('wo_qnty')];
			}
			else if($row[csf('sensitivity')]==4)// AS Per Color and Size
			{
				$po_fab_prev_color_size_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size')]]['wo_qnty']+=$row[csf('wo_qnty')];
			}
		}
	}
		
	//echo $type;die;
	$tot_prev_wo_qty=0;
	if($type==0)
	{
		$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size,a.process, sensitivity, a.job_no, c.booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate, a.amount, b.size_number_id, b.color_number_id, a.dia_width, a.labdip_no, a.fin_gsm,a.lib_composition, a.lib_supplier_rate_id, b.plan_cut_qnty from wo_booking_dtls a, wo_po_color_size_breakdown b,wo_booking_mst c where a.booking_mst_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id and a.booking_type=3 and a.process=31  and c.process=31 and a.booking_no='$txt_booking_no' and a.status_active=1  and a.is_deleted=0 and c.entry_form=535";
		
		// echo $sql;
		$dataArray=sql_select($sql);
		?>
		<div id="content_search_panel_<?=$fabric_description_id; ?>" style="" class="accord_close">
		<table class="rpt_table" border="1" width="1500" cellpadding="0" cellspacing="0" rules="all" id="table_list_view">
            <thead>
                <th colspan="14"  ><input type="button" id="copy_qnty" value="W.Clear" class="formbutton" style="width:50px;float: right; clear: both;" name="copy_qnty" onClick="copy_value('txt_woqnty');"/></th>
                <th colspan="3"> <input type="button" id="copy_rate"   value="R.Clear" name="copy_rate" class="formbutton"  style="width:50px;float: left; clear: both;" onClick="copy_value('txt_rate');"/></th>
            </thead>
            <thead>
                <th>Po Number</th>
                <th>Fabric Description</th>
                <th>Artwork No</th>
                <th>Gmts. Color</th>
                <th>Item Color</th>
                <th>Gmts.Size</th>
                <th>Item Size</th>							
                <th>UOM</th>
                <th>Fin Dia</th>
                <th>Fin GSM</th>
                <th>Labdip No</th>
                <th>Delivery Start Date</th>
                <th>Delivery End Date</th>
                <th>WO. Qnty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Plan Cut Qnty</th>
            </thead>
            <tbody>
            <?
            $z=1; $i=1;
            foreach($dataArray as $row)
            {
				$sensitivity=$row[csf("sensitivity")];
				$fabric_description_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
				//$row[csf("wo_qnty")]=$row[csf("wo_qnty")]-$prev_wo_qty;
				//$row[csf("amount")]=$row[csf("amount")]-$prev_wo_amount;
				$prev_wo_amount=$po_fab_prev_booking_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['amount'];
				$prev_wo_qty=$po_fab_prev_booking_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['wo_qty'];
				if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;
				
				if($sensitivity==1 || $sensitivity==3)
				{
					$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]]);
					echo $wo_prev_qnty=$po_fab_prev_color_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]]['wo_qnty'];
				}
				else if($sensitivity==4)
				{
					$pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]);
					$wo_prev_qnty=$po_fab_prev_color_size_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]][$row[csf("size_number_id")]]['wo_qnty'];
				}
				else if($sensitivity==2 || $sensitivity==0)
				{
					$pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("size_number_id")]]);
					$wo_prev_qnty=$po_fab_prev_size_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf("size_number_id")]]['wo_qnty'];
				}
				//echo $row[csf('po_break_down_id')].'='.$fabric_description_id.',';
				$pre_rate=$po_fab_pre_cost_rate_arr[$row[csf('po_break_down_id')]][$fabric_description_id]['pre_rate'];
				if($currency_id==1)
				{
					$pre_rate=$pre_rate*$currency_rate;
				}
				
				?>
				<tr align="center">
                    <td><?=create_drop_down("po_no_".$i, 110, $po_number,"", 1,'', $row[csf("po_break_down_id")],"",1); ?>
                        <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><?=create_drop_down("fabric_description_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1); ?>
                    <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $fabric_description_id; ?>" style="width:80px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $row[csf("artwork_no")]; ?>"  onBlur="copy_value('<? echo $i; ?>','artworkno_')" style="width:70px;" class="text_boxes"></td>
                    <td>
                        <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
                        <input type="text" name="gmts_color_<? echo $i; ?>" id="gmts_color_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" readonly />
                        <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>" disabled="disabled" />
                    </td>
                    <td>
                        <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("fabric_color_id")]];} else { echo "";}?>" readonly/>
                        <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("fabric_color_id")];} else { echo "";}?>" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gmts_size_<? echo $i; ?>" id="gmts_size_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled"/>
                        <input type="hidden" name="gmts_size_id_<? echo $i; ?>" id="gmts_size_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="item_size_<? echo $i; ?>" id="item_size_<? echo $i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";} //echo $row[csf("item_size")]; ;?>">
                        <input type="hidden" name="item_size_id_<? echo $i; ?>" id="item_size_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";}?>" disabled="disabled" />
                        <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<?  echo $row[csf("id")]; ?>">
                        <input type="hidden" name="sizesensitive_id_<? echo $i; ?>" id="sizesensitive_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $sensitivity;?>" disabled="disabled"/>
                    </td>
                    <td><?=create_drop_down("uom_".$i, 70, $unit_of_measurement,"", 1, "--Select--",$row[csf("uom")],"copy_value(".$i.",'uom')","","$uom_item"); ?></td>
                    <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $row[csf("dia_width")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes"></td>
                    <td><input type="text" name="fingsm_<? echo $i; ?>" id="fingsm_<? echo $i; ?>" value="<? echo $row[csf("fin_gsm")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $i; ?>','fingsm_')" class="text_boxes"></td>
                    <td><input type="text" name="labdipno_<? echo $i; ?>" id="labdipno_<? echo $i; ?>" value="<? echo $row[csf("labdip_no")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdipno_')" class="text_boxes"></td>
                    <td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-'); ?>" onChange="copy_value('<? echo $i; ?>','startdate_')" style="width:70px;" class="datepicker"></td>
                    <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delivery_end_date")],'dd-mm-yyyy','-'); ?>" onChange="copy_value('<? echo $i; ?>','enddate_')" style="width:70px;" class="datepicker"></td>
                    <td>
                        <input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="calculate_amount(<? echo $i; ?>)" value="<? if($row[csf("wo_qnty")]==0 || $row[csf("wo_qnty")]=="") echo ""; else echo number_format($row[csf("wo_qnty")],4,'.',''); ?>" placeholder="<?=number_format($wo_prev_qnty,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,4,'.','');?>" />
                        <input type="hidden" name="txt_reqwoqnty_<? echo $i; ?>" id="txt_reqwoqnty_<? echo $i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? echo number_format($pre_req_qnty,4,'.',''); ?>"/>
                        <input type="hidden" name="txt_balqnty_<? echo $i; ?>" id="txt_balqnty_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $pre_req_qnty;?>" />
                    </td>
                    <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="calculate_amount(<? echo $i; ?>)" pre-cost-rate="<? echo $pre_rate; ?>" value="<? echo $row[csf("rate")]; ?>" <?php echo $rate_disable; ?> placeholder="<?=$row[csf("rate")];?>"></td>
                    <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($row[csf("amount")],4,'.',''); ?>" disabled="disabled"></td>
                    <td><input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled></td>
				</tr>
				<?
				$i++;
				$z++;
            }?>
            </tbody>
		</table>
		</div>
		<?
	}
	if($type==1)
	{
		$fabric_description_id=$data[2];
		$process=$data[3];
		$txt_order_no_id=$data[5];
		$color_size_sensitive=sql_select( "select color_size_sensitive as sensitive from wo_pre_cost_fabric_cost_dtls where status_active=1 ".where_con_using_array($jobArr,1,'job_no').""  );

		// print_r($color_size_sensitive);
		$sizeSensitive="";
		foreach($color_size_sensitive as $val){
		 	if($val[csf('sensitive')]==0 || $val[csf('sensitive')]==2 || $val[csf('sensitive')]==4){
				$sizeSensitive=",c.size_number_id,g.gmts_sizes,g.item_size";
		    }
		}

		if($base_no==1){

			$groupby="group by b.id,b.po_number,c.color_number_id";
			$sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty,
			 d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per,e.id as fab_desc_id,f.color_size_sensitive,
			 CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN
			 round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			 round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty,g.dia_width $sizeSensitive
			 
			 from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,
			 wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g 
			  
			 where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id
			 and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes 
			 and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id ".where_con_using_array($jobArr,1,'a.job_no')."
			 and e.id in($fabric_description_id) and b.id in($txt_order_no_id)  and a.status_active=1 and a.is_deleted=0  and b.status_active=1
			 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 
			 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  
			
			 group by b.id,b.po_number,c.color_number_id,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,
			 e.color_break_down,f.body_part_id,f.costing_per,e.id,f.color_size_sensitive,g.dia_width $sizeSensitive";
			 
			  $sql2="select b.id as po_break_down_id, c.color_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  
			 from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_id=c.job_id and b.id=c.po_break_down_id  ".where_con_using_array($jobArr,1,'b.job_no_mst')." and b.id in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
			 c.is_deleted=0 $groupby";
		}else{
			$booking_sql_data=sql_select( "select sum(b.wo_qnty) as wo_qnty,b.po_break_down_id,b.gmts_color_id,b.pre_cost_fabric_cost_dtls_id as pre_fab_cost_id from wo_booking_mst a,wo_booking_dtls b where a.id=b.booking_mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=535 and b.entry_form_id=535 and a.tagged_booking_no='$txt_booking_no' ".where_con_using_array($jobArr,1,'job_no')." and a.booking_basis=2 AND b.po_break_down_id IN ($txt_order_no_id)  group by b.po_break_down_id,b.gmts_color_id,b.pre_cost_fabric_cost_dtls_id"  );


				foreach($booking_sql_data as $row){
					$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('pre_fab_cost_id')]][$row[csf('gmts_color_id')]]['qnty']+=$row[csf('wo_qnty')];
				}
			
			 $sql1="SELECT b.id	AS po_break_down_id,b.po_number,MIN (c.id)	AS color_size_table_id,c.color_number_id,SUM (c.plan_cut_qnty)
					AS plan_cut_qnty,SUM (h.grey_fab_qnty)	AS wo_req_qnty,d.costing_per,	e.fabric_description,e.cons_process,
					e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.gsm_weight,f.uom,f.costing_per,e.id AS fab_desc_id,f.color_size_sensitive,g.dia_width,h.rate $sizeSensitive
					FROM wo_po_details_master    a,wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d,
					wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls  f,wo_pre_cos_fab_co_avg_con_dtls g,wo_booking_dtls h
					WHERE  a.id = b.job_id AND a.id = c.job_id	AND a.id = d.job_id	AND a.id = e.job_id
					AND a.id = f.job_id	AND a.id = g.job_id	and b.id=h.po_break_down_id	AND b.id = c.po_break_down_id
					AND b.id = g.po_break_down_id AND c.color_number_id = g.color_number_id	AND c.size_number_id = g.gmts_sizes
					AND c.item_number_id = f.item_number_id	AND f.id = g.pre_cost_fabric_cost_dtls_id AND e.fabric_description = f.id
					AND c.color_number_id=h.gmts_color_id ".where_con_using_array($jobArr,1,'a.job_no')." AND h.BOOKING_NO='$txt_booking_no'
					AND e.id IN ($fabric_description_id) AND h.PO_BREAK_DOWN_ID IN ($txt_order_no_id) AND a.status_active = 1
					AND a.status_active = 1	AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0
					AND c.status_active = 1	AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0
					AND e.status_active = 1	AND e.is_deleted = 0 AND f.status_active = 1 AND f.is_deleted = 0
					GROUP BY b.id,b.po_number,c.color_number_id,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,
					e.amount,e.color_break_down,f.body_part_id,f.costing_per,e.id,f.gsm_weight,f.uom,f.color_size_sensitive,g.dia_width,h.rate";
		}

		
		
		//echo $sql1;
		$prev_wo_qty=$po_fab_prev_booking_arr2[$fabric_description_id]['wo_qty'];
		if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;
		?>
			
            
			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close">
            
				<table class="rpt_table" border="1" width="1500" cellpadding="0" cellspacing="0" rules="all" id="table_list_view">
				<thead>
					<th colspan="14"  ><input type="button" id="copy_qnty" value="W.Clear" class="formbutton" style="width:50px;float: right; clear: both;" name="copy_qnty" onClick="copy_value('txt_woqnty');"/></th>
					<th colspan="3"> <input type="button" id="copy_rate"   value="R.Clear" name="copy_rate" class="formbutton"  style="width:50px;float: left; clear: both;" onClick="copy_value('txt_rate');"/></th>
				</thead>
					<thead>
						<th>Po Number</th>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Gmts.Size</th>
						<th>Item Size</th>
                       
                        <th>UOM</th>
                        <th>Fin Dia</th>
                        <th>Fin GSM</th>
                        <th>Labdip No</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
					</thead>
					<tbody>
					<?
					// echo "document.getElementById('hide_fabric_description').value = '".$fabric_description_id."';\n";
					 
					//echo $sql1;
					$dataArray=sql_select($sql1);
					if(count($dataArray)==0)
					{
					$dataArray=sql_select($sql2);
					}
					$i=1;
					//print_r($dataArray);
					$sql_labdip="select lapdip_no,po_break_down_id,color_name_id from wo_po_lapdip_approval_info where approval_status=3  and is_deleted=0 ".where_con_using_array($jobArr,1,'job_no_mst')." ";
					$labdip_dataArray=sql_select($sql_labdip);
					foreach($labdip_dataArray as $row)
					{
						$lapdip_noArr[$row[csf("po_break_down_id")]][$row[csf("color_name_id")]]=$row[csf("lapdip_no")];
					}
				foreach($dataArray as $row)
				{
						$sensitivity=$row[csf("color_size_sensitive")];
						$fab_desc_id=$row[csf("fab_desc_id")];
						//$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info"," approval_status=3  ".where_con_using_array($jobArr,1,'job_no_mst')." and is_deleted=0  and color_name_id=".$row[csf('color_number_id')]." and  po_break_down_id=".$row[csf('po_break_down_id')]." ");
					
					$lapdip_no=$lapdip_noArr[$row[csf("po_break_down_id")]][$row[csf("color_number_id")]];
					$uomId=$row[csf('uom')];
				    if($base_no==1){

						
						if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
						{
							$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fab_desc_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]]);
							$wo_prev_qnty=$po_fab_prev_color_booking_arr[$row[csf('po_break_down_id')]][$fab_desc_id][$row[csf('color_number_id')]]['wo_qnty'];
						}
						else if($sensitivity==4) // AS Per Color and Size
						{
							$pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fab_desc_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]);
							$wo_prev_qnty=$po_fab_prev_color_size_booking_arr[$row[csf('po_break_down_id')]][$fab_desc_id][$row[csf('color_number_id')]][$row[csf("size_number_id")]]['wo_qnty'];
						}
						else if($sensitivity==2 || $sensitivity==0) // AS Per Size or Select
						{
							$pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fab_desc_id][$row[csf("po_break_down_id")]][$row[csf("size_number_id")]]);
							$wo_prev_qnty=$po_fab_prev_size_booking_arr[$row[csf('po_break_down_id')]][$fab_desc_id][$row[csf("size_number_id")]]['wo_qnty'];
						}
							
						if($row[csf("body_part_id")]==3)
						{ 
							$woqnty=$pre_req_qnty*2;
							$uom_item="1,2";
							$selected_uom="1";
							$bal_woqnty=$woqnty-$wo_prev_qnty;
						}
						else if($row[csf("body_part_id")]==2)
						{
							$woqnty=$pre_req_qnty*1;
							$uom_item="1,2,12";
							$selected_uom="12";
							$bal_woqnty=$woqnty-$wo_prev_qnty;
							//echo $row[csf('body_part_id')].'=='.$selected_uom.'=='.$uom_item;
						}
						else if($row[csf("body_part_id")]!=2 || $row[csf("body_part_id")]!=3 )
						{
							$woqnty=$pre_req_qnty;//$row[csf("wo_req_qnty")];
							$selected_uom="12";
							$bal_woqnty=$woqnty-$wo_prev_qnty;
						}
						
						if($row[csf("body_part_id")]==2 || $row[csf("body_part_id")]==3)
						{
						    $rate=""; $amount="";	
						}
						else
						{
							$bal_woqnty=$woqnty-$wo_prev_qnty;
							
							$rate=$row[csf("charge_unit")];
							if($currency_id==1)
							{
								$rate=$rate*$currency_rate;
								
								//$row[csf("amount")]=$pre_req_qnty*($row[csf("rate")]*$currency_rate);
								//$row[csf("rate")]=($row[csf("rate")]*$currency_rate);
							}
							$amount=$rate*$bal_woqnty;
						
						}
						
						$color_break_down=$row[csf("color_break_down")];
						$color_break_down=explode("__",$row[csf("color_break_down")]);
						foreach($color_break_down as $gcid)
						{
							$color_down=explode("_",$gcid);
							$gmt_color=$color_down[0];
							$fab_color=$color_down[3];
						    $item_color_arr[$fab_desc_id][$gmt_color]=$fab_color;
						}
						
 						if($sensitivity==3)
						{
							$itemColor=$color_library[$contrast_color_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['contrast_color']];
							$item_color_id=$contrast_color_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['contrast_color'];
						}
						else if($sensitivity==1 || $sensitivity==4)
						{
							$itemColor=$color_library[$row[csf("color_number_id")]];
							$item_color_id=$row[csf("color_number_id")];
						}
						else
						{
							$itemColor=$color_library[$item_color_arr[$fab_desc_id][$row[csf("color_number_id")]]];
							$item_color_id=$item_color_arr[$fab_desc_id][$row[csf("color_number_id")]];
						}
						
					}else{
						$woqnty=$row[csf('wo_req_qnty')];
						$rate=$row[csf("charge_unit")];
						if($currency_id==1)
						{
							$rate=$rate*$currency_rate;
							
							//$row[csf("amount")]=$pre_req_qnty*($row[csf("rate")]*$currency_rate);
							//$row[csf("rate")]=($row[csf("rate")]*$currency_rate);
						}
						
						
						
						$booking_qnty=$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('fabric_description')]][$row[csf('color_number_id')]]['qnty'];
						$bal_woqnty=$woqnty-$booking_qnty;
						$amount=$bal_woqnty*$rate;
						
						$color_break_down=$row[csf("color_break_down")];
						$color_break_down=explode("__",$row[csf("color_break_down")]);
						foreach($color_break_down as $gcid)
						{
							$color_down=explode("_",$gcid);
							$gmt_color=$color_down[0];
							$fab_color=$color_down[3];
						    $item_color_arr[$fab_desc_id][$gmt_color]=$fab_color;
						}
					//	echo $sensitivity.'='.$row[csf('fabric_description')].'='.$row[csf('color_number_id')].'<br> ';
 						if($sensitivity==3)
						{
							$itemColor=$color_library[$contrast_color_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['contrast_color']];
							$item_color_id=$contrast_color_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['contrast_color'];
						}
						else if($sensitivity==1 || $sensitivity==4)
						{
							$itemColor=$color_library[$row[csf("color_number_id")]];
							$item_color_id=$row[csf("color_number_id")];
						}
						else
						{
							$itemColor=$color_library[$item_color_arr[$fab_desc_id][$row[csf("color_number_id")]]];
							$item_color_id=$item_color_arr[$fab_desc_id][$row[csf("color_number_id")]];
						}
						
					}
						$woqnty=$woqnty;
						$amount=$amount;
						if($woqnty<=0) $td_color='#FF0000'; else $td_color='';
						
				 	// echo $bal_woqnty.'-'.$wo_prev_qnty.'-'.$rate.',<br>';
						
						if($bal_woqnty>0){
							?>
						<tr align="center">
							<td>
								<?
									echo create_drop_down("po_no_".$i, 100, $po_number,"", 1,'', $row[csf("po_break_down_id")],"",1);
								?>
								<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("fabric_description_".$i, 250, $fabric_description_array,"", 1,'', $fab_desc_id,"",1);
								?>
								<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $fab_desc_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
							</td>
							
							<td>
								<input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onBlur="copy_value('<? echo $fab_desc_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
                            
								<input type="text" name="gmts_color_<? echo $i; ?>" id="gmts_color_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")]];} else { echo "";}?>"  onClick="copy_value('gmts_color',<?=$i; ?>,<?=$row[csf("color_number_id")]; ?>)" readonly/>
                                <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:70px;" class="text_boxes" readonly value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
							
							</td>
							<td>
								<input type="text" name="gmts_size_<? echo $i; ?>" id="gmts_size_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled" />
                                <input type="hidden" name="gmts_size_id_<? echo $i; ?>" id="gmts_size_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								<input type="hidden" name="sizesensitive_id_<? echo $i; ?>" id="sizesensitive_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $sensitivity;?>" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="item_size_<? echo $i; ?>" id="item_size_<? echo $i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fab_desc_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>">
                                <input type="hidden" name="item_size_id_<? echo $i; ?>" id="item_size_id_<? echo $i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
								<input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="">
							</td>
                           
                            <td>
								<?
								echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uomId,"copy_value(".$i.",'uom')","","$uom_item");
								?>
							</td>

                             <td>
								<input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<?=$row[csf('dia_width')];?>" style="width:100px;" onBlur="copy_value('<? echo $fab_desc_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td> 
							<td>
								<input type="text" name="fingsm_<? echo $i; ?>" id="fingsm_<? echo $i; ?>" value="<? echo $row[csf("gsm_weight")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fab_desc_id; ?>','<? echo $i; ?>','fingsm_')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="labdipno_<? echo $i; ?>" id="labdipno_<? echo $i; ?>" value="<? echo $lapdip_no; ?>" style="width:100px;" onBlur="copy_value('<? echo $fab_desc_id; ?>','<? echo $i; ?>','labdipno_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="" style="width:70px;" onChange="copy_value('sdate',<?=$i; ?>,<?=$row[csf("color_number_id")]; ?>)" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="" style="width:70px;" onChange="copy_value('edate',<?=$i; ?>,<?=$row[csf("color_number_id")]; ?>)" class="datepicker">
							</td>
                            <td >
								<input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="" placeholder="<?=number_format($bal_woqnty,4,'.','');?>"/>
								 
								 <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,4,'.',''); ?>" />
                                <input type="hidden" name="txt_reqwoqnty_<? echo $i; ?>" id="txt_reqwoqnty_<? echo $i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,4,'.',''); ?>" />
								<input type="hidden" name="txt_balqnty_<? echo $i; ?>" id="txt_balqnty_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $bal_woqnty;?>" />
								
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" pre-cost-rate="<? echo $rate; ?>" value="<? echo $rate; ?>" <?php echo $rate_disable; ?>  placeholder="<?=number_format($rate,4,'.','');;?>">
								<input type="hidden" name="hidden_rate_<? echo $i; ?>" id="hidden_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" pre-cost-rate="<? echo $rate; ?>" value="<? echo $rate; ?>" <?php echo $rate_disable; ?>  placeholder="<?=number_format($rate,4,'.','');;?>">
							</td>
                            <td>
								<input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($amount,4,'.',''); ?>" disabled="disabled"/>
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
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
	exit();
}

if ($action=="fabric_detls_list_view")
{
	$data=explode("**",$data);
	$po_number=return_library_array( "select id,po_number from wo_po_break_down where job_no_mst='$data[0]'", "id", "po_number"  );
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
  	where b.booking_mst_id=a.id  and a.id='$data[2]' and a.process=31  and b.process=31  and b.status_active=1 and a.entry_form=535 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	
  	group by a.job_no,a.id,b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date";


	// echo $sql;
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
                <th>&nbsp;</th>
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
					if($allorder!="") $allorder.=",".$po_number[$po_id]; else $allorder=$po_number[$po_id];
				}
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")];?>","child_form_input_data","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
                    <td><? echo $i; ?><input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><p><? echo $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p> </td>
                    <td><? echo $row[csf('job_no')]; ?></td>
                    <td><? echo $row[csf('booking_no')]; ?></td>
                    <td><p><? echo implode(",",array_unique(explode(",",$allorder))); ?></p></td>
                    <td><? echo $conversion_cost_head_array[$row[csf('process')]]; ?></td>
                    <td><? echo $size_color_sensitive[$row[csf('sensitivity')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('wo_qnty')],4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],4,'.',''); ?></td>
                    <td>&nbsp;</td>
                </tr>
            <?	
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
	<?
	exit();
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
		
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$response_booking_no="";
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SBD', date("Y",time()), 5,"select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and entry_form=535 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, booking_type, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, supplier_id, currency_id, exchange_rate, booking_date, delivery_date, pay_mode, attention, remarks, ready_to_approved, process, tagged_booking_no, booking_basis, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",3,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$txt_attention.",".$txt_remarks.",".$cbo_ready_to_approved.",31,".$txt_fab_booking.",".$cbo_basis_on.",535,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$response_booking_no=$new_booking_no[0];
		//  echo "insert into wo_booking_mst($field_array)values".$data_array;die;
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		check_table_status( $_SESSION['menu_id'],0); 
			
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_booking_no."**".$id;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$response_booking_no."**".$id;
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
		 $field_array_up="booking_type*buyer_id*job_no*po_break_down_id*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*attention*remarks*ready_to_approved*tagged_booking_no*updated_by*update_date";
		 $data_array_up ="3*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$txt_attention."*".$txt_remarks."*".$cbo_ready_to_approved."*".$txt_fab_booking."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 
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
		else if($db_type==2 || $db_type==1 )
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
	// echo "10**<pre>";
	// print_r($process);
	// echo "</pre>";die;
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
		 $field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_form_id, artwork_no, po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, item_size, gmts_size, description, uom, process, sensitivity, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_gsm, labdip_no, inserted_by, insert_date, status_active, is_deleted";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $artworkno="artworkno_".$i;
             $color_size_table_id="color_size_table_id_".$i;			 
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color_id="item_color_id_".$i;
			 $item_color="item_color_".$i;
			 $gmts_size_id="gmts_size_id_".$i;
			 $item_size="item_size_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $findia="findia_".$i;
			 $fingsm="fingsm_".$i;
			 $labdipno="labdipno_".$i;
			 $sizesensitive_id="sizesensitive_id_".$i;
			 
			$woqnty=str_replace("'","",$$txt_woqnty);
			 
			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","232");  
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }
			 else $color_id =0;
			 
			 if($color_id=='' || $color_id==0) $color_id=0;else $color_id=$color_id;
			 
			 if($woqnty>0)
			 {
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$booking_mst_id.",".$$fabric_description_id.",535,".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$txt_job_no.",".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$$uom.",31,".$$sizesensitive_id.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$fingsm.",".$$labdipno.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		     $id_dtls=$id_dtls+1;
			 }
		 }
		// echo "10**";die;
		 // echo "10**insert into wo_booking_dtls($field_array1)values".$data_array1; check_table_status( $_SESSION['menu_id'],0);die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);   
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
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
		 
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id*gmts_color_id*item_size*gmts_size*description*uom*sensitivity*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_gsm*labdip_no*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $artworkno="artworkno_".$i;
             $color_size_table_id="color_size_table_id_".$i;			 
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color_id="item_color_id_".$i;
			 $item_color="item_color_".$i;
			 $gmts_size_id="gmts_size_id_".$i;
			 $item_size="item_size_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $sizesensitive_id="sizesensitive_id_".$i;
			 $findia="findia_".$i;
			 $fingsm="fingsm_".$i;
			 $labdipno="labdipno_".$i;

		
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","232");  
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }
			 else $color_id =0;
			 

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$po_id."*".$$color_size_table_id."*".$txt_job_no."*".$txt_booking_no."*3*".$color_id."*".$$gmts_color_id."*".$$item_size."*".$$gmts_size_id."*".$$fabric_description_id."*".$$uom."*".$$sizesensitive_id."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$findia."*".$$fingsm."*".$$labdipno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		 
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
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
		$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";
		$txt_all_update_id=str_replace("*",",",str_replace("'","",$txt_all_update_id));
		$rID=sql_multirow_update("wo_booking_dtls",$field_array,$data_array,"id","".$txt_all_update_id."",1);
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);;
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
	var permission="<? echo $_SESSION['page_permission']; ?>";
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
        <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
            <thead>
            	<tr>
                    <th colspan="9">
                      <?
                       echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                      ?>
                    </th>
                </tr>
                <tr>
                    <th width="160">Company Name</th>
                    <th width="160">Buyer Name</th>
                    <th width="120">Booking No</th>
                    <th width="120">Job No</th>
					<th width="120">Ref. No</th>
					<th width="120">File No</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="reset" id="rst" class="formbutton" style="width:60px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>   
                </tr>                	 
            </thead>
            <tbody>
                <tr class="general">
                    <td> <input type="hidden" id="selected_booking">
                    <? 
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_dyeing_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );?></td>
                    <td>
                    <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">	
                    </td>
                    <td>
                    <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write Job No">	
                    </td>
					  <td>
                    <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Write Ref. No">	
                    </td>
					  <td>
                    <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:100px" placeholder="Write File No">	
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td> 
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_file_no').value, 'create_booking_search_list_view', 'search_div', 'service_booking_dyeing_controller_v2', 'setFilterGrid(\'table_body\',-1)')" style="width:60px;" />
                    </td>
                </tr>
                <tr>
                	<td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </tbody>
        </table>   
    	<div id="search_div"> </div>
    </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$job_no=$data[6];
	$ref_no=$data[7];
	$file_no=$data[8];
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $sql_cond .=" and a.buyer_id='$buyer_id'";
	if($ref_no!="") $ref_no_cond=" and c.grouping='$ref_no'";else  $ref_no_cond="";
	if($file_no!="") $file_no_cond=" and c.file_no=$file_no";else  $file_no_cond="";
	if($db_type==0)
	{
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	if($job_no!="")
	{
		if($search_catgory==1)
		{
			$sql_cond .=" and b.job_no_prefix_num='$job_no'";
		}
		else if($search_catgory==2)
		{
			$sql_cond .=" and b.job_no like '$job_no%'";
		}
		else if($search_catgory==3)
		{
			$sql_cond .=" and b.job_no like '%$job_no'";
		}
		else
		{
			$sql_cond .=" and b.job_no like '%$job_no%'";
		}
	}
	
	if($booking_no!="") $sql_cond .=" and a.booking_no_prefix_num=$booking_no";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$job_no_arr=return_library_array( "select b.id, a.job_no_prefix_num from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst",'id','job_no_prefix_num');
	$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//
	$arr=array (2=>$comp_arr,3=>$buyer_arr,4=>$job_no_arr,5=>$po_no_arr,6=>$item_category,7=>$fabric_source,8=>$suplier_arr);

	
	if($db_type==0) { $group_concat="group_concat(c.file_no) as file_no"; $group_concat.=",group_concat(c.file_no) as file_no";}
	if($db_type==2)
	 { $group_concat="listagg(cast(c.grouping as varchar2(4000)),',') within group (order by c.grouping) as grouping";
	   $group_concat.=",listagg(cast(c.file_no as varchar2(4000)),',') within group (order by c.file_no) as file_no";
	}
	
	$sql= "select a.id,a.process, a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.pay_mode, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num,$group_concat
	from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c
	where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=3 and a.entry_form=535 and  a.status_active=1 and a.is_deleted=0  and a.process=31 $sql_cond $ref_no_cond $file_no_cond
	group by  a.id, a.process, a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.pay_mode, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num order by a.id DESC"; 
	//echo $sql;
	//echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Item Category,Fabric Source,Supplier", "50,70,60,60,60,200,120,100","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,item_category,fabric_source,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,po_break_down_id,item_category,fabric_source,supplier_id", '','','0,3,0,0,0,0,0,0,0','','');
	
	?>
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
            	<th width="50">Booking No</th>
                <th width="65">Booking Date</th>
                <th width="60">Buyer</th>
                <th width="60">Job No</th>
                <th width="160">PO number</th>
				 <th width="60">Ref. No</th>
				 <th width="50">File No</th>
                <th width="120">Item Category</th>
                <th width="110">Fabric Source</th>
                <th>Supplier</th>  
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:970px" >
    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="950" id="table_body">
        <tbody>
        <?
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$supplier_str="";
			if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) 
			{
				$supplier_str=$comp_arr[$row[csf('supplier_id')]];
			}
			else 
			{
				$supplier_str=$suplier_arr[$row[csf('supplier_id')]];
			}
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="65" align="center"><p><? if($row[csf("booking_date")]!="" && $row[csf("booking_date")]!="0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>&nbsp;</p></td>
                <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                <td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="160" style="word-break:break-all">
				<?
				$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
				$all_po="";
				foreach($po_id_arr as $po_id)
				{
					$all_po.=$po_no_arr[$po_id].",";
				}
				$all_po=chop($all_po," , ");
				echo $all_po; 
				?>&nbsp;</td>
				 <td width="60" style="word-break:break-all"><? echo implode(",",array_unique(explode(",", $row[csf("grouping")]))); ?>&nbsp;</td>
				 <td width="50" style="word-break:break-all"><? echo implode(",",array_unique(explode(",", $row[csf("file_no")]))); ?>&nbsp;</td>
                <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                <td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
                <td style="word-break:break-all"><? echo $supplier_str; ?>&nbsp;</td>  
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
	exit();
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
exit();
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
	 $sql= "select booking_no, booking_date, company_id, buyer_id, job_no, material_id, po_break_down_id, tagged_booking_no, booking_basis, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, tenor, delivery_date, source, booking_year, ready_to_approved, id, remarks from wo_booking_mst  where id='$data' and booking_type=3 and entry_form=535 and process=31";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		 echo "load_drop_down( 'requires/service_booking_dyeing_controller_v2', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' )\n";
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_process').value = '31';\n";
		echo "document.getElementById('cbo_basis_on').value = '".$row[csf("booking_basis")]."';\n";
		echo "$('#txt_fab_booking').attr('disabled',false);\n";
		echo "document.getElementById('txt_fab_booking').value = '".$row[csf("tagged_booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";

		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
	
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	
	
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		$po_no=chop($po_no," , ");
		echo "document.getElementById('txt_order_no').value = '".$po_no."';\n";
		//echo "load_drop_down( 'requires/service_booking_dyeing_controller_v2', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_id")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_dyeing_controller_v2', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
		echo "print_button_setting(".$row[csf("company_id")].")\n";
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
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.process_type_id as PROCESS_TYPE_ID,d.const_comp as CONST_COMP,d.process_id AS PROCESS_ID,d.gsm as GSM,d.color_id as COLOR_ID,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=21 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=3 and d.comapny_id=$cbo_company_name and a.id=$cbo_supplier_name");
						
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
								<td><?php echo number_format($rate,4,".",""); ?>
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
<?php
exit();
}


if($action=="show_trim_booking_report")//Print B3=>19-05-2022(md mamun ahmed sagor)-ISD-10430
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$booking_id=str_replace("'","",$booking_mst_id);
	
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	//$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		
		// echo "<pre>";
		// print_r();

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.id=$booking_id");


		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$job_no_str=$nameArray[0][csf('job_no')];
		$tagged_booking_no=$nameArray[0][csf('tagged_booking_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$product_code=$nameArray[0][csf('product_code')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		$jobqtypcs=$nameArray[0][csf('jobqtypcs')];
		$inserted_by2=$user_name_arr[$nameArray[0][csf('inserted_by')]];
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$pay_mode=$nameArray[0][csf('pay_mode')];
		$style_ref_no=$nameArray[0][csf('style_ref_no')];
		$team_leader=$team_leader_arr[$nameArray[0][csf('team_leader')]];
		$style_description=$nameArray[0][csf('style_description')];
		$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];

		$job_no_str=$nameArray[0][csf('job_no')];
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$job_no_str'");

	

		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing </font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
							  
                            </td>
							<td><strong style="background-color:yellow;padding:5%;font-size: 30px;"><?=str_replace("'","",$txt_booking_no);;?></strong><br><strong style="margin-left:20%;"><?=str_replace("'","",$txt_booking_date);;?></strong></td>
							
                        </tr>
						
						
                    </table>
					
                </td>
                <td width="200">
                	<table style="border:1px solid black; font-family:Arial Narrow;" width="100%">
                		<tr>
                			<td><b>Min. Ship Date:</b></td>
                			<td><b><?php echo  date('d-m-Y',strtotime($min_shipment_date));?></b></td>
                		</tr>
                		<tr>
                			<td><b>Max. Ship Date:</b></td>
                			<td><b><?php echo date('d-m-Y',strtotime($max_shipment_date));?></b></td>
                		</tr>
                	</table>
                	<br>
                	<table style="border:1px solid black; font-family:Arial Narrow;font-size: 10px;" width="100%">
                		<tr>
                			<td>Printing Date :</td>
                			<td><?php echo  date('d-m-Y');?></td>
                		</tr>
                		<tr>
                			<td>Printing Time:</td>
                			<td><?php echo  date('h:i:sa');?></td>
                		</tr>
                		<tr>
                			<td>User Name:</td>
                			<td><?php echo $user_name_arr[$user_id];?></td>
                		</tr>
                		<tr>
                			<?php 
                				function get_client_ip() {
								    $ipaddress = '';
								    if (getenv('HTTP_CLIENT_IP'))
								        $ipaddress = getenv('HTTP_CLIENT_IP');
								    else if(getenv('HTTP_X_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
								    else if(getenv('HTTP_X_FORWARDED'))
								        $ipaddress = getenv('HTTP_X_FORWARDED');
								    else if(getenv('HTTP_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_FORWARDED_FOR');
								    else if(getenv('HTTP_FORWARDED'))
								       $ipaddress = getenv('HTTP_FORWARDED');
								    else if(getenv('REMOTE_ADDR'))
								        $ipaddress = getenv('REMOTE_ADDR');
								    else
								        $ipaddress = 'UNKNOWN';
								    return $ipaddress;
								}

                			 ?>
                			<td>IP Address:</td>
                			<td><?php if(empty($user_ip)){echo get_client_ip();} echo $user_ip;?></td>
                		</tr>
                	</table>
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td colspan="2" rowspan="5" width="210">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" width="210">
                                <legend>Image </legend>
                                <table width="208">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../../../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="200" height="200" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
					</td>
					<td width="100"><b>Service Provider </b></td>		 
					<td width="140"> <span style="font-size:18px"><?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$result[csf('supplier_id')]];
						}
						else{
						echo $supplier_name_arr[$result[csf('supplier_id')]];
						}
					?></span> </td>
					<td width="100"><span style="font-size:18px"><b>Address</b></span></td>
					<td width="110"><span style="font-size:18px"><?

					$supplier_id=$result[csf('supplier_id')];
				
					if($pay_mode==5 || $pay_mode==3){
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
						foreach ($nameArray as $result)
						{
							$company_address= "Plot No:".$result[csf('plot_no')].",Level No:".$result[csf('level_no')].",Road No:".$result[csf('road_no')].",Block No:".$result[csf('block_no')].",City No:".$result[csf('city')].",Zip Code:".$result[csf('zip_code')].",Province No:".$result[csf('province')].",Country:".$country_arr[$result[csf('country_id')]]; 
						}
						echo $company_address;
						}
						else{
						echo $supplier_address_arr[$result[csf('supplier_id')]];
						}
					
					?> </span> </td>
					<td width="110"><b>Dealing Merchandiser</b></td>
					<td width="100"><? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
				
				</tr>
				<tr>
					<td width="100"><b>Job No</b></td>		 
					<td width="140"> <span style="font-size:18px"><? echo trim($txt_job_no,"'");if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="100"><span style="font-size:18px"><b>Fabric Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><?=str_replace("'","",$tagged_booking_no);;?> </span> </td>
					<td width="100"><span style="font-size:18px"><b>Team Leader</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?=$team_leader;    ?></span></td>	
				</tr>
				<tr>		
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					<td width="100"><span style="font-size:18px"><b>Dept. (Prod Code)</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $product_code; ?></span></td>
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo $style_ref_no; ?></td>				
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="110"><b>Process</b></td>
					<td width="100"><? echo $process; ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Fabric Description</b></span></td>
					<td width="350" ><span style="font-size:18px">
						<? 
							$sql_fab="SELECT a.lib_yarn_count_deter_id AS determin_id, a.construction
							    FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_mst_id = $booking_id AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{
								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]].",";
							}
							echo implode(",", array_unique(explode("***", $des)));
						?>
						</span></td>			
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350"><span style="font-size:18px"><? echo $style_description; ?></span></td>
					<td width="110"><b>Sample Req With Order</b></td>
					<td width="100"></td>
				</tr>
			</table>
			<br>
			
			<?
		}	
			
	  	?>
		<h5 style="color:red;">PLS NOTE: BEFORE START KNITTING MUST CHECK ALL THE BELLOW INFORMATIONS, SPECIALLY DIA, GREY GSM, S/L & COUNT ETC. ANTIQUE WHITE MUST BE TEFLON FINISH TREATMENT</h5>
		<br>

		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$txt_job_no'");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				


				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
			
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_no='$txt_job_no'");

				foreach( $fabric_description as $fabric_description_row)
				{
				

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}

			// echo "<pre>";
			// print_r($fabric_desc_arr);



		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_no='$txt_job_no'  and po_break_down_id in (".$po_id_all.") order by id");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}


	
		//  $nameArray_fabric_description= sql_select("select a.body_part_id, a.lib_yarn_count_deter_id as determin_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width, b.remarks, avg(b.cons) as cons, b.process_loss_percent, avg(b.requirment) as requirment,b.po_break_down_id,  d.fabric_color_id, d.gmts_color_id, d.id as dtls_id, sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty,a.id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0   AND c.status_active = 1  AND c.is_deleted = 0  AND b.status_active = 1  AND b.is_deleted = 0 group by a.body_part_id,a.id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, b.remarks,d.fabric_color_id, d.gmts_color_id, d.id,b.po_break_down_id, b.process_loss_percent order by a.id, a.body_part_id, b.dia_width");

		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,
		sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id,b.delivery_date,b.delivery_end_date	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 	and b.wo_qnty>0	and b.booking_type=3 and b.job_no='$txt_job_no' and b.process=31 and b.booking_mst_id =$booking_id 		group by b.job_no,c.id,c.charge_unit,b.po_break_down_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description,b.delivery_date,b.delivery_end_date ");
		

			

		
		
	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
			$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];

			$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
			$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
			$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
			$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
			$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
			if($body_part_type==40 || $body_part_type==50){
				$body_part_type_arr[$body_part_type]=$body_part_type;
			}


			$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
			$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
			$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
			$process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
		



			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='$txt_job_no'  and approval_status=3 and color_name_id=".$row[csf('fabric_color_id')]."");
	
			$grouping_item=$row[csf('fabric_color_id')].'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$color_type_id;	
				$pp=100+$process_loss_percent;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['lapdip_no'] = $lapdip_no;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['body_part_id'] = $body_part_id;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gsm'] = $gsm_weight;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['color_type_id'] = $color_type_id;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['finsh_cons'] = $finsh_cons;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gray_cons'] = $gray_cons;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fin_fab_qnty'] +=($row[csf('wo_qnty')]/$pp)*100;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('wo_qnty')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;

			if($row[csf('delivery_date')]){
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_start_date'] = $row[csf('delivery_date')];
			}
			if($row[csf('delivery_end_date')]){			
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_end_date'] = $row[csf('delivery_end_date')];
			}
			
	
		}

		$body_part_type_ids=implode(",",$body_part_type_arr);
		// echo $body_part_type_ids;
		// echo "<pre>";
		// print_r($fabric_data_arr);
	
		?>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>
				 <th>Gmts Colors</th>
				 <th>Fabric Color</th>				
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>
			
				 <th>Color Type</th>
				 <th>Finsh Cons.</th>
				 <th>Finish  Qty</th>
				 <th>Grey Cons.</th>
				 <th>Grey Qty</th>
				 <th>Process Loss %</th>
				 <th>Delivery Start Date</th>
				 <th>Delivery End Date</th>
			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $gmts_id=>$fabric_data_arr) {  
			 $i=1;     		  		
				 foreach ($fabric_data_arr as $fabric_id => $value) {
						 $fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
						 $grey_fab_qnty+=$value['grey_fab_qnty'];   		 	
						  if($i==1){
						   ?>
						  <tr>
							 <td rowspan="<? echo count($fabric_data_arr) ?>"><? echo $color_library[$gmts_id] ?></td>
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
							
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td><? echo $color_type[$value['color_type_id']] ?></td>
							 <td align="right"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>		     			
							 <td align="right"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
							 <td align="center"><? echo $value['process_loss_percent'] ?></td>
							 <td align="center"><? echo $value['delivery_start_date'] ?></td>
							 <td align="center"><? echo $value['delivery_end_date'] ?></td>
						 </tr>
						  <? } 
						  else { ?>
							  <tr>
								 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
								 
								 <td><? echo $body_part[$value['body_part_id']] ?></td>
								 <td><? echo $value['fabric_des'] ?></td>
								 <td><? echo $value['gsm'] ?></td>
								 <td><? echo $value['fabric_dia'] ?></td>
								 <td><? echo $color_type[$value['color_type_id']] ?></td> 
								 <td align="right"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
								 <td align="right"><? echo number_format($value['fin_fab_qnty'],4) ?></td>
								 <td align="right"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>			     			
								 <td align="right"><? echo number_format($value['grey_fab_qnty'],4) ?></td>
								 <td align="center"><? echo $value['process_loss_percent'] ?></td>
								 <td align="center"><? echo $value['delivery_start_date'] ?></td>
								 <td align="center"><? echo $value['delivery_end_date'] ?></td>
							 </tr>
						  <? }
						  $i++;
					  //}
				 }
			 } 
			 ?>
			 <tr>
				 <th colspan="8">Total</th>
				 <th align="right"><?echo number_format($fin_fab_qnty,4);  ?></th>
				 <th></th>
				 <th align="right"><?echo number_format($grey_fab_qnty,4);  ?></th>
				 <th></th>
			 </tr>
		 </table>
		  <br/>



      	<!--  Here will be the main portion  -->
		<?
        $costing_per=""; $costing_per_qnty=0;
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$txt_job_no'");
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




      
		
		?>
        <br/>
        

       		
        <br/>
        <table  width="100%" style="margin: 0px;padding: 0px;">
      
       
        <tr>
        	<td width="70%">
        		<table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
        		        <tr>
        		            <td align="center" colspan="9">  Stripe Details</td>
        		            
    		            </tr>
        		        <?
        				$color_name_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
        				$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom,d.totfidder  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d, wo_po_color_size_breakdown e where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$txt_job_no'  and d.job_no='$txt_job_no'  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and e.id=b.color_size_table_id and e.is_deleted=0 and e.status_active=1 and e.color_number_id=d.color_number_id  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width,d.totfidder order by d.id ");

						


        				$result_data=sql_select($sql_stripe);
        				foreach($result_data as $row)
        				{
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
							$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['totfidder']=$row[csf('totfidder')];
        				}
        				?>
        		            <tr>
	        		            <td align="center" width="30"> SL</td>
	        		            <td align="center" width="100"> Body Part</td>
	        		            <td align="center" width="80"> Fabric Color</td>
	        		            <td align="center" width="70"> Fabric Qty(KG)</td>
	        		            <td align="center" width="70"> Stripe Color</td>
	        		            <td align="center" width="70"> Stripe Measurement</td>
	        		            <td align="center" width="70"> Stripe Uom</td>
								<td align="center" width="70"> Total Fedder</td>
	        		            <td  align="center" width="70"> Qty.(KG)</td>
	        		            <td  align="center" width="70"> Y/D Req.</td>
        		            </tr>
        		            <?
        					$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
        		            foreach($stripe_arr as $body_id=>$body_data)
        		            {
        						foreach($body_data as $color_id=>$color_val)
        						{
        							$rowspan=count($color_val['stripe_color']);
        							$composition=$stripe_arr2[$body_id][$color_id]['composition'];
        							$construction=$stripe_arr2[$body_id][$color_id]['construction'];
        							$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
        							$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
        							$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
									$totfidder=$stripe_arr2[$body_id][$color_id]['totfidder'];

        							if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
        							else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";

        							$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
        								WHERE a.job_id=b.job_id and
        								a.id=b.pre_cost_fabric_cost_dtls_id and
        								c.job_no_mst=a.job_no and
        								c.id=b.color_size_table_id and
        								b.po_break_down_id=d.po_break_down_id and
        								b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
        								d.booking_mst_id =$booking_id and
        								a.body_part_id='".$body_id."' and
        								a.color_type_id='".$color_type_id."' and
        								a.construction='".$construction."' and
        								a.composition='".$composition."' and
        								a.gsm_weight='".$gsm_weight."' and
        								$color_cond and
        								d.status_active=1 and
        								d.is_deleted=0
        								");
        						
        								list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
        							$sk=0;
    								foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
        							{
        								
        								?>
	        							<tr>
		        							<?
		        							if($sk==0)
		        							{


			        							$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			        							?>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?>&nbsp; </td>
			        							<?
			        							$total_fab_qty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			        							$i++;
			        						}
		        							$sk=0;
		        							

		        								$measurement=$color_val['measurement'][$strip_color_id];
		        								$uom=$color_val['uom'][$strip_color_id];
		        								$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
		        								$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
		        								
		        								?>
		        							
			        								<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
			        								<td align="right"> <? echo  number_format($measurement,2); ?> &nbsp; </td>
			        		                        <td> <? echo  $unit_of_measurement[$uom]; ?></td>
													<td align="right"> <? echo  $totfidder; ?></td>
			        								<td align="right"><? echo  number_format($fabreqtotkg,2); ?> &nbsp;</td>
			        								<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
		        								
		        								<?
		        								
		        								$sk++;
		        								$total_fabreqtotkg+=$fabreqtotkg;
		        								$stripe_color_wise[$color_name_arr[$s_color_val]]+=$fabreqtotkg;
		        							
		        							
		        							?>
	        							</tr>
	        							<?
	        						}
        						}
        					}
        					?>
	        		            <tfoot>
		        		            <tr>
			        		            <td colspan="3">Total </td>
			        		            <td align="right">  <? echo  number_format($total_fab_qty,2); ?> &nbsp;</td>
			        		            <td></td>
			        		            <td></td>
			        		            <td>   </td>
										<td>   </td>
			        		            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> &nbsp;</td>
			        		        </tr>
	        		            </tfoot>
        		            </table>
        	</td>
        	
        	<td width="20%" >
        		        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;"  >        		       

        		            <tr><td align="left" colspan="3"> Stripe  Color wise Summary</td></tr>        		       
        		            <tr>
	        		            <td width="30"> SL</td>	        		            
	        		            <td width="70"> Stripe Color</td>	        		           
	        		            <td  width="70"> Qty.(KG)</td>	        		           
        		            </tr>
        		            <?

        					$i=1;$total_stripe_qnt=0;        		            
        						foreach($stripe_color_wise as $color=>$val){
        							?>
        							<tr>
	        							<td> <? echo $i; ?></td>	        							
	        							<td > <? echo $color; ?></td>
	        							<td align="right"> <?php echo number_format($val,2); ?></td>
	        						</tr>
        							
        							<?
        							$total_stripe_qnt+=$val;
        							
        							$i++;
        						}
        					
        					?>
        		            <tfoot>
        		            <tr>
        		            
        		            <td></td>
        		            <td></td>
        		            
        		            <td align="right"><? echo  number_format($total_stripe_qnt,2); ?> </td>
        		            </tr>
        		            </tfoot>
        		            </table>
        	</td>
        </tr>
         </table >

			<br>
		<fieldset>
                   
		 <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
	            	<thead>
	                	<tr>
	                    	<th width="30">Sl</th>	                    	
	                    	<th >Special Instruction</th>
	                    	
	                    </tr>
	                </thead>
	                <tbody>
	                    <?
					
						$data_array=sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no=$txt_booking_no and entry_form = 232  order by id");
						
							$is_update=1;
							$i=1;
							foreach( $data_array as $row )
							{
								?>
	                            	<tr>
	                                    <td> <? echo $i;?></td >	   
										<td> <? echo $row[csf('terms')]; ?></td>	                                   
	                                </tr>
	                            <?
								$i++;

							}
						
						
						?>
	            	</tbody>
	            </table>
			</fieldset>
         
       
			
        
		
	
        
        <div ><? echo signature_table(1, $cbo_company_name, "1400px",'',70,$inserted_by2);  //signature_table(1, $cbo_company_name, "1400px"); //$user_name_arr[$user_id] ?></div>
		<br>
     





        <?


			$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");
			$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down  where job_no_mst ='$txt_job_no'", "id", "po_number");
			$shipment_date_arr=return_library_array( "select id,shipment_date from wo_po_break_down  where job_no_mst ='$txt_job_no'", "id", "shipment_date");
        	$grand_order_total=0; $grand_plan_total=0; $size_wise_total=array();
			$nameArray_size=sql_select( "select size_number_id,size_order from wo_po_color_size_breakdown where po_break_down_id in (".$po_id_all.") and is_deleted=0 and status_active=1 group by size_number_id,size_order order by size_order ");



			
			$booking_dtls_sql="SELECT a.id as booking_dtls_id, b.id, a.fabric_color_id, a.fin_fab_qnty, a.grey_fab_qnty, a.amount, a.rate, a.colar_cuff_per  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.pre_cost_fabric_cost_dtls_id=c.id  and b.po_break_down_id in (".$po_id_all.") and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$booking_dtls_res=sql_select($booking_dtls_sql);
			
			$booking_dtls_id_array=array(); $fabric_color_array=array(); $finish_fabric_qnty_array=array(); $grey_fabric_qnty_array=array(); $grey_fabric_amount_array=array(); $grey_fabric_rate_array=array(); $colar_cuff_percent_array=array();
	
			foreach($booking_dtls_res as $row)
			{
				$booking_dtls_id_array[$row[csf("id")]]=$row[csf("booking_dtls_id")];
				//$job_no=$row[csf("job_no")];
				$fabric_color_array[$row[csf("id")]]=$row[csf("fabric_color_id")];
				$finish_fabric_qnty_array[$row[csf("id")]]+=$row[csf("fin_fab_qnty")];
				$grey_fabric_qnty_array[$row[csf("id")]]+=$row[csf("grey_fab_qnty")];
				$grey_fabric_amount_array[$row[csf("id")]]=$row[csf("amount")];
				$grey_fabric_rate_array[$row[csf("id")]]['rate']=$row[csf("rate")];
				$grey_fabric_rate_array[$row[csf("id")]]['colar_cuff_per']=$row[csf("colar_cuff_per")];
			}
			unset($booking_dtls_res);
		

			$name_sql="select a.id as pre_cost_fabric_cost_dtls_id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id, a.fabric_source, a.color_type_id, a.gsm_weight, a.construction, a.composition, a.color_size_sensitive, a.costing_per, a.color, a.color_break_down, a.rate as rate_mst, b.id, b.po_break_down_id, b.color_size_table_id, b.color_number_id, b.gmts_sizes as size_number_id, b.dia_width, b.item_size, b.cons, b.process_loss_percent, b.requirment, b.rate, b.pcs, b.remarks FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$txt_job_no'   and a.status_active=1 and a.is_deleted=0 order by a.id,b.color_size_table_id";
			
	
						$nameArray=sql_select($name_sql);

						$po_fabric_wise_data=array();
						foreach ($nameArray as $result){

								if($finish_fabric_qnty_array[$result[csf("id")]]>0  || $grey_fabric_qnty_array[$result[csf("id")]]> 0  )
								{

									$ship_date=change_date_format($shipment_date_arr[$result[csf('po_break_down_id')]]);

									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['finish_kg']+=$finish_fabric_qnty_array[$result[csf("id")]];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['grey_kg']+=$grey_fabric_qnty_array[$result[csf("id")]];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['process_loss']=$result[csf('process_loss_percent')];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['color_size_sensitive']=$result[csf('color_size_sensitive')];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['id']=$result[csf('id')];
							
								}
						}

			// echo "<pre>";
			// print_r($po_fabric_wise_data);


			?>
                <div id="div_size_color_matrix" class="pagebreak">
                    <fieldset id="div_size_color_matrix" >
                        <legend>PO & Fabric Color wise fabric Required Quantity</legend>
                        <table  class="rpt_table" style="float:left;"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<td>PO Number</td>
                            
                            	<td>Ship Date</td>
								<td>fabric color</td>
								<td>Body Color</td>                           	
                                <td  align="center"> Total Finish Fabric(kg)</td>
                                <td  align="center"> Total Grey Fabric(kg)</td>
                                <td  align="center"> Process Loss</td>
                            </tr>
                            <?
                           $fab_fin_color_tot=array();   $fab_grey_color_tot=array();   $fab_fin_po_tot=array();   $fab_grey_po_tot=array();

							foreach ($po_fabric_wise_data as $po_id=>$shipdate_data){
								foreach ($shipdate_data as $date_id=>$color_data) {
									foreach ($color_data as  $color_id=>$result){

								?>
								<tr>
									<td title="<?=$po_id;?>"><?php echo $po_number_arr[$po_id]; ?></td>									
									<td><?php echo $date_id; ?></td>									
									<td><? if($result["color_size_sensitive"]!=0) echo $color_library[$color_id]; ?></td>									
									<td><?php
									
									$type=1;
									$color_id="";
									if($type==1)
									{
										echo $color_library[$fabric_color_array[$result["id"]]];
										$color_id=$fabric_color_array[$result["id"]];
									}
									else if($type==2)
									{
										if($result["color_size_sensitive"]==3)
										{
											echo $constrast_color_arr[$result["color_number_id"]]; $color_id=$contrastcolor_arr[$result["job_no"]][$result["pre_cost_fabric_cost_dtls_id"]][$result["color_number_id"]];
										}
										else if($result["color_size_sensitive"]==0)
										{
											echo $color_library[$result["color"]]; $color_id=$result["color"];
										}
										else
										{
											echo $color_library[$result["color_number_id"]]; $color_id=$result["color_number_id"];
										}
									}
									 ?></td>
									<?

                                	$grand_fabric_total+=$result["finish_kg"];
        							$grand_grey_total+=$result["grey_kg"];

        			

                                	?>
                                	<td align="center"><?php echo number_format($result["finish_kg"],2); ?></td>
                                	<td align="center"><?php echo number_format($result["grey_kg"],2); ?></td>
                                	<td align="center"><?php echo number_format($result['process_loss']); ?></td>
								</tr>
								<?
					
								$fab_fin_po_tot[$po_id]+=$result["finish_kg"];
								$fab_grey_po_tot[$po_id]+=$result["grey_kg"];
								$color_wise_arr[$color_id]['finish_kg']+=$result["finish_kg"];
								$color_wise_arr[$color_id]['grey_kg']+=$result["grey_kg"];


								?>
							
						   	<?
							     }

								
						      }
							  ?>
								<tr>
									<td align="right" colspan="4"><b>Po wise Total</b></td>									
									<td align="center"><strong><?=number_format($fab_fin_po_tot[$po_id],2)?></strong></td>                                
									<td align="center"><strong><?=number_format($fab_grey_po_tot[$po_id],2)?></strong></td>
									<td></td>
								</tr>
						   	<?
							}
                            ?>
                            <tr>
                            	<td align="right" colspan="4"><b>Grand Total</b></td>                            	
                                <td align="center"><strong><?=number_format($grand_fabric_total,2)?></strong></td>                                
                                <td align="center"><strong><?=number_format($grand_grey_total,2)?></strong></td>
								<td></td>
                            </tr>
                        </table>
						<table  class="rpt_table"  style="float:left;margin-left:5px;" border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                                <td colspan="4" align="center">Color wise Summary</td>                               
                            </tr>
                            <tr>
                                <td>Sl</td>
                                <td>Color Name</td>
                                <td>Finish Fabric(kg)</td>
                                <td>Grey Fabric(kg)</td>
                            </tr>
                            <?php
                            $sl=1;
                                foreach($color_wise_arr as $cid=> $val){
                            ?>
                            <tr>
                                <td width="30"><?=$sl;?></td>
                                <td width="100"><?=$color_library[$cid];?></td>
                                <td width="100" align="right"><?=number_format($val['finish_kg'],2);?></td>
                                <td width="100" align="right"><?=number_format($val['grey_kg'],2);?></td>
                            </tr>
                            <?$sl++;
                        
                                    $grand_fabric_tot+=$val['finish_kg'];
        							$grand_grey_tot+=$val['grey_kg'];
                        }?>
                            <tr>
                              
                                <td colspan="2">Total</td>
                                <td align="right"><?=number_format($grand_fabric_tot,2);?></td>
                                <td align="right"><?=number_format($grand_grey_tot,2);?></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
			<?

			$actule_po_size=sql_select("select gmts_size_id from wo_po_acc_po_info where po_break_down_id in  (".$po_id_all.") and is_deleted=0 and status_active=1 group by gmts_size_id ");
			$actule_po_data=sql_select( "SELECT a.id as po_id,a.po_number, b.acc_po_no, a.po_received_date, b.acc_ship_date, b.gmts_color_id, b.gmts_size_id, b.acc_po_qty, b.id as actule_po_id , b.gmts_item from wo_po_break_down a join wo_po_acc_po_info b on a.id=b.PO_BREAK_DOWN_ID where b.po_break_down_id in (".$po_id_all.") and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1");
			$actule_po_arr=array();
			$attribute=array('po_number','acc_po_no','po_received_date','acc_ship_date','gmts_color_id','gmts_size_id','acc_po_qty','gmts_item');
			foreach ($actule_po_data as $row) {
				foreach ($attribute as $attr) {
					$actule_po_arr[$row[csf('po_id')]][$row[csf('actule_po_id')]][$attr]=$row[csf($attr)];
				}
				$actual_color_size[$row[csf('po_id')]][$row[csf('actule_po_id')]][$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]] =$row[csf('acc_po_qty')];				
			}


		
		?>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
	*/		
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}


if($action=="show_trim_booking_report1")//Shariar
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$booking_id=str_replace("'","",$booking_mst_id);
	
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id,user_name from user_passwd  where status_active=1 ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	$designation_arr=return_library_array( "select id,designation from   lib_supplier  where status_active=1 and is_deleted=0",'id','designation');
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_no');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.id=$booking_id and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		
		// echo "<pre>";
		// print_r();

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent,a.currency_id, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.id=$booking_id");


		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$job_no_str=$nameArray[0][csf('job_no')];
		$tagged_booking_no=$nameArray[0][csf('tagged_booking_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$product_code=$nameArray[0][csf('product_code')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		$jobqtypcs=$nameArray[0][csf('jobqtypcs')];
		$inserted_by2=$user_name_arr[$nameArray[0][csf('inserted_by')]];
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$pay_mode=$nameArray[0][csf('pay_mode')];
		$style_ref_no=$nameArray[0][csf('style_ref_no')];
		$team_leader=$team_leader_arr[$nameArray[0][csf('team_leader')]];
		$style_description=$nameArray[0][csf('style_description')];
		$currency_id=$nameArray[0][csf('currency_id')];
		$remarks=$nameArray[0][csf('remarks')];
		$attention=$nameArray[0][csf('attention')];
		$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];
		if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}

		$job_no_str=$nameArray[0][csf('job_no')];
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$job_no_str'");

	

		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Service Booking For Dyeing </font></strong></b></span> 
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
							<!-- <?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?> -->
							  
                            </td>
							<td><strong style="background-color:yellow;padding:5%;font-size: 30px;"><?=str_replace("'","",$txt_booking_no);;?></strong><br><strong style="margin-left:20%;"><?=str_replace("'","",$txt_booking_date);;?></strong></td> 
							
                        </tr>
						
						
                    </table>
					
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="100"><b>Service Provider </b></td>		 
					<td width="140"> <span style="font-size:18px"><?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$result[csf('supplier_id')]];
						}
						else{
						echo $supplier_name_arr[$result[csf('supplier_id')]];
						}
					?></span> </td>
					<td width="100"><span style="font-size:18px"><b>Address</b></span></td>
					<td width="110"><span style="font-size:18px"><?

					$supplier_id=$result[csf('supplier_id')];
				
					if($pay_mode==5 || $pay_mode==3){
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$supplier_id");
						foreach ($nameArray as $result)
						{
							$company_address= "Plot No:".$result[csf('plot_no')].",Level No:".$result[csf('level_no')].",Road No:".$result[csf('road_no')].",Block No:".$result[csf('block_no')].",City No:".$result[csf('city')].",Zip Code:".$result[csf('zip_code')].",Province No:".$result[csf('province')].",Country:".$country_arr[$result[csf('country_id')]]; 
						}
						echo $company_address;
						}
						else{
						echo $supplier_address_arr[$result[csf('supplier_id')]];
						}
					
					?> </span> </td>
					<td width="110"><b>Attention</b></td>
					<td width="100"><? echo $attention; ?></td>
				
				</tr>
				<tr>
					<td width="100"><b>Job No</b></td>		 
					<td width="140"> <span style="font-size:18px"><? echo trim($txt_job_no,"'");if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="100"><span style="font-size:18px"><b>Fabric Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><?=str_replace("'","",$tagged_booking_no);;?> </span> </td>
					<td width="100"><span style="font-size:18px"><b>Designation</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <? echo $designation_arr[$result[csf('supplier_id')]]; ?></span></td>	
				</tr>
				<tr>		
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					<td width="100"><span style="font-size:18px"><b>Currency</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $currency[$currency_id]; ?></span></td>
					<td width="100"><b>Contact No</b></td>
					<td width="140"><? echo $contact_no_arr[$result[csf('supplier_id')]]; ?> </td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo $style_ref_no; ?></td>				
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="110"><b>Delivery Date</b></td>
					<td width="100"><? echo change_date_format($delivery_date); ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Remarks</b></span></td>
					<td width="350" ><span style="font-size:18px"><? echo $remarks;?></span></td>			
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350"><span style="font-size:18px"><? echo $style_description; ?></span></td>
					<td width="110"><b>Responsible Merchandiser</b></td>
					<td width="100"><? echo $inserted_by2; ?></td>
				</tr>
			</table>
			<br>
			
			<?
		}	
			
	  	?>
		
		<br>
		<h5 style="color:red;"></h5>
		<br>


		<?php
		$fabric_desc_arr=array();

		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$txt_job_no'");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				


				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
				
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				
			
				$fabric_description=sql_select("select id,body_part_id,body_part_type,color_type_id,fabric_description,construction,composition,gsm_weight,width_dia_type from  wo_pre_cost_fabric_cost_dtls 
				where  job_no='$txt_job_no'");

				foreach( $fabric_description as $fabric_description_row)
				{
				

				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_id']=$fabric_description_row[csf("body_part_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['construction']=$fabric_description_row[csf("construction")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['composition']=$fabric_description_row[csf("composition")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['color_type_id']=$fabric_description_row[csf("color_type_id")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['gsm_weight']=$fabric_description_row[csf("gsm_weight")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['width_dia_type']=$fabric_description_row[csf("width_dia_type")];
				$fabric_desc_arr[$fabric_description_row[csf("id")]]['body_part_type']=$fabric_description_row[csf("body_part_type")];
		
				}
				
			}


		}

			// echo "<pre>";
			// print_r($fabric_desc_arr);



		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_no='$txt_job_no'  and po_break_down_id in (".$po_id_all.")");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}

		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,a.style_ref_no,d.po_number,d.id,
		sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id,b.delivery_date,b.delivery_end_date,b.labdip_no	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b,wo_po_details_master a,wo_po_break_down d where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.job_no=a.job_no and b.po_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 	and b.wo_qnty>0	and b.booking_type=3 and b.job_no='$txt_job_no' and b.process=31 and b.booking_mst_id =$booking_id 	group by b.job_no,c.id,c.charge_unit,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description,b.delivery_date,b.delivery_end_date,a.style_ref_no, b.po_break_down_id,d.po_number,d.id,b.labdip_no ");
		
		

	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			$body_part_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_id'];
			$body_part_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['body_part_type'];
			$construction=$fabric_desc_arr[$row[csf("fab_desc_id")]]['construction'];
			$composition=$fabric_desc_arr[$row[csf("fab_desc_id")]]['composition'];
			$color_type_id=$fabric_desc_arr[$row[csf("fab_desc_id")]]['color_type_id'];
			$gsm_weight=$fabric_desc_arr[$row[csf("fab_desc_id")]]['gsm_weight'];
			$width_dia_type=$fabric_desc_arr[$row[csf("fab_desc_id")]]['width_dia_type'];
			if($body_part_type==40 || $body_part_type==50){
				$body_part_type_arr[$body_part_type]=$body_part_type;
			}


			$finsh_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['finsh_cons'];
			$gray_cons=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['gray_cons'];
			$dia_width=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['dia_width'];
			$process_loss_percent=$color_wise_data[$row[csf("po_id")]][$row[csf("fab_desc_id")]][$row[csf("gmts_color_id")]]['process_loss_percent'];
		
	
			$grouping_item=$row[csf('fabric_color_id')].'*'.$color_type_id.'*'.$row[csf('po_id')].'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$dia_width;	
				$pp=100+$process_loss_percent;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['style_ref_no'] = $row[csf('style_ref_no')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['po_id'] = $row[csf('po_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['po_number'] = $row[csf('po_number')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['labdip_no'] = $row[csf('labdip_no')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['body_part_id'] = $body_part_id;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gsm'] = $gsm_weight;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['color_type_id'] = $color_type_id;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['finsh_cons'] = $finsh_cons;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['gray_cons'] = $gray_cons;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['fin_fab_qnty'] =($row[csf('wo_qnty')]/$pp)*100;
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['grey_fab_qnty'] = $row[csf('wo_qnty')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['amount'] = $row[csf('amount')];
			$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;

			if($row[csf('delivery_date')]){
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_start_date'] = $row[csf('delivery_date')];
			}
			if($row[csf('delivery_end_date')]){			
				$fabric_data_arr[$row[csf('labdip_no')]][$grouping_item]['delivery_end_date'] = $row[csf('delivery_end_date')];
			}
			
	
		}

		$body_part_type_ids=implode(",",$body_part_type_arr);
	
		?>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>	
			 	 <th>Lab Dip No</th>
				 <th>Gmts Colors</th>
				 <th>Fabric Color</th>	
				 <th>Style Ref</th>		
				 <th>Order No</th>	
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Finish  Qty</th>
				 <th>Grey Qty</th>
				 <th>Rate</th>
				 <th>Amount</th>

			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $lab_id=>$fabric_data_arr) {  
			 $i=1;     		 	
			 $sub_fin_fab_qnty=0;   		 	
			 $sub_grey_fab_qnty=0;
			 $sub_amount=0;
				 foreach ($fabric_data_arr as $fabric_id => $value) {	
					$sub_fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
					$sub_grey_fab_qnty+=$value['grey_fab_qnty'];  
					$sub_amount+=$value['amount'];
					
							 	
						  if($i==1){
							
						   ?>
						  <tr>
							 <td rowspan="<? echo count($fabric_data_arr) ?>"><? echo  $lab_id;?></td>
							 <td><? echo $color_library[$value['gmts_color_id']] ?></td>	
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>	
							 <td><? echo $value['style_ref_no'] ?></td>		
							 <td><? echo $value['po_number'] ?></td>										
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td><? echo $color_type[$value['color_type_id']] ?></td>
							 <td align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['amount']/$value['grey_fab_qnty'],2); ?></td>
							 <td align="right"><? echo fn_number_format($value['amount'],2) ; ?></td>

						 </tr>
						  <? } 
						  else {
							?>
							  <tr>
							 <td><? echo $color_library[$value['gmts_color_id']] ?></td>	
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>	
							 <td><? echo $value['style_ref_no'] ?></td>		
							 <td><? echo $value['po_number'] ?></td>			
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td><? echo $color_type[$value['color_type_id']] ?></td>
							 <td align="right"><? echo fn_number_format($value['fin_fab_qnty'],2) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['grey_fab_qnty'],2) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['amount']/$value['grey_fab_qnty'],2); ?></td>
							 <td align="right"><? echo fn_number_format($value['amount'],2) ; ?></td>
							 </tr>
						  <? }
						  $i++;
				 }
				 ?>
				 <tr>
				 <th align="right" colspan="10"> Sub Total</th>
				 <th align="right"><?echo number_format($sub_fin_fab_qnty,2);  ?></th>
				 <th align="right"><?echo number_format($sub_grey_fab_qnty,2);  ?></th>
				 <th></th>
				 <th align="right"><?echo number_format($sub_amount,2);  ?></th>
				 </tr>
				 <?
				 	$fin_fab_qnty+=	$sub_fin_fab_qnty;   		 	
					$grey_fab_qnty+=$sub_grey_fab_qnty;
					$amount+=$sub_amount;
					?>
				 
			 <?
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="10"> Grand Total</th>
				 <th align="right"><?echo number_format($fin_fab_qnty,2);  ?></th>
				 <th align="right"><?echo number_format($grey_fab_qnty,2);  ?></th>
				 <th></th>
				 <th align="right"><?echo number_format($amount,2);  ?></th>
			</tr>
			<tr>
				<th colspan="14" align="left">Grand Total Booking Amount (in word):<? echo number_to_words(def_number_format($amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		  <br/>



      	<!--  Here will be the main portion  -->
		<?
        $costing_per=""; $costing_per_qnty=0;
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$txt_job_no'");
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




      
		
		?>
        <br/>
        

       		
        <br/>
        
		<fieldset>
                   
		 <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
	            	<thead>
	                	<tr>
	                    	<th width="30">Sl</th>	                    	
	                    	<th >Special Instruction</th>
	                    	
	                    </tr>
	                </thead>
	                <tbody>
	                    <?
					
						$data_array=sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no=$txt_booking_no and entry_form = 232  order by id");
						
							$is_update=1;
							$i=1;
							foreach( $data_array as $row )
							{
								?>
	                            	<tr>
	                                    <td> <? echo $i;?></td >	   
										<td> <? echo $row[csf('terms')]; ?></td>	                                   
	                                </tr>
	                            <?
								$i++;

							}
						
						
						?>
	            	</tbody>
	            </table>
			</fieldset>       
        <div ><? echo signature_table(1, $cbo_company_name, "1400px",'',70,$inserted_by2);  //signature_table(1, $cbo_company_name, "1400px"); //$user_name_arr[$user_id] ?></div>
		<br>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
	*/		
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}



?>