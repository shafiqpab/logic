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
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

$selected_dyeing_process_id="25,26,31,32,33,34,38,39,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,88,89,90,91,92,93,94,100,128,29,132,133,135,136,137,138,139,140,141,142,143, 144,145,146,147,148,149,150,151,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,180,181,182,183,184,185,186,187,189,190,191,192,193, 194,195,196,197,198,199,200,201,203,205,209,210,211,212,219,221,224,225,227,231,263,232,233,234,238,239,242,245,257,258,259,260,261,265,266,267,276,281,287,298,299,300,303,304,305,306,309, 310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355, 356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,385,386,387,388,390,391,394,395,396,397,398,399,400,401,402,403, 404,405,412,413,414,415,416,417,418,419,420,421,422,423,424,425,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,476,481,289";

//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and color_name is not null", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and size_name is not null", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 ",'id','short_name');
$trim_group= return_library_array( "select id, item_name from lib_item_group where status_active=1 ",'id','item_name');

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=207 and is_deleted=0 and status_active=1");
    //echo $print_report_format; die;
    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
    echo "print_report_button_setting('".$print_report_format."');\n";
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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();	  	 
}

if ($action=="load_drop_down_supplier")
{ 
	//echo "dsdsd";
	
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_dyeing_controller');",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0 and b.party_type in(21,25) group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_dyeing_controller');","");

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
if($action=="budget_conversion_info_popup")
{
	echo load_html_head_contents("Budget Conversation Info ","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $permission.'DDZ';
	?>
	<table width="700" class="rpt_table" border="1" rules="all" id="budget_tbl">
	<thead>
		<tr>
		    <th width="300">Particulars</th>
			<th width="110">Process</th>
			<th  width="80">Total Cons/ DZN</th>
			<th width="40">UOM</th>
			<th width="80">Rate (USD)</th>
			<th>Total Amount (USD)</th>
		</tr>
	</thead>
	<tbody>
<?
$po_id=str_replace("'","",$txt_order_no_id);
$condition= new condition();
if(str_replace("'","",$txt_job_no) !=''){
	$condition->job_no("='$txt_job_no'");
}

if(str_replace("'","",$po_id)!='')
{
	$condition->po_id_in("$po_id");
}
$condition->init();
$conversion= new conversion($condition);
$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
$conv_data_qty=$conversion->getQtyArray_by_conversionid();
$conv_data_amt=$conversion->getAmountArray_by_conversionid();
//echo $txt_order_no_id.'D'.;die;
$sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit,a.amount,a.color_break_down, a.status_active,b.body_part_id,b.uom ,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id and b.status_active=1 and b.is_deleted=0 where a.job_no='".$txt_job_no."' and a.status_active=1 and a.is_deleted=0  and a.cons_process in($selected_dyeing_process_id) order by  a.cons_process";
		$data_array=sql_select($sql);
		foreach( $data_array as $row)
		{
		$convsion_qty=$conv_data_qty[$row[csf('id')]][$row[csf('uom')]];
		$conversion_cost=$conv_data_amt[$row[csf('id')]][$row[csf('uom')]];
		$data_str=$row[csf('fabric_description')].'_'.$row[csf('cons_process')];

		$conv_data_arr[$data_str]['uom']=$row[csf('uom')];
		$conv_data_arr[$data_str]['req_qnty']+=$convsion_qty;
		$conv_data_arr[$data_str]['amount']+=$conversion_cost;

		}
//print_r($conv_data_arr);
	$k=1;
	
		foreach($conv_data_arr as $descKey=>$row)
		{
			if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 $desc_itemArr=explode("_",$descKey);
		 $desc_item= $desc_itemArr[0];
		 $process_id= $desc_itemArr[1];
		 $total_convsion_qty=$row['req_qnty'];
		 $total_convsion_cost=$row['amount'];
?>
	<tr bgcolor="<? echo $bgcolor;?>" > 
	 
	 
	<td align="left"><strong><? echo $desc_item; ?></strong></td>
	<td align="left"><strong><? echo $conversion_cost_head_array[$process_id]; ?></strong></td>
	<td align="right"><strong><?  echo fn_number_format($total_convsion_qty,2); ?></strong></td>
	<td> <?=$unit_of_measurement[$row['uom']]; ?></td>
	<td align="right"><strong><? echo fn_number_format($total_convsion_cost/$total_convsion_qty,4); ?></strong></td>
	<td align="right"><strong><? echo fn_number_format($total_convsion_cost,2); ?></strong></td>
	 
	</tr>
	<?
	$k++;
		}
	?>
	 
	</tbody>
	</table>
	 
<?
exit();

}

if($action=="colur_cuff_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $permission.'DDZ';

 ?>

	<script>
var permission='<? echo $permission; ?>';

function show_sub_form()
{
show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+document.getElementById('po_id').value+'**'+<? echo "'$permission'";?>,'load_color_size_form','form_data_con','service_booking_dyeing_controller','');
}

function show_list()
{
 
	//var po_id='<? //echo $po_id;?>';
	var booking_no_po='<? echo $txt_booking_no.'__'.$po_id;?>';
	//alert(booking_no_po);

	show_list_view(booking_no_po,'show_list_view','list_view_con','service_booking_dyeing_controller','');
}
function show_sub_form_with_data(booking_no,fabric_cost_id){
	//alert(booking_no);
	document.getElementById('cbo_fabric_part').value=fabric_cost_id;
	document.getElementById('booking_no').value=booking_no;
	show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+<? echo "'$po_id'";?>+'**'+<? echo "'$permissions'";?>,'load_color_size_form','form_data_con','service_booking_dyeing_controller','');
	set_button_status(1, permission, 'fnc_colar_culff_dtls',1);

}

function calculate_qty(i,body_part_type){
	var gmts_qty=(document.getElementById('gmts_qty_'+i).value)*1;
	var excess_per=(document.getElementById('excess_per_'+i).value)*1;
	var txt_body_part=(document.getElementById('txt_body_part').value)*1;
	var qty=0;
	/*if(txt_body_part==3){
		qty=gmts_qty*2
	}else{
		qty=gmts_qty*1;
	}*/
	body_part_type=body_part_type*1;
	if(body_part_type==50){
		qty=gmts_qty*2
	}else{
		qty=gmts_qty*1;
	}

	var excess=(qty*excess_per)/100;
	qty=Math.ceil(qty+excess);
	document.getElementById('qty_'+i).value=qty;
}

function fnc_colar_culff_dtls( operation ){
		freeze_window(operation);
		var delete_cause='';
		var booking_no=document.getElementById('booking_no').value;
		var booking=return_global_ajax_value(booking_no, 'check_booking_approved', '', 'service_booking_dyeing_controller');
		if(operation == 1 || operation == 2){
			if(booking == 'approved'){
				alert("This booking is approved So Update/Delete Not Possible");
				release_freezing();
				return;
			}
		}
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}

		var row_num=$('#colar_cuff_tbl tbody tr').length;
		//update_dtls_id_
		var data_all="";
		for (var i=1; i<=row_num; i++){
			data_all=data_all+get_submitted_data_string('cbo_fabric_part*booking_no*txt_body_part*txt_job*po_id_'+i+'*color_id_'+i+'*size_id_'+i+'*item_size_'+i+'*gmts_qty_'+i+'*qty_'+i+'*update_dtls_id_'+i,"../../../",i);
		}
		var data="action=save_update_delete_colar_culff_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;

		http.open("POST","service_booking_dyeing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_colar_culff_dtls_reponse;
	}

	function fnc_colar_culff_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				show_list();
				reset_form('','form_data_con','','');
				release_freezing();
				//show_msg(trim(reponse[0]));
			 }
			  if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1){
			  	set_button_status(1, permission, 'fnc_colar_culff_dtls',1);
					release_freezing();
			 }
			 if(trim(reponse[0])=='approved'){
				 alert("This booking is approved So Update/Delete Not Possible");
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='sal1'){
				 alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		    }
			 release_freezing();
		}
	}

	function copy_value(row_id,type)
	{
	 	var copy_val=document.getElementById('check_excess_id').checked;

		//alert(body_part_type_id);
		var rowCount=$('#colar_cuff_tbl tbody tr').length;
		if(copy_val==true)
		  {
		 // alert(rowCount);

		   	for(var j=row_id; j<=rowCount; j++)
		  	{
				  if(type=='excessper')
				  {

					 var body_part_type_id=document.getElementById('body_part_type_id_'+j).value*1;
					  var excess_per=(document.getElementById('excess_per_'+row_id).value)*1;
					  document.getElementById('excess_per_'+j).value=excess_per;
					  calculate_qty(j,body_part_type_id);

				  }
			 } //Loop End
		  }

	 }
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
 <?
 $booking_no=str_replace("'","",$txt_booking_no);
  $po_id=str_replace("'","",$po_id);
 //$sql="select a.id from lib_body_part a, wo_pre_cost_fabric_cost_dtls b ,wo_booking_dtls c   where a.id=b.body_part_id and b.job_no=c.job_no and b.id=c.pre_cost_fabric_cost_dtls_id   and a.body_part_type=40 and a.status_active=1 and a.is_deleted=0";
//echo  "select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and and c.body_part_id=d.id c.body_part_id in (2,3) and a.booking_no='$txt_booking_no' and d.body_part_type=40  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
//echo $po_id.'f';
  $FabricPart=array();//wo_pre_cost_fab_conv_cost_dtls
  $sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d,wo_pre_cost_fab_conv_cost_dtls e where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=e.id and e.fabric_description=c.id and  c.job_no=e.job_no and b.job_no=e.job_no  and c.body_part_id=d.id  and b.po_break_down_id in($po_id) and d.body_part_type in(40,50) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1");//and c.body_part_id in (2,3)

  foreach($sql as $row){
	  $FabricPart[$row[csf('id')]]=$body_part[$row[csf('body_part_id')]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")].', '.$row[csf("gsm_weight")];
  }
  if(count($FabricPart)==0){
	  echo "No Colar Or Cullf Found in this Booking";

	  die;
  }
  
 ?>
<fieldset>
    <form autocomplete="off">
        <table width="550" class="rpt_table" border="1" rules="all">
            <tr>
            <td>Body Part</td>
            <td>
            <?
            echo create_drop_down( "cbo_fabric_part", 400, $FabricPart,"", 1, "-- Select--", 0, "","","");
            ?>
			 <input style="width:40px;" type="checkbox" class="text_boxes"  name="check_excess_id" id="check_excess_id" checked="checked"  />

            <input style="width:60px;" type="hidden" class="text_boxes"  name="booking_no" id="booking_no" value="<? echo trim($booking_no); ?> " />
			 <input style="width:60px;" type="hidden" class="text_boxes"  name="po_id" id="po_id" value="<? echo $po_id; ?> " />
            </td>
            <td><input type="button" class="formbutton" value="Show" onClick="show_sub_form()"/> </td>
            </tr>
        </table>

    <div id="form_data_con">
    </div>
    </form>
    <div id="list_view_con">
    </div>
</fieldset>
</div>
</body>
<script>
show_list('<? echo $txt_booking_no."__".$po_id?>');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="load_color_size_form")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$data=explode("**",$data);
	$fabric_cost_id=trim($data[0]);
	$booking_no=trim($data[1]);
	$po_ids=trim($data[2]);
	$permission=$_SESSION['page_permission'];
	//echo $permission.'PP';
	$job_no="";
	$bodyPart=0;
	$body_part_type=0;
	$sql=sql_select("select a.job_no,a.body_part_id,b.body_part_type from wo_pre_cost_fabric_cost_dtls a, lib_body_part b   where a.body_part_id=b.id and  a.id='$fabric_cost_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sql as $row){
		$job_no=$row[csf('job_no')];
	    $bodyPart=$row[csf('body_part_id')];
		$body_part_type=$row[csf('body_part_type')];
	}
	
	$condition= new condition();
	if(str_replace("'","",$po_ids) !=''){
		$condition->po_id_in("$po_ids");
	}
	 
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery();die;

	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorGmtsSizeAndItemSize_knitAndwoven_greyAndfinish();
	//print_r($req_qty_arr);
	
	$book_data=array();
	   $sql_collor="select id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,excess_per,qty  from wo_booking_colar_culff_dtls where booking_no ='$booking_no' and pre_cost_fabric_cost_dtls_id= '$fabric_cost_id' and status_active=1 and is_deleted=0 ";
	$sql_data_collor=sql_select($sql_collor);

	foreach($sql_data_collor as $row){
		$book_data[$row[csf('gmts_color_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['gmts_qty']+=$row[csf('gmts_qty')];
		//$book_data[$row[csf('gmts_color_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['gmts_qty']=$row[csf('item_size')];
		$book_data[$row[csf('gmts_color_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['excess_per']=$row[csf('excess_per')];
	   $book_data[$row[csf('gmts_color_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['qty']+=$row[csf('qty')];
	   $book_data[$row[csf('gmts_color_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['dtls_id']=$row[csf('id')];


	}
	//if($po_ids=='00') $booking_no_cond="and b.booking_no='$booking_no'";else  $booking_no_cond="";
	//if($po_ids=='00') $po_no_cond="and b.booking_no='$po_ids'";else  $po_no_cond="";
	//  $sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d,wo_pre_cost_fab_conv_cost_dtls e where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=e.id and e.fabric_description=c.id and  c.job_no=e.job_no and b.job_no=e.job_no  and c.body_part_id=d.id  and b.po_break_down_id in($po_id) and d.body_part_type in(40,50) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1"); 
	
	    $sql="select e.item_size,e.body_part_id,e.po_break_down_id,sum(e.wo_qnty) as wo_qnty,f.size_number_id,f.color_number_id,f.color_order,f.size_order,sum(f.plan_cut_qnty) as plan_cut_qnty,g.po_number from wo_po_color_size_breakdown f join (select b.job_no,b.po_break_down_id,b.wo_qnty,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight,d.color_number_id,d.gmts_sizes ,d.dia_width,d.item_size from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls d,wo_pre_cost_fab_conv_cost_dtls g where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=g.id and c.id=d.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and d.color_number_id=b.gmts_color_id and g.fabric_description=c.id  and c.id ='$fabric_cost_id' and b.po_break_down_id in($po_ids)  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1) e on e.job_no=f.job_no_mst and e.po_break_down_id=f.po_break_down_id and e.color_number_id=f.color_number_id and e.gmts_sizes=f.size_number_id   and f.status_active=1 and f.is_deleted=0 join wo_po_break_down g on g.id=f.po_break_down_id and g.job_no_mst=f.job_no_mst group by e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,g.po_number  order by f.color_order,f.size_order";
	  
	  $sql_req=sql_select("select c.id as pre_fab_id,b.job_no,b.po_break_down_id as po_id,d.item_size,d.color_number_id,(b.grey_fab_qnty) as grey_fab_qnty,d.gmts_sizes,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=c.id and d.color_size_table_id=b.color_size_table_id   and b.po_break_down_id=d.po_break_down_id and b.booking_type=1  and c.id ='$fabric_cost_id' and b.po_break_down_id in($po_ids)  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ");
	  foreach($sql_req as $row){
		$gmts_qty=array_sum($req_qty_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('pre_fab_id')]][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('item_size')]]);
		if($gmts_qty>0)
		{   
		$book_data_ReqQty[$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('item_size')]]['req_qty']+=$gmts_qty;
		//echo $gmts_qty.'d';
		}
		//$book_data_ReqQty[$row[csf('po_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['item_number_id']=$row[csf('item_number_id')];
	  }
	 // $ddd="select ";
	$sqldata=sql_select($sql);
	foreach($sqldata as $row)
	{
		$collor_cuff_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('item_size')]]['poid'].=$row[csf('po_break_down_id')].',';
		//echo $row[csf('po_break_down_id')].'-';
	}
		?>
        <input style="width:125px;" type="hidden" class="text_boxes"  name="txt_body_part" id="txt_body_part" value="<? echo $bodyPart;  ?>" />
        <input style="width:125px;" type="hidden" class="text_boxes"  name="txt_job" id="txt_job" value="<? echo $job_no;  ?>" />

            <table width="550" class="rpt_table" border="1" rules="all" id="colar_cuff_tbl">
                <thead>
                    <tr>
                       <!-- <th width="130">PO Number</th>-->
                        <th>Gmts Color</th>
                        <th>Gmts Size</th>
                        <th>Item Size</th>
                        <th>Req Qty.</th>
                        <th><? echo $body_part[$bodyPart]; ?> Qty Pcs</th>
                    </tr>
                </thead>
                <tbody>
            <?
			    $i=1;
				foreach($collor_cuff_arr as $colorid=>$colorData){
					foreach($colorData as $gmtsizeid=>$gmtData){
						foreach($gmtData as $itemsize=>$row){
							$poid=rtrim($row['poid'],",");
							$poidArr=array_unique(explode(",",$poid));
					$gmts_qty=0;$wo_gmts_qty=0;	$book_gmts_qty=0;
					foreach($poidArr as $pid)
					{
					 $gmts_qty+=$book_data_ReqQty[$pid][$colorid][$gmtsizeid][$itemsize]['req_qty'];
					}
					$wo_gmts_qty= $book_data[$colorid][$gmtsizeid][$itemsize]['gmts_qty'];
					$book_gmts_qty= $book_data[$colorid][$gmtsizeid][$itemsize]['qty'];
					// echo $wo_gmts_qty.'d';
					
					
			?>
                <tr>
               
                <td>
                 <input style="width:125px;" type="hidden" class="text_boxes"  name="po_number_<? echo $i ?>" id="po_number_<? echo $i ?>" value="<? //echo $row[csf('po_number')]; ?>" readonly />
				 <? $dtls_id=$book_data[$colorid][$gmtsizeid][$itemsize]['dtls_id'];?>
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="po_id_<? echo $i ?>" id="po_id_<? echo $i ?>" value="<? //echo $row[csf('po_break_down_id')];  ?>"  /> <input style="width:30px;" type="hidden" class="text_boxes"  name="update_dtls_id_<? echo $i ?>" id="update_dtls_id_<? echo $i ?>" value="<? echo $dtls_id;  ?>"  />
				  <input style="width:30px;" type="hidden" class="text_boxes"  name="body_part_type_id_<? echo $i ?>" id="body_part_type_id_<? echo $i ?>" value="<? echo $body_part_type;  ?>"  />
               <input style="width:60px;" type="text" class="text_boxes"  name="color_number_<? echo $i ?>" id="color_number_<? echo $i ?>" value="<? echo $color_library[$colorid];  ?>" readonly />
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="color_id_<? echo $i ?>" id="color_id_<? echo $i ?>" value="<? echo $colorid;  ?>" readonly  />
                </td>
                <td>
               	<input style="width:60px;" type="text" class="text_boxes"  name="size_number_<? echo $i ?>" id="size_number_<? echo $i ?>" value="<? echo $size_library[$gmtsizeid];  ?>" readonly />
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="size_id_<? echo $i ?>" id="size_id_<? echo $i ?>" value="<? echo $gmtsizeid;  ?>"  />
                </td>
                 <td>
               <input style="width:60px;" type="text" class="text_boxes"  name="item_size_<? echo $i ?>" id="item_size_<? echo $i ?>" value="<? echo $itemsize;  ?>"  readonly/>
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmts_qty_<? echo $i ?>" id="gmts_qty_<? echo $i ?>"  onChange="calculate_qty(<? echo $i; ?>,<? //echo $body_part_type ?>)" value="<? if($wo_gmts_qty){echo $wo_gmts_qty;}else{ echo $gmts_qty;} ?>" readonly  />
                </td>
                 
                <td>

                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="qty_<? echo $i ?>" id="qty_<? echo $i ?>" value="<? if($book_gmts_qty){echo $book_gmts_qty;}else{//echo $qty;
				} ?>"   />
                </td>
                </tr>

                <?
				$i++;
				if($dtls_id>0) $button_id=1;else $button_id=0;
						}
					}
				}
				//echo $button_id.'AAAAAAAAAAAA';
				?>
                </tbody>
                </table>
                <table>
                <tr>
                <td align="center"  class="button_container" colspan="6">
              <?
			  if(count($sql_data)>0)
			  {
				 echo load_submit_buttons( $permission, "fnc_colar_culff_dtls", 1,0,"",2);
			  }
			  else
			  {
			  	echo load_submit_buttons( $permission, "fnc_colar_culff_dtls", 0,0,"",2);
			  }
			   ?>
                </td>
                </tr>
            </table>
        <?
		exit();
}

if($action=='save_update_delete_colar_culff_dtls')
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  //Insert Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }

		 $booking=trim(str_replace("'", '', $booking_no));

		 $id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		 $field_array="id,booking_no,job_no,pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,qty,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $po_id="po_id_".$i;
			 $color_id="color_id_".$i;
			 $size_id="size_id_".$i;
			 $item_size="item_size_".$i;
			 $gmts_qty="gmts_qty_".$i;
			 $excess_per="excess_per_".$i;
			 $qty="qty_".$i;
			 $update_dtls_id="update_dtls_id_".$i;
			 $chk_qty=str_replace("'",'',$$qty);
			 if($chk_qty>0)
			 {
			  
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".trim($booking)."',".$txt_job.",".$cbo_fabric_part.",".$$color_id.",".$$size_id.",".$$item_size.",".$$gmts_qty.",".$$qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			 }
		 }
		 		// echo "10**insert into wo_booking_colar_culff_dtls (".$field_array.") values ".$data_array; die;
		$rID1=execute_query( "update  wo_booking_colar_culff_dtls set status_active=0,is_deleted=1  where  pre_cost_fabric_cost_dtls_id =$cbo_fabric_part",1);

		 $rID=sql_insert("wo_booking_colar_culff_dtls",$field_array,$data_array,0);
		 //echo "insert into wo_booking_colar_culff_dtls (".$field_array.") values ".$data_array;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".$booking_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$booking_no."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".$booking_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$booking_no."**".$rID;
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
		 $booking=trim(str_replace("'", '', $booking_no));
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
		disconnect($con);	die;
		}
		 $id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		$field_array_up="job_no*pre_cost_fabric_cost_dtls_id*gmts_color_id*size_number_id*item_size*gmts_qty*qty*updated_by*update_date*status_active*is_deleted";
		$field_array="id,booking_no,job_no,pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,qty,inserted_by,insert_date";
			$new_data =1;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $po_id="po_id_".$i;
			 $color_id="color_id_".$i;
			 $size_id="size_id_".$i;
			 $item_size="item_size_".$i;
			 $gmts_qty="gmts_qty_".$i;
			 $excess_per="excess_per_".$i;
			 $qty="qty_".$i;
			 $update_dtls_id="update_dtls_id_".$i;
			 if(str_replace("'",'',$$update_dtls_id)>0)
			 {
				$updateID_array[]=str_replace("'",'',$$update_dtls_id);
				$data_array_up[str_replace("'",'',$$update_dtls_id)]=explode("*",("".$txt_job."*".$cbo_fabric_part."*".$$color_id."*".$$size_id."*".$$item_size."*".$$gmts_qty."*".$$qty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
			}
			else
			{
				if ($new_data!=1) $data_array .=",";
				$data_array .="(".$id.",'".trim($booking)."',".$txt_job.",".$cbo_fabric_part.",".$$color_id.",".$$size_id.",".$$item_size.",".$$gmts_qty.",".$$qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id=$id+1;
				$new_data++;
			}
		 }
		 $flag=1;
		 if(count($data_array_up)>0){
		 	$rID=execute_query(bulk_update_sql_statement("wo_booking_colar_culff_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
		 	if($rID) $flag=1; else $flag=0;
		 }
		 
		 if($data_array!='')
		 {
		  $rID1=sql_insert("wo_booking_colar_culff_dtls",$field_array,$data_array,0);
		  //echo "10** Insert into wo_booking_colar_culff_dtls ($field_array) values $data_array"; die;
		  if($flag==1)
			{
				if($rID1) $flag=1; else $flag=0;
			}
		 }

		//echo "10**".bulk_update_sql_statement("wo_booking_colar_culff_dtls","id",$field_array_up,$data_array_up,$updateID_array);die;

			//if($dtlsrID) $flag=1; else $flag=0;


	//============================================================================================

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$booking."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$booking."**".$flag;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$booking."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$booking."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		//$field_array="status_active*is_deleted";
		//$data_array="'2'*'1'";
		//$rID=sql_delete("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$hiddenid."",1);
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$rID=execute_query( "update  wo_booking_colar_culff_dtls set status_active=0,is_deleted=1  where  pre_cost_fabric_cost_dtls_id =$cbo_fabric_part and job_no=".$txt_job." ",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$txt_job."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}


}

if($action=="show_list_view")
{
	$FabricPart=array();
	$dataArr=explode("__",$data);
	$booking_no=$dataArr[0];
	$po_id=$dataArr[1];
	/*$sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id  and c.body_part_id=d.id  and b.po_break_down_id in($po_id) and d.body_part_type in(40,50)  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1");*/
	
	$sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no 
	  and c.body_part_id=d.id  and b.po_break_down_id in($po_id) and d.body_part_type in(40,50)  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1");
 	 

	  foreach($sql as $row){
		  $FabricPart[$row[csf('id')]]=$body_part[$row[csf('body_part_id')]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")].', '.$row[csf("gsm_weight")];
	  }

	  $sql_fab="select booking_no,job_no,pre_cost_fabric_cost_dtls_id,sum(gmts_qty) as gmts_qty,avg(excess_per) as excess_per,sum(qty) as qty  from wo_booking_colar_culff_dtls where booking_no ='$booking_no' and status_active=1 and is_deleted=0 group by booking_no,job_no,pre_cost_fabric_cost_dtls_id";
	$sql_data=sql_select($sql_fab);
	?>
	 <table width="550" class="rpt_table" border="1" rules="all">
            <thead>
            <tr>
                <th>
                Sl
                </th>
                <th>
                Job No
                </th>
                <th>
                Body Part
                </th>
                <th>
                Req. Qty 
                </th>
              
                <th>
                 Qty (Pcs)
                </th>
                </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($sql_data as $row){
				?>
                <tr onClick="show_sub_form_with_data('<? echo $row[csf('booking_no')]  ?>','<? echo $row[csf('pre_cost_fabric_cost_dtls_id')] ?>');">
                <td>
               <? echo $i; ?>
                </td>
                <td>
                 <? echo $row[csf('job_no')]; ?>
                </td>
                <td>
                <? echo $FabricPart[$row[csf('pre_cost_fabric_cost_dtls_id')]];  ?>
                </td>
                <td align="right">
                 <? echo $row[csf('gmts_qty')]; ?>
                </td>
               
                <td align="right">
                 <? echo $row[csf('qty')]; ?>
                </td>
                </tr>
                <?
				}
				?>
                </tbody>
                </table>
                <?


}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_short_type.'D';
	$cbo_short_type=str_replace("'","",$cbo_short_type);
?>
	<!-- <script>
	/*  var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		} */
		var selected_id = new Array, selected_name = new Array();
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
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
			//alert(str_data);
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			//alert(str_all[2]);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed');return;	
			}
			toggle( tr_id, '#FFFFCC');
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

		
    </script> -->

	<script>
		/* var selected_id = new Array, selected_name = new Array();
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		} */
		var selected_id = new Array, selected_name = new Array();
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
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

$job_no=return_field_value( "job_no", "wo_booking_mst","booking_no=$txt_fab_booking","job_no");

if($cbo_short_type==10) $th_show_hide="";
	else $th_show_hide="display:none";
	//echo $cbo_short_type.'d';

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
									<th width="80" style="<?=$th_show_hide;?>">Short Booking No</th>
                                    <th width="60">Ref No</th>
                                    <th width="130">Order No</th>
                                    <th width="60">Style No</th>
                                    <th width="60">File No</th>
									
                                    <th width="150">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
								 <td style="<?=$th_show_hide;?>"><input name="txt_booking_search"  id="txt_booking_search" class="text_boxes" style="width:80px"></td>
                                 <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
                                 <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:60px"></td>
                                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
								
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="<? //echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="<? //echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $txt_booking_date ?>+'_'+<? echo "'$job_no'" ?>+'_'+document.getElementById('txt_booking_search').value+'_'+'<?=$cbo_short_type;?>', 'create_po_search_list_view', 'search_div', 'service_booking_dyeing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
                            </tr>
                            <tr>
                                <td  align="center"  valign="top" colspan="4">
                                    <? //echo load_month_buttons();  ?>
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
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
                        <td colspan="11" align="center">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All 
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
	$short_booking=$data[12];	
	$booking_type=$data[13];	
	if ($job_no!="") $job_no_cond=" and a.job_no='$job_no' "; else  $job_no_cond=""; 
	if ($short_booking!="") $booking_no_cond=" and c.booking_no like '%$short_booking%' "; else  $booking_no_cond=""; 
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


	

	$arr=array (2=>$comp,3=>$buyer_arr);
		


	if($booking_type==0)
	{
		if ($data[2]==0)
		{
			$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1  and b.shiping_status not in(3)  $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $job_no_cond order by a.job_no";  
		//	echo $sql;

			echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,File No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,60,120,60,90,120,70,80","1020","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,1,0,1,3','','');
		}
		else
		{
			$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_no_cond order by a.job_no";
			
			//echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
		}
	}
	else{ //is short
		if($data[2]==0)
		{
			$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no,c.booking_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.id=b.job_id and a.job_no=c.job_no and b.id=c.po_break_down_id and c.is_short=1 and c.booking_type=1 and  c.status_active=1 and c.status_active=1 and a.status_active=1 and b.status_active=1  and b.shiping_status not in(3) $booking_no_cond  $approval_cond $shipment_date $company $buyer $job_cond $order_cond $ref_cond $style_ref_cond $file_no_cond $job_no_cond group by a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no,a.insert_date,c.booking_no order by a.job_no";  
		  // 	echo $sql;

			echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Ref No,Style Ref. No,Short Booking,File No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,60,120,100,60,90,120,70,80","1120","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,grouping,style_ref_no,booking_no,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,0,1,0,1,3','','');
		}
		else
		{
			$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_no_cond order by a.job_no";
			
			//echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
		}
	}
	
		 die;
		?>
<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
				<th width="60">Job No</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="80">Ref. No</th>
                <th width="80">File No</th>
                <th width="80">Job Qty </th>
                <th width="100">PO number</th>
                <th width="80">PO Qty</th>
                <th width="80">Shipment Date</th>

               
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:820px" >
        <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
            <tbody>
            <?
            $sl=1;
            $result_data=sql_select($sql);
            foreach($result_data as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//job_quantity,po_number,po_quantity,shipment_date
				//id,po_number,job_no
				?>

				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo  $row[csf("id")].'_'.$row[csf("po_number")].'_'.$row[csf("job_no")];?>','<?=$sl;?>')" style="cursor:pointer">
                    <td width="30"><? echo $sl; ?></td>
                    <td width="60"><? echo $row[csf("job_no_prefix_num")];?></td>
                    <td width="100"><? echo $comp[$row[csf("company_name")]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_name")]];?></td>
                    <td width="80"><? echo $row[csf("grouping")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("file_no")]; ?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("job_quantity")];?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("po_number")];?></td>
					<td width="80" style="word-break:break-all"><? echo $row[csf("po_quantity")];?> </td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("shipment_date")];?></td>
                    
                   
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('order_no_id').value+'_'+<?="'$job_no'";?>, 'create_booking_search_list_view2', 'search_div', 'service_booking_dyeing_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$sql="select min(a.id) as id, a.booking_no_prefix_num, a.pay_mode,b.job_no, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond $job_no_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type in (1,4)  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $po_ids_cond and a.entry_form=232 group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no,b.job_no order by id DESC";
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
	$dataArr=explode("_",$data);
	$poid=$dataArr[0];
	$bookingType=$dataArr[1];
	 
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,c.booking_no from wo_po_break_down b,wo_po_details_master a left join wo_booking_mst c on a.job_no=c.job_no and c.booking_type=3 and c.entry_form=232 and c.status_active=1 and c.is_deleted=0 where b.id in (".$poid.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		$job_noStr=$row[csf("job_no")].'__'.$bookingType;
		echo "load_drop_down( 'requires/service_booking_dyeing_controller', '".$job_noStr."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_dyeing_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if ($action=="load_drop_down_fabric_description")
{

	$data=explode("_",$data);
	$bookingType=$data[2];
	$fabric_description_array=array();
	//$process_id="31,25,26,31,32,33,34,36,37,38,39,40,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,77,80,81,82,83,84,85,86,87,88,89,90,92,93,94,135,136,137,138,140,141,142,143,144,146,147,148,149,150,155,156,158,159,160,161,162,163,427";
	$process_id=$selected_dyeing_process_id;
	if($bookingType==0) 
	{
		if($data[1] =="")
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and cons_process in($process_id) and status_active=1 and is_deleted=0 ");
		}
		else
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  
			where job_no='$data[0]' and status_active=1 and is_deleted=0 and cons_process in($process_id)  ");
		}
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select distinct a.id,c.id as fabric_description from wo_pre_cost_fabric_cost_dtls c,WO_BOOKING_DTLS b,
		wo_pre_cost_fab_conv_cost_dtls a where 
		 b.pre_cost_fabric_cost_dtls_id=c.id and b.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and c.job_no in('$data[0]')  and b.is_short=1
		 and a.cons_process=31 and b.booking_type=1
		and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0   and a.status_active=1 and a.is_deleted=0  ");
		if(count($wo_pre_cost_fab_conv_cost_dtls_id)<=0)
		{
			echo "<div><b> No Conversation Description Found.</b> </div>";
		}
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
	$type=$data[1];
	$process=$data[3]; 
	$sensitivityId=$data[4];
	$job_po_id=$data[5];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
	$short_type=$data[9];
	// echo $short_type.'D';
	$fabric_description_array_empty=array();
	$fabric_description_array=array();
	$po_number=return_library_array( "select id,po_number from wo_po_break_down where job_no_mst='$job_no'", "id", "po_number"  );
	
	$fabric_description_array=array();// 
	$wo_pre_cost_fab_co_color_sql=sql_select("select gmts_color_id,contrast_color_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls  where job_no='$job_no'");
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	if($short_type==0)
	{
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$job_no' and status_active=1");
	}
	else
	{
		$short_cond=" and b.is_short=1";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,a.id,a.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b,wo_booking_dtls d,
	wo_pre_cost_fab_conv_cost_dtls a where 
	 c.job_id=b.job_id and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and d.is_short=1 and d.booking_type=1  and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 
	 and d.status_active=1 and d.is_deleted=0 group by c.job_no,a.id,a.fabric_description");
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
	$sql_wo=sql_select("select job_no,currency_id,booking_date,company_id from wo_booking_mst where  job_no='$job_no' and booking_no='$txt_booking_no' and status_active=1" );
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
	
	 $condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("='$job_no'");
		}

		$condition->init();
		
		$conversion= new conversion($condition);
	//	echo $conversion->getQuery(); die;
		 // echo $job_no.'ddd';
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
		//print_r($conversion_knit_qty_arr);
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
	  $sql_data_charge_unit=sql_select("SELECT c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,c.charge_unit from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3  and b.job_no='$job_no'  and b.process=$process  and b.booking_no='$booking_no' group by b.job_no, c.id, b.po_break_down_id, b.sensitivity, b.uom, b.gmts_color_id, b.fabric_color_id, b.gmts_size, c.charge_unit"); 
	   
	 foreach ($sql_data_charge_unit as $row) {
	 	//$po_fab_con_charge_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['rate']=$row[csf('charge_unit')];
		$po_fab_pre_cost_rate_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['pre_rate']=$row[csf('charge_unit')];
	 }
	 
	 if($short_type==0) // Only For Booking type Select
	 {

	 
	   $sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,c.charge_unit from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b,wo_booking_mst a where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3  and b.job_no='$job_no'  and b.process=$process $booking_cond group by b.job_no,c.id,c.charge_unit,b.po_break_down_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size";
	 
		$dataResultPre=sql_select($sql_data_Priv);
		$po_fab_prev_booking_arr=array();
		foreach($dataResultPre as $row)
		{
			$po_fab_prev_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['wo_qty']+=$row[csf('wo_qnty')];
			$po_fab_prev_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]]['amount']=$row[csf('amount')];
			if($type==1)
			{
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
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
	if($short_type==10) // Only For Booking type Short
	{
	    $sql_data_short_fab="select c.id as fab_conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,b.gmts_size,
		(b.grey_fab_qnty) as grey_fab_qnty,(b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  b.pre_cost_fabric_cost_dtls_id=a.id 
		and b.job_no=a.job_no  and b.pre_cost_fabric_cost_dtls_id=c.fabric_description and a.id=c.fabric_description and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1 and b.po_break_down_id in($job_po_id) $short_cond";
		$dataResultFabShort=sql_select($sql_data_short_fab);
		foreach($dataResultFabShort as $row)
		{
			if($sensitivityId==1 || $sensitivityId==3)
			{
			$shortFabQtyArr[$row[csf("po_id")]][$row[csf("fab_conv_dtl_id")]][$row[csf("gmts_color_id")]]+=$row[csf("grey_fab_qnty")];
			}
			else if($sensitivityId==0 || $sensitivityId==2)
			{
			$shortFabQtyArr[$row[csf("po_id")]][$row[csf("fab_conv_dtl_id")]][$row[csf("gmts_size")]]+=$row[csf("grey_fab_qnty")];
			}
			else if($sensitivityId==4)
			{
			$shortFabQtyArr[$row[csf("po_id")]][$row[csf("fab_conv_dtl_id")]][$row[csf("gmts_color_id")]][$row[csf("gmts_size")]]+=$row[csf("grey_fab_qnty")];
			}
		}
	}
		
	 //   print_r($shortFabQtyArr);
	$tot_prev_wo_qty=0;
	if($type==0)
	{
			
	  	$sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.fin_fab_qnty,a.process_loss_percent as process_loss,a.mc_dia,a.brand,a.lot_no,a.yarn_count,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.color_range,a.shade_per,a.dia_type,a.remark as remarks,a.sensitivity,a.wo_qnty,a.rate, a.amount, b.size_number_id, b.color_number_id, a.dia_width,a.labdip_no,a.fin_gsm,a.lib_composition,a.lib_supplier_rate_id from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id and a.job_no='$job_no' and a.booking_type=3 and a.process=$process and   a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and   a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 ";
	//echo $sql;
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
	
		
				
	//$row[csf("wo_qnty")]=$row[csf("wo_qnty")]-$prev_wo_qty;
	//$row[csf("amount")]=$row[csf("amount")]-$prev_wo_amount;
	$prev_wo_amount=$po_fab_prev_booking_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['amount'];
	$prev_wo_qty=$po_fab_prev_booking_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['wo_qty'];
	if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;
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
				<table class="rpt_table" border="1"    width="1940" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Color Range</th>
						<th>Shade %</th>
						<th>Gmts.Size</th>
						<th>Item Size</th>
                        <th>Fab. Mapping</th>
                        <th>UOM</th>
						<th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Fin Dia</th>
						<th>Fin Type</th>
                        <th>Fin GSM</th>
                        <th>Y.Count</th>
                        <th>Labdip No</th>
                         
                        <th>Lot</th>
                        <th>Brand</th>
                        <th>M/C Dia</th>
                        
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                      
                        <th>Amount</th>
                        <th>P. Loss</th>
                        <th>Req.Fin Qty</th>
                        <th>Plan Cut Qnty</th>
                        <th>Remark</th>
					</thead>
		   <?
		  }
		
					if($sensitivity==1 || $sensitivity==3)
					{
						$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]]);
						$wo_prev_qnty=$po_fab_prev_color_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]]['wo_qnty'];
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
					if($short_type==10)
					{
						$poid=$row[csf('po_break_down_id')];
						if($sensitivity==1 || $sensitivity==3)
						{
						$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("color_number_id")]];
						//echo $pre_req_qnty.'='.$fabric_description_id.'='.$row[csf("color_number_id")].'<br>';
						}
						if($sensitivity==0 || $sensitivity==2)
						{
						$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("size_number_id")]];
						}
						if($sensitivity==4)
						{
						$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("color_number_id")]][$row[csf("size_number_id")]];
						}
					}
					
			//echo $row[csf('po_break_down_id')].'='.$fabric_description_id.',';
			$pre_rate=$po_fab_pre_cost_rate_arr[$row[csf('po_break_down_id')]][$fabric_description_id]['pre_rate'];
			if($currency_id==1)
			{
				$pre_rate=$pre_rate*$currency_rate;
				
				//$row[csf("amount")]=$pre_req_qnty*($row[csf("rate")]*$currency_rate);
				//$row[csf("rate")]=($row[csf("rate")]*$currency_rate);
			}
			//echo "AAA";

        ?>
                <tbody id="table_search">
					<?
					if($row[csf("wo_qnty")]>0){?>
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
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("artwork_no")]; ?>"  onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" style="width:70px;" class="text_boxes">
							</td>
							
							
							<td> 
                             <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
								<input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" disabled="disabled" /> 
								<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>" disabled="disabled" />
							</td>
							<td>
								<input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("fabric_color_id")]];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("fabric_color_id")];} else { echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								 
								<?
								echo create_drop_down("cbo_color_range_".$fabric_description_id."_".$i, 60, $color_range,"", 1,'', $row[csf("color_range")],"","");
								?>
                                
							</td>
							<td>
								<input type="text" name="item_shade_per_<? echo $fabric_description_id.'_'.$i; ?>" id="item_shade_per_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()"  value="<?=$row[csf("shade_per")];?>"/>
                                 
							</td>

							<td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled"/>
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $row[csf("item_size")];} else{ echo "";} //echo $row[csf("item_size")]; ;?>">
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
							<td title="<? echo 'Prev Wo Qty='.$prev_wo_qty;?>">
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty'); calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($row[csf("wo_qnty")],4,'.',''); ?>"/>
								 <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,4,'.','');?>" />
                                <input type="hidden" name="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? echo number_format($pre_req_qnty,4,'.',''); ?>"/>
								
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" pre-cost-rate="<? echo $pre_rate; ?>" value="<? echo $row[csf("rate")]; ?>" <?php echo $rate_disable; ?>>
							
							</td>

                            <td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("dia_width")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							<td>
								 
								<?
								echo create_drop_down("cbo_dia_type_".$fabric_description_id."_".$i, 60, $fabric_typee,"", 1,"", $row[csf("dia_type")],"","");
								?>
                                
							</td>
							<td>
								<input type="text" name="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("fin_gsm")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','fingsm_')" class="text_boxes">
							</td>
							<td>
								<input type="text" name="ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("yarn_count")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','ycount_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" id="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("labdip_no")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdipno_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="ylot_<? echo $fabric_description_id.'_'.$i; ?>" id="ylot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("lot_no")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','ylot_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="brand_<? echo $fabric_description_id.'_'.$i; ?>" id="brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("brand")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','brand_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("mc_dia")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','mcdia_')" class="text_boxes">
							</td>
                            
                            <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-'); ?>" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" style="width:70px;" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_end_date")],'dd-mm-yyyy','-'); ?>" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" style="width:70px;" class="datepicker">
							</td>
                            
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($row[csf("amount")],4,'.',''); ?>" disabled="disabled">
							</td>
                            <td>
								<input type="text" name="txt_prodess_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prodess_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_prodess_loss_');calculate_fin_req(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($row[csf("process_loss")],4,'.',''); ?>"/>
								
							</td>
                             <td title="<? echo 'Wo Qty-(Wo Qty*Process Loss%)';?>">
								<input type="text" name="txt_req_fin_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_req_fin_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo number_format($row[csf("fin_fab_qnty")],4,'.',''); ?>" />
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td><input type="text" name="txt_remark_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_remark_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("remarks")]; ?>" style="width:100px;"  class="text_boxes"></td>
						</tr>
						<? $i++;
		$z++;
		$tot_wo_qnty+=$row[csf("wo_qnty")];
		$tot_amount+=$row[csf("amount")];
		$tot_finFabQnty+=$row[csf("fin_fab_qnty")];}?>
						
                </tbody>
		<?
		
	}
	if($z>1)
	{
	?>
			 
					 <tfoot>
							<tr>
							<th colspan="11" align="right"> Total</th>  
							<th><input type="text" align="right" class="text_boxes_numeric" style="width:60px;"  id="tot_wo_qnty" value="<?=number_format($tot_wo_qnty,4,'.','');;?>"disabled></th>  
							<th colspan="11" align="right">&nbsp;  </th>
							
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:50px;"  disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:60px;"  id="tot_amount" value="<?=number_format($tot_amount,4,'.','');;?>"disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:50px;" disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:60px;" id="tot_finFabQnty" value="<?=number_format($tot_finFabQnty,4,'.','');;?>"disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:70px;" disabled></th>
							</tr>
						</tfoot>
			</table>
		
		</div>
		 
		
	<?
	}
	} //Update end
	
	if($type==1) // save start here
	{
		
		$fabric_description_id=$data[2];
		$process=$data[3];
		$sensitivity=$data[4];
		$txt_order_no_id=$data[5];
		//e.cons_process=$process
		//echo $fabric_description_id.'ss';
		if($sensitivity==0)
		{
			$groupby="group by b.id,b.po_number";
		    $sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnt ,
			d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per ,
			g.gmts_sizes,g.item_size,CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN 
			round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
			round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
			
			from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no 
			and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and 
			c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and 
			f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no' and e.id in($fabric_description_id) and b.id 
			in($txt_order_no_id)  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
			c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 
			
			group by b.id,b.po_number,d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,
			f.costing_per ,g.gmts_sizes,g.item_size";
			
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
		//echo $sql1;die;
		$prev_wo_qty=$po_fab_prev_booking_arr2[$fabric_description_id]['wo_qty'];
		if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;

		 

		?>
			
            
			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close">
            
				<table class="rpt_table" border="1" width="1740" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Color Range</th>
						<th>Shade %</th>
						<th>Gmts.Size</th>
						<th>Item Size</th>
                        <th>Fab. Mapping</th>
                        <th>UOM</th>
						<th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Fin Dia</th>
						<th>Dia Type</th>
                        <th>Fin GSM</th>
                        <th>Y.Count</th>
                        <th>Labdip No</th>
                        <th>Lot</th>
                        <th>Brand</th>
                        <th>M/C Dia</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        
                        <th>Amount</th>
                        <th>P. Loss</th>
                        <th>Req. Fin Qty</th>
                        <th>Plan Cut Qnty</th>
						<th>Remarks</th>
					</thead>
					<tbody  id="table_search">
					<?
					// echo "document.getElementById('hide_fabric_description').value = '".$fabric_description_id."';\n";
					 // echo $sql1;
					
					$dataArray=sql_select($sql1);
					if(count($dataArray)==0)
					{
					$dataArray=sql_select($sql2);
					}
					$i=1;
					//print_r($dataArray);
					foreach($dataArray as $row)
					{
						
						
						
						if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
							{
								$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]]);
								$wo_prev_qnty=$po_fab_prev_color_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]]['wo_qnty'];
							}
							else if($sensitivity==4) // AS Per Color and Size
							{
								$pre_req_qnty=array_sum($conversion_color_size_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]);
								$wo_prev_qnty=$po_fab_prev_color_size_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf('color_number_id')]][$row[csf("size_number_id")]]['wo_qnty'];
							}
							else if($sensitivity==2 || $sensitivity==0) // AS Per Size or Select
							{
								$pre_req_qnty=array_sum($conversion_po_size_knit_qty_arr[$fabric_description_id][$row[csf("po_break_down_id")]][$row[csf("size_number_id")]]);
								$wo_prev_qnty=$po_fab_prev_size_booking_arr[$row[csf('po_break_down_id')]][$fabric_description_id][$row[csf("size_number_id")]]['wo_qnty'];
							}
							if($short_type==10)
							{
								$poid=$row[csf('po_break_down_id')];
								if($sensitivity==1 || $sensitivity==3)
								{
								$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("color_number_id")]];
								//echo $pre_req_qnty.'='.$fabric_description_id.'='.$row[csf("color_number_id")].'<br>';
								}
								if($sensitivity==0 || $sensitivity==2)
								{
								$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("size_number_id")]];
								}
								if($sensitivity==4)
								{
								$pre_req_qnty=$shortFabQtyArr[$poid][$fabric_description_id][$row[csf("color_number_id")]][$row[csf("size_number_id")]];
								}
							}

							
						if($row[csf("body_part_id")]==3)
						{
							$woqnty=$pre_req_qnty;
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
						    $rate="";
							$amount="";	
						}
						else
						{
							
							$bal_woqnty=$woqnty-$wo_prev_qnty;

							$color_break_down_rate=$row[csf("color_break_down")];
							$color_break_down_rate=explode("__",$row[csf("color_break_down")]);
							foreach($color_break_down_rate as $rcid)
							{
								$rate_down=explode("_",$rcid);
								$gmts_color=$rate_down[0];
								$rate_count=$rate_down[1];
								$item_rate_arr[$fabric_description_id][$gmts_color]=$rate_count;
							}
							if($sensitivity==1)
							{
								$rate=$item_rate_arr[$fabric_description_id][$row[csf("color_number_id")]];
							}else{
								$rate=$row[csf("charge_unit")];
							}
							
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
							$item_color_arr[$fabric_description_id][$gmt_color]=$fab_color;
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
							$itemColor=$color_library[$item_color_arr[$fabric_description_id][$row[csf("color_number_id")]]];
							$item_color_id=$item_color_arr[$fabric_description_id][$row[csf("color_number_id")]];
						}
						
						$woqnty=$woqnty;
						$amount=$amount;
						if($woqnty<=0)
						{
							$td_color='#FF0000';
						}
						else
						{
							$td_color='';
						}//woqnty-$wo_prev_qnty
				 	 //echo $woqnty.'-'.$wo_prev_qnty.',';
						
						if($bal_woqnty>0)
						 {
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
								<input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','artworkno_')" class="text_boxes">
							</td>
							<td>
                            <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[csf("color_size_table_id")];?>" disabled="disabled"/>
                            
								<input type="hidden" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>" disabled="disabled"/> 
								<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$row[csf("color_number_id")] ];} else { echo "";}?>
                                <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $row[csf("color_number_id")];} else { echo "";}?>"disabled="disabled"/>
							</td>
							<td>
								 <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $itemColor;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $color_library[$itemColor];} else { echo "";}?>"/>
                                <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id;//if($sensitivity==1 || $sensitivity==3  || $sensitivity==4 ){ echo $itemColor;} else { echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_color_range_".$fabric_description_id."_".$i, 60, $color_range,"", 1,'--Select--', "","","");
								?>
							</td>
							<td>
								<input type="text" name="item_shade_per_<? echo $fabric_description_id.'_'.$i; ?>" id="item_shade_per_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value()" value="" />
                                 
							</td>

							<td>
								<input type="text" name="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>" disabled="disabled" />
                                <input type="hidden" name="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_size_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==2 || $sensitivity==4 ){echo $row[csf("size_number_id")];} else{ echo "";}?>" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','item_size_')" value="<? if($sensitivity==2 || $sensitivity==4 ){ echo $size_library[$row[csf("size_number_id")]];} else{ echo "";}?>">
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
							<td   title="<? echo 'Prev Wo Qty='.$wo_prev_qnty;?>">
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px; background:<? echo $td_color;?>" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,4,'.',''); ?>"/>
								 
								 <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,4,'.',''); ?>" />
                                <input type="hidden" name="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqwoqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:30px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,4,'.',''); ?>"/>
								
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" pre-cost-rate="<? echo $rate; ?>" value="<? echo $rate; ?>" <?php echo $rate_disable; ?> placeholder="<?=number_format($rate,4,'.','');?>">
							</td>

                             <td>
								<input type="text" name="findia_<? echo $fabric_description_id.'_'.$i; ?>" id="findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','findia_')" class="text_boxes">
							</td>
							<td>
								 
								<?
								echo create_drop_down("cbo_dia_type_".$fabric_description_id."_".$i, 60, $fabric_typee,"", 1,"--Select--", "","","","1,2");
								?>
                                
							</td>

							<td>
								<input type="text" name="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','fingsm_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("yarn_count")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','ycount_')" class="text_boxes">
							</td>
                            
							<td>
								<input type="text" name="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" id="labdipno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdipno_')" class="text_boxes">
							</td>
                             <td>
								<input type="text" name="ylot_<? echo $fabric_description_id.'_'.$i; ?>" id="ylot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("lot_no")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','ylot_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="brand_<? echo $fabric_description_id.'_'.$i; ?>" id="brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("brand")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','brand_')" class="text_boxes">
							</td>
                            <td>
								<input type="text" name="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("mc_dia")]; ?>" style="width:70px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','mcdia_')" class="text_boxes">
							</td>
                            
                            <td>
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','startdate_')" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','enddate_')" class="datepicker">
							</td>
                            
                            <td>
								<input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($amount,4,'.',''); ?>" disabled="disabled"/>
							</td>
                             <td>
								<input type="text" name="txt_prodess_loss_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prodess_loss_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_prodess_loss_');calculate_fin_req(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? //echo number_format($row[csf("process_loss")],4,'.',''); ?>"/>
								
							</td>
                             <td title="<? echo 'Wo Qty-(Wo Qty*Process Loss%)';?>">
								<input type="text" name="txt_req_fin_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_req_fin_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" value="<?  echo number_format($bal_woqnty,4,'.',''); ?>" readonly />
							</td>
                            <td>
								<input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
							</td>
							<td><input type="text" name="txt_remark_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_remark_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("remarks")]; ?>" style="width:100px;" onBlur="copy_value('<? echo $fabric_description_id; ?>','<? echo $i; ?>','labdipno_')" class="text_boxes"></td>
						</tr>
					<?	
						$i++;
						$total_wo_qnty+=$bal_woqnty;
						$total_amount+=$amount;
						$total_finFabQnty="";
						}
					}
					?>
					</tbody>
					   <tfoot>
							<tr>
							<th colspan="11" align="right"> Total</th>  
							<th><input type="text" align="right" class="text_boxes_numeric" style="width:60px;"  id="tot_wo_qnty" value="<?=number_format($total_wo_qnty,4,'.','');;?>"disabled></th>  
							<th colspan="10" align="right">&nbsp;  </th>
								 
								<th> </th>
								<th><input type="text" align="right" class="text_boxes_numeric"  id="tot_amount" style="width:60px;" value="<?=number_format($total_amount,4,'.','');?>"disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:50px;" disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" id="tot_finFabQnty"  style="width:60px;" value="<?=number_format($total_finFabQnty,4,'.','');?>"disabled></th>
								<th><input type="text" align="right" class="text_boxes_numeric" style="width:70px;" disabled></th>
								<th> </th>
							</tr>
						</tfoot>
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
	  $sql="select a.id, a.job_no,b.booking_no,$group_concat,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,b.process,b.sensitivity,sum(b.wo_qnty) as wo_qnty,b.insert_date from wo_booking_dtls b, wo_booking_mst a where b.booking_no=a.booking_no and a.booking_no='$data[1]'and a.job_no='$data[0]' and a.entry_form=232 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.job_no,a.id,b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date";

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
			if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		    $response_booking_no="";
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DSB', date("Y",time()), 5,"select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and entry_form=232 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
			
			
			$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
			$field_array="id, booking_type, booking_month,is_short, booking_year, booking_no_prefix, booking_no_prefix_num, booking_no, entry_form, company_id, buyer_id, job_no, po_break_down_id, item_category, supplier_id, currency_id, exchange_rate, booking_date, delivery_date, pay_mode, source, attention, tenor, process, material_id, tagged_booking_no,ready_to_approved, inserted_by, insert_date, status_active, is_deleted";
			$data_array ="(".$id.",3,".$cbo_booking_month.",".$cbo_short_type.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',232,".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$cbo_process.",".$cbo_material.",".$txt_fab_booking.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$response_booking_no=$new_booking_no[0];
			// echo "insert into wo_booking_mst($field_array)values".$data_array;die;
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
		 $is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 $field_array_up="booking_month*is_short*booking_year*buyer_id*job_no*po_break_down_id*
		 item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*tenor*material_id*tagged_booking_no*ready_to_approved*updated_by*update_date";
		 $data_array_up ="".$cbo_booking_month."*".$cbo_short_type."*".$cbo_booking_year."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_material."*".$txt_fab_booking."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
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
	 
	// print_r($process);
	// echo "</pre>";die;
	extract(check_magic_quote_gpc( $process )); 
	// echo "10**<pre>";die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_form_id, artwork_no, po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, item_size, gmts_size, description, uom, process, sensitivity, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_gsm, labdip_no,mc_dia,brand,lot_no,yarn_count, lib_composition, lib_supplier_rate_id,process_loss_percent,fin_fab_qnty,color_range,shade_per,dia_type,remark, inserted_by, insert_date, status_active, is_deleted";

		 $new_array_color=array();
		// echo "10**".$row_num; check_table_status( $_SESSION['menu_id'],0); die;
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
			 $fingsm="fingsm_".$hide_fabric_description."_".$i;
			 $labdipno="labdipno_".$hide_fabric_description."_".$i;

			 $cbo_dia_type="cbo_dia_type_".$hide_fabric_description."_".$i;
			 $item_shade_per="item_shade_per_".$hide_fabric_description."_".$i;
			 $cbo_color_range="cbo_color_range_".$hide_fabric_description."_".$i;
			 $txt_remark="txt_remark_".$hide_fabric_description."_".$i;

			
			$ylot="ylot_".$hide_fabric_description."_".$i;
			$ycount="ycount_".$hide_fabric_description."_".$i;
			$brand="brand_".$hide_fabric_description."_".$i;
			$mcdia="mcdia_".$hide_fabric_description."_".$i;
			
			$txt_prodess_loss="txt_prodess_loss_".$hide_fabric_description."_".$i;
			$txt_req_fin_woqnty="txt_req_fin_woqnty_".$hide_fabric_description."_".$i;
			//echo "10**".$row_num;  //die;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
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
			 
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$update_id.",".$$fabric_description_id.",232,".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$txt_job_no.",".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$findia.",".$$fingsm.",".$$labdipno.",".$$mcdia.",".$$brand.",".$$ycount.",".$$ylot.",".$$lib_composition.",".$$lib_supplier_rateId.",".$$txt_prodess_loss.",".$$txt_req_fin_woqnty.",".$$cbo_color_range.",".$$item_shade_per.",".$$cbo_dia_type.",".$$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		     $id_dtls=$id_dtls+1;
		 }
		//echo "10**".$data_array1; check_table_status( $_SESSION['menu_id'],0);die;
		//echo "10**insert into wo_booking_dtls($field_array1)values".$data_array1;check_table_status( $_SESSION['menu_id'],0);  die;
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
		 
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id*gmts_color_id*item_size*gmts_size*description*uom*process*sensitivity*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_gsm*labdip_no*mc_dia*brand*lot_no*yarn_count*lib_composition*lib_supplier_rate_id*process_loss_percent*fin_fab_qnty*color_range*shade_per*dia_type*remark*updated_by*update_date";
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
			 $cbo_dia_type="cbo_dia_type_".$hide_fabric_description."_".$i;
			 $item_shade_per="item_shade_per_".$hide_fabric_description."_".$i;
			 $cbo_color_range="cbo_color_range_".$hide_fabric_description."_".$i;
			 $txt_remark="txt_remark_".$hide_fabric_description."_".$i;


			 $findia="findia_".$hide_fabric_description."_".$i;
			 $fingsm="fingsm_".$hide_fabric_description."_".$i;
			 $labdipno="labdipno_".$hide_fabric_description."_".$i;
			 $ylot="ylot_".$hide_fabric_description."_".$i;
			$ycount="ycount_".$hide_fabric_description."_".$i;
			$brand="brand_".$hide_fabric_description."_".$i;
			$mcdia="mcdia_".$hide_fabric_description."_".$i;
			$txt_prodess_loss="txt_prodess_loss_".$hide_fabric_description."_".$i;
			$txt_req_fin_woqnty="txt_req_fin_woqnty_".$hide_fabric_description."_".$i;

			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
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
				$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$po_id."*".$$color_size_table_id."*".$txt_job_no."*".$txt_booking_no."*3*".$color_id."*".$$gmts_color_id."*".$$item_size."*".$$gmts_size_id."*".$$fabric_description_id."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$findia."*".$$fingsm."*".$$labdipno."*".$$mcdia."*".$$brand."*".$$ycount."*".$$ylot."*".$$lib_composition."*".$$lib_supplier_rateId."*".$$txt_prodess_loss."*".$$txt_req_fin_woqnty."*".$$cbo_color_range."*".$$item_shade_per."*".$$cbo_dia_type."*".$$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		
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
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive)."**".$rID;
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
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive)."**".$rID;
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
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_dyeing_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_file_no').value, 'create_booking_search_list_view', 'search_div', 'service_booking_dyeing_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:60px;" />
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
	/*$sql= "select a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
		a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id from wo_booking_mst a , wo_booking_dtls b 
		where $company $buyer $booking_date and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and  a.booking_no=b.booking_no 
		and b.status_active=1 and b.is_deleted=0 and b.process=31
		
		group by a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
		a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id
		order by booking_no";*/
	//$process_id="31,25,26,31,32,33,34,36,37,38,39,40,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,77,80,81,82,83,84,85,86,87,88,89,90,92,93,94,135,136,137,138,140,141,142,143,144,146,147,148,149,150,155,156,158,159,160,161,162,163";
	if($db_type==0) { $group_concat="group_concat(c.file_no) as file_no"; $group_concat.=",group_concat(c.file_no) as file_no";}
	if($db_type==2)
	 { $group_concat="listagg(cast(c.grouping as varchar2(4000)),',') within group (order by c.grouping) as grouping";
	   $group_concat.=",listagg(cast(c.file_no as varchar2(4000)),',') within group (order by c.file_no) as file_no";
	}
	$sql= "select a.id,a.process, a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.pay_mode, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num,$group_concat
	from wo_booking_mst a, wo_po_details_master b,wo_po_break_down c
	where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=3 and  a.status_active=1 and a.is_deleted=0  and a.process in($selected_dyeing_process_id) $sql_cond $ref_no_cond $file_no_cond
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
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]; ?>')" style="cursor:pointer;">
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

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
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
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            {  
								$plot=$result[csf('plot_no')];
								$city=$result[csf('city')];
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$job_no=""; $po_no=""; $file=''; $ref=$style_ref='';
		
		
		$sqljobBooking="select c.job_no, c.style_ref_no, c.buyer_name as buyer_id, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$po_no.=$result_job[csf('po_number')].",";
			$style_ref.=$result_job[csf('style_ref_no')].",";
			$file.=$result_job[csf('file_no')].",";
			$ref.=$result_job[csf('grouping')].",";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
		$file=implode(",",array_filter(array_unique(explode(',',$file))));
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));

        $nameArray=sql_select( "select a.booking_no,a.booking_date, a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.company_id  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$address="";
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) //In_house(3)_Within_group(5)
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
				$address=$plot.", ".$city;
				
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
				$address=$supplier_address_arr[$result[csf('supplier_id')]];
				
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="90" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="150">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="90" style="font-size:12px"><b>Booking Date</b></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Currency</b></td>
                <td width="150">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="90" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Source</b></td>
                <td  width="150" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="90" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="150">:&nbsp;<? echo $supplier_str;?>    </td>
                 <td width="90" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="150">:&nbsp;<? echo $address;?></td>
                <td  width="90" style="font-size:12px"><b>Attention</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="90" style="font-size:12px"><b>Job No</b>   </td>
                <td width="150">:&nbsp;<?=rtrim($job_no,','); ?></td>
                 
               	<td width="150" style="font-size:12px"><b>PO No</b> </td>
                <td width="90" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
            <tr>
           		<td style="font-size:12px"><b>Style</b> </td>
                <td>:&nbsp;<?=$style_ref; ?></td>
                <td width="90" style="font-size:12px"><b>File No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($file,','); ?></td>
                <td width="90" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($ref,','); ?></td> 
               	
            </tr> 
            <tr>
            	<td>
            		Buyer
            	</td>
            	<td colspan="5">:&nbsp;<?php echo $buyer_name_arr[$buyer_name]; ?></td>
            </tr>
        </table>  
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
	$nameArray_color_size_qnty=sql_select( "select process, sensitivity,description, fabric_color_id, wo_qnty as cons, rate,labdip_no,fin_gsm,dia_width,color_range,shade_per,dia_type from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity in(1,3)  and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	$color_size_qty_arr=array();
	foreach($nameArray_color_size_qnty  as $row)
	{
		if($row[csf('sensitivity')]==1)
		{
		$color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
		else
		{
		$contrast_color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
	}
	unset($nameArray_color_size_qnty);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
        $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and  wo_qnty!=0"); 
            
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                //$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('fabric_color_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
                $wo_qty=0;
				 $wo_qty=$color_size_qty_arr[$result_item[csf('process')]][$result_itemdescription[csf('description')]][$result_color[csf('fabric_color_id')]][$result_itemdescription[csf('rate')]][$result_itemdescription[csf('labdip_no')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('dia_width')]];
				  
				?>
					<td style="border:1px solid black; text-align:right">
					<? 
					if($wo_qty!= "")
					{
						echo number_format($wo_qty,2);
						$item_desctiption_total+=$wo_qty ;
						if (array_key_exists($wo_qty, $color_tatal))
						{
							$color_tatal[$result_color[csf('fabric_color_id')]]+=$wo_qty;
						}
						else
						{
							$color_tatal[$result_color[csf('fabric_color_id')]]+=$wo_qty; 
						}
					}
					else echo "";
					?>
					</td>
					<?   
					}
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
			<? } ?>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and dia_width='".$result_itemdescription[csf('dia_width')]."' and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."' and labdip_no='".$result_itemdescription[csf('labdip_no')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+11; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
	               /* $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and rate=".$result_itemdescription[csf('rate')]." and labdip_no=".$result_itemdescription[csf('labdip_no')]." and fin_gsm=".$result_itemdescription[csf('fin_gsm')]." and dia_width=".$result_itemdescription[csf('dia_width')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
	                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
	                */
		                  $wo_qty=0;
				 $wo_qty=$contrast_color_size_qty_arr[$result_item[csf('process')]][$result_itemdescription[csf('description')]][$result_color[csf('color_number_id')]][$result_itemdescription[csf('rate')]][$result_itemdescription[csf('labdip_no')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('dia_width')]];
				 
						?>
		                <td style="border:1px solid black; text-align:right">
		                <? 
		                if($wo_qty!= "")
		                {
		                echo number_format($wo_qty,2);
		                $item_desctiption_total += $wo_qty ;
		                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
		                {
		                $color_tatal[$result_color[csf('color_number_id')]]+=$wo_qty;
		                }
		                else
		                {
		                $color_tatal[$result_color[csf('color_number_id')]]=$wo_qty; 
		                }
		                }
		                else echo "0";
		                ?>
		                </td>
		                <?   
	             
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		 }
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3"); 

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+11; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>

                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
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
               		 $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."  and  dia_width='". $result_itemdescription[csf('dia_width')]."'  and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."'  and  labdip_no='". $result_itemdescription[csf('labdip_no')]."'  and  rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+11; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,dia_width,fin_gsm,labdip_no from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  dia_width='". $result_itemdescription[csf('dia_width')]."' and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."' and  labdip_no='". $result_itemdescription[csf('labdip_no')]."' and  rate='". $result_itemdescription[csf('rate')]."'  and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
	   
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
	   <? if ($show_comments!=1) { ?>
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;">
				<?
				$mcurrency="";
				$dcurrency="";
				$currency_id=$result[csf('currency_id')];
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
				$currency_name=$currency[$result[csf('currency_id')]];
				echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
				?></td>
            </tr>
			<? } ?>
       </table>
      	<br>
        <?
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
    <br><? if ($show_comments!=1) { ?>
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
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					$job_no=rtrim($job_no,',');
					$job_nos=implode(",",array_unique(explode(",",$job_no)));
					$condition= new condition();
					if(str_replace("'","",$job_nos) !=''){
					$condition->job_no("in('$job_nos')");
					}
					$condition->init();
					$conversion= new conversion($condition);
					//echo $conversion->getQuery();
					$convAmt=$conversion->getAmountArray_by_orderAndProcess();
					//print_r($convAmt);
					$po_qty_arr=array();$aop_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS aop_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=31 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];	
					}
					
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
				
					$sql_aop=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_aop=$pre_cost_dyeing=array_sum($convAmt[$selectResult[csf("po_id")]][31]);//($aop_data_arr[$selectResult[csf('job_no')]]['aop']/$costing_per_qty)*$po_qty;
						
	
						$wo_aop_charge=$selectResult[csf("amount")];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
						
						if($db_type==0)
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "Y-m-d", "-",1);
						}
						else
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "d-M-y", "-",1);
						}
						
						//echo $currency_rate;
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$aop_charge=$wo_aop_charge/$currency_rate;	
						}
						else
						{
							$aop_charge=$wo_aop_charge;
						}
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
                     <td style="border:1px solid black;" width="80" align="right">
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
        <? } ?>
         <br/>
        
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
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

if($action=="show_trim_booking_inhouse")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$buyer_code_arr=return_library_array( "select id,remark from lib_buyer",'id','remark');
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
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            {  
								$plot=$result[csf('plot_no')];
								$city=$result[csf('city')];
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$job_no=""; $po_no=""; $file=''; $ref=$style_ref='';
		
		
		$sqljobBooking="select c.job_no, c.style_ref_no, c.buyer_name as buyer_id, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.id=b.booking_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no and a.entry_form=232";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$po_no.=$result_job[csf('po_number')].",";
			$style_ref.=$result_job[csf('style_ref_no')].",";
			$file.=$result_job[csf('file_no')].",";
			$ref.=$result_job[csf('grouping')].",";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
		$file=implode(",",array_filter(array_unique(explode(',',$file))));
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));

        $nameArray=sql_select( "select a.booking_no,a.booking_date, a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.company_id  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=232"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$address="";
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) //In_house(3)_Within_group(5)
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
				$address=$plot.", ".$city;
				
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
				$address=$supplier_address_arr[$result[csf('supplier_id')]];
				
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="90" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="150">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="90" style="font-size:12px"><b>Booking Date</b></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Currency</b></td>
                <td width="150">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="90" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Source</b></td>
                <td  width="150" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="90" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="150">:&nbsp;<? echo $supplier_str;?>    </td>
                 <td width="90" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="150">:&nbsp;<? echo $address;?></td>
                <td  width="90" style="font-size:12px"><b>Attention</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="90" style="font-size:12px"><b>Job No</b>   </td>
                <td width="150">:&nbsp;<?=rtrim($job_no,','); ?></td>
                 
               	<td width="150" style="font-size:12px"><b>PO No</b> </td>
                <td width="90" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
            <tr>
           		<td style="font-size:12px"><b>Style</b> </td>
                <td>:&nbsp;<?=$style_ref; ?></td>
                <td width="90" style="font-size:12px"><b>File No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($file,','); ?></td>
                <td width="90" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($ref,','); ?></td> 
               	
            </tr> 
            <tr>
            	<td>
            		Buyer
            	</td>
            	<td colspan="5">:&nbsp;<?php echo $buyer_name_arr[$buyer_name]; ?></td>
            </tr>
        </table>  
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
	$nameArray_color_size_qnty=sql_select( "select process, sensitivity,description, fabric_color_id, wo_qnty as cons, rate,labdip_no,fin_gsm,dia_width,color_range,shade_per,dia_type from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity in(1,3)  and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	$color_size_qty_arr=array();
	foreach($nameArray_color_size_qnty  as $row)
	{
		if($row[csf('sensitivity')]==1)
		{
		$color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
		else
		{
		$contrast_color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
	}
	unset($nameArray_color_size_qnty);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
        $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="14" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black" align="center"><strong>item Color</strong> </td>
				<td style="border:1px solid black" align="center"><strong>Color Range</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,color_range,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,color_range,remark"); 
            
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $color_range[$result_itemdescription[csf('color_range')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="12"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;$sub_item_tot=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
					$sub_item_total=0;
                ?>

                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and dia_width='".$result_itemdescription[csf('dia_width')]."' and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."' and labdip_no='".$result_itemdescription[csf('labdip_no')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="13" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
			$nameArray_item_description=sql_select( "select dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark"); 
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		 }
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="14" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black"><strong>Item Color</strong> </td>
				<td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select item_size,dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,item_size");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description); ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$sub_item_total=0;
					?>
                    <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            
			}
            ?>
            <tr>
			<td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
            <?
            } 
            ?>
			<?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,dia_width,fin_gsm,labdip_no from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  dia_width='". $result_itemdescription[csf('dia_width')]."' and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."' and  labdip_no='". $result_itemdescription[csf('labdip_no')]."' and  rate='". $result_itemdescription[csf('rate')]."'  and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
	   
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
	   <? if ($show_comments!=1) { ?>
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black;">Total Booking Amount</th>
				<td width="70%" style="border:1px solid black;"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black;">Total Booking Amount (in word)</th>
				<td width="70%" style="border:1px solid black;">
				<?
				$mcurrency="";
				$dcurrency="";
				$currency_id=$result[csf('currency_id')];
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
				$currency_name=$currency[$result[csf('currency_id')]];
				echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
				?></td>
            </tr>
			<? } ?>
       </table>
      	<br>
        <?
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
         <br/>
        
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
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

if($action=="show_trim_booking_outside")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	//$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$buyer_code_arr=return_library_array( "select id,remark from lib_buyer",'id','remark');
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
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            {  
								$plot=$result[csf('plot_no')];
								$city=$result[csf('city')];
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$job_no=""; $po_no=""; $file=''; $ref=$style_ref='';
		
		
		$sqljobBooking="select c.job_no, c.style_ref_no, c.buyer_name as buyer_id, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.id=b.booking_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no and a.entry_form=232";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$po_no.=$result_job[csf('po_number')].",";
			$style_ref.=$result_job[csf('style_ref_no')].",";
			$file.=$result_job[csf('file_no')].",";
			$ref.=$result_job[csf('grouping')].",";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
		$file=implode(",",array_filter(array_unique(explode(',',$file))));
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));

        $nameArray=sql_select( "select a.booking_no,a.booking_date, a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.company_id  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=232"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$address="";
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) //In_house(3)_Within_group(5)
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
				$address=$plot.", ".$city;
				
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
				$address=$supplier_address_arr[$result[csf('supplier_id')]];
				
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="90" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="150">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="90" style="font-size:12px"><b>Booking Date</b></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Currency</b></td>
                <td width="150">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="90" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Source</b></td>
                <td  width="150" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="90" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="150">:&nbsp;<? echo $supplier_str;?>    </td>
                 <td width="90" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="150">:&nbsp;<? echo $address;?></td>
                <td  width="90" style="font-size:12px"><b>Attention</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="90" style="font-size:12px"><b>Job No</b>   </td>
                <td width="150">:&nbsp;<?=rtrim($job_no,','); ?></td>
                 
               	<td width="150" style="font-size:12px"><b>PO No</b> </td>
                <td width="90" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
            <tr>
           		<td style="font-size:12px"><b>Style</b> </td>
                <td>:&nbsp;<?=$style_ref; ?></td>
                <td width="90" style="font-size:12px"><b>File No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($file,','); ?></td>
                <td width="90" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($ref,','); ?></td> 
               	
            </tr> 
            <tr>
            	<td>
            		Buyer
            	</td>
            	<td colspan="5">:&nbsp;<?php echo $buyer_code_arr[$buyer_name]; ?></td>
            </tr>
        </table>  
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
	$nameArray_color_size_qnty=sql_select( "select process, sensitivity,description, fabric_color_id, wo_qnty as cons, rate,labdip_no,fin_gsm,dia_width,color_range,shade_per,dia_type from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity in(1,3)  and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	$color_size_qty_arr=array();
	foreach($nameArray_color_size_qnty  as $row)
	{
		if($row[csf('sensitivity')]==1)
		{
		$color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
		else
		{
		$contrast_color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
	}
	unset($nameArray_color_size_qnty);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
        $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="14" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black" align="center"><strong>item Color</strong> </td>
				<td style="border:1px solid black" align="center"><strong>Color Range</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,color_range,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,color_range,remark"); 
            
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $color_range[$result_itemdescription[csf('color_range')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="12"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;$sub_item_tot=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
					$sub_item_total=0;
                ?>

                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and dia_width='".$result_itemdescription[csf('dia_width')]."' and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."' and labdip_no='".$result_itemdescription[csf('labdip_no')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="13" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
			$nameArray_item_description=sql_select( "select dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark"); 
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		 }
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="14" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black"><strong>Item Color</strong> </td>
				<td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black"><strong>Fin Dia</strong> </td>
                <td style="border:1px solid black"><strong>Fin GSM</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Shade %</strong> </td>			
                <td style="border:1px solid black" align="center"><strong>Dia Type</strong></td>
                
				<td style="border:1px solid black" align="center"><strong>WO. QNTY</strong></td>
				<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
				<?  } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select item_size,dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,sum(wo_qnty) as wo_qty from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0 group by dia_type,fabric_color_id,description,rate,uom,labdip_no,fin_gsm,dia_width,shade_per,remark,item_size");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description); ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$sub_item_total=0;
					?>
                    <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?> </td>
				<td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('shade_per')]; ?> </td>

				<td style="border:1px solid black"><? echo $fabric_typee[$result_itemdescription[csf('dia_type')]]; ?> </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qty')],2);$sub_item_tot+=$result_itemdescription[csf('wo_qty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('wo_qty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;"><? echo $result_itemdescription[csf('remark')]; ?> </td>
				<? } ?>
            </tr>
            <?
            
			}
            ?>
            <tr>
			<td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($sub_item_tot,2);  ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>		
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
            <?
            } 
            ?>
			<?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
            </tr>
			<? } ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,dia_width,fin_gsm,labdip_no from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                $total_amount_as_per_gmts_color=0;$sub_item_tot=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $sub_item_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  dia_width='". $result_itemdescription[csf('dia_width')]."' and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."' and  labdip_no='". $result_itemdescription[csf('labdip_no')]."' and  rate='". $result_itemdescription[csf('rate')]."'  and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
				<? if ($show_comments!=1) { ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
			<? if ($show_comments!=1) { ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
			<? } ?>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
	   
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
	   <? if ($show_comments!=1) { ?>
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black;">Total Booking Amount</th>
				<td width="70%" style="border:1px solid black;"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black;">Total Booking Amount (in word)</th>
				<td width="70%" style="border:1px solid black;">
				<?
				$mcurrency="";
				$dcurrency="";
				$currency_id=$result[csf('currency_id')];
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
				$currency_name=$currency[$result[csf('currency_id')]];
				echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
				?></td>
            </tr>
			<? } ?>
       </table>
      	<br>
        <?
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
         <br/>
        
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
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
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sql="select id from electronic_approval_setup where company_id=$cbo_company_name and page_id in(1087) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

	?>
	<div style="width:1150px" align="left">       
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1050">                                     
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
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,bin_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
											if($result[csf('bin_no')]!='') echo "<br> BIN: ".$result[csf('bin_no')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		$po_no="";$po_id="";
		$nameArray_job=sql_select( "select distinct b.id as po_id,b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('po_id')].",";
			$po_number[$result_job[csf('po_id')]]=$result_job[csf('po_number')];
		}
        $nameArray=sql_select( "select a.booking_no, a.pay_mode, a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.is_approved  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_id,',').")";
		
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_id,',').")", "po_break_down_id", "article_number"  );
		//print_r($article_number_arr);
		$booking_date=$nameArray[0][csf('booking_date')];
		$is_approved=$nameArray[0][csf('is_approved')];
        foreach ($nameArray as $result)
        {
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) 
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
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
                <td width="110">:&nbsp;<? echo $supplier_str;?></td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="3">:&nbsp;
				<? 
				echo rtrim($job_no,',');
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
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); //and sensitivity=1
		
       
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong title="<? echo $fabric_description_array[$result_item[csf('description')]];?>">As Per Garments Color |&nbsp; <? echo $fabric_description_array[$result_item[csf('description')]]; ?></strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description='".$result_item[csf('description')]."' and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0  group by po_break_down_id,fabric_color_id,description,rate,artwork_no ");//and sensitivity=1 
			//echo  "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0  group by po_break_down_id,fabric_color_id,description,rate,artwork_no ";
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_item[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
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
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
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
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        
        
        
        
        
        
        
        
        
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1"); 
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and wo_qnty!=0");                          
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
       &nbsp;
       
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
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
      	<br>
        <?
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
    
     <br><br>
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
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					
					$job_no=rtrim($job_no,',');
					$job_nos=implode(",",array_unique(explode(",",$job_no)));
					$condition= new condition();
					if(str_replace("'","",$job_nos) !=''){
					$condition->job_no("in('$job_nos')");
					}
					$condition->init();
					$conversion= new conversion($condition);
					//echo $conversion->getQuery();
					$convAmt=$conversion->getAmountArray_by_orderAndProcess();
					//print_r($convAmt);
					$po_qty_arr=array();$dyeing_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS dyeing_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=31 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$dyeing_data_arr[$row[csf('job_no')]]['dyeing']=$row[csf('dyeing_cost')];	
					}
					
					$i=1; $total_balance_dyeing=0;$tot_dyeing_cost=0;$tot_pre_cost=0;
				
					$sql_aop=("select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_dyeing=$pre_cost_dyeing=array_sum($convAmt[$selectResult[csf("po_id")]][31]);;//($dyeing_data_arr[$selectResult[csf('job_no')]]['dyeing']/$costing_per_qty)*$po_qty;
						$wo_dyeing_charge=$selectResult[csf("amount")];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
						if($db_type==0)
						{
						$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
						}
						else
						{
						$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
						}
						
						//echo $currency_rate;
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$dyeing_charge=$wo_dyeing_charge/$currency_rate;	
						}
						else
						{
							$dyeing_charge=$wo_dyeing_charge;
						}
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
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_dyeing,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($dyeing_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_dyeing-$dyeing_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_dyeing>$dyeing_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_dyeing<$dyeing_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_dyeing==$dyeing_charge) 
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
	  	 $tot_pre_cost+=$pre_cost_dyeing;
	  	 $tot_dyeing_cost+=$dyeing_charge;
		 $total_balance_dyeing+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_dyeing_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_dyeing,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
	<br>
		<table width="780" align="center">
				<tr>
					<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
							<?
							if(count($approval_arr)>0)
							{				
								if($is_approved == 0){echo "Draft";}else{}
							}
							?>
					</div>
				</tr>
		</table>
	<br>
        
		 <?
            echo signature_table(82, $cbo_company_name, "1150px","",1);
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
	 $sql= "select booking_no,booking_date,company_id,buyer_id,is_short,job_no,material_id,po_break_down_id,tagged_booking_no,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,ready_to_approved,is_approved from wo_booking_mst  where booking_no='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		 echo "load_drop_down( 'requires/service_booking_dyeing_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' )\n";
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_short_type').value = '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('cbo_process').value = '31';\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "$('#txt_fab_booking').attr('disabled',false);\n";
		echo "document.getElementById('txt_fab_booking').value = '".$row[csf("tagged_booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_material').value = '".$row[csf("material_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";

		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_status').innerHTML = 'This booking is Approved';\n";

		}
		if($row[csf("is_approved")]==3){
			echo "document.getElementById('app_status').innerHTML = 'This booking is Partial Approved';\n";

		}
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		$po_no=chop($po_no," , ");
		echo "document.getElementById('txt_order_no').value = '".$po_no."';\n";
		echo "load_drop_down( 'requires/service_booking_dyeing_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=3 and company_name=".$row[csf("company_id")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_dyeing_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
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


if($action=="show_trim_booking_report3")//Print B3=>19-05-2022(md mamun ahmed sagor)-ISD-10430
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
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
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
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

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no");
		
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

		 $sqljobBooking="select c.job_no, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		foreach ($nameArray_job as $result_job)
        {	
			$ref.=$result_job[csf('grouping')].",";
		}
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));

        
        
       
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
							<td><strong style="background-color:yellow;padding:2%;font-size: 30px;"><?=str_replace("'","",$tagged_booking_no);;?></strong></td>
							
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
					<td width="100"><b>Job No/IR</b></td>
					
					<?
					$job=trim($txt_job_no,"'");
					$ir=rtrim($ref,',');
					?>
					<td width="140"> <span style="font-size:18px"><? echo $job.'  /  '.$ir; if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="100"><span style="font-size:18px"><b>Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><?=$result[csf('booking_no')];?> </span> </td>
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
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_no = $txt_booking_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
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
		sum(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 		and b.booking_type=3 and b.job_no='$txt_job_no'  and b.booking_no =$txt_booking_no
		group by b.job_no,c.id,c.charge_unit,b.po_break_down_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,c.fabric_description ");
	
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
	
		}
		$body_part_type_ids=implode(",",$body_part_type_arr);
		// echo $body_part_type_ids;
		
	
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
							 <td align="center"><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td align="center"><? echo $color_type[$value['color_type_id']] ?></td>
							 <td align="center"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
							 <td align="center"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <td align="center"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>		     			
							 <td align="center"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
							 <td align="center"><? echo $value['process_loss_percent'] ?></td>
						 </tr>
						  <? } 
						  else { ?>
							  <tr>
								 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
								 
								 <td><? echo $body_part[$value['body_part_id']] ?></td>
								 <td><? echo $value['fabric_des'] ?></td>
								 <td align="center"><? echo $value['gsm'] ?></td>
								 <td><? echo $value['fabric_dia'] ?></td>
								 <td align="center"><? echo $color_type[$value['color_type_id']] ?></td> 
								 <td align="center"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
								 <td align="center"><? echo number_format($value['fin_fab_qnty'],4) ?></td>
								 <td align="center"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>			     			
								 <td align="center"><? echo number_format($value['grey_fab_qnty'],4) ?></td>
								 <td align="center"><? echo $value['process_loss_percent'] ?></td>
							 </tr>
						  <? }
						  $i++;
					  //}
				 }
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="8">Total</th>
				 <th align="right"><?echo number_format($fin_fab_qnty);  ?></th>
				 <th></th>
				 <th align="right" ><?echo number_format($grey_fab_qnty);  ?></th>
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

						if(count($stripe_arr)>0){
        				?>
        <table  width="100%" style="margin: 0px;padding: 0px;">
      		<tr>
        	<td width="70%">
        		<table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
        		        <tr>
        		            <td align="center" colspan="9">  Stripe Details</td>
        		            
    		            </tr>
        		       
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
        								d.booking_no =$txt_booking_no and
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
			
		<? } ?>
			
		<?
		 
               
				 
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
        
        <div ><?
            echo signature_table(82, $cbo_company_name, "1150px");
         ?></div>
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

if($action=="show_trim_booking_report4")//Print B4=>06-06-2022(md mamun ahmed sagor)-ISD-11570
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
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
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
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
		$job_no=""; $po_no=""; $file=''; $ref=$style_ref='';
		
		
		$sqljobBooking="select c.job_no, c.style_ref_no, c.buyer_name as buyer_id, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$po_no.=$result_job[csf('po_number')].",";
			$style_ref.=$result_job[csf('style_ref_no')].",";
			$file.=$result_job[csf('file_no')].",";
			$ref.=$result_job[csf('grouping')].",";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
		$file=implode(",",array_filter(array_unique(explode(',',$file))));
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));
		
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date, a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) 
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="90" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="150">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="90" style="font-size:12px"><b>Booking Date</b></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="150">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="90" style="font-size:12px"><b>Currency</b></td>
                <td width="150">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="90" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Source</b></td>
                <td  width="150" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="90" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="150">:&nbsp;<? echo $supplier_str;?>    </td>
                 <td width="90" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="150">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  width="90" style="font-size:12px"><b>Attention</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="90" style="font-size:12px"><b>Job No</b>   </td>
                <td width="150">:&nbsp;<?=rtrim($job_no,','); ?></td>
                 
               	<td width="150" style="font-size:12px"><b>PO No</b> </td>
                <td width="90" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
            <tr>
           		<td style="font-size:12px"><b>Style</b> </td>
                <td>:&nbsp;<?=$style_ref; ?></td>
                <td width="90" style="font-size:12px"><b>File No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($file,','); ?></td>
                <td width="90" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="150">:&nbsp;<? echo rtrim($ref,','); ?></td> 
               	
            </tr> 
            <!-- <tr>
            	<td>
            		Buyer
            	</td>
            	<td colspan="5">:&nbsp;<?php echo $buyer_name_arr[$buyer_name]; ?></td>
            </tr> -->
        </table>  
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
	$nameArray_color_size_qnty=sql_select( "select process, sensitivity,description, fabric_color_id, wo_qnty as cons, rate,labdip_no,fin_gsm,dia_width from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity in(1,3)  and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	$color_size_qty_arr=array();
	foreach($nameArray_color_size_qnty  as $row)
	{
		if($row[csf('sensitivity')]==1)
		{
		$color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
		else
		{
		$contrast_color_size_qty_arr[$row[csf('process')]][$row[csf('description')]][$row[csf('fabric_color_id')]][$row[csf('rate')]][$row[csf('labdip_no')]][$row[csf('fin_gsm')]][$row[csf('dia_width')]]+=$row[csf('cons')];
		}
	}
	unset($nameArray_color_size_qnty);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
        $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and  wo_qnty!=0"); 
            
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                //$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('fabric_color_id')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
                $wo_qty=0;
				 $wo_qty=$color_size_qty_arr[$result_item[csf('process')]][$result_itemdescription[csf('description')]][$result_color[csf('fabric_color_id')]][$result_itemdescription[csf('rate')]][$result_itemdescription[csf('labdip_no')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('dia_width')]];
				  
				?>
					<td style="border:1px solid black; text-align:right">
					<? 
					if($wo_qty!= "")
					{
						echo number_format($wo_qty,2);
						$item_desctiption_total+=$wo_qty ;
						if (array_key_exists($wo_qty, $color_tatal))
						{
							$color_tatal[$result_color[csf('fabric_color_id')]]+=$wo_qty;
						}
						else
						{
							$color_tatal[$result_color[csf('fabric_color_id')]]+=$wo_qty; 
						}
					}
					else echo "";
					?>
					</td>
					<?   
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
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+10; ?>"><strong>Total</strong></td>
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty!=0");
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and dia_width='".$result_itemdescription[csf('dia_width')]."' and fin_gsm='".$result_itemdescription[csf('fin_gsm')]."' and labdip_no='".$result_itemdescription[csf('labdip_no')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");  
	
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
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+10; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+11; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
	               /* $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and rate=".$result_itemdescription[csf('rate')]." and labdip_no=".$result_itemdescription[csf('labdip_no')]." and fin_gsm=".$result_itemdescription[csf('fin_gsm')]." and dia_width=".$result_itemdescription[csf('dia_width')]." and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
	                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
	                */
		                  $wo_qty=0;
				 $wo_qty=$contrast_color_size_qty_arr[$result_item[csf('process')]][$result_itemdescription[csf('description')]][$result_color[csf('color_number_id')]][$result_itemdescription[csf('rate')]][$result_itemdescription[csf('labdip_no')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('dia_width')]];
				 
						?>
		                <td style="border:1px solid black; text-align:right">
		                <? 
		                if($wo_qty!= "")
		                {
		                echo number_format($wo_qty,2);
		                $item_desctiption_total += $wo_qty ;
		                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
		                {
		                $color_tatal[$result_color[csf('color_number_id')]]+=$wo_qty;
		                }
		                else
		                {
		                $color_tatal[$result_color[csf('color_number_id')]]=$wo_qty; 
		                }
		                }
		                else echo "0";
		                ?>
		                </td>
		                <?   
	             
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
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+10; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		 }
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3"); 

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+11; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('dia_width')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('fin_gsm')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
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
               		 $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."  and  dia_width='". $result_itemdescription[csf('dia_width')]."'  and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."'  and  labdip_no='". $result_itemdescription[csf('labdip_no')]."'  and  rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+11; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                 <td style="border:1px solid black"><strong>F Dia</strong> </td>
                <td style="border:1px solid black"><strong>F GSM</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom,dia_width,fin_gsm,labdip_no from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
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
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('dia_width')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('fin_gsm')]; ?> </td>
                <td style="border:1px solid black"><? echo$result_itemdescription[csf('labdip_no')]; ?> </td>
               
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  dia_width='". $result_itemdescription[csf('dia_width')]."' and  fin_gsm='". $result_itemdescription[csf('fin_gsm')]."' and  labdip_no='". $result_itemdescription[csf('labdip_no')]."' and  rate='". $result_itemdescription[csf('rate')]."'  and status_active=1 and is_deleted=0 and wo_qnty!=0");                          
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
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;">
				<?
				$mcurrency="";
				$dcurrency="";
				$currency_id=$result[csf('currency_id')];
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
				$currency_name=$currency[$result[csf('currency_id')]];
				echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);
				?></td>
            </tr>
       </table>
      	<br>
        <?
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
    <br><? if ($show_comments!=1) { ?>
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
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					$job_no=rtrim($job_no,',');
					$job_nos=implode(",",array_unique(explode(",",$job_no)));
					$condition= new condition();
					if(str_replace("'","",$job_nos) !=''){
					$condition->job_no("in('$job_nos')");
					}
					$condition->init();
					$conversion= new conversion($condition);
					//echo $conversion->getQuery();
					$convAmt=$conversion->getAmountArray_by_orderAndProcess();
					//print_r($convAmt);
					$po_qty_arr=array();$aop_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS aop_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=31 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];	
					}
					
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
				
					$sql_aop=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_aop=$pre_cost_dyeing=array_sum($convAmt[$selectResult[csf("po_id")]][31]);//($aop_data_arr[$selectResult[csf('job_no')]]['aop']/$costing_per_qty)*$po_qty;
						
	
						$wo_aop_charge=$selectResult[csf("amount")];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
						
						if($db_type==0)
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "Y-m-d", "-",1);
						}
						else
						{
						$conversion_date=change_date_format($result[csf('booking_date')], "d-M-y", "-",1);
						}
						
						//echo $currency_rate;
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$aop_charge=$wo_aop_charge/$currency_rate;	
						}
						else
						{
							$aop_charge=$wo_aop_charge;
						}
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
                     <td style="border:1px solid black;" width="80" align="right">
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
        <? } ?>
         <br/>
        
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
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

if($action=="show_trim_booking_report5") //29305 shariar
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_template_id=str_replace("'","",$cbo_template_id);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";

	// print_r($cbo_template_id);die;
	
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
	<div style="width:1000px" align="left">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
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
		$service_process=sql_select("SELECT b.process from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=$txt_booking_no and b.booking_type=3");
		foreach($service_process as $row){
			$process_arr[$row[csf('process')]]=$conversion_cost_head_array[$row[csf('process')]];
		}
		$process=implode(", ",$process_arr);
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,a.process,b.team_leader from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no");


		
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
		//$process=$conversion_cost_head_array[$nameArray[0][csf('process')]];

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

		 $sqljobBooking="select c.job_no, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		foreach ($nameArray_job as $result_job)
        {	
			$ref.=$result_job[csf('grouping')].",";
		}
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));

        
        
       
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
										
        <table width="1200" align="left" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='50%' width='50%' /></td>
                <td width="1000">
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
                        <?php /*?><tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
							  
                            </td>
							<td><strong style="background-color:yellow;padding:2%;font-size: 30px;"><?=str_replace("'","",$tagged_booking_no);;?></strong></td>
							
                        </tr><?php */?>
						
						
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
			<table width="1200" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td width="150"><b>Service Provider </b></td>		 
					<td width="250"> <span style="font-size:18px"><?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$result[csf('supplier_id')]];
						}
						else{
						echo $supplier_name_arr[$result[csf('supplier_id')]];
						}
					?></span> </td>
					<td width="150"><span style="font-size:18px"><b>Supplier Address</b></span></td>
					<td width="250"><span style="font-size:18px"><?

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
					<td width="150"><span style="font-size:18px"><b>Work Order No</b></span></td>
					<td width="250"><span style="font-size:18px"><?=$result[csf('booking_no')];?> </span> </td>
					
				
				</tr>
				<tr>
					<td width="150"><b>Job No/IR</b></td>
					
					<?
					$job=trim($txt_job_no,"'");
					$ir=rtrim($ref,',');
					?>
					<td width="250"> <span style="font-size:18px"><? echo $job.'  /  '.$ir; if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></span> </td>
					<td width="150"><b>Dealing Merchandiser</b></td>
					<td width="250"><? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
					<td width="150"><span style="font-size:18px"><b>Team Leader</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?=$team_leader;    ?></span></td>	
				</tr>
				<tr>		
					<td width="150"><span style="font-size:18px"><b>Buyer</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
					<td width="100"><b>Fabric Booking No</b></td>
					<td width="140"><?php echo str_replace("'","",$tagged_booking_no); ?></td>
				</tr>
				<tr>
					<td width="150" style="font-size:16px;"><b>Style</b></td>
					<td width="250"style="font-size:16px;" >&nbsp;<? echo $style_ref_no; ?></td>				
					<td width="150"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="250">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>	
					
					<td width="150"><b>Process</b></td>
					<td width="250"><? echo $process; ?></td>
				</tr>

						<? 
							$sql_fab="SELECT a.lib_yarn_count_deter_id AS determin_id, a.construction
							    FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_no = $txt_booking_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
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
							//echo implode(",", array_unique(explode("***", $des)));
						?>
			</table>
			
			<br>
			<br>
			<br>
			<br>

			
			
			<?
		}	
			
	  	?>

<br>
			<br>
			<br>
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


		$pre_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, remarks ,pre_cost_fabric_cost_dtls_id  as fab_desc_id from wo_pre_cos_fab_co_avg_con_dtls where job_no='$txt_job_no'  and po_break_down_id in (".$po_id_all.") order by id");


		foreach($pre_cons_data as $row){

			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['finsh_cons']=$row[csf("cons")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['gray_cons']=$row[csf("requirment")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['dia_width']=$row[csf("dia_width")];
			$color_wise_data[$row[csf("po_break_down_id")]][$row[csf("fab_desc_id")]][$row[csf("color_number_id")]]['process_loss_percent']=$row[csf("process_loss_percent")];

		}


		$nameArray_fabric_description= sql_select("select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,b.gmts_size,(b.wo_qnty) as wo_qnty,b.rate,b.fin_fab_qnty,b.process_loss_percent,b.labdip_no,b.delivery_date,b.delivery_end_date,
		(b.amount) as amount,c.charge_unit,c.fabric_description  as fab_desc_id	from wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_type=3 and b.job_no='$txt_job_no'  and b.booking_no =$txt_booking_no order by c.fabric_description");
	
		$body_part_type_arr=array();
		foreach ($nameArray_fabric_description as $row) {	

			if($row[csf('wo_qnty')]>0){

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
			
		
				$grouping_item=$row[csf('fabric_color_id')].'*'.$body_part_id.'*'.$construction.'*'.$composition.'*'.$gsm_weight.'*'.$width_dia_type.'*'.$color_type_id;	
					$pp=100+$process_loss_percent;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['labdip_no'] =  $row[csf('labdip_no')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_date'] =  $row[csf('delivery_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['delivery_end_date'] =  $row[csf('delivery_end_date')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['body_part_id'] = $body_part_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_des'] = $construction.','.$composition;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gsm'] = $gsm_weight;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_dia'] = $dia_width.",".$fabric_typee[$width_dia_type];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['color_type_id'] = $color_type_id;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['finsh_cons'] = $finsh_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gray_cons'] = $gray_cons;
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fin_fab_qnty'] +=$row[csf('fin_fab_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss'] =$row[csf('process_loss_percent')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('wo_qnty')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['amount'] += $row[csf('amount')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['rate']= $row[csf('rate')];
				$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss_percent'] = $process_loss_percent;
		   }
	
		}
		$body_part_type_ids=implode(",",$body_part_type_arr);
		// echo $body_part_type_ids;
		$gmts_colorWiseArr=array();
		 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) 
		 {  
			   $gmt_color_span=0;   		  		
			 foreach ($gmts_data_arr as $fabric_id => $value) 
			 {
				 $gmt_color_span++;
			 }
			 $gmts_colorSpanArr[$gmts_id]=$gmt_color_span;
				 
		 }
	
		?>
		 <table class="rpt_table" width="1200"  align="left" border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>
				 <th>Gmts Colors</th>
				 <th>Fabric Color</th>	
				 <th>LD No</th>			
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Work Order Qty</th>
                  <th>Process Loss%</th>
                  <th>Req. Fin Qty</th>
                   
				 <? if ($show_comments!=1) { ?>
				 <th>Rate</th>
				 <th>Amount</th>
				 <? } ?>
				 <th>TNA Start Date</th>
				 <th>TNA End Date</th>
			 </tr>
			 <? $grey_fab_qnty=$tot_fin_fab_qnty=0;
			 foreach ($fabric_data_arr as $gmts_id=>$gmts_data_arr) {  
			 $jj=1;     		  		
				 foreach ($gmts_data_arr as $fabric_id => $value) {
						 $amount+=$value['amount'];   		 	
						 $grey_fab_qnty+=$value['grey_fab_qnty'];  
						  $tot_fin_fab_qnty+=$value['fin_fab_qnty'];   
						   $gmts_colorRowspan=$gmts_colorSpanArr[$gmts_id];	
						  // echo $gmts_colorRowspan.'d,';	 	
						
						   ?>
						  <tr>
						   <? if($jj==1){
                               ?>
                             <td rowspan="<? echo $gmts_colorRowspan; ?>"><? echo $color_library[$gmts_id] ?></td>
                             <?
							   }
							 ?>
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
							 <td><? echo $value['labdip_no'] ?></td>
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td align="center"><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td align="center"><? echo $color_type[$value['color_type_id']] ?></td>		     			
							 <td align="center"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['process_loss'],4) ; ?></td>
                             <td align="center"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <? if ($show_comments!=1) { ?>
							 <td align="center"><? echo $value['rate'] ; ?></td>
							 <td align="center"><? echo fn_number_format($value['amount'],4) ; ?></td>
							 <? } ?>
							 <td  align="center"><? echo change_date_format($value['delivery_date']) ?></td>
							 <td  align="center"><? echo change_date_format($value['delivery_end_date']) ?></td>
						 </tr>
						  <?  
						 
						  $jj++;
					  //}
				 }
			 } 
			 ?>
			 <tr>
				 <th align="right" colspan="8">Total</th>
				 <th align="right" ><? echo number_format($grey_fab_qnty);  ?></th>
                  <th align="right" ><? //echo number_format($grey_fab_qnty);  ?></th>
                   <th align="right" ><? echo number_format($tot_fin_fab_qnty);  ?></th>
                   
				 <? if ($show_comments!=1) { ?>
				 <th>&nbsp;</th>
				 <th align="right" ><?echo number_format($amount);  ?></th>
				 <? } ?>
				 <th>&nbsp;</th>
				 <th>&nbsp;</th>

			 </tr>
		 </table>
		  <br/>
		  <br>
		  <br>
		  <br> 
		<? 
       	echo get_spacial_instruction($txt_booking_no,'1200px',232);
        ?>
        
        <div ><?
		    echo signature_table(82, $cbo_company_name, "1200px",$cbo_template_id,"3px");
            
         ?></div>
		<br> 
		    

	   </div>
       <?
	$emailBody=ob_get_contents();

	if($is_mail_send==1){		
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

if($action=="show_trim_booking_report6")// 21189
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id,brand_name from lib_buyer_brand",'id','brand_name');
	$lib_dealing_mer_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	
	$booking_grand_total=0;
		$job_no=""; $po_no=""; $file=''; $ref=$style_ref='';
		
		
		  $sqljobBooking="select c.job_no, c.style_ref_no,c.style_owner,c.style_description,c.dealing_marchant,c.team_leader,c.brand_id,c.gmts_item_id, c.buyer_name as buyer_id,d.pub_shipment_date, d.id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.job_no=c.job_no and a.booking_no=$txt_booking_no";
		$nameArray_job=sql_select($sqljobBooking);
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
		foreach ($nameArray_job as $result_job)
        {
		$job_no.=$result_job[csf('job_no')].",";
		$po_no.=$result_job[csf('po_number')].",";
		$style_owner=$result_job[csf('style_owner')];
		
		$po_noArr[$result_job[csf('id')]]['po_no']=$result_job[csf('po_number')];
		$po_noArr[$result_job[csf('id')]]['ship_date']=$result_job[csf('pub_shipment_date')];
		//	$gmts_item_id=$result_job[csf('gmts_item_id')];
		$dealing_marchant=$lib_dealing_mer_arr[$result_job[csf('dealing_marchant')]];
		$team_leader=$team_leader_arr[$result_job[csf('team_leader')]];
		//echo $team_leader.'D'.$result_job[csf('team_leader')];
		$brand_id=$brand_name_arr[$result_job[csf('brand_id')]];
		$gmts_item_name="";
		$gmts_item=explode(',',$result_job[csf('gmts_item_id')]);
		for($g=0;$g<=count($gmts_item); $g++)
		{
		$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
		}
		// echo rtrim($gmts_item_name,',');
		
		$style_ref.=$result_job[csf('style_ref_no')].",";
		if($result_job[csf('style_description')]!="")
		{
		$style_description=$result_job[csf('style_description')];
		}
		$file.=$result_job[csf('file_no')].",";
		$ref.=$result_job[csf('grouping')].",";
		$po_numberArr[$result_job[csf('id')]]=$result_job[csf('po_number')];
		$poIdArr[$result_job[csf('id')]]=$result_job[csf('id')];
		}
		
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
                                    echo "Company Name:".$company_library[$cbo_company_name].'<br>Style Owner:'.$company_library[$style_owner];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Dyeing</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id" > 
              
               </td>      
            </tr>
       </table>
		<?
		
		$gmts_item_name=implode(",",array_filter(array_unique(explode(',',$gmts_item_name))));
		$job_no=implode(",",array_filter(array_unique(explode(',',$job_no))));
		$po_no=implode(",",array_filter(array_unique(explode(',',$po_no))));
		$style_ref=implode(",",array_filter(array_unique(explode(',',$style_ref))));
		
		$file=implode(",",array_filter(array_unique(explode(',',$file))));
		$ref=implode(",",array_filter(array_unique(explode(',',$ref))));
		
		$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			
			$fabric_des_master_arr.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]."## ";
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";
			
				$fabric_des_master_arr.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]."## ";
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	
	
	 $nameArray_item_summery=sql_select( "select  distinct description,process from wo_booking_dtls  where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and  wo_qnty!=0");
	// echo  "select  distinct description,process from wo_booking_dtls  where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and  wo_qnty!=0"; 
	  foreach ($nameArray_item_summery as $result)
        {
			$itemdescriptionArr.=$fabric_description_array[$result[csf('description')]].'##';
			$processId=$conversion_cost_head_array[$result[csf('process')]];
		}
		$fabric_des_master_arr=rtrim($itemdescriptionArr,'##');
	$fabric_des_master_all=implode(",",array_filter(array_unique(explode('##',$fabric_des_master_arr))));
	
	
	
	//print_r($fabric_des_master_arr);
	
		
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date, a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$supplier_str="";
			if($result[csf('pay_mode')]==3 || $result[csf('pay_mode')]==5) 
			{
				$supplier_str=$company_library[$result[csf('supplier_id')]];
				$supplier_address=$company_address_arr[$result[csf('supplier_id')]];
			}
			else 
			{
				$supplier_str=$supplier_name_arr[$result[csf('supplier_id')]];
				$supplier_address=$supplier_address_arr[$result[csf('supplier_id')]];
			}
        ?>
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="90" style="font-size:12px"><b>Service Provider:  </b>   </td>
                <td width="150">:&nbsp;<?  echo $supplier_str;?> </td>
                <td width="90" style="font-size:12px"><b>Address:</b></td>
                <td width="150">:&nbsp;<? echo $supplier_address;//echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="90"><span style="font-size:12px"><b>Dealing Merchandiser:</b></span></td>
                <td width="150">:&nbsp;<? echo $dealing_marchant;?></td>	
            </tr> 
            <tr>
                <td width="90" style="font-size:12px"><b>Job NO</b></td>
                <td width="150">:&nbsp;<? echo rtrim($job_no,',');; ?></td>
                <td  width="90" style="font-size:12px"><b>Booking No :</b></td>
                <td  width="150" >:&nbsp;<? echo $result[csf('booking_no')]; ?></td>
                <td  width="90" style="font-size:12px"><b>Team Leader :</b></td>
                <td  width="150" >:&nbsp;<? echo $team_leader; ?></td>
            </tr> 
             <tr>
                <td width="90" style="font-size:12px"><b>Buyer/Agent Name :</b>   </td>
                <td width="150">:&nbsp;<?  echo $buyer_name_arr[$buyer_name];?>    </td>
                 <td width="90" style="font-size:12px"><b>Garments Item :</b></td>
               	<td width="150">:&nbsp;<? echo $gmts_item_name;?></td>
                <td  width="90" style="font-size:12px"><b>Brand :</b></td>
                <td  width="150" >:&nbsp;<? echo $brand_id; ?></td>
            </tr>  
            <tr>
                <td width="90" style="font-size:12px"><b>Style :</b>   </td>
                <td width="150">:&nbsp;<?=$style_ref; ?></td>
                 
               	<td width="150" style="font-size:12px"><b>Style Description :</b> </td>
                <td width="90" style="font-size:12px">:&nbsp;<? echo $style_description; ?> </td>
                	<td width="150" style="font-size:12px"><b>Process :</b> </td>
                <td width="90" style="font-size:12px">:&nbsp;<? echo $processId; ?> </td>
            </tr> 
            <tr>
           		<td style="font-size:12px"><b>Fabric Description :</b> </td>
                <td colspan="3">:&nbsp;<?=$fabric_des_master_all; ?></td>
                <td width="90" style="font-size:12px">&nbsp;   </td>
                <td width="150"> &nbsp;<? //echo rtrim($file,','); ?></td>
               
               	
            </tr> 
           
        </table>  
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		
	//print_r($fabric_description_array);
	$nameArray_color_book=sql_select( "select c.id as fab_id,b.gmts_color_id as gmt_color,b.process,b.po_break_down_id as po_id, b.slength,b.description, b.fabric_color_id as fab_color, b.wo_qnty as cons, b.rate,b.labdip_no,b.fin_gsm,b.dia_width as fin_dia,b.mc_dia as mc_dia,c.body_part_id as body_id,c.lib_yarn_count_deter_id,c.construction,c.composition,c.color_type_id as c_type from wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d where c.id=d.fabric_description and b.pre_cost_fabric_cost_dtls_id=d.id and b.booking_no=$txt_booking_no  and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.wo_qnty!=0");
	 
	 
	 
	  
	$color_size_qty_arr=array();
	foreach($nameArray_color_book  as $row)
	{
		$fabdesc=$row[csf('construction')].','.$row[csf('composition')];
		$fabcolorwise_arr[$row[csf('gmt_color')]][$row[csf('fab_color')]][$row[csf('body_id')]][$fabdesc][$row[csf('fin_gsm')]][$row[csf('mc_dia')]]['qty']+=$row[csf('cons')];
		$fabcolorwise_arr[$row[csf('gmt_color')]][$row[csf('fab_color')]][$row[csf('body_id')]][$fabdesc][$row[csf('fin_gsm')]][$row[csf('mc_dia')]]['labdip_no']=$row[csf('labdip_no')];
		$fabcolorwise_arr[$row[csf('gmt_color')]][$row[csf('fab_color')]][$row[csf('body_id')]][$fabdesc][$row[csf('fin_gsm')]][$row[csf('mc_dia')]]['fin_dia']=$row[csf('fin_dia')];
		$fabcolorwise_arr[$row[csf('gmt_color')]][$row[csf('fab_color')]][$row[csf('body_id')]][$fabdesc][$row[csf('fin_gsm')]][$row[csf('mc_dia')]]['c_type']=$row[csf('c_type')];
		$fabcolorwise_arr[$row[csf('gmt_color')]][$row[csf('fab_color')]][$row[csf('body_id')]][$fabdesc][$row[csf('fin_gsm')]][$row[csf('mc_dia')]]['slength']=$row[csf('slength')];
		$po_fabcolor_wise_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['qty']+=$row[csf('cons')];
		$po_fabcolor_wise_summary_arr[$row[csf('gmt_color')]]['qty']+=$row[csf('cons')];
		
		$fab_pre_idArr[$row[csf('fab_id')]]=$row[csf('fab_id')];
	}
	unset($nameArray_color_book);
	//=================================================
      //  $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1";  gmts_color_id
      //  $nameArray_color=sql_select( "select distinct  fabric_color_id as fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty!=0"); 

		if(count($fabcolorwise_arr)>0)
		{
        ?>
        <br>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            
            <tr>
             
                <th width="20" style="border:1px solid black"><strong>Sl</strong> </th>
                <th  width="100" style="border:1px solid black"><strong>Gmts Colors</strong> </th>
                <th width="100" style="border:1px solid black"><strong>Fabric Color</strong> </th>
                <th width="50" style="border:1px solid black"><strong>Labdip Number</strong> </th>
                <th width="100" style="border:1px solid black"><strong>Body Part</strong> </th>
                <th width="200" style="border:1px solid black"><strong>Fabrication</strong> </th>
                <th width="40" style="border:1px solid black"><strong>GSM</strong> </th>
                <th width="40" style="border:1px solid black"><strong>Stich Length</strong> </th>
                <th width="50" style="border:1px solid black"><strong>MC Dia</strong> </th>
                <th width="50" style="border:1px solid black" align="center"><strong>Fin Dia</strong></th>
                <th width="70" style="border:1px solid black" align="center"><strong>Color Type</strong></th>
                <th width="" style="border:1px solid black" align="center"><strong>Grey Qty</strong></th>
               
            </tr>
            <?
			$i=0;$tot_wo_qty=0;
            $grand_total_as_per_gmts_color=0;
            foreach($fabcolorwise_arr as $gmts_color=>$gmtscolor_data)
            {
			 foreach($gmtscolor_data as $fab_color=>$fabcolor_data)
              {
			  foreach($fabcolor_data as $body_id=>$body_data)
              {
			  foreach($body_data as $fab_desc=>$fabdes_data)
              {
			   foreach($fabdes_data as $fin_gsm=>$gsm_data)
              	{
				 foreach($gsm_data as $fab_dia=>$row)
             	 {
				$i++;
           // $nameArray_item_description=sql_select( "select distinct description,rate,uom,labdip_no,fin_gsm,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 and  wo_qnty!=0"); 
            
            ?>
            <tr>
                <td style="border:1px solid black"> <? echo $i; ?> </td>
                <td align="center" style="border:1px solid black" rowspan="<? //echo count($nameArray_item_description)+1; ?>">
                <? echo $color_library[$gmts_color]; ?>
                </td>
                <? 
              //  $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $color_library[$fab_color]; ?> </td>
                <td style="border:1px solid black"><? echo $row['labdip_no']; ?> </td>
                <td style="border:1px solid black" title="<? echo $body_id; ?>"><? echo $body_part[$body_id]; ?> </td>
                <td style="border:1px solid black"><? echo $fab_desc; ?> </td>
               
                <td style="border:1px solid black"><?  echo $fin_gsm; ?> </td>
              
                <td style="border:1px solid black; text-align:center"><? echo $row['slength']; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $fab_dia; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $row['fin_dia']; ?> </td>
                <td style="border:1px solid black; text-align:center"> <? echo $color_type[$row['c_type']]; ?></td>
                  <td style="border:1px solid black; text-align:right"><?  echo number_format($row['qty'],2); ?> </td>
            </tr>
            
             
            <?
				$tot_wo_qty+=$row['qty'];
			     }
				}
			   }
			  }
			 }
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($tot_wo_qty,2); ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		$lab_dip_color_arr=array();
			$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$job_no'");
			foreach($lab_dip_color_sql as $row)
			{
				$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
			}
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select color_number_id, size_number_id,item_number_id, (plan_cut_qnty) as plan_cut_qnty, (order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in (".implode(",",$poIdArr).") and status_active=1 and is_deleted =0 ");
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']+=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']+=$row[csf('order_quantity')];
			}
			
			//	 $id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		// $field_array="id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,qty,inserted_by,insert_date";

			$collar_cuff_percent_arr=array();
			$collar_cuff_body_arr=array();
			$collar_cuff_color_arr=array();
			$collar_cuff_size_arr=array();
			$collar_cuff_item_size_arr=array();
			$color_size_sensitive_arr=array();

			/* $collar_cuff_sql="select a.id, a.item_number_id,a.color_size_sensitive, a.color_break_down,b.qty, b.gmts_color_id as color_number_id,b.size_number_id as gmts_sizes,b.item_size, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b, wo_booking_dtls d, lib_body_part e

			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=e.id and e.body_part_type in (40,50)    and d.po_break_down_id=b.po_break_down_id and d.gmts_color_id=b.gmts_color_id and d.status_active=1 and d.is_deleted=0 order by  b.size_number_id,b.item_size";*/
			
			
			 $collar_cuff_sql="select a.id, a.item_number_id,b.po_break_down_id as po_id,a.color_size_sensitive, a.color_break_down,b.qty, b.gmts_color_id as color_number_id,b.size_number_id as gmts_sizes,b.item_size,  e.body_part_full_name, e.body_part_type
			FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b, lib_body_part e

			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and a.body_part_id=e.id and e.body_part_type in (40,50)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.size_number_id,b.item_size";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('qty')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('gmts_sizes')];
				$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('gmts_sizes')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
				$item_id_arr[$collar_cuff_row[csf('id')]]=$collar_cuff_row[csf('item_number_id')];
				
				$order_plan_qty_arr2[$collar_cuff_row[csf('item_number_id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]['plan']+=$collar_cuff_row[csf('qty')];
				$order_plan_qty_arr2[$collar_cuff_row[csf('item_number_id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]['order']+=$collar_cuff_row[csf('qty')];
				

			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;">
					<table width="90%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                           
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
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
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												$item_id=$item_id_arr[$pre_cost_id];
												?>
												<td title="<? //echo $item_id.'='.$pre_cost_id;?>" align="center" style="border:1px solid black">
													<? 
												  $collerqty=$collar_cuff_qty_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$plan_cut;

                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
                        </tr>
                        <tr>
                            <td>Size Total</td>
								<?
                                foreach($pre_size_total_arr  as $size_qty)
                                {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
									<?
                                }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            
                        </tr>
					</table>
                </div>
                <br/>
                <?
            }
        }
		 
		
		 
		?>
        <table width="90%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
			<caption> <strong style="float:left"> Stripe Details</strong></caption>
			<?
			$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
		/*	echo $sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$job_no'  and d.job_no='$job_no' and b.booking_no=$txt_booking_no  and c.color_type_id in (2,3,4,6,32,33,34)  and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width order by d.id ");*/
		  $sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from  wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where d.pre_cost_fabric_cost_dtls_id=c.id   and d.job_no='$job_no'  and c.color_type_id in (2,3,4,6,32,33,34)    and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and c.id in(".implode(",",$fab_pre_idArr).")   group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction order by d.id ");
			$result_data=sql_select($sql_stripe);
			foreach($result_data as $row){
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
				$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
			}
			?>

				<tr>
				<th width="30"> SL</th>
				<th width="100"> Body Part</th>
				<th width="80"> Fabric Color</th>
				
				<th width="70"> Stripe Color</th>
				<th width="70"> Stripe Measurement</th>
				<th width="70"> Stripe Uom</th>
				
				<th  width="70"> Y/D Req.</th>
				</tr>
				<?
				//if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
				//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
					//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";


				$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
				foreach($stripe_arr as $body_id=>$body_data){
					foreach($body_data as $color_id=>$color_val){
						$rowspan=count($color_val['stripe_color']);
						$composition=$stripe_arr2[$body_id][$color_id]['composition'];
						$construction=$stripe_arr2[$body_id][$color_id]['construction'];
						$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
						$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
						$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

						?>
						<tr>
						<?
						$color_qty= array_sum($stripe_arr[$body_id][$color_id]['fabreqtotkg']);
						?>
						<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
						
						<?
						$total_fab_qty+=$color_qty;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$measurement=$color_val['measurement'][$strip_color_id];
							$uom=$color_val['uom'][$strip_color_id];
							$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
							?>
							<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
							<td align="right"> <? echo  number_format($measurement,2); ?></td>
							<td> <? echo  $unit_of_measurement[$uom]; ?></td>
							
							<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
							</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
						}
						$i++;
					}
				}
				?>
				<tfoot>
				<tr>
				<td colspan="2">&nbsp; </td>
				<td align="right">  <? //echo  number_format($total_fab_qty,2); ?> </td>
				<td></td>
				<td></td>
				<td>   </td>
				 
				</tr>
				</tfoot>
				</table>
                <br>
               
        <table width="90%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
			<caption> <strong style="float:left"> PO & Fabric Color wise fabric Required Quantity</strong></caption>
       
            <tr>
            <th width="100" style="text-align:center">PO Number</th>
            <th width="70" style="text-align:center">Ship Date</th>
            <th width="70" style="text-align:center">Fabric Color</th>
            <th width="70" style="text-align:center">Body Color</th>
            <th width="70" style="text-align:right">Total Grey Fabric(kg)</th>
            </tr>
            <?
			$k=1;
			//print_r($po_fabcolor_wise_arr);//po_fabcolor_wise_summary_arr
			$grand_tot_qty=0;
            foreach($po_fabcolor_wise_arr as $po_id=>$poData)
			{
				$sub_tot_qty=0;
			foreach($poData as $fab_color_id=>$fcolorData)
			{
			foreach($fcolorData as $gmts_color_id=>$row)
			{
				
			?>
            <tr>
                <td> <? echo $po_noArr[$po_id]['po_no'];?></td>
                <td> <? echo $po_noArr[$po_id]['ship_date'];?></td>
                <td> <? echo $color_library[$fab_color_id];?></td>
                <td> <? echo $color_library[$gmts_color_id];?></td>
                <td align="right"><? echo number_format($row['qty'],2);?></td>
            </tr>
            <?
			$k++;
			$sub_tot_qty+=$row['qty'];
			$grand_tot_qty+=$row['qty'];
			}
			}
			?>
            <tr style="background-color:#CCCCCC">
				<td colspan="4" align="right"><strong> PO Wise</strong> </td>
                <td align="right"><? echo number_format($sub_tot_qty,2);?></td>
			</tr>
<?
			}
			?>
            <tfoot>
               <tr style="background-color:#CCCCCC">
				<td colspan="4" align="right"><strong> Grand Total</strong> </td>
                <td align="right"><? echo number_format($grand_tot_qty,2);?></td>
			</tr>
            </tfoot>
       </table>
      	<br>
        <?
		 
               
				 
       	echo get_spacial_instruction($txt_booking_no,'90%',232);
        ?>
    </tbody>
    </table>
    <br>
      <table width="40%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
			<caption> <strong style="float:left"> Color wise Summary</strong></caption>
       
            <tr>
            <th width="20" style="text-align:center">SL</th>
            <th width="100" style="text-align:center">Color Name</th>
            <th width="100" style="text-align:right">Grey Fabric(kg)</th>
            </tr>
            <?
			$j=1;
            foreach($po_fabcolor_wise_summary_arr as $color_id=>$row)
			{
			?>
            	<tr>
               
                <td> <? echo $j;?></td>
                <td> <? echo $color_library[$color_id];?></td>
                <td align="right"><? echo number_format($row['qty'],2);?></td>
            </tr>
            <?
			$j++;
			}
			?>
            
            </table>
            
            
            
    
        
		 <?
            echo signature_table(82, $cbo_company_name, "1150px");
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


?>
