<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	18-10-2012
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
Comments		         :
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$colorTypeId_static="2,3,4,5,6,7,20,31,32,33,34,44,45,47,48,63,65,71,76,72,74,82,84";
//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
} 

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Requisition No</th>
                        <th width="150">Style Ref.</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'stripe_color_measurement_sample_requisition_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:150px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style_search').value, 'create_po_search_list_view', 'search_div', 'stripe_color_measurement_sample_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
            <td align="center" valign="top" id="search_div"> 
	
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

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if (str_replace("'","",$data[4])!="") $req_cond=" and a.requisition_number_prefix_num='$data[4]'  $year_cond"; else  $req_cond=""; 
	if (str_replace("'","",$data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'  "; else  $style_cond=""; 
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $req_date = "and a.requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $req_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $req_date = "and a.requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $req_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);
	
	//$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=117 and b.sample_mst_id='$data[0]' group by a.id,a.sample_name,b.id order by b.id";
	//sample_development_fabric_acc
	
	if($db_type==0) $select_year_con="YEAR(a.insert_date) as year";else $select_year_con="to_char(a.insert_date,'YYYY') as year";
	 $sql= "select $select_year_con, a.id,a.requisition_number_prefix_num,a.company_id as company_name,a.buyer_name,a.style_ref_no,a.requisition_date,a.requisition_number,c.id as pre_id,c.required_dzn,c.required_qty from sample_development_mst  a left join sample_development_fabric_acc c on a.id=c.sample_mst_id and c.status_active=1 and c.is_deleted=0 where a.status_active=1 $req_date $company $buyer $req_cond $style_cond order by a.id desc";  
	
	echo  create_list_view("list_view", "Year,Req. No,Company,Buyer,Style Ref.,Req. Qty.,Req/Dzn,Requisition Date", "60,120,100,100,90,140,90,80","920","320",0, $sql , "js_set_value", "requisition_number", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "year,requisition_number_prefix_num,company_name,buyer_name,style_ref_no,required_qty,required_dzn,requisition_date", "",'','0,0,0,0,0,1,1,3') ;
}

if ($action=="populate_data_from_job_table")
{
	$data_array=sql_select("select requisition_number,company_id,buyer_name,style_ref_no from sample_development_mst where requisition_number='$data' and is_deleted=0 and status_active=1 and entry_form_id=117");
	//echo "select requisition_number,company_id,buyer_name,style_ref_no from sample_development_mst where requisition_number='$data' and is_deleted=0 and status_active=1";
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/stripe_color_measurement_sample_requisition_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("requisition_number")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("requisition_number")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	}
}

if ($action=="show_fabric_cost_listview")
{
	$data=explode("_",$data);
	
	?>
                  
    	<fieldset style="width:810px;">
        	<form id="fabriccost_3" autocomplete="off">
            <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" /> 
             
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="415">Fabric Description</th><th width="150">Gmts Item</th><th width="115">Fab Nature</th><th width="125">Color</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$mst_id=return_field_value("id", "sample_development_mst", "requisition_number='$data[0]'  and entry_form_id=117 and status_active=1");
					
					$fab_description=array();//body_part_id,fabric_nature_id,fabric_description
					$fab_description_array=sql_select("select id, gmts_item_id,body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$mst_id and color_type_id in ($colorTypeId_static) and status_active=1 and is_deleted=0");
					//echo "select id, gmts_item_id,body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$mst_id and color_type_id in (2,3,4,6,31,32,33,34)";
				//	echo "select id, body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$mst_id and color_type_id in (2,3,4,6,31,32,33,34)";
					foreach( $fab_description_array as $row_fab )
					{
					  $fab_description[$row_fab[csf("id")]]=	$body_part[$row_fab[csf("body_part_id")]].', '.$color_type[$row_fab[csf("color_type_id")]].', '.$row_fab[csf("fabric_description")];
					  $gmts_item_id=$row_fab[csf("gmts_item_id")];
					}
							?>
                            	<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
                                 <td>
                                    <input type="hidden" id="libyarncountdeterminationid"  name="libyarncountdeterminationid" class="text_boxes" style="width:10px"/>
                                    <? 
									
									echo create_drop_down( "fabricdescription", 415, $fab_description, "",1," -- Select--","", "set_data(this.value)","","" ); 
									?> 
                                    </td>
                                    <td>
									<? 
										echo create_drop_down( "cbogmtsitem", 150, $garments_item,"", $gmts_item_id, "Display", "", "",1,"" ); 
									?>
                                    </td>
                                   
                                    <td>
									<?  echo create_drop_down( "cbofabricnature", 115, $item_category,"", 1, "Display", "", "",1,"2,3" ); ?>
                                    </td>
                                    <td id="color_td"> 
                                   <?  echo create_drop_down( "cbo_color_name", 125, $blank_array,"", 1, "-- Select Color --", $selected, "open_color_popup()" );?>
                                    <input type="hidden" id="updateid" name="updateid"  class="text_boxes" style="width:20px"  />   
                                   </td>
                                </tr>
                </tbody>
                </table>
            </form>
        </fieldset>
<?
}

if ($action=="set_data"){
	$data_array=sql_select("select gmts_item_id,fabric_nature_id as fab_nature_id from sample_development_fabric_acc where id='$data' and is_deleted=0 and status_active=1");
	//echo "select gmts_item_id,fabric_nature_id as fab_nature_id from sample_development_fabric_acc where id='$data' and is_deleted=0 and status_active=1";
	foreach ($data_array as $row){
		echo "document.getElementById('cbogmtsitem').value = '".$row[csf("gmts_item_id")]."';\n"; 
		echo "document.getElementById('cbofabricnature').value = '".$row[csf("fab_nature_id")]."';\n";
	}
}
if ($action=="load_drop_down_color"){
	$color_arr=array();
	$data=explode('_',$data);
	//echo "AAAAAAAAAAAA";
	$color_arr=array();
	$sql_data=sql_select("select c.color_id as color_id from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id   and a.requisition_number='$data[0]' and b.gmts_item_id=$data[1] and b.id in($data[2]) and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.color_type_id in ($colorTypeId_static) group by c.color_id order by c.color_id");//gmts_item_id
	//echo "select c.color_id as color_id from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id   and a.requisition_number='$data[0]' and b.gmts_item_id=$data[1] and b.id in($data[2]) and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.color_type_id in (2,3,4,6,31,32,33,34) group by c.color_id order by c.color_id";
	
	foreach($sql_data as $row){
		$color_arr[$row[csf('color_id')]]=$color_library[$row[csf('color_id')]];
	}
	
	echo create_drop_down( "cbo_color_name", 125, $color_arr,"", 1, "-- Select Color --", $selected, "open_color_popup()" );     	 
}

if ($action=="open_color_list_view"){
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script>
var permission='<? echo $permission; ?>';
function add_break_down_set_tr( i )
{
	var row_num=$('table#tbl_set_details tbody tr').length;
	if (row_num!=i)
	{
		return false;
	}
	if (form_validation('stcolor_'+i+'*measurement_'+i+'*cboorderuom_'+i,'Stripe Color*Measurement*UOM')==false)
	{
		return;
	}
	else
	{
		i++;
		 $("table#tbl_set_details tbody tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("table#tbl_set_details tbody");
		 // $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
		  $('#measurement_'+i).removeAttr("onChange").attr("onChange","calculate_fidder("+i+")");
		  $('#totfidder_'+i).removeAttr("onChange").attr("onChange","calculate_fidder("+i+")");
		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		//  alert(i);
		  $('#stcolor_'+i).val(''); 
		  $('#measurement_'+i).val(''); 
		  $('#cboorderuom_'+i).val(''); 
		  $('#totfidder_'+i).val('');
		  $('#stcolor_'+i).removeAttr('disabled','disabled');
	}
}

function fn_delete_down_tr(rowNo,table_id) 
{   
	if(table_id=='tbl_set_details')
	{
		var numRow = $('table#tbl_set_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_set_details tbody tr:last').remove();
			set_sum();
			calculate_fab_req()
		}
	}
}

function color_select_popup(buyer_name,texbox_id)
{
	//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
	//alert(texbox_id)
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'stripe_color_measurement_sample_requisition_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=300px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#'+texbox_id).val(color_name.value);
		}
	}
}

function calculate_fidder(i){
	set_sum();
	calculate_fab_req()
}
function set_sum(){
	var tottalmeasurement=0;
	var totaltotfidder=0;
	var row_num=$('table#tbl_set_details tbody tr').length;
	for (var i=1; i<=row_num; i++)
	{
		var measurement=document.getElementById('measurement_'+i).value*1;
		var totfidder=document.getElementById('totfidder_'+i).value*1;
		tottalmeasurement+=measurement;
		totaltotfidder+=totfidder;
	}
	if(tottalmeasurement>0){
		document.getElementById('tottalmeasurement').value=number_format_common(tottalmeasurement,5,0);
	}
	else{
		document.getElementById('tottalmeasurement').value='';
	}
	if(totaltotfidder>0){
		document.getElementById('totaltotfidder').value=number_format_common(totaltotfidder,5,0);
	}
	else{
		document.getElementById('totaltotfidder').value='';
	}
}

function calculate_fab_req(){
    var consdzn=document.getElementById('consdzn').value*1;
	var TotalGreyreq=document.getElementById('TotalGreyreq').value*1;
	var totaltotfidder=document.getElementById('totaltotfidder').value*1;
	var tottalmeasurement=document.getElementById('tottalmeasurement').value*1;
	var row_num=$('table#tbl_set_details tbody tr').length;
	var totalfabreq=0;
	for (var i=1; i<=row_num; i++)
	{
		/*var totfidder=document.getElementById('totfidder_'+i).value*1;
		var fabreq=0;
		var fabreqtotkg=0;
		if(totfidder>0){
			fabreq=(consdzn/totaltotfidder)*totfidder;
			totalfabreq+=fabreq;
			fabreqtotkg=(TotalGreyreq/totaltotfidder)*totfidder;
		}else{
			var measurement=document.getElementById('measurement_'+i).value*1;
			fabreq=(consdzn/tottalmeasurement)*measurement;
			totalfabreq+=fabreq;
			fabreqtotkg=(TotalGreyreq/tottalmeasurement)*measurement;
		}*/
		var measurement=document.getElementById('measurement_'+i).value*1;
		var fabreq=0;
		var fabreqtotkg=0;
		if(measurement>0){
			fabreq=(consdzn/tottalmeasurement)*measurement;
			totalfabreq+=fabreq;
			fabreqtotkg=(TotalGreyreq/tottalmeasurement)*measurement;
			
		}else{
			var totfidder=document.getElementById('totfidder_'+i).value*1;
			fabreq=(consdzn/totaltotfidder)*totfidder;
			totalfabreq+=fabreq;
			fabreqtotkg=(TotalGreyreq/totaltotfidder)*totfidder;
		}
		document.getElementById('fabreq_'+i).value=number_format_common(fabreq,5,0);
		document.getElementById('fabreqtotkg_'+i).value=number_format_common(fabreqtotkg,5,0);
	}
	if(totalfabreq>0){
		document.getElementById('totalfabreq').value=number_format_common(totalfabreq,5,0);
	}else{
		document.getElementById('totalfabreq').value='';
	}
}

function fnc_stripe_color( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted")
		return;
	}
	    var row_num=$('table#tbl_set_details tbody tr').length;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('stcolor_'+i+'*measurement_'+i+'*cboorderuom_'+i,'Stripe Color*Measurement*UOM')==false)
			{
				return;
			}
			data_all=data_all+get_submitted_data_string('txt_job_no*cbogmtsitem*fabric_cost_id*cbo_color_name*stcolor_'+i+'*measurement_'+i+'*cboorderuom_'+i+'*totfidder_'+i+'*fabreq_'+i+'*fabreqtotkg_'+i+'*yarndyed_'+i,"../../../",i);
		}
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		freeze_window(operation);
		http.open("POST","stripe_color_measurement_sample_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_stripe_color_reponse;
}

function fnc_stripe_color_reponse()
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
 <?
   /* $condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("='$txt_job_no'");
	}
	$condition->init();
	$GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
	//print_r($GmtsitemRatioArr);
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();	*/
	//$TotalGreyreq=array_sum($fabric_costing_arr['knit']['grey'][$fabric_cost_id][$cbo_color_name]);
	$fabric_color=array();
	$color_type_id=0;				
	$fab_des='';
	
	/*$sql_data=sql_select("select a.job_no, b.id ,c.item_number_id ,c.country_id ,c.color_number_id ,c.size_number_id ,c.order_quantity ,c.plan_cut_qnty  ,d.id as pre_cost_dtls_id ,d.body_part_id ,d.fab_nature_id ,d.fabric_source ,d.color_type_id, d.fabric_description,d.color_size_sensitive,d.rate ,e.cons ,e.requirment,f.contrast_color_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 and d.id=$fabric_cost_id and c.color_number_id=$cbo_color_name and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,d.id");//
	 
	foreach($sql_data as $row){
		$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		$color_type_id=$row[csf("color_type_id")];
		if($row[csf('color_size_sensitive')]==1){
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		}else{
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
		}
	}*/
	//$fab_description_array=sql_select("select id, gmts_item_id,body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$fabric_cost_id and color_type_id in (2,3,4,6,31,32,33,34)");
	/*$tot_sample_dtls=0;
	$sample_dtls=sql_select("select b.sample_prod_qty from sample_development_dtls b,sample_development_mst a where b.sample_mst_id=a.id and a.requisition_number='$txt_job_no' and b.sample_color=$cbo_color_name and b.gmts_item_id=$cbogmtsitem");
	//echo "select b.sample_prod_qty from sample_development_dtls b,sample_development_mst a where b.sample_mst_id=a.id and a.requisition_number='$txt_job_no' and b.sample_color=$cbo_color_name and b.gmts_item_id=$cbogmtsitem";
	foreach($sample_dtls as $row)
	{
			$tot_sample_dtls+=$row[csf('sample_prod_qty')];;
	}*/
	
	$sql_sample_data=sql_select("SELECT a.requisition_number,b.id,b.gmts_item_id,b.body_part_id,b.fabric_description,b.color_type_id,c.grey_fab_qnty as required_dzn,c.grey_fab_qnty as required_qty,c.color_id as color_id,c.contrast as contrast,c.grey_fab_qnty from  sample_development_mst a,sample_development_rf_color c, sample_development_fabric_acc b where a.id=b.sample_mst_id and c.dtls_id=b.id and a.id=c.mst_id   and a.requisition_number='$txt_job_no' and c.color_id=$cbo_color_name and b.id in($fabric_cost_id) and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.color_type_id in ($colorTypeId_static)");
	foreach( $sql_sample_data as $row )
	{
		$samp_req_qnty+=$row[csf('grey_fab_qnty')];
		$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		$color_type_id=$row[csf("color_type_id")];
		$fabric_color[$row[csf('color_id')]]=$row[csf('color_id')];
		$fab_color[$row[csf('color_id')]]=$row[csf('contrast')];
		$fabric_color_qty_arr[$row[csf('id')]][$row[csf('color_id')]]=$row[csf('required_dzn')];
	}
	$TotalGreyreq=$samp_req_qnty;
 ?>
 <table width="460" cellspacing="0" class="rpt_table" border="1" rules="all">
 <!--<tr>
    <td width="150">Order Qty of Respective Color</td>
    <td width="150" align="right"><input type="hidden" id="pluncut" value="<?  //echo $plan_cut_qnty;?> "/><? //echo $plan_cut_qnty; ?></td>
    <td width="60">Pcs</td>
 </tr>-->
  <tr>
    <td width="150">Cons</td>
    <td width="150" align="right">
    <input type="hidden" id="TotalGreyreq" value="<?  echo $TotalGreyreq;?> "/>
    <input type="hidden" id="consdzn" value="<?  echo number_format($TotalGreyreq,4);//number_format(($TotalGreyreq/$samp_req_qnty)*$cost_per_qty*$GmtsitemRatio,4);?> "/>
	<? echo number_format($TotalGreyreq,4); ?>
    </td>
    <td width="60">Kg <? //echo $plan_cut_qnty; ?></td>
 </tr>
 <!--  <tr >
    <td width="150">Total Grey req</td>
    <td width="150" align="right"><input type="hidden" id="TotalGreyreq" value="<?  //echo $TotalGreyreq;?> "/><? //echo $TotalGreyreq; ?></td>
    <td width="60">Kg</td>
 </tr>-->
  </tr>
   <tr>
    <td width="150">Fabric Desc</td>
    <td width="150" colspan="2"><? echo $fab_des; ?></td>
 </tr>
   </tr>
   <tr>
    <td width="150">Body Color</td>
    <td width="150" colspan="2"><? echo $color_library[$cbo_color_name]; ?></td>
 </tr>
 </table>
 <br/>
 <input type="hidden" id="txt_job_no" name="txt_job_no" style="width:150px" class="text_boxes" value="<? echo $txt_job_no; ?>"/>
 <input type="hidden" id="cbogmtsitem" name="cbogmtsitem" style="width:150px" class="text_boxes" value="<? echo $cbogmtsitem; ?>"/>
 <input type="hidden" id="fabric_cost_id" name="fabric_cost_id" style="width:150px" class="text_boxes" value="<? echo $fabric_cost_id; ?>"/>
 <input type="hidden" id="cbo_color_name" name="cbo_color_name" style="width:150px" class="text_boxes" value="<? echo $cbo_color_name; ?>"/>

<table width="680" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
    <thead>
        <tr>
            <th width="150">Stripe Color</th><th width="150">Measurement</th><th width="60">UOM</th><th width="80">Total Feeder</th><th width="70">Fab Req. Qty (kg)</th><th width="70">Yarn Dyed</th><th></th>
        </tr>
    </thead>
    <tbody>
    <?
	$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
	if($color_from_library==1)
	{
	   $readonly="readonly='readonly'";
	   $plachoder="placeholder='Click'";
	   $onClick="onClick='color_select_popup($cbo_buyer_name,this.id)'";
	}
	else
	{
	   $readonly="";
	   $plachoder="";
	   $onClick="";
	}
	$save_update=1;
	$sql_data=sql_select("select stripe_color, measurement, uom,totfidder,fabreq,fabreqtotkg,yarn_dyed from  wo_sample_stripe_color where sample_fab_dtls_id=$fabric_cost_id and color_number_id=$cbo_color_name");
	if(count($sql_data)>0)
	{
		$i=1;
		$totmeasurement=0;
		$totfidder=0;
		$fabreq=0;
		foreach($sql_data as $row)
		{
		$totmeasurement+=$row[csf('measurement')];
		$totfidder+=$row[csf('totfidder')];
		$fabreq+=$row[csf('fabreq')];
	?>
        <tr>
            <th>
            <input type="text" id="stcolor_<? echo $i; ?>" name="stcolor_<? echo $i; ?>" style="width:150px" class="text_boxes" value="<? echo $color_library[$row[csf('stripe_color')]]; ?>" <? echo $onClick." ".$readonly." ".$plachoder; ?> />
            </th>
            <th>
            <input type="text" id="measurement_<? echo $i; ?>" name="measurement_<? echo $i; ?>" style="width:150px" class="text_boxes_numeric" value="<? echo $row[csf('measurement')]; ?>" onChange="calculate_fidder(<? echo $i;?>)"/> 
            </th>
            <th><? echo create_drop_down( "cboorderuom_".$i,60, $unit_of_measurement, "",1, "-Select-", $row[csf('uom')],"","","25,26,29,79" ); ?></th>
            <th>
            <input type="text" id="totfidder_<? echo $i; ?>" name="totfidder_<? echo $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('totfidder')]; ?>" onChange="calculate_fidder(<? echo $i;?>)"/> 
            </th>
            <th>
            <input type="text" id="fabreq_<? echo $i; ?>" name="fabreq_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $row[csf('fabreq')]; ?>" readonly/> 
            <input type="hidden" id="fabreqtotkg_<? echo $i; ?>" name="fabreqtotkg_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $row[csf('fabreqtotkg')]; ?>" readonly/> 
            </th>
           <th><? echo create_drop_down( "yarndyed_".$i,60, $yes_no, "",0, "", $row[csf('yarn_dyed')],"","","" ); ?></th>
            <th>
            <?
			if($color_type_id !=6 && $color_type_id !=31 && $color_type_id !=32){
			?>
            <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?>)" />
            <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
            <?
			}
			?>
            </th>
        </tr>  
    <?
	$i++;
		}
	}
	else
	{
		$colorTypeArr=array(2,3,4,5,6,7,20,33,34,35,45);
		
		$save_update=0;
		if($color_type_id ==31 || $color_type_id ==32 || $color_type_id ==63 || $color_type_id ==71 || $color_type_id ==76){
			$color=$color_library[$fabric_color[$cbo_color_name]];
			$dis="disabled";
		}elseif(in_array($color_type_id,$colorTypeArr)){
			$color=$fab_color[$cbo_color_name];
			$dis="disabled";
		}else{
			$color="";
			$dis="";
		}
		 //echo $color_type_id.'=D';
		$StripcolorTypeArr=array(2,7,35,44,45,47,48,65,74,82,84);
		if(in_array($color_type_id,$StripcolorTypeArr)) // As Per Saeed for Micro // Issue Id=11159
		{
			$dis="";
		}
	?>
        <tr>
            <th><input type="text" id="stcolor_1" name="stcolor_1" style="width:150px" class="text_boxes" <? echo $onClick." ".$readonly." ".$plachoder." ".$dis; ?> value="<? echo $color;  ?>"/> </th>
            <th><input type="text" id="measurement_1" name="measurement_1" style="width:150px" class="text_boxes_numeric" onChange="calculate_fidder(<? echo $i;?>)"/> </th>
            
            <th><? echo create_drop_down( "cboorderuom_1",60, $unit_of_measurement, "",0, "", 25, "","","25,26,29,79" ); ?></th>
            <th><input type="text" id="totfidder_1" name="totfidder_1" style="width:80px" class="text_boxes_numeric"/> </th>
            <th>
            <input type="text" id="fabreq_1" name="fabreq_1" style="width:70px" class="text_boxes_numeric" readonly/> 
            <input type="hidden" id="fabreqtotkg_1" name="fabreqtotkg_1" style="width:70px" class="text_boxes_numeric" readonly/>
            </th>
            <th><? echo create_drop_down( "yarndyed_1",60, $yes_no, "",0, "", "","","","" ); ?></th>
            <th>
            <?
			if($color_type_id !=6 && $color_type_id !=31 && $color_type_id !=32){
			?>
            <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
            <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
            <?
			}
			?>
            </th>
        </tr>
   <? 
   }
   ?>
    </tbody>
    
    <tfoot>
        <tr>
            <th style=" width:150px"></th>
            <th><input type="text" id="tottalmeasurement" name="tottalmeasurement" style="width:150px" class="text_boxes_numeric" value="<? echo number_format($totmeasurement,4); ?>" readonly/> </th>
            <th style=" width:80px"></th>
            <th><input type="text" id="totaltotfidder" name="totaltotfidder" style="width:80px" class="text_boxes_numeric" value="<? echo number_format($totfidder,4); ?>" readonly/> </th>
            <th>
            <input type="text" id="totalfabreq" name="totalfabreq" style="width:70px" class="text_boxes_numeric" value="<? echo number_format($fabreq,4); ?>" readonly/> 
            </th>
            <th style=" width:70px"></th>
            <th>
           </th>
        </tr>
    <tr>
    <td align="center" valign="middle" class="button_container" colspan="8"> 
	<?
	if ( count($sql_data)>0)
	{
	    echo load_submit_buttons( $permission, "fnc_stripe_color", 1,0 ,"",1,0) ;
		// echo load_submit_buttons( $permission, "fnc_stripe_color", 1,0 ,"",1,0) ; Approve

	}
	else
	{
	    echo load_submit_buttons( $permission, "fnc_stripe_color", 0,0 ,"",1,0) ;
	}
    ?>  
    </td>
    </tr>
    </tfoot>
</table>
</div>
</body> 
<script>
//set_sum();
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="color_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script> 
function js_set_value(data)
{
	document.getElementById('color_name').value=data;
    parent.emailwindow.hide();
}
</script> 
</head>
<body>
<body>
<div align="center">
<form>
<input type="hidden" id="color_name" name="color_name" />
<?
if($buyer_name=="" || $buyer_name==0)
{
    $sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0"; 
}
else
{
	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0"; 
}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	
	
	
	?>
    </form>
    </div>
    </body>
    </html>
    <?
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0){
	$con = connect();
	if($db_type==0){
	mysql_query("BEGIN");
	}
//	die;
	if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}	
	$new_array_color=array();
	
	$id=return_next_id( "id", "wo_sample_stripe_color", 1 ) ;
	$field_array="id,req_no,item_number_id,sample_fab_dtls_id,color_number_id,stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed,inserted_by,insert_date";
	for ($i=1;$i<=$total_row;$i++){
	$stcolor="stcolor_".$i;
	$measurement="measurement_".$i;
	$cboorderuom="cboorderuom_".$i;
	$fabreqtotkg="fabreqtotkg_".$i;
	$totfidder="totfidder_".$i;
	$fabreq="fabreq_".$i;
	$yarndyed="yarndyed_".$i;

	if(str_replace("'","",$$stcolor)!="")
	{
		if (!in_array(str_replace("'","",$$stcolor),$new_array_color)){
			$color_id = return_id( str_replace("'","",$$stcolor), $color_library, "lib_color", "id,color_name","117");  
			$new_array_color[$color_id]=str_replace("'","",$$stcolor);
		}
		else $color_id =  array_search(str_replace("'","",$$stcolor), $new_array_color);
	}
	else $color_id =0;
	
	if ($i!=1) $data_array .=",";
	$data_array .="(".$id.",".$txt_job_no.",".$cbogmtsitem.",".$fabric_cost_id.",".$cbo_color_name.",".$color_id.",".$$measurement.",".$$cboorderuom.",".$$totfidder.",".$$fabreq.",".$$fabreqtotkg.",".$$yarndyed.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
	$id=$id+1;
	}
	$rID=sql_insert("wo_sample_stripe_color",$field_array,$data_array,1);
	//echo "10**insert into wo_sample_stripe_color($field_array) values".$data_array;die;
	check_table_status( $_SESSION['menu_id'],0);
	if($db_type==0){
		if($rID ){
			mysql_query("COMMIT");  
			echo "0";
		}
		else{
			mysql_query("ROLLBACK"); 
			echo "10";
		}
	}
	
	if($db_type==2 || $db_type==1 ){
		if($rID ){
			oci_commit($con);  
			echo "0";
		}
		else{
			oci_rollback($con);  
			echo "10";
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
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}	
		 $new_array_color=array();
		 $id=return_next_id( "id", "wo_sample_stripe_color", 1 ) ;
		 $field_array="id,req_no,item_number_id,sample_fab_dtls_id,color_number_id,stripe_color,measurement,uom,totfidder,fabreq,fabreqtotkg,yarn_dyed,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$stcolor="stcolor_".$i;
			$measurement="measurement_".$i;
			$cboorderuom="cboorderuom_".$i;
			$fabreqtotkg="fabreqtotkg_".$i;
			$totfidder="totfidder_".$i;
			$fabreq="fabreq_".$i;
			$yarndyed="yarndyed_".$i;
			
			if(str_replace("'","",$$stcolor)!="")
			{
				if (!in_array(str_replace("'","",$$stcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$stcolor), $color_library, "lib_color", "id,color_name","117");  
					$new_array_color[$color_id]=str_replace("'","",$$stcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$stcolor), $new_array_color);
			}
			else $color_id =0;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$cbogmtsitem.",".$fabric_cost_id.",".$cbo_color_name.",".$color_id.",".$$measurement.",".$$cboorderuom.",".$$totfidder.",".$$fabreq.",".$$fabreqtotkg.",".$$yarndyed.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		 $rID_de3=execute_query( "delete from wo_sample_stripe_color where  sample_fab_dtls_id =".$fabric_cost_id." and color_number_id=$cbo_color_name",0);
		 $rID=sql_insert("wo_sample_stripe_color",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0)
		 {
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		 }
		 if($db_type==2 || $db_type==1 )
		 {
			if($rID ){
				oci_commit($con);  
				echo "1";
			}
			else{
				oci_rollback($con);  
				echo "10";
			}
		 }
		disconnect($con);
		die;
	}
}
if($action=="delete_row"){
	$data=explode("_",$data);
	/*$yarn_booking=0;
	$yarn_booking_sql=sql_select("select id from wo_yarn_dyeing_dtls where job_no ='$data[2]' and status_active=1 and is_deleted=0");
	foreach($yarn_booking_sql as $yarn_booking_row){
		$yarn_booking=$yarn_booking_row[csf('id')];
	}
	if($yarn_booking>0){
		echo 11;
		die;
	}*/
	$con = connect();
	if($db_type==0){
		mysql_query("BEGIN");
	}
	$rID_de3=execute_query( "delete from wo_sample_stripe_color where  sample_fab_dtls_id =".$data[0]." and color_number_id=$data[1]",0);
	if($db_type==0){
		if($rID_de3){
			mysql_query("COMMIT");  
			echo 1;
		}
		else{
			mysql_query("ROLLBACK"); 
			echo 10;
		}
	}
	
	if($db_type==2 || $db_type==1 ){
		if($rID_de3){
			oci_commit($con);  
			echo 1;
		}
		else{
			oci_rollback($con);  
			echo 10;
		}
	}
	disconnect($con);
}


if($action=="stripe_color_list_view"){
	$data=explode("_",$data);
	$fab_description=array();
	$mst_id=return_field_value("id", "sample_development_mst", "requisition_number='$data[0]' and entry_form_id=117");
	$fab_description_array=sql_select("select id, body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$mst_id and  color_type_id in (2,3,4,6,31,32,33,34,47,63,65,71,76)");
	//echo "select id, body_part_id, color_type_id, fabric_description from sample_development_fabric_acc where sample_mst_id=$mst_id and  color_type_id in (2,3,4,6,31,32,33,34)";
	foreach( $fab_description_array as $row_fab_description_array ){
	  $fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("fabric_description")];
	}
	
	$color_arr=array();
	$sql_data=sql_select("select a.color_id as color_id from  sample_development_rf_color a, sample_development_fabric_acc b where a.dtls_id=b.id  and a.mst_id=$mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.color_type_id in (2,3,4,6,31,32,33,34,47,63,65,71,76) group by a.color_id order by a.color_id");
	//echo "select a.fabric_color as color_id from  sample_development_rf_color a, sample_development_fabric_acc b where a.dtls_id=b.id  and a.mst_id=$mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.color_type_id in (2,3,4,6,31,32,33,34) group by a.fabric_color order by a.fabric_color";
	foreach($sql_data as $row){
		$color_arr[$row[csf('color_id')]]=$row[csf('color_id')];
	}
	
	$sql_data=sql_select("select sample_fab_dtls_id,color_number_id from wo_sample_stripe_color where req_no='$data[0]' and is_deleted=0 and status_active=1 group by sample_fab_dtls_id,color_number_id");
	//echo "select sample_fab_dtls_id,color_number_id from wo_sample_stripe_color where req_no='$data[0]' and is_deleted=0 and status_active=1 group by sample_fab_dtls_id,color_number_id";
	$i=1;
	foreach($sql_data as $row){
		?>
        <div style="width:90%; float:left">
            <h3 align="left" class="accordion_h" onClick="show_content_data(<? echo $row[csf('sample_fab_dtls_id')]; ?>, <? echo $row[csf('color_number_id')]; ?>)"><div style="width:50%; float:left"><? echo $fab_description[$row[csf('sample_fab_dtls_id')]].", ". $color_library[$row[csf('color_number_id')]];  ?></div> <div style="width:50%; float:left; text-align:right; color:#F00" title="<? echo $row[csf('color_number_id')].'='.$color_arr[$row[csf('color_number_id')]];?>"><? if($row[csf('color_number_id')] !=$color_arr[$row[csf('color_number_id')]]){ echo "This Color is deleted From Color Size Break Down";} ?></div></h3>
        </div>
        <div style="width:10%; float:left">
           <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $row[csf('sample_fab_dtls_id')]; ?>, <? echo $row[csf('color_number_id')]; ?> );" />
        </div>
        <?
		$i++;
	}
}
?>
