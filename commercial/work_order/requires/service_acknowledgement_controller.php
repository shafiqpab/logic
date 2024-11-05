<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Service Acknowledgement				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	04-09-2022
Updated by 		:	
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
	
$menuArr=array(1=>"Job No",2=>"Order No",3=>"Gmts Item",4=>"Embl Name",5=>"Embl Type",6=>"Body Part",7=>"UOM",8=>"WOQ",9=>"Ackn. Qty",10=>"Rate",11=>"Amount",12=>"Remarks",13=>"Test Item",14=>"Test For",15=>"Test Category",16=>"Color",17=>"Vat %",18=>"Total Amount",19=>"Fabric Description",20=>"GSM",21=>"Dia",22=>"Fabric Color",23=>"Service For",24=>"Service Details",25=>"Item Description",26=>"Item Category",27=>"Item Group",28=>"Service Number",31=>"Ackn Qnty Bal",32=>"Cum. Ackn");

$menuFieldArr=array(1=>"txt_job_no_",2=>"txt_order_no_",3=>"txt_gmts_item_",4=>"txt_emb_name_",5=>"txt_emb_type_",6=>"txt_body_part_",7=>"uom_",8=>"txt_wo_qnty_",9=>"txt_ackn_qnty_",10=>"txt_rate_",11=>"txt_amount_",12=>"txt_remarks_",13=>"txt_test_item_",14=>"txt_test_for_",15=>"txt_test_category_",16=>"txt_color_",17=>"txt_vat_amount_",18=>"txt_tot_amount_",19=>"txt_fab_description_",20=>"txt_gsm_",21=>"txt_dia_",22=>"txt_fab_color_",23=>"txt_service_for_",24=>"txt_service_details_",25=>"txt_item_description_",26=>"txt_item_category_",27=>"txt_item_group_",28=>"txt_service_number_",31=>"txt_woq_qnty_bal_",32=>"txt_cum_qnty_");	

$hidField=array(2=>"order_id_",3=>"gmts_item_id_",4=>"emb_name_id_",5=>"emb_type_id_",6=>"body_part_id_",7=>"uom_id_",13=>"test_item_id_",14=>"test_for_id_",15=>"test_category_id_",16=>"color_id_",22=>"fab_color_id_",23=>"service_for_id_",24=>"service_lib_id_",26=>"item_category_id_",27=>"item_group_id_",29=>"update_dtls_id_",30=>"wo_dtls_id_");


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
	//echo "dsdsd";
	
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_service_company",100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_acknowledgement_controller');",0,"" );
	}
	else{		
	   echo create_drop_down( "cbo_service_company",120, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and c.id=$data  and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0  group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_acknowledgement_controller');","");

	}
	
	exit();
}



if ($action=="work_order_embellishment_popup")
{
    echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	?>
	<script>
	function js_set_value(id)
	{
		var value=trim(id).split('_');
		document.getElementById('selected_booking').value=value[1];
		document.getElementById('selected_booking_id').value=value[0];
		document.getElementById('hidd_supplier_id').value=value[2];
		document.getElementById('hidd_exchange_rate').value=value[3];
		
		parent.emailwindow.hide();
	}
	function check_orphan( str )
	{
		if($("#chk_orphan").prop('checked')==true) $('#chk_orphan').val(1); else $('#chk_orphan').val(0);
	}
    </script>
 </head>
 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            	<table width="980" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        	<th colspan="8">
                              <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" );?>
                            </th>
                    </thead>
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                        <th width="100">Job No</th>
                        <th width="100">Internal Ref. No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th> <input type="checkbox" id="chk_orphan" onClick="check_orphan(this.value)" value="0"> Orphan WO</th>
                    </thead>
        			<tr class="general">
                        <td> <input type="hidden" id="selected_booking"><input type="hidden" id="selected_booking_id"><input type="hidden" id="hidd_supplier_id"><input type="hidden" id="hidd_exchange_rate">
                        <?
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3)  order by company_name","id,company_name",1, "-- Select Company --", "$cbo_company_name", "load_drop_down( 'service_acknowledgement_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                        </td>

                        <td id="buyer_td">
                        <?
                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                        ?>
                        </td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td ><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px"></td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"></td>
                        <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_work_order_search_list_view', 'search_div', 'service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
        			</tr>
                    <tr>
                        <td  align="center" colspan="8" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
             </table>
          <div id="search_div"></div>
    </form>
   </div>
 </body>
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}
if ($action=="create_work_order_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
		{
		 if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($data[6]==1)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		}
   if($data[6]==2)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[6]==3)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	if(trim($data[9])!="") $ref_cond=" and d.grouping like '%$data[9]'"; else $ref_cond="";
	if(trim($data[8])!="") $job_cond=" and a.job_no like '%$data[8]%'"; else $job_cond="";
	


	// $sql="SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.pay_mode,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty as ec_qnty, e.wo_qnty from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c,wo_po_break_down d, wo_service_acknowledgement_dtls e
	// where $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id and b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and d.id=e.po_break_down_id and a.booking_type=6 and a.entry_form in (201,199) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $ref_cond  group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,
	// a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty,e.wo_qnty order by a.id DESC ";

	// $sql="SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.pay_mode,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty as ec_qnty  
	// from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c,wo_po_break_down d 
	// where $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id and b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id   and a.booking_type=6 and a.entry_form in (201,199,574) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $ref_cond  group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,
	// a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty  order by a.id DESC ";

	$sql="SELECT a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.pay_mode,a.job_no,B.ID as BOOKING_DETAILS_ID,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty as ec_qnty 
	from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c,wo_po_break_down d
	where $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id and b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.entry_form in (201,199,574) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $ref_cond  group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.id,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping,a.entry_form,a.exchange_rate,b.wo_qnty order by a.id DESC ";
	//echo $sql;
	//echo $data[7].'='.$sql;
	$sql_res=sql_select($sql);
	foreach($sql_res as $row)
	{
		$booking_id .= $row['BOOKING_DETAILS_ID'].',';
	}
	$all_booking_id = ltrim(implode(",", array_unique(explode(",", chop($booking_id, ",")))), ',');

	$ack_sql = "SELECT A.ID AS MST_ID,A.SYSTEM_NO_PREFIX,A.SYSTEM_PREFIX_NUM,B.WO_QNTY,B.ACKN_QTY,B.WO_BOOKING_DTLS_ID
	FROM WO_SERVICE_ACKNOWLEDGEMENT_MST A,WO_SERVICE_ACKNOWLEDGEMENT_DTLS B WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.ID=B.MST_ID AND B.WO_BOOKING_DTLS_ID in ($all_booking_id) ";
	//echo $ack_sql;

	$ack_sql_data=sql_select($ack_sql);	
	foreach($ack_sql_data as $row){
		$ackg_booking_data_arr[$row['WO_BOOKING_DTLS_ID']]['ackn_qty']+=$row['ACKN_QTY'];
	}


	// foreach($sql_res as $row)
	// {
	// 	if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
	// 	{
	// 		$supp_com_arr[$row[csf('booking_no')]]=$comp[$row[csf('supplier_id')]];
	// 	}
	// 	else
	// 	{
	// 		$supp_com_arr[$row[csf('booking_no')]]=$suplier[$row[csf('supplier_id')]];
	// 	}
		
	// }
	//  $arr=array (2=>$buyer_arr,3=>$po_num,5=>$po_array,6=>$garments_item,7=>$emblishment_name_array,8=>$supp_com_arr,9=>$approved,10=>$is_ready);
	//   echo  create_list_view("list_view", "Booking No,Booking Date,Buyer,Job No.,Ref. No,PO number,Gmts Item,Embl Name,Supplier,Approved,Is-Ready", "60,75,70,70,60,100,110,110,80,110,50","970","320",0, $sql , "js_set_value", "id,booking_no,supplier_id,exchange_rate", "", 1, "0,0,buyer_id,job_no,0,po_break_down_id,gmt_item,emb_name,booking_no,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,buyer_id,job_no,grouping,po_break_down_id,gmt_item,emb_name,booking_no,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0','','');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	// echo "<pre>";
	// print_r($po_arr);die;
	 ?>
       
	   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">
            <thead>
                <th width="50">SL No</th>
                <th width="60">Booking No</th>
                <th width="75">Booking Date</th>
                <th width="70">Buyer</th>
                <th width="70">Job No.</th>
                <th width="60">Ref. No</th>
                <th width="100">PO number</th>
                <th width="110">Gmts Item</th>
                <th width="110">Embl Name</th>
                <th width="80">Supplier</th>
                <th width="80">Approved</th>
                <th width="50">Is-Ready</th>
            </thead>
         </table>
            <div style="width:960px; max-height:320px; overflow-y:scroll">
              <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" id="list_view">
				<? 
				$i=1;
				foreach ($sql_res as $row)
				{
					if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
					{
						$supp_com_arr[$row[csf('booking_no')]]=$comp[$row[csf('supplier_id')]];
					}
					else
					{
						$supp_com_arr[$row[csf('booking_no')]]=$suplier[$row[csf('supplier_id')]];
					}
					//$qty_total=number_format($row[csf("wo_qnty")],4)- number_format($row[csf("ec_qnty")],4);
					$qty_total=number_format($row[csf("ec_qnty")],4)- number_format($ackg_booking_data_arr[$row['BOOKING_DETAILS_ID']]['ackn_qty'],4);

					//  echo $i."#".$qty_total."_".$row[csf("wo_qnty")]."__".$row[csf("ec_qnty")]."__".$row[csf('booking_no_prefix_num')]."______";
					if($qty_total>0){

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]."_".$row[csf('supplier_id')]."_".$row[csf('exchange_rate')]; ?>')"> 											
								<td align="center" width="50"><? echo "$i"; ?> </td>	
								<td align="center" width="60"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
								<td align="center" width="75"><p><? echo change_date_format($row[csf('booking_date')]);?></p></td>
								<td align="center" width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]];?></p></td>
								<td align="center" width="70"><p><? echo $row[csf('job_no')];?></p></td>
								<td align="center" width="60"><p><? echo $row[csf('grouping')];?></p></td>
								<td align="center" width="100"><p><? echo $po_arr[$row[csf('po_break_down_id')]];?></p></td>
								<td align="center" width="110"><p><? echo $garments_item[$row[csf('gmt_item')]]; ?></p></td> 
								<td align="center" width="110" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?>&nbsp;</p></td>
								<td align="center" width="80"><p><? echo $supp_com_arr[$row[csf('booking_no')]]; ?></p></td>
								<td width="80" align="center"><p><? echo $approved[$row[csf('is_approved')]]; ?></p></td>
								<td width="50" align="center"><p><? echo $approved[$row[csf('ready_to_approved')]]; ?></p></td>
							</tr>
							<?
							$i++; 
						}    
				    }
				?>
                </table>
			</div>
			
	 <?
	
	 exit();
}
if ($action=="work_order_lab_test_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
 ?>
  <script>
	
	function js_set_value(id)
	{
		
		var value=trim(id).split('_');
		document.getElementById('selected_booking').value=value[1];
		document.getElementById('selected_booking_id').value=value[0];
		document.getElementById('hidd_supplier_id').value=value[2];
		document.getElementById('hidd_exchange_rate').value=value[3];
		parent.emailwindow.hide();
	}

	function set_checkvalue()
	{
		$('input[type="checkbox"]').change(function(){
		    this.value = (Number(this.checked));
		});
	}
 </script>
 </head>
 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            	<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <th colspan="9">
                        	<?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </thead>
                    <thead>
                        <th width="130">Company Name</th>
                        <th width="130">Test Company</th>
                        <th width="80">WO No</th>
                        <th width="120">Buyer</th>
                        <th width="80">Style Ref.</th>
                        <th width="80">Job No</th>
                        <th width="130" colspan="2">WO Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">Orphan</th>
                    </thead>
        			<tr class="general">
                    	<td> <input type="hidden" id="selected_booking"><input type="hidden" id="selected_booking_id">
						<input type="hidden" id="hidd_supplier_id"><input type="hidden" id="hidd_exchange_rate">
						<?=create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $cbo_company_name, "load_drop_down( 'service_acknowledgement_controller', this.value, 'load_drop_down_buyerpop', 'buyer_td');"); ?></td>
                   		<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "--Test Company--", 0, "","" ); ?></td>
                     	<td><input name="txt_wo_prifix" id="txt_wo_prifix" class="text_boxes" style="width:70px" ></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Buyer--", 0, "","" ); ?></td>
                        <td><input name="txt_styleref" id="txt_styleref" class="text_boxes" style="width:70px" ></td>
                        <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" ></td>
                    	<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
					 	<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
            		 	<td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_wo_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_styleref').value+'_'+document.getElementById('txt_job_no').value, 'create_lab_test_search_list_view', 'search_div', 'service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
                <tr>
                    <td colspan="9" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
                </tr>
             </table>
          <div id="search_div"></div>
    </form>
   </div>
 </body>
 <script></script>
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}

if ($action=="create_lab_test_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	if ($data[0]!=0) $company="  and a.company_id='$data[0]'"; else { echo "Please Select Company First."; disconnect($con);die; }
	if ($data[1]!=0) $supplierCond=" and a.supplier_id='$data[1]'"; else $supplierCond="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$year_id=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_id=" to_char(a.insert_date,'YYYY') as year";
	}
	
	if($data[8]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyerCond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyerCond="";
		}
		else $buyerCond="";
	}
	else $buyerCond=" and c.buyer_name=$data[8]";
	if (str_replace("'","",$data[10])!="") $jobCond=" and c.job_no_prefix_num='$data[10]' "; else $jobCond="";

	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '%$data[5]%' "; else $booking_cond="";
		if (str_replace("'","",$data[9])!="") $styleRefCond=" and c.style_ref_no like '%$data[9]%' "; else $styleRefCond="";
	}
    if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num ='$data[5]' "; else $booking_cond="";
		if (str_replace("'","",$data[9])!="") $styleRefCond=" and c.style_ref_no='$data[9]' "; else $styleRefCond="";
	}
   	if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '$data[5]%'  "; else $booking_cond="";
		if (str_replace("'","",$data[9])!="") $styleRefCond=" and c.style_ref_no like '$data[9]%' "; else $styleRefCond="";
	}
	if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '%$data[5]' "; else $booking_cond="";
		if (str_replace("'","",$data[9])!="") $styleRefCond=" and c.style_ref_no like '%$data[9]' "; else $styleRefCond="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$suplier=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name",'id','supplier_name');
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");	
	//$arr=array (2=>$comp,2=>$suplier,3=>$currency,7=>$pay_mode,9=>$approved);

	if($data[7]==1)
	{
		if($db_type==2)
		{
			$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id, (select listagg(cast(b.job_no as varchar2(4000)),',') within group (order by b.id ) as job_no from wo_labtest_dtls b where a.id=b.mst_id AND b.is_deleted = 0 and b.status_active=1) as job_no from wo_labtest_mst a where a.status_active=1 $company $supplierCond $booking_date $booking_cond $booking_year_cond  and a.is_deleted=0 and a.entry_form=79 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id DESC";
		}
		else 
		{
			$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date,
			a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id, (select listagg (CAST (b.job_no AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY b.id) AS job_no  from wo_labtest_dtls b where a.id=b.mst_id AND b.is_deleted =0 and b.status_active=1) as job_no from wo_labtest_mst a where a.status_active=1 $company $supplierCond $booking_date $booking_cond $booking_year_cond and  a.is_deleted=0 and a.entry_form=79 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id DESC";
		}
	}
	else
	{
		if($db_type==2)
		{
			$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id, listagg(cast(b.job_no as varchar2(4000)),',') within group (order by b.id ) as job_no, listagg(cast(c.buyer_name as varchar2(4000)),',') within group (order by c.buyer_name ) as buyer_name, listagg(cast(c.style_ref_no as varchar2(4000)),',') within group (order by c.style_ref_no) as style_ref_no from wo_labtest_mst a, wo_labtest_dtls b, wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.status_active=1 $company $supplierCond $booking_date $booking_cond $booking_year_cond $buyerCond $styleRefCond $jobCond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=79 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id DESC";
		}
		else 
		{
			$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id, group_concat(b.job_no) as job_no, group_concat(c.buyer_name) as buyer_name, group_concat(c.style_ref_no) as style_ref_no from wo_labtest_mst a, wo_labtest_dtls b, wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.status_active=1 $company $supplierCond $booking_date $booking_cond $booking_year_cond $buyerCond $styleRefCond $jobCond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=79 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id DESC";
		}
	}
	//echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1050" >
        <thead>
            <th width="30">SL</th>
            <th width="50">Wo No</th>
            <th width="50">Wo Year</th>
            <th width="70">Wo Date</th>
            <th width="70">Delivery Date</th>
            <th width="100">Job No</th>
            <th width="100">Test Company</th>
            <th width="60">Currency</th>
            <th width="40">Ex. Rate</th>
            <th width="70">Pay Mode</th>
            <th width="120">Attention</th>
            <th width="60">Ready To Approved</th>
            <th width="100">Buyer</th>
            <th>Style Ref.</th>
        </thead>
    </table>
    <div style="width:1050px; max-height:300px;overflow-y:scroll;" >  
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="list_view">
            <tbody>
                <? 
                $i=1;
                foreach($data_array as $row)
                {  
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$jobNo=implode(",",array_unique(explode(",",$row[csf('job_no')])));
					
					$buyerName="";
					$exbuyer=array_unique(explode(",",$row[csf('buyer_name')]));
					foreach($exbuyer as $bid)
					{
						if($buyerName=="") $buyerName=$buyer_arr[$bid]; else $buyerName.=', '.$buyer_arr[$bid];
					}
					$styleRef=implode(",",array_unique(explode(",",$row[csf('style_ref_no')])));
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row[csf('id')].'_'.$row[csf('labtest_no')].'_'.$row[csf('supplier_id')].'_'.$row[csf('ecchange_rate')]; ?>');" style="cursor:pointer" >
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="50" align="center" style="word-break:break-all"><?=$row[csf('labtest_prefix_num')]; ?></td>
                        <td width="50" align="center" style="word-break:break-all"><?=$row[csf('year')]; ?></td>
                        <td width="70" style="word-break:break-all"><?=change_date_format($row[csf('wo_date')]); ?></td>
                        <td width="70" style="word-break:break-all"><?=change_date_format($row[csf('delivery_date')]); ?></td>
                        <td width="100" style="word-break:break-all"><?=$jobNo; ?></td>
                        <td width="100" style="word-break:break-all"><?=$suplier[$row[csf('supplier_id')]]; ?></td>
                        <td width="60" style="word-break:break-all"><?=$currency[$row[csf('currency')]]; ?></td>
                        <td width="40" align="center" style="word-break:break-all"><?=$row[csf('ecchange_rate')]; ?></td>
                        <td width="70" style="word-break:break-all"><?=$pay_mode[$row[csf('pay_mode')]]; ?></td>
                        <td width="120" style="word-break:break-all"><?=$row[csf('attention')]; ?></td>
                        <td width="60" style="word-break:break-all"><?=$approved[$row[csf('ready_to_approved')]]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$buyerName; ?></td>
                        <td style="word-break:break-all"><?=$styleRef; ?></td>
                    </tr>
                    <? 
                    $i++; 
                }
                ?>
            </tbody>
        </table>
    </div>
    <?
	
	//
	
	//echo create_list_view("list_view", "WO No,Year,Job No,Test Companys,Currency,Exchange Rate,Wo Date,Delivery Date,Pay Mode,Attention,Ready To Approved", "60,60,130,150,70,60,70,70,100,150,100","1100","300",0, $sql , "js_set_value", "id,labtest_no", "", 1, "0,0,supplier_id,currency,0,0,0,pay_mode,0,ready_to_approved", $arr , "labtest_prefix_num,year,job_no,supplier_id,currency,ecchange_rate,wo_date,delivery_date,pay_mode,attention,ready_to_approved", '','','1,0,0,0,1,3,3,0,0,0','','');
	exit();
}
if ($action=="load_drop_down_buyerpop")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "--Buyer--", $selected, "" );
	exit();
}
if ($action=="work_order_knitting_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	 
	  extract($_REQUEST);
	  $cbo_company_name=str_replace("'","",$cbo_company_name);
	?>
	<script>
	function js_set_value(id)
	{
		var value=trim(id).split('_');
		document.getElementById('selected_booking').value=value[0];
		document.getElementById('selected_booking_id').value=value[1];
		document.getElementById('hidd_supplier_id').value=value[2];
		document.getElementById('hidd_exchange_rate').value=value[3];
		document.getElementById('selected_job_no').value=value[4];
		
		parent.emailwindow.hide();
	}
    </script>
 </head>

 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="970" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Buyer Name</th>
                <th>Booking No</th>
				 <th>Booking Source</th>
                <th>Job No</th>
                <th colspan="2">Booking Date Range</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td align="center"> <input type="hidden" id="selected_booking"><input type="hidden" id="selected_booking_id"><input type="hidden" id="selected_job_no">
				<input type="hidden" id="hidd_supplier_id"><input type="hidden" id="hidd_exchange_rate">
                    <?

                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", "$cbo_company_name", "load_drop_down( 'service_acknowledgement_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                </td>
                <td id="buyer_td"  align="center">
                    <?
                    echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                    ?>
                </td>
                <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write"></td>
				 <td>
                    <?
					$booking_source_arr=array(1=>'In House',2=>'Outside');
                    echo create_drop_down( "cbo_booking_source", 100, $booking_source_arr,"", 1, "-- Select Source --" );
                    ?>
                </td>
                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"  placeholder="Write"></td>
                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
                <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_booking_source').value+'_'+'<?=$process_id;?>', 'create_knitting_search_list_view', 'search_div', 'service_acknowledgement_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
            </tr>
            <tr>
                <th align="center" valign="middle" colspan="8"><? echo load_month_buttons(1); ?> </th>
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
if ($action=="create_knitting_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	//if ($data[4]!="") $jobNoCond=" and a.job_no='$data[4]'"; else $jobNoCond="";
   	if (str_replace("'","",$data[4])!="") $jobNoCond=" and a.job_no like '%$data[4]%'"; else  $jobNoCond="";
    if (str_replace("'","",$data[5])!="") $bookingNoCond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $bookingNoCond="";

    if (str_replace("'","",$data[6])!="" && str_replace("'","",$data[6])==1)//In
	{
		$source_cond="and a.pay_mode in(3,5)";
	}
	elseif (str_replace("'","",$data[6])!="" && str_replace("'","",$data[6])==2)//Out
	{
		$source_cond="and a.pay_mode in(1,2,4)";
	}
	else $source_cond="";

	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}

	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$suplier=return_library_array( "select c.supplier_name, c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id','supplier_name');


    $sql= "select a.id as mst_id,a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id,
    a.job_no, a.po_break_down_id, b.id, a.item_category, a.fabric_source, a.supplier_id, a.pay_mode, b.po_number,a.exchange_rate from wo_booking_mst a, wo_po_break_down b
    where a.job_no = b.job_no_mst and $company $buyer $booking_date $jobNoCond $bookingNoCond $source_cond and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.process=$data[7] and b.status_active=1 and b.is_deleted=0   order by a.id DESC";
	$result = sql_select($sql);
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="980" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="70">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="110">Job No.</th>
                <th width="130">PO Number</th>
                <th width="100">Fabric Nature</th>
                <th width="100">Fabric Source</th>
                <th>Supplier</th>
            </tr>
        </thead>
	</table>
    <div style="max-height:320px; width:985px; overflow-y:scroll" id="">
    <table class="rpt_table" id="list_view" rules="all" width="963" height="" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$suplier_name="";
				if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $suplier_name=$comp[$row[csf('supplier_id')]]; else $suplier_name=$suplier[$row[csf('supplier_id')]];
            ?>
            <tr onClick="js_set_value('<? echo $row[csf('booking_no')]."_".$row[csf('mst_id')]."_".$row[csf('supplier_id')]."_".$row[csf('exchange_rate')]."_".$row[csf('job_no')]; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td width="35"><? echo $i; ?></td>
                <td width="70"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                <td width="70"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                <td width="100" style="word-break:break-all"><? echo $comp[$row[csf('company_id')]]; ?></td>
                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                <td width="110"><? echo $row[csf('job_no')]; ?></td>
                <td width="130" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all"><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td width="100"><p><? echo $fabric_source[$row[csf('fabric_source')]]; ?></p></td>
                <td style="word-break:break-all"><? echo $suplier_name; ?></td>
            </tr>
            <?
			}
			?>
        </tbody>
    </table>
    </div>
	<?
	exit();
}

if($action=="service_wo_popup")
{
  
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    ?>

    <script>
    function js_set_value(wo_number)
    {
		var value=trim(wo_number).split('_');
		document.getElementById('selected_booking').value=value[1];
		document.getElementById('selected_booking_id').value=value[0];
		document.getElementById('hidd_supplier_id').value=value[2];
		document.getElementById('hidd_exchange_rate').value=value[3];
      
        //alert(wo_number);return;
        parent.emailwindow.hide();
    }

    </script>
    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="160">Item Category</th>
                            <th width="160" align="center">WO Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="160">
                            <?
                                echo create_drop_down( "cboitem_category", 160, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "1", "","","4,11");
                            ?>
                            </td>
                            <td width="160" align="center">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_wo" id="txt_wo" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_wo').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_name; ?>, 'create_service_wo_search_list_view', 'search_div', 'service_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="selected_booking" name="selected_booking" value="" />
					<input type="hidden" id="selected_booking_id" name="selected_booking_id" value="" />
					<input type="hidden" id="hidd_supplier_id"><input type="hidden" id="hidd_exchange_rate">
                </td>
            </tr>
            <tr>
            <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_service_wo_search_list_view")
{

    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $itemCategory = $ex_data[0];
    $txt_wo_number = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];

    $sql_cond="";
    if(trim($itemCategory)) $sql_cond .= " and b.item_category_id='$itemCategory'";
    if ($txt_wo_number!="") $sql_cond .= " and a.wo_number like '%".trim($txt_wo_number)."'";

    if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if (trim($company) !="") $sql_cond .= " and a.company_name='$company'";

	// $sql = " select a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode order by a.id desc"; 

     $sql = " SELECT a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode, sum(d.ackn_qty) as ackn_qty ,sum(b.supplier_order_quantity) supplier_order_quantity from wo_non_order_info_mst a, wo_non_order_info_dtls b
	  left join  wo_service_acknowledgement_mst c on b.mst_id = c.wo_booking_id and c.status_active=1 and c.is_deleted=0  
	  left join wo_service_acknowledgement_dtls d on  c.id = d.mst_id and d.status_active=1 and d.is_deleted=0 
	  where a.id = b.mst_id and a.entry_form=484 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $sql_cond group by a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode  order by a.id desc"; 

    // echo $sql;die;
    $result = sql_select($sql);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

    $requisition_data=sql_select("select id, requ_no from inv_purchase_requisition_mst where company_id='$company' and status_active=1 and is_deleted=0 and entry_form=526");
    $requisition_arr=array();
    foreach($requisition_data as $row){
        $requisition_arr[$row[csf("id")]]=$row[csf("requ_no")];
    }

    //$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$source);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">WO Number</th>
            <th width="120">Requisition No</th>
            <th width="80">WO Date</th>
            <th width="80">Pay Mode</th>
            <th width="130">Supplier</th>
            <th width="100">Source</th>
        </thead>
    </table>
    <div style="width:920px; max-height:220px; overflow-y:scroll">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;$qty_total=0;
            foreach ($result as $row)
            {
				$qty_total=number_format($row[csf("supplier_order_quantity")]-$row[csf("ackn_qty")],4);
					// echo $i."#".$qty_total."_".$row[csf("supplier_order_quantity")]."__".$row[csf("ackn_qty")]."__".$row[csf('wo_number')]."______";

                if($i%2==0) $bgcolor="#E9F3FF"; 
                else $bgcolor="#FFFFFF";                  
				$conversion_date=change_date_format($row[csf('wo_date')], "d-M-y", "-",1); 
				$currency_rate=set_conversion_rate( $row[csf('currency_id')], $conversion_date );
				if($qty_total>0){
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('wo_number')]."_".$row[csf('supplier_id')]."_".$currency_rate; ?>')">
                    <td width="40"><? echo $i; ?></td>
                    <td width="130"><p><? echo $company_arr[$row[csf('company_name')]];?></p></td>
                    <td width="120"><p><? echo $row[csf('wo_number')]; ?></p></td>
                    <td width="120"><p><? echo $requisition_arr[$row[csf('requisition_no')]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf('wo_date')]); ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $pay_mode[$row[csf('pay_mode')]]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $source[$row[csf('source')]]; ?></p></td>                    
                </tr>
                <?
                $i++;
				}
            } 
            ?>    
        </table>
    </div>
    <?
    exit();   
}


if ($action=="print_booking_list_view")
{
	$dataArr=explode("_",$data);
	$data=$dataArr[0];
	$wo_number=$dataArr[1];
	$wo_type=$dataArr[2];
	$job_no=$dataArr[3];
	$process_id=$dataArr[4];
 
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	



		
	if($wo_type==1)
	{
		$sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, company_id, a.buyer_id, a.job_no, b.job_no as dtls_job_no, b.po_break_down_id as po_id, b.gmt_item, c.emb_name,
		c.emb_type,c.body_part_id, a.supplier_id, a.is_approved,b.uom,b.wo_qnty,b.rate,b.id as booking_dtls_id from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c,wo_po_break_down d where   d.job_no_mst=b.job_no   and d.id=b.po_break_down_id and b.job_no=c.job_no and a.booking_no=b.booking_no
		 and b.pre_cost_fabric_cost_dtls_id=c.id and  a.id=$data and a.booking_type=6  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.booking_no_prefix_num, a.booking_no, a.booking_date,company_id, a.buyer_id, a.job_no, b.job_no, b.po_break_down_id, b.gmt_item, c.emb_name, c.emb_type,c.body_part_id, a.supplier_id, a.is_approved,b.uom,b.wo_qnty, b.rate,b.id order by a.entry_form";

		$menuId = array(1,2,3,4,5,6,7,8,31,32,9,10,11,12); 
	}
	elseif($wo_type==2)
	{

		$sql= " select b.id as booking_dtls_id,b.embl_cost_dtls_id,b.req_booking_no,b.req_id, b.req_no,b.gmt_item_id,b.req_id,
		b.req_no,b.req_booking_no,b.emb_name,b.emb_type,b.body_part_id,b.uom_id, b.sensitivity,b.wo_qnty, a.exchange_rate, b.rate, 
		b.amount,b.delivery_date from wo_non_ord_embl_booking_mst a ,wo_non_ord_embl_booking_dtls b
		 where  a.booking_no=b.booking_no and a.id=$data and a.is_deleted=0 and a.status_active=1 order by b.id";


		 $menuId = array(1,2,3,4,5,6,7,8,31,32,9,10,11,12); 
	}
	elseif($wo_type==3)
	{

		$sql= "select b.id as booking_dtls_id,b.mst_id,b.po_id,b.job_no,b.entry_form,b.test_for,b.test_item_id,b.color,b.amount,b.discount,b.labtest_charge,b.wo_with_vat_value,b.remarks,c.test_item,b.qty_breakdown,c.test_category,c.rate from wo_labtest_mst a,wo_labtest_dtls b,lib_lab_test_rate_chart c
		where a.id=b.mst_id and a.id=$data and  b.test_for=c.test_for and c.testing_company=a.supplier_id  and  b.status_active = 1 and b.is_deleted=0 and b.entry_form=79
		group by b.id,b.mst_id,b.po_id,b.job_no,b.entry_form,b.test_for,b.test_item_id,b.color,b.amount,b.discount,b.labtest_charge,b.wo_with_vat_value,b.remarks,c.test_item,b.qty_breakdown,c.test_category,c.rate order by b.id";
		//echo $sql;

		$menuId = array(1,2,13,14,15,16,8,31,32,9,10,11,17,18,12);
	}elseif($wo_type==4 || $wo_type==5){

		//================================================================
		$fabric_description_array=array();// 
		$wo_pre_cost_fab_co_color_sql=sql_select("select gmts_color_id,contrast_color_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls  where job_no='$job_no'");
		foreach( $wo_pre_cost_fab_co_color_sql as $row)
		{
			$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
		}
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
		//===============================================================

		
		if($db_type==2)
		{ 
 			$group_concat="listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) as order_id";
		}
		
		
	    $sql="select a.id, b.job_no, b.booking_no, $group_concat, b.dia_width, b.pre_cost_fabric_cost_dtls_id, sum(b.amount) as amount, b.process, b.sensitivity,b.uom,
		sum(b.wo_qnty) as wo_qnty, b.insert_date,b.program_no,b.po_break_down_id as po_id,b.fin_gsm as gsm_weight,b.fabric_color_id as color,b.rate,b.id as booking_dtls_id
		from wo_booking_dtls b, wo_booking_mst a
		where b.booking_no=a.booking_no and a.booking_no='$wo_number' and a.id='$data' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
		and b.process=$process_id	group by b.job_no, a.id, b.dia_width, b.pre_cost_fabric_cost_dtls_id, b.process, b.sensitivity,b.uom, b.booking_no, b.insert_date, b.program_no,b.po_break_down_id,b.fin_gsm,b.fabric_color_id,b.rate,b.id";

		$menuId = array(1,2,19,20,21,22,7,8,31,32,9,10,11,12);
	}
	elseif($wo_type==6)
	{
		
		 $sql = "SELECT b.id, a.company_name, a.wo_amount, a.up_charge, a.discount, a.net_wo_amount, a.upcharge_remarks, a.discount_remarks, b.item_id,b.supplier_order_quantity as wo_qnty,b.rate, b.amount, b.service_number,  b.remarks, b.service_for, b.service_details, b.uom, b.requisition_no, b.requisition_dtls_id, b.tag_materials, b.id as booking_dtls_id, a.currency_id, a.wo_date, b.service_lib_id 
		 from wo_non_order_info_mst a, wo_non_order_info_dtls b  
		 where a.id=$data and a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 order by b.id";
		
		
		//$sql = "SELECT b.id, a.company_name,a.upcharge_remarks,a.discount_remarks, b.item_id, sum(b.supplier_order_quantity) as wo_qnty, (sum(b.amount)/sum(b.supplier_order_quantity)) as rate, sum(b.amount) as amount, b.service_number,  b.remarks, b.service_for, b.service_details, b.uom, b.requisition_no, b.requisition_dtls_id, b.tag_materials, b.id as booking_dtls_id  from wo_non_order_info_mst a, wo_non_order_info_dtls b  where a.id=$data and a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  group by b.id, a.company_name,a.upcharge_remarks,a.discount_remarks, b.item_id,b.service_number,  b.remarks, b.service_for, b.service_details, b.uom, b.requisition_no, b.requisition_dtls_id, b.tag_materials, b.id order by b.id"; 
	 
		//  echo $sql;die;
		$result = sql_select($sql);

			foreach ($result as $val) 
			{
				if ($val[csf('item_id')] != '')
				$prod_id.=$val[csf('item_id')].',';
			}
			$prod_ids=rtrim($prod_id,',');
			if ($prod_ids != '')
			{
				$sql_prod=sql_select("select id, item_description, item_category_id, item_group_id from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
				$prod_arr=array();
				foreach ($sql_prod as $val) {
				$prod_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
				$prod_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
				$prod_arr[$val[csf('id')]]['item_group_id']=$val[csf('item_group_id')];
				}
			}
			$unit_of_measurement=$service_uom_arr;
		$menuId = array(23,24,25,26,27,7,8,31,32,9,10,11,28,12);
	}

	// echo $sql; die;
	//============================================Acknolowledgement Data=============================================
	$ackg_sql="select a.id as mst_id,a.system_no_prefix,a.system_prefix_num,a.system_no,a.company_id,a.wo_type,a.service_company_id,a.exchange_rate,a.ackn_date,a.manual_challan,a.remark,b.id as dtls_id,b.job_no,b.po_break_down_id as po_id,b.gmt_item,b.emb_name,b.emb_type,b.body_part,b.uom,b.wo_qnty,b.ackn_qty,b.rate,b.amount,b.remark,b.entry_form_id,b.test_item,b.test_for,b.item_category,b.item_group,b.service_for,b.color_id,b.vat_per,b.fabric_description,b.item_description,b.service_details,
				b.gsm,b.dia,b.service_number,b.wo_booking_dtls_id from wo_service_acknowledgement_mst a,wo_service_acknowledgement_dtls b where a.id=b.mst_id and a.wo_booking_id=$data and b.status_active=1 and b.is_deleted=0";
	//echo $ackg_sql;die;
				

		$ackg_data=sql_select($ackg_sql);	
		foreach($ackg_data as $row){
			$ackg_booking_data_arr[$row[csf('wo_type')]][$row[csf('wo_booking_dtls_id')]]['ackn_qty']+=$row[csf('ackn_qty')];
			 
		}

		foreach($menuId as $val){
			$menu_arr_no[$val]=$menuArr[$val];
			$field_arr_no[$val]=$menuFieldArr[$val];
		}

		
		

 		// echo $sql;
		 $dataArray=sql_select($sql);
        //    echo "<pre>";
		//    print_r($dataArray);die;
	
		$m=1;$wo_qnty="";
		foreach($dataArray as $row)
		{
			
			if($row[csf('emb_name')]==1)
			{
				$emb_type_name=$emblishment_print_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==2)
			{
				$emb_type_name=$emblishment_embroy_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==3)
			{
				$emb_type_name=$emblishment_wash_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==4)
			{
				$emb_type_name=$emblishment_spwork_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==5)
			{
				$emb_type_name=$emblishment_gmts_type[$row[csf('emb_type')]];
			}
		
			if($row[csf('qty_breakdown')] !=""){
				$wo_qnty_arr=explode("_",$row[csf('qty_breakdown')]);
				$wo_qnty+=$wo_qnty_arr[1];
			}else{
				$wo_qnty=$row[csf('wo_qnty')]-$row[csf('ackn_qty')];
			}
		
				
				$row_arr[$m]=$m;
			foreach($menuId as $mid)
			{

				if($mid==8 || $mid==9 || $mid==10 || $mid==11){
					$text_type[$mid]="text_boxes_numeric";
				}else{
					$text_type[$mid]="text_boxes";
				}
				if($row[csf('job_no')]==""){
					
					$job_no=$row[csf('dtls_job_no')];
				}else{
					$job_no=$row[csf('job_no')];
				}
				
				if($mid==1)
				{
					$manu_field_val[$mid][$m]=$job_no;
				}
				elseif($mid==2)
				{
					$manu_field_val[$mid][$m]=$po_arr[$row[csf('po_id')]];
					$hidd_field_val[$mid][$m]=$row[csf('po_id')];
				}
				elseif($mid==3)
				{
					$manu_field_val[$mid][$m]=$garments_item[$row[csf('gmt_item')]];
					$hidd_field_val[$mid][$m]=$row[csf('gmt_item')];
				}
				elseif($mid==4)
				{
					$manu_field_val[$mid][$m]=$emblishment_name_array[$row[csf('emb_name')]];
					$hidd_field_val[$mid][$m]=$row[csf('emb_name')];
				}
				elseif($mid==5)
				{
					$manu_field_val[$mid][$m]=$emb_type_name;
					$hidd_field_val[$mid][$m]=$row[csf('emb_type')];
				}
				elseif($mid==6)
				{
					$manu_field_val[$mid][$m]=$body_part[$row[csf('body_part_id')]];
					$hidd_field_val[$mid][$m]=$row[csf('body_part_id')];
				}
				elseif($mid==7)
				{
					$manu_field_val[$mid][$m]=$unit_of_measurement[$row[csf('uom')]];
					$hidd_field_val[$mid][$m]=$row[csf('uom')];}
				elseif($mid==8)
				{
					$manu_field_val[$mid][$m]=number_format($wo_qnty, 4,'.','');
				}				
				elseif($mid==10)
				{
					$manu_field_val[$mid][$m]=number_format($row[csf('rate')], 6,'.','') ;
				}
				elseif($mid==11)
				{
					$manu_field_val[$mid][$m]="";
				}
				elseif($mid==12)
				{
					$manu_field_val[$mid][$m]=$row[csf('remarks')];
				}
				elseif($mid==13)
				{
					$manu_field_val[$mid][$m]=$row[csf('test_item')];
					$hidd_field_val[$mid][$m]=$row[csf('test_item_id')];
				}
				elseif($mid==14)
				{
					$manu_field_val[$mid][$m]=$test_for[$row[csf('test_for')]];
					$hidd_field_val[$mid][$m]=$row[csf('test_for')];
				}
				elseif($mid==15)
				{
					$manu_field_val[$mid][$m]=$testing_category[$row[csf('test_category')]];
					$hidd_field_val[$mid][$m]=$row[csf('test_category')];
				}
				elseif($mid==16)
				{
					$manu_field_val[$mid][$m]=$color_library[$row[csf('color')]];
					$hidd_field_val[$mid][$m]=$row[csf('color')];
				}
				elseif($mid==17){$manu_field_val[$mid][$m]=$row[csf('remarks')];}
				elseif($mid==18){$manu_field_val[$mid][$m]=$row[csf('remarks')];}
				elseif($mid==19){$manu_field_val[$mid][$m]=$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]];}
				elseif($mid==20){$manu_field_val[$mid][$m]=$row[csf('gsm_weight')];}
				elseif($mid==21){$manu_field_val[$mid][$m]=$row[csf('dia_width')];}
				elseif($mid==22){
					$manu_field_val[$mid][$m]=$color_library[$row[csf('color')]];
					$hidd_field_val[$mid][$m]=$row[csf('color')];}
				elseif($mid==23){
					$manu_field_val[$mid][$m]=$service_for_arr[$row[csf('service_for')]];
					$hidd_field_val[$mid][$m]=$row[csf('service_for')];}
				elseif($mid==24){
					$manu_field_val[$mid][$m]=$row[csf('service_details')];
					$hidd_field_val[$mid][$m]=$row[csf('service_lib_id')];
				}
				elseif($mid==25){$manu_field_val[$mid][$m]=$prod_arr[$row[csf('item_id')]]['item_description'];;}
				elseif($mid==26){
					$manu_field_val[$mid][$m]=$item_category[$prod_arr[$row[csf('item_id')]]['item_category_id']];
					$hidd_field_val[$mid][$m]=$prod_arr[$row[csf('item_id')]]['item_category_id'];}
				elseif($mid==27){
					$manu_field_val[$mid][$m]=$item_group_arr[$prod_arr[$row[csf('item_id')]]['item_group_id']];
					$hidd_field_val[$mid][$m]=$prod_arr[$row[csf('item_id')]]['item_group_id'];}
				elseif($mid==28){$manu_field_val[$mid][$m]=$row[csf('service_number')];}				 
				elseif($mid==31){$manu_field_val[$mid][$m]=number_format($wo_qnty-$ackg_booking_data_arr[$wo_type][$row[csf('booking_dtls_id')]]['ackn_qty'], 4,'.','');;}
				elseif($mid==32){$manu_field_val[$mid][$m]=number_format($ackg_booking_data_arr[$wo_type][$row[csf('booking_dtls_id')]]['ackn_qty'], 4,'.','');;}
				$hidd_field_val[30][$m]=$row[csf('booking_dtls_id')];
			}
			$m++;
		}
		// echo "<pre>";
		// print_r($manu_field_val);

   		 ?>
   		 <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"  >
				<thead>
					
					<?
					foreach($menuId as $mid){
						?>
					<th width="90" title="<?=$mid;?>"><?=$menu_arr_no[$mid];?></th>
					<? } ?>
				</thead>
            </table>

           
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=count($menuId)*90;?>px" class="rpt_table"  id="table_list_view" >
					<?
					$i=1;
					foreach($dataArray as $row)
					{
						if($row[csf('qty_breakdown')] !=""){
							$wo_qnty_arr=explode("_",$row[csf('qty_breakdown')]);
							$wo_qnty+=$wo_qnty_arr[1];
						}else{
							$wo_qnty=$row[csf('wo_qnty')];
						}
						$qty_total=number_format($wo_qnty,4)- number_format($ackg_booking_data_arr[$wo_type][$row[csf('booking_dtls_id')]]['ackn_qty'],4);
						
						//echo $qty_total."<br>";
						// if($qty_total>0)
						// {
							?>
							<tr id="tr_<?=$i;?>">
								
								<?
								foreach($menuId as $mid)
								{
									
									if($mid==9)
									{
										
										$readonly="";
										
									}
									else
									{	
								
									$readonly="disabled";
								
									}
									?>
									<td width="90" style="word-break:break-all">
                                    <input type="text" class="<?=$text_type[$mid];?>"style="width:100px;" name="<?=$field_arr_no[$mid].$i ?>" id="<?=$field_arr_no[$mid].$i ?>" value="<?=$manu_field_val[$mid][$i];?>" <?=$readonly;?> <? if($mid==9) { ?> onChange="fnc_amount(<?=$i;?>)" <? } ?>/>
									<?
									foreach($menuFieldArr as $id=>$field)
									{
										if(in_array($id, $menuId))
										{
										}
										else
										{
											
											?>
											<input type="hidden" class="text_boxes"style="width:100px;" name="<?=$menuFieldArr[$id].$i ?>" id="<?=$menuFieldArr[$id].$i ?>" value=""/>
											<? 
										}
									}
									foreach($hidField as $hid=>$hiddfield)
									{
										?>
										<input type="hidden" class="text_boxes"style="width:100px;" name="<?=$hiddfield.$i ?>" id="<?=$hiddfield.$i ?>" value="<?=$hidd_field_val[$hid][$i];?>"/>
									<? } ?>

								</td>
								<? } ?>
							</tr>
							<?
							$i++;
						}
					// }?>

				</table>
            
    <?

	exit();
}

if ($action=="service_ackn_booking_list_view")
{
	$dataArr=explode("_",$data);
	
	$data=str_replace("'","",$dataArr[0]);	
	$wo_type=$dataArr[1];

	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		
	if($wo_type==1){
		$menuId = array(1,2,3,4,5,6,7,8,32,31,9,10,11,12); 
	}elseif($wo_type==2){
		$menuId = array(1,2,3,4,5,6,7,8,32,31,9,10,11,12); 
	}elseif($wo_type==3){
		$menuId = array(1,2,13,14,15,16,8,32,31,9,10,11,17,18,12);
	}elseif($wo_type==4 || $wo_type==5){
		$menuId = array(1,2,19,20,21,22,7,8,32,31,9,10,11,12);
	}elseif($wo_type==6){
		$unit_of_measurement=$service_uom_arr;
		$menuId = array(23,24,25,26,27,7,8,32,31,9,10,11,28,12);
	}

	foreach($menuId as $val){
		$menu_arr_no[$val]=$menuArr[$val];
		$field_arr_no[$val]=$menuFieldArr[$val];
	}
	$ackg_sql="select a.id as mst_id,a.system_no_prefix,a.system_prefix_num,a.system_no,a.company_id,a.wo_type,a.service_company_id,a.exchange_rate,a.ackn_date,a.manual_challan,a.remark,b.id as dtls_id,b.job_no,b.po_break_down_id as po_id,b.gmt_item,b.emb_name,b.emb_type,b.body_part,b.uom,b.wo_qnty,b.ackn_qty,b.rate,b.amount,b.remark,b.entry_form_id,b.test_item,b.test_for,b.item_category,b.item_group,b.service_for,b.color_id,b.vat_per,b.fabric_description,b.item_description,b.service_details,
	b.gsm,b.dia,b.service_number,b.wo_booking_dtls_id from wo_service_acknowledgement_mst a,wo_service_acknowledgement_dtls b where a.id=b.mst_id and a.id=$data";

	$ackg_data=sql_select($ackg_sql);	
	foreach($ackg_data as $row){
	$ackg_booking_data_arr[$row[csf('wo_type')]][$row[csf('wo_booking_dtls_id')]]['ackn_qty']+=$row[csf('ackn_qty')];
	 
	}


	$sql="select a.id as mst_id,a.system_no_prefix,a.system_prefix_num,a.system_no,a.company_id,a.wo_type,a.service_company_id,a.exchange_rate,a.ackn_date,a.manual_challan,a.remark,b.id as dtls_id,b.job_no,b.po_break_down_id as po_id,b.gmt_item,b.emb_name,b.emb_type,b.body_part,b.uom,b.wo_qnty,b.ackn_qty,b.rate,b.amount,b.remark,b.entry_form_id,b.test_item,b.test_for,b.item_category,b.item_group,b.service_for,b.color_id,b.vat_per,b.fabric_description,b.item_description,b.service_details,
	b.gsm,b.dia,b.service_number,b.wo_booking_dtls_id, b.service_lib_id 
	from wo_service_acknowledgement_mst a,wo_service_acknowledgement_dtls b where a.id=b.mst_id and a.id=$data";
		

		$dataArray=sql_select($sql);
           
			$m=1;$wo_qnty="";
			foreach($dataArray as $row){
				
				if($row[csf('emb_name')]==1)
				{
					$emb_type_name=$emblishment_print_type[$row[csf('emb_type')]];
				}
				if($row[csf('emb_name')]==2)
				{
					$emb_type_name=$emblishment_embroy_type[$row[csf('emb_type')]];
				}
				if($row[csf('emb_name')]==3)
				{
					$emb_type_name=$emblishment_wash_type[$row[csf('emb_type')]];
				}
				if($row[csf('emb_name')]==4)
				{
					$emb_type_name=$emblishment_spwork_type[$row[csf('emb_type')]];
				}
				if($row[csf('emb_name')]==5)
				{
					$emb_type_name=$emblishment_gmts_type[$row[csf('emb_type')]];
				}
			
					
					$row_arr[$m]=$m;
			    foreach($menuId as $mid)
				{
					if($mid==8 || $mid==9 || $mid==10 || $mid==11)
					{
						$text_type[$mid]="text_boxes_numeric";
					}else
					{
						$text_type[$mid]="text_boxes";
					}
					if($mid==1){$manu_field_val[$mid][$m]=$row[csf('job_no')];}
					elseif($mid==2){$manu_field_val[$mid][$m]=$po_arr[$row[csf('po_id')]];$hidd_field_val[$mid][$m]=$row[csf('po_id')];}
					elseif($mid==3){$manu_field_val[$mid][$m]=$garments_item[$row[csf('gmt_item')]];$hidd_field_val[$mid][$m]=$row[csf('gmt_item')];}
					elseif($mid==4){$manu_field_val[$mid][$m]=$emblishment_name_array[$row[csf('emb_name')]];$hidd_field_val[$mid][$m]=$row[csf('emb_name')];}
					elseif($mid==5){$manu_field_val[$mid][$m]=$emb_type_name;$hidd_field_val[$mid][$m]=$row[csf('emb_type')];}
					elseif($mid==6){$manu_field_val[$mid][$m]=$row[csf('body_part_id')];$hidd_field_val[$mid][$m]=$row[csf('body_part_id')];}
					elseif($mid==7){$manu_field_val[$mid][$m]=$unit_of_measurement[$row[csf('uom')]];$hidd_field_val[$mid][$m]=$row[csf('uom')];}
					elseif($mid==8){$manu_field_val[$mid][$m]=$row[csf('wo_qnty')];}
					elseif($mid==9){$manu_field_val[$mid][$m]=$row[csf('ackn_qty')];}
					elseif($mid==10){$manu_field_val[$mid][$m]=$row[csf('rate')];}
					elseif($mid==11){$manu_field_val[$mid][$m]=$row[csf('rate')]*$row[csf('ackn_qty')];}
					elseif($mid==12){$manu_field_val[$mid][$m]=$row[csf('remarks')];}
					elseif($mid==13){$manu_field_val[$mid][$m]=$row[csf('test_item')];$hidd_field_val[$mid][$m]=$row[csf('test_item_id')];}
					elseif($mid==14){$manu_field_val[$mid][$m]=$test_for[$row[csf('test_for')]];$hidd_field_val[$mid][$m]=$row[csf('test_for')];}
					elseif($mid==15){$manu_field_val[$mid][$m]=$testing_category[$row[csf('test_category')]];$hidd_field_val[$mid][$m]=$row[csf('test_category')];}
					elseif($mid==16){$manu_field_val[$mid][$m]=$color_library[$row[csf('color')]];$hidd_field_val[$mid][$m]=$row[csf('color')];}
					elseif($mid==17){$manu_field_val[$mid][$m]=$row[csf('remarks')];}
					elseif($mid==18){$manu_field_val[$mid][$m]=$row[csf('remarks')];}
					elseif($mid==19){$manu_field_val[$mid][$m]=$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]];}
					elseif($mid==20){$manu_field_val[$mid][$m]=$row[csf('gsm_weight')];}
					elseif($mid==21){$manu_field_val[$mid][$m]=$row[csf('dia_width')];}
					elseif($mid==22){$manu_field_val[$mid][$m]=$color_library[$row[csf('color')]];$hidd_field_val[$mid][$m]=$row[csf('color')];;}
					elseif($mid==23){$manu_field_val[$mid][$m]=$service_for_arr[$row[csf('service_for')]];$hidd_field_val[$mid][$m]=$row[csf('service_for')];}
					elseif($mid==24){$manu_field_val[$mid][$m]=$row[csf('service_details')];$hidd_field_val[$mid][$m]=$row[csf('service_lib_id')];}
					elseif($mid==25){$manu_field_val[$mid][$m]=$row[csf('item_description')];;}
					elseif($mid==26){$manu_field_val[$mid][$m]=$item_category[$row[csf('item_category')]];$hidd_field_val[$mid][$m]=$row[csf('item_category')];}
					elseif($mid==27){$manu_field_val[$mid][$m]=$item_group_arr[$row[csf('item_group')]];$hidd_field_val[$mid][$m]=$row[csf('item_group')];}
					elseif($mid==28){$manu_field_val[$mid][$m]=$row[csf('service_number')];}					
					elseif($mid==31){$manu_field_val[$mid][$m]=number_format($row[csf('wo_qnty')]-$ackg_booking_data_arr[$wo_type][$row[csf('wo_booking_dtls_id')]]['ackn_qty'], 4,'.','');;}
					elseif($mid==32){$manu_field_val[$mid][$m]=number_format($ackg_booking_data_arr[$wo_type][$row[csf('wo_booking_dtls_id')]]['ackn_qty'], 4,'.','');;}
					$hidd_field_val[30][$m]=$row[csf('wo_booking_dtls_id')];
					$hidd_field_val[29][$m]=$row[csf('dtls_id')];
					
				}
				$m++;
			}

			// echo "<pre>";
			//  print_r($hidd_field_val[30]);
   		 ?>
   		
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"  >
				<thead>
				
					<? foreach($menuId as $mid){?>
					<th width="90px" title="<?=$mid;?>"><?=$menu_arr_no[$mid];?></th>
					<?}?>

				</thead>
            </table>

            
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=count($menuId)*90;?>px" class="rpt_table" id="table_list_view" >
					<?
					$i=1;
					foreach($row_arr as $rowId){
						
					?>
					   <tr id="tr_<?=$rowId;?>">
 						<?

						foreach($menuId as $mid){
							//========================show field======================
							if($mid==9)
							{
								$readonly="";
								 
							}
							else
							{	$readonly="disabled";
							
							}
							
							?>
							<td  style="word-break:break-all">
                            <input type="text" class="<?=$text_type[$mid];?>" name="<?=$field_arr_no[$mid].$rowId ?>"  <?= $onkeyup ?>  id="<?=$field_arr_no[$mid].$rowId ?>" value="<?=$manu_field_val[$mid][$rowId];?>" <?=$readonly;?>  <? if($mid==9) { ?> onChange="fnc_amount(<?=$i;?>)" <? } ?> />
							
							<?
							//========================other type hidden id======================
							foreach($menuFieldArr as $id=>$field){
							if(in_array($id, $menuId)){}else{?>
							<input type="hidden" class="text_boxes"  name="<?=$menuFieldArr[$id].$rowId ?>" id="<?=$menuFieldArr[$id].$rowId ?>" value=""/>
							<? }}
							//========================hidden id======================
							foreach($hidField as $hid=>$hiddfield)
                            {
								?>
								<input type="hidden" class="text_boxes"  name="<?=$hiddfield.$rowId ?>" id="<?=$hiddfield.$rowId ?>" value="<?=$hidd_field_val[$hid][$rowId];?>"/>
							<? }
							?>

					      </td>
						<? } ?>
					</tr>
					<?
					$i++;}?>

				</table>
		
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
if ($action=="embellishment_wo_without_order_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	  extract($_REQUEST);
	  $cbo_company_name=str_replace("'","",$cbo_company_name);
 ?>

	<script>

	function js_set_value(booking_no)
	{
		var value=trim(booking_no).split('_');
		document.getElementById('selected_booking').value=value[1];
		document.getElementById('selected_booking_id').value=value[0];
		document.getElementById('hidd_supplier_id').value=value[2];
		document.getElementById('hidd_exchange_rate').value=value[3];
		parent.emailwindow.hide();
	}
	function check_orphan( str )
	{
		if($("#chk_orphan").prop('checked')==true)

			$('#chk_orphan').val(1);

		else

			$('#chk_orphan').val(0);

	}
    </script>

 </head>

 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="700"cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th colspan="8">
                  <?
                   echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" );
                  ?>
                </th>
            </thead>
            <thead>
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Booking No</th>
                <th width="130" colspan="2">Date Range</th>
                <th> <input type="checkbox"  id="chk_orphan" onClick="check_orphan(this.value)" value="0"> Orphan WO</th>
            </thead>
            <tr class="general">

                <td> <input type="hidden" id="selected_booking" name="selected_booking" value="" />
					<input type="hidden" id="selected_booking_id" name="selected_booking_id" value="" />
					<input type="hidden" id="hidd_supplier_id"><input type="hidden" id="hidd_exchange_rate">
                <?
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", "$cbo_company_name", "load_drop_down( 'embellishment_wo_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
                </td>

                <td id="buyer_td">
                <?
                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                ?>
                </td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
               

                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                <td>
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value, 'create_emb_wo_without_order_list_view', 'search_div', 'service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center" valign="middle"> <?  echo load_month_buttons(1);  ?></td>
            </tr>
     	</table>
     <div id="search_div"></div>
    </form>
   </div>
 </body>
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}
if ($action=="create_emb_wo_without_order_list_view")
{
	$data=explode('_',$data);
	$without_check=$data[7];
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
		{
		 if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($data[6]==1)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		}
   if($data[6]==2)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[6]==3)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
 //	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	$arr=array (2=>$buyer_arr,3=>$po_num,5=>$po_array,6=>$garments_item,7=>$emblishment_name_array,8=>$suplier,9=>$approved,10=>$is_ready);
	//echo $without_check.'DDDXCX';;
	if($without_check==1)
		  $sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.category_id,a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode,a.id, a.exchange_rate from wo_non_ord_embl_booking_mst a where  a.status_active=1 and a.is_deleted=0  $company ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond  and a.booking_type=6 and a.entry_form_id=399 and a.booking_no not in (select booking_no from  wo_non_ord_embl_booking_dtls where status_active=1) order by a.id DESC";
	else
		  $sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.category_id,a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode,a.id, a.exchange_rate from wo_non_ord_embl_booking_mst a where  a.status_active=1 and a.is_deleted=0  $company ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond  and a.booking_type=6 and a.entry_form_id=399 order by a.id DESC";
		 $result=sql_select($sql);

	
	?>
	<br>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="820" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="30">SL No</th>
                <th width="110">Booking No</th>
                <th width="100">Booking Date</th>
                <th width="120">Buyer</th>
                <th width="120">Supplier</th>
				<th width="70">Approved</th>
				<th width="">Is-Ready</th>
            </tr>
        </thead>
	</table>
    <div style="max-height:320px; width:820px; overflow-y:scroll" id="">
    <table class="rpt_table" id="list_view" rules="all" width="800" height="" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$suplier_name="";
				if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $suplier_name=$comp[$row[csf('supplier_id')]]; else $suplier_name=$suplier[$row[csf('supplier_id')]];
            ?>
            <tr  onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]."_".$row[csf('supplier_id')]."_".$row[csf('exchange_rate')]; ?>')" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><? echo $i; ?></td>
                <td width="110"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                <td width="100"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>

                <td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                <td width="120" style="word-break:break-all"><? echo $suplier_name; ?></td>
				 <td width="70"><p><? echo $yes_no[$row[csf('is_approved')]]; ?></p></td>
				 <td width=""><p><? echo $is_ready[$row[csf('ready_to_approved')]]; ?></p></td>
            </tr>
            <?
			}
			?>
        </tbody>
    </table>
    </div>
	<?
	 exit();
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
		// echo "load_drop_down( 'requires/service_acknowledgement_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_acknowledgement_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
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
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$response_booking_no="";
		if($db_type==0)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SAW', date("Y",time()), 5, "select id,system_no_prefix system_prefix_num, booking_no_prefix_num from wo_service_acknowledgement_mst where company_id=$cbo_company_name  and entry_form_id=558 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "system_no_prefix", "system_prefix_num" ));
		}
		else if($db_type==2)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SAW', date("Y",time()), 5,"select id, system_no_prefix, system_prefix_num from wo_service_acknowledgement_mst where company_id=$cbo_company_name  and entry_form_id=558 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "system_no_prefix", "system_prefix_num" ));
			
		}
		
		$id=return_next_id( "id", "wo_service_acknowledgement_mst", 1 ) ;
		$field_array="id,system_no_prefix,system_prefix_num,system_no,wo_booking_no,wo_booking_id,company_id,wo_type,service_company_id,exchange_rate,ackn_date,manual_challan,remark,entry_form_id,inserted_by,insert_date";
		$data_array ="(".$id.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$txt_workorder_no.",".$txt_workorder_no_id.",".$cbo_company_name.",".$cbo_wo_type.",".$cbo_service_company.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_manual_challan.",".$txt_remarks.",558,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$response_booking_no=$new_booking_no[0];
		

	//   echo "10**insert into wo_service_acknowledgement_mst($field_array)values".$data_array;die;
		

		//===========================================Details========================================================
		$id_dtls=return_next_id( "id", "wo_service_acknowledgement_dtls", 1 ) ;
		$field_array1="id,mst_id,job_no,po_break_down_id,gmt_item,emb_name,emb_type,body_part,uom,wo_qnty,ackn_qty,rate,amount,remark,entry_form_id,test_item,lab_test_rate_chart_id,test_for,test_category,item_category,item_group,service_for,fab_color_id,color_id,vat_per,fabric_description,item_description,service_details,gsm,dia,service_number,wo_booking_dtls_id, service_lib_id, inserted_by, insert_date";

		
	
		$new_array_color=array();
		for ($i=1;$i<=$row_num;$i++)
		{
			
			$job_no="txt_job_no_".$i;
			$po_id="order_id_".$i;
			$gmt_item="gmts_item_id_".$i;			 
			$emb_name="emb_name_id_".$i;
			$emb_type="emb_type_id_".$i;
			$body_part="body_part_id_".$i;
			$uom="uom_id_".$i;
			$wo_qnty="txt_wo_qnty_".$i;
			$ackn_qnty="txt_ackn_qnty_".$i;								
			$txt_rate="txt_rate_".$i;
			$txt_amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$test_item_id="test_item_id_".$i;
			$txt_test_item="txt_test_item_".$i;
			
			$test_for_id="test_for_id_".$i;
			$test_category_id="test_category_id_".$i;
			$color_id="color_id_".$i;			
			$fab_color_id="fab_color_id_".$i;	 
			$txt_vat_amount="txt_vat_amount_".$i;
			$txt_tot_amount="txt_tot_amount_".$i;
			$fab_description="txt_fab_description_".$i;
			$gsm="txt_gsm_".$i;
			$dia="txt_dia_".$i;
									
			$service_for_id="service_for_id_".$i;
			$service_details="txt_service_details_".$i;
			$item_description="txt_item_description_".$i;
			$item_category_id="item_category_id_".$i;
			$item_group_id="item_group_id_".$i;
			$service_number="txt_service_number_".$i;	
			$wo_dtls_id="wo_dtls_id_".$i;
			$service_lib_id="service_lib_id_".$i;			 
			
			

			
			
			if ($i!=1) $data_array1 .=",";
			$data_array1 .="(".$id_dtls.",".$id.",".$$job_no.",".$$po_id.",".$$gmt_item.",".$$emb_name.",".$$emb_type.",".$$body_part.",".$$uom.",".$$wo_qnty.",".$$ackn_qnty.",".$$txt_rate.",".$$txt_amount.",".$$remarks.",558,".$$txt_test_item.",".$$test_item_id.",".$$test_for_id.",".$$test_category_id.",".$$item_category_id.",".$$item_group_id.",".$$service_for_id.",".$$fab_color_id.",".$$color_id.",".$$txt_vat_amount.",".$$fab_description.",".$$item_description.",".$$service_details.",".$$gsm.",".$$dia.",".$$service_number.",".$$wo_dtls_id.",".$$service_lib_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_dtls=$id_dtls+1;
		}
	   	// echo "10**";die;
		//  echo "10**insert into wo_service_acknowledgement_dtls($field_array1)values".$data_array1;die;
		$rID=sql_insert("wo_service_acknowledgement_mst",$field_array,$data_array,0);
		$rID1=sql_insert("wo_service_acknowledgement_dtls",$field_array1,$data_array1,0);

		check_table_status( $_SESSION['menu_id'],0); 
			
		if($db_type==0)
		{
			if($rID && $rID1){
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
			if($rID && $rID1){
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
		 $field_array_up="wo_booking_no*wo_booking_id*company_id*wo_type*service_company_id*exchange_rate*ackn_date*manual_challan*remark*updated_by*update_date";
		
		 $data_array_up =$txt_workorder_no."*".$txt_workorder_no_id."*".$cbo_company_name."*".$cbo_wo_type."*".$cbo_service_company."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_manual_challan."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
		 $rID=sql_update("wo_service_acknowledgement_mst",$field_array_up,$data_array_up,"id",$booking_mst_id,0);
		
		//   echo "10**".$rID;
		  
		 
		 //=======================================================================================================
		 $field_array_up1="job_no*po_break_down_id*gmt_item*emb_name*emb_type*body_part*uom*wo_qnty*ackn_qty*rate*amount*remark*test_item*lab_test_rate_chart_id*test_for*test_category*item_category*item_group*service_for*fab_color_id*color_id*vat_per*fabric_description*item_description*service_details*gsm*dia*service_number*service_lib_id*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {

			$job_no="txt_job_no_".$i;
			$po_id="order_id_".$i;
			$gmt_item="gmts_item_id_".$i;			 
			$emb_name="emb_name_id_".$i;
			$emb_type="emb_type_id_".$i;
			$body_part="body_part_id_".$i;
			$uom="uom_id_".$i;
			$wo_qnty="txt_wo_qnty_".$i;
			$ackn_qnty="txt_ackn_qnty_".$i;								
			$txt_rate="txt_rate_".$i;
			$txt_amount="txt_amount_".$i;
			$remarks="txt_remarks_".$i;
			$test_item_id="test_item_id_".$i;

			$txt_test_item="txt_test_item_".$i;

			$test_for_id="test_for_id_".$i;
			$test_category_id="test_category_id_".$i;
			$color_id="color_id_".$i;	
			$fab_color_id="fab_color_id_".$i;	
				 
			$txt_vat_amount="txt_vat_amount_".$i;
			$txt_tot_amount="txt_tot_amount_".$i;
			$fab_description="txt_fab_description_".$i;
			$gsm="txt_gsm_".$i;
			$dia="txt_dia_".$i;
						
			$service_for_id="service_for_id_".$i;
			$service_details="txt_service_details_".$i;
			$item_description="txt_item_description_".$i;
			$item_category_id="item_category_id_".$i;
			$item_group_id="item_group_id_".$i;
			$service_number="txt_service_number_".$i;	
			$updatedtlsid="update_dtls_id_".$i;	
			$service_lib_id="service_lib_id_".$i;	 

			
			

			if(str_replace("'",'',$$updatedtlsid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updatedtlsid);
				$data_array_up1[str_replace("'",'',$$updatedtlsid)] =explode("*",("".$$job_no."*".$$po_id."*".$$gmt_item."*".$$emb_name."*".$$emb_type."*".$$body_part."*".$$uom."*".$$wo_qnty."*".$$ackn_qnty."*".$$txt_rate."*".$$txt_amount."*".$$remarks."*".$$txt_test_item."*".$$test_item_id."*".$$test_for_id."*".$$test_category_id."*".$$item_category_id."*".$$item_group_id."*".$$service_for_id."*".$$fab_color_id."*".$$color_id."*".$$txt_vat_amount."*".$$fab_description."*".$$item_description."*".$$service_details."*".$$gsm."*".$$dia."*".$$service_number."*".$$service_lib_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		
		//   echo "10**".bulk_update_sql_statement( "wo_service_acknowledgement_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );
		 $rID1=execute_query(bulk_update_sql_statement( "wo_service_acknowledgement_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		//  echo "10**".$rID."&&".$rID1;die;
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
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
		$rID=sql_delete("wo_service_acknowledgement_mst",$field_array,$data_array,"id","".$booking_mst_id."",1);
		$rID2=sql_delete("wo_service_acknowledgement_dtls",$field_array,$data_array,"mst_id","".$booking_mst_id."",1);
		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
			}
			else{
				mysql_query("ROLLBACK");  
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$booking_mst_id);
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



	
if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	  extract($_REQUEST);
	  $cbo_company_name=str_replace("'","",$cbo_company_name);
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
                    <th width="160">Service Company</th>
                    <th width="120">System No</th>
                    <th width="120">Job No</th>
					<th width="120">Service WO</th>
					<th width="80">WO Type</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="reset" id="rst" class="formbutton" style="width:60px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>   
                </tr>                	 
            </thead>
            <tbody>
                <tr class="general">
                    <td> <input type="hidden" id="selected_booking">
                    <? 
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", "$cbo_company_name", "load_drop_down( 'service_acknowledgement_controller', this.value, 'load_drop_down_supplier', 'buyer_td' );");
                    ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_service_company", 150, $blank_array,"", 1, "-- Select Buyer --" );?></td>
                    <td>
                    <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">	
                    </td>
                    <td>
                    <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write Job No">	
                    </td>
					  <td>
                    <input type="text" id="txt_service_wo_no" name="txt_service_wo_no" class="text_boxes" style="width:100px" placeholder="Write service wo No">	
                    </td>
					  <td>
					  <? 
					  $basis_on=array(1=>"Embellishment",2=>"Lab Test",3=>"Knitting",4=>"Dyeing",5=>"Service WO");
					  echo create_drop_down( "cbo_wo_type", 80, $basis_on,"", 1, "-- Select  --" );?>
                   
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td> 
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_service_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_service_wo_no').value+'_'+document.getElementById('cbo_wo_type').value, 'create_booking_search_list_view', 'search_div', 'service_acknowledgement_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:60px;" />
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
	$service_company_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$job_no=$data[6];
	$wo_no=$data[7];
	$wo_type=$data[8];
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $sql_cond .=" and ,a.service_company_id='$service_company_id'";
	if($wo_no!="") $wo_no_cond=" and a.wo_booking_no like '%$wo_no%' ";else  $wo_no_cond="";
	if($wo_type!=0) $wo_type_cond=" and a.wo_type=$wo_type";else  $wo_type_cond="";
	if($db_type==0)
	{
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.ackn_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.ackn_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	if($job_no!="")
	{
		if($search_catgory==1)
		{
			$sql_cond .=" and b.job_no='$job_no'";
			
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
	
	if($booking_no!="") $sql_cond .=" and a.system_prefix_num=$booking_no";
		
	$sql= "select a.id, a.system_prefix_num, a.company_id, a.wo_type, a.service_company_id, a.ackn_date, a.wo_booking_no, b.job_no, listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as order_no
	from wo_service_acknowledgement_mst a,wo_service_acknowledgement_dtls b left join  wo_po_break_down c on b.po_break_down_id=c.id	 where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond $wo_no_cond $wo_type_cond group by a.id ,a.system_prefix_num,a.company_id,a.wo_type,a.service_company_id,a.exchange_rate,a.ackn_date,a.manual_challan,a.remark,b.job_no,a.wo_booking_no"; 
	//echo $sql;
	
	?>
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
    	<thead>
        	<tr>
            	<th width="30">SL</th> 
            	<th width="50">System No</th>
                <th width="65">Ackn Date</th>
                <th width="60">Wo Booking No</th>
                <th width="60">Job No</th>
                <th width="160">PO number</th>
				 <th width="60">Wo Type</th>				 
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:970px" >
    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="950" id="table_body">
        <tbody>
		
        <?
		$basis_on=array(1=>"Embellishment",2=>"Lab Test",3=>"Knitting",4=>"Dyeing",5=>"Service WO");
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
                <td width="50" align="center"><p><? echo $row[csf("system_prefix_num")]; ?>&nbsp;</p></td>
                <td width="65" align="center"><p><? if($row[csf("ackn_date")]!="" && $row[csf("ackn_date")]!="0000-00-00") echo change_date_format($row[csf("ackn_date")]); ?>&nbsp;</p></td>
                <td width="60"><p><? echo $row[csf("wo_booking_no")]; ?>&nbsp;</p></td>
                <td width="60" align="center"><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                <td width="160" style="word-break:break-all"><?	echo $row[csf("order_no")]; ?>&nbsp;</td>
				 <td width="60" style="word-break:break-all"><? echo $basis_on[$row[csf("wo_type")]]; ?>&nbsp;</td>
 
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




if ($action=="populate_data_from_search_popup")
{

	 $sql= "select id,system_no_prefix,system_prefix_num,system_no,wo_booking_no,wo_booking_id,company_id,wo_type,service_company_id,exchange_rate,ackn_date,manual_challan,
  remark from wo_service_acknowledgement_mst  where id='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		$list_data=$data."_".$row[csf("wo_type")];
		 echo "load_drop_down( 'requires/service_acknowledgement_controller', '".$row[csf("service_company_id")]."', 'load_drop_down_supplier', 'supplier_td' )\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("system_no")]."';\n";  
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  	
		echo "document.getElementById('txt_workorder_no').value = '".$row[csf("wo_booking_no")]."';\n";
		echo "document.getElementById('cbo_wo_type').value = '".$row[csf("wo_type")]."';\n";
		echo "document.getElementById('txt_workorder_no_id').value = '".$row[csf("wo_booking_id")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remark")]."';\n";
		
		echo "document.getElementById('txt_manual_challan').value = '".$row[csf("manual_challan")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("ackn_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_service_company').value = '".$row[csf("service_company_id")]."';\n";
		echo "show_list_view('".$list_data."','service_ackn_booking_list_view','booking_list_view2','requires/service_acknowledgement_controller','setFilterGrid(\'list_view\',-1)')\n";
	 }

	 exit();
}


if($action == "show_service_ackn_report")
{
	extract($_REQUEST);
	
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$company_name=str_replace("'","",$cbo_company_name);
	$wo_type=str_replace("'","",$cbo_wo_type);
	$workorder_no=str_replace("'","",$txt_workorder_no);
	$txt_manual_challan=str_replace("'","",$txt_manual_challan);
	$txt_booking_date=str_replace("'","",$txt_booking_date);
	$service_company=str_replace("'","",$cbo_service_company);
	$exchange_rate=str_replace("'","",$txt_exchange_rate);
	$txt_remarks=str_replace("'","",$txt_remarks);
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	// echo $booking_mst_id; die;
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$basis_on=array(1=>"Embellishment",2=>"Embellishment Without Order",3=>"Lab Test",4=>"Knitting",5=>"Dyeing",6=>"Service WO");

	$division_arr = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$section_arr = return_library_array("select id, section_name from lib_section", "id", "section_name");

	$sql_data = sql_select("SELECT a.DIVISION_ID, a.DEPARTMENT_ID, a.SECTION_ID, a.CBO_CURRENCY
	from INV_PURCHASE_REQUISITION_MST a, WO_NON_ORDER_INFO_DTLS b, WO_SERVICE_ACKNOWLEDGEMENT_MST c
	where a.STATUS_ACTIVE = 1 and a.IS_DELETED=0 and a.ID = b.REQUISITION_NO and b.MST_ID=c.WO_BOOKING_ID and  c.ID = $booking_mst_id
	group by a.DIVISION_ID, a.DEPARTMENT_ID, a.SECTION_ID, a.CBO_CURRENCY");

	// echo "<pre>"; print_r($sql_data); die;

	$division_id = $sql_data[0]['DIVISION_ID'];
	$department_id = $sql_data[0]['DEPARTMENT_ID'];
	$section_id = $sql_data[0]['SECTION_ID'];
	$currency = $currency[$sql_data[0]['CBO_CURRENCY']];
	
	ob_start();
    ?>
	<div style="width: 1200px;" align="center">
	   <table border="1" align="left" style=" margin:15px;"  cellpadding="0" width="1000" cellspacing="0" rules="all" >
		<tr>
			<td colspan="8" align="center"><h1>Service Acknowledgement</h1></td>
			
		</tr>
		<tr>
			<td width="120"><b>Service Acknowledge No:</b></td>
			<td width="120"><?=$txt_booking_no;?></td>
			<td width="120"><b>Company Name:</b></td>
			<td width="100"><?=$company_library[$company_name];?></td>
			<td width="120"><b>WO Type:</b></td>
			<td width="80"><?=$basis_on[$wo_type];?></td>
			<td width="120"><b>Work Order:</b></td>
			<td width="120"><?=$workorder_no;?></td>
			
		</tr>
		<tr>
			<td ><b>Service Company:</b></td>
			<td ><?=$supplier_name_arr[$service_company];?></td>
			<td ><b>Exchange Rate:</b></td>
			<td ><?=$exchange_rate;?></td>
			<td ><b>Currency:</b></td>
			<td ><?=$currency;?></td>
			<td ><b>Ackn. Date:</b></td>
			<td ><?=$txt_booking_date;?></td>
		</tr>
		<tr>
			<td ><b>For Division:</b></td>
			<td ><?=$division_arr[$division_id];?></td>
			<td ><b>For Department:</b></td>
			<td ><?=$department_arr[$department_id];?></td>
			<td ><b>For Section:</b></td>
			<td ><?=$section_arr[$section_id];?></td>
			<td ><b>Remarks:</b></td>
			<td ><?=$txt_remarks;?></td>
		</tr>
	</table>
	<?

	$menuArr=array(1=>"Job No",2=>"Order No",3=>"Gmts Item",4=>"Embl Name",5=>"Embl Type",6=>"Body Part",7=>"UOM",8=>"WOQ",9=>"Ackn. Qty",10=>"Rate",11=>"Amount",12=>"Remarks",13=>"Test Item",14=>"Test For",15=>"Test Category",16=>"Color",17=>"Vat %",18=>"Total Amount",19=>"Fabric Description",20=>"GSM",21=>"Dia",22=>"Fabric Color",23=>"Service For",24=>"Service Details",25=>"Item Description",26=>"Item Category",27=>"Item Group",28=>"Service Number");
		
	if($wo_type==1){
		$menuId = array(1,2,3,4,5,6,7,8,9,10,11,12); 
		$colspan_arr[$wo_type]=7;
	}elseif($wo_type==2){
		$menuId = array(1,2,3,4,5,6,7,8,9,10,11,12); 
		$colspan_arr[$wo_type]=7;
	}elseif($wo_type==3){
		$menuId = array(1,2,13,14,15,16,8,9,10,11,17,18,12);
		$colspan_arr[$wo_type]=6;
	}elseif($wo_type==4 || $wo_type==5){
		$menuId = array(1,2,19,20,21,22,7,8,9,10,11,12);
		$colspan_arr[$wo_type]=7;
	}elseif($wo_type==6){
		$unit_of_measurement=$service_uom_arr;
		$menuId = array(23,24,25,26,27,7,8,9,10,11,28,12);
		$colspan_arr[$wo_type]=6;
	}

	foreach($menuId as $val){
		$menu_arr_no[$val]=$menuArr[$val];
		$field_arr_no[$val]=$menuFieldArr[$val];
		if($val==8 || $val==9 || $val==10 || $val==11){
			$alignArr[$val]="center";
		}else{
			$alignArr[$val]="left";
		}
		
	}
	$sql="SELECT a.id as mst_id,a.system_no_prefix,a.system_prefix_num,a.system_no,a.company_id,a.wo_type,a.service_company_id,a.exchange_rate,a.ackn_date,a.manual_challan,a.remark,b.id as dtls_id,b.job_no,b.po_break_down_id as po_id,b.gmt_item,b.emb_name,b.emb_type,b.body_part,b.uom,b.wo_qnty,b.ackn_qty,round(b.rate,4) rate,round(b.ackn_qty*b.rate,4) amount,b.remark,b.entry_form_id,b.test_item,b.test_for,b.item_category,b.item_group,b.service_for,b.color_id,b.vat_per,b.fabric_description,b.item_description,b.service_details, b.gsm,b.dia,b.service_number 
	FROM wo_service_acknowledgement_mst a, wo_service_acknowledgement_dtls b 
	WHERE a.id=b.mst_id and a.id=$booking_mst_id and a.wo_type=$wo_type";
	
	$dataArray=sql_select($sql);
		
		$m=1;$wo_qnty="";
		foreach($dataArray as $row)
		{
			if($row[csf('emb_name')]==1)
			{
				$emb_type_name=$emblishment_print_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==2)
			{
				$emb_type_name=$emblishment_embroy_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==3)
			{
				$emb_type_name=$emblishment_wash_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==4)
			{
				$emb_type_name=$emblishment_spwork_type[$row[csf('emb_type')]];
			}
			if($row[csf('emb_name')]==5)
			{
				$emb_type_name=$emblishment_gmts_type[$row[csf('emb_type')]];
			}
		
			$main_data_arr[$row[csf('dtls_id')]][1]=$row[csf('job_no')];
			$main_data_arr[$row[csf('dtls_id')]][2]=$po_arr[$row[csf('po_id')]];
			$main_data_arr[$row[csf('dtls_id')]][3]=$garments_item[$row[csf('gmt_item')]];
			$main_data_arr[$row[csf('dtls_id')]][4]=$emblishment_name_array[$row[csf('emb_name')]];
			$main_data_arr[$row[csf('dtls_id')]][5]=$emb_type_name;
			$main_data_arr[$row[csf('dtls_id')]][6]=$row[csf('body_part_id')];
			$main_data_arr[$row[csf('dtls_id')]][7]=$unit_of_measurement[$row[csf('uom')]];
			$main_data_arr[$row[csf('dtls_id')]][8]=$row[csf('wo_qnty')];
			$main_data_arr[$row[csf('dtls_id')]][9]=$row[csf('ackn_qty')];
			$main_data_arr[$row[csf('dtls_id')]][10]=$row[csf('rate')];
			$main_data_arr[$row[csf('dtls_id')]][11]=$row[csf('amount')];
			$main_data_arr[$row[csf('dtls_id')]][12]=$row[csf('remarks')];
			$main_data_arr[$row[csf('dtls_id')]][13]=$row[csf('test_item')];
			$main_data_arr[$row[csf('dtls_id')]][14]=$test_for[$row[csf('test_for')]];
			$main_data_arr[$row[csf('dtls_id')]][15]=$testing_category[$row[csf('test_category')]];
			$main_data_arr[$row[csf('dtls_id')]][16]=$color_library[$row[csf('color')]];
			$main_data_arr[$row[csf('dtls_id')]][17]=$row[csf('remarks')];
			$main_data_arr[$row[csf('dtls_id')]][18]=$row[csf('remarks')];
			$main_data_arr[$row[csf('dtls_id')]][19]=$fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]];
			$main_data_arr[$row[csf('dtls_id')]][20]=$row[csf('gsm_weight')];
			$main_data_arr[$row[csf('dtls_id')]][21]=$row[csf('dia_width')];
			$main_data_arr[$row[csf('dtls_id')]][22]=$color_library[$row[csf('color')]];
			$main_data_arr[$row[csf('dtls_id')]][23]=$service_for_arr[$row[csf('service_for')]];
			$main_data_arr[$row[csf('dtls_id')]][24]=$row[csf('service_details')];
			$main_data_arr[$row[csf('dtls_id')]][25]=$row[csf('item_description')];;
			$main_data_arr[$row[csf('dtls_id')]][26]=$item_category[$row[csf('item_category')]];
			$main_data_arr[$row[csf('dtls_id')]][27]=$item_group_arr[$row[csf('item_group')]];
			$main_data_arr[$row[csf('dtls_id')]][28]=$row[csf('service_number')];		
		}
		?>
	
		<table cellspacing="0" cellpadding="0" border="1" rules="all"  style=" margin-left:15px;"width="<?=count($menuId)*110;?>" align="left" class="rpt_table"  >
			<thead>
				<? foreach($menuId as $mid){?>
				<th width="110px" title="<?=$mid;?>"align="center"> <?=$menu_arr_no[$mid];?></th>
				<?}?>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" style=" margin-left:15px;" rules="all" width="<?=count($menuId)*110;?>px" align="left" class="rpt_table" id="table_list_view" >
			<?
			$i=1;
			$grand_tot_arr=array();
			
			foreach($main_data_arr as $dtls_id=>$val){?>
				<tr>
					<?
					foreach($menuId as $mid){?>
						<td width="110px" align="<?=$alignArr[$mid];?>"><?=$val[$mid];?></td>
					<?}?>
				</tr>
				<?
					$grand_tot_arr[11]+=$val[11];
					$grand_tot_arr[9]+=$val[9];
					$grand_tot_arr[8]+=$val[8];
			}?>

			<tr>
				<td width="110px" colspan="<?=$colspan_arr[$wo_type];?>" align="right"><b>Grand Total &nbsp;&nbsp;&nbsp;</b></td>
				<?	foreach($menuId as $mid){
					if($mid==8 || $mid==9 || $mid==10 || $mid==11 || $mid==12 || $mid==17 || $mid==18 || $mid==28  ){
					?>
					<td width="110px" align="<?=$alignArr[$mid];?>"><b><?=$grand_tot_arr[$mid];?></b></td>
					<?}
				}?>
			</tr>

		</table>
		<?
		echo signature_table(295, $company_name, "1200px");
		?>
	</div>
	<? 
	exit();
}

?>