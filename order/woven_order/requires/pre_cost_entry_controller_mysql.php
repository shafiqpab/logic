<? 
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Garments Pre Cost Entry.
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
Updated by 		         : 	Aziz: 1. (Line No-> 16603,  Added> 'and status_active=1 and is_deleted=0' ), 2. Added =>Remark in Trim Cost PopUp(18-5-15)	;
Update date		         : 	13-5-15   
QC Performed BY	         :		
QC Date			         :	
Comments		         : This version  is oracle Compatible
-------------------------------------------------------------------------------*/

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
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");

//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************

if($action=="save_post_session")
{
	/*$data=explode( "&",$data);
	foreach($data as $datas)
	{
		$fdata=explode( "=",$datas);
		$_SESSION['logic_erp'][$fdata[0]]="";
		$_SESSION['logic_erp'][$fdata[0]]=$fdata[1];
	}*/
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$_SESSION['logic_erp']['cons_breck_downn']="";
	$_SESSION['logic_erp']['msmnt_breack_downn']="";
	$_SESSION['logic_erp']['marker_breack_down']="";
	
	$_SESSION['logic_erp']['cons_breck_downn']=$cons_breck_downn;
	$_SESSION['logic_erp']['msmnt_breack_downn']=$msmnt_breack_downn;
	$_SESSION['logic_erp']['marker_breack_down']=$marker_breack_down;
	echo 1;
	die;
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 160, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );   
	 	 
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

if ($action=="load_drop_down_po")
{
	echo create_drop_down( "txt_po_breack_down_id", 160, "select id,po_number from  wo_po_break_down   where job_no_mst='$data' and status_active =1 and is_deleted=0 ","id,po_number", 1, "-- Select po --", $selected, "" );   
	 	 
}

if ($action=="cm_cost_predefined_method")
{
	$cm_cost_method=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=22 and status_active=1 and is_deleted=0");
	if($cm_cost_method=="")
	{
		$cm_cost_method=0;
	}
	echo $cm_cost_method;
	die;
}

if ($action=="cost_per_minute")
{
	$data=explode("_",$data);
	if($data[1]=="" || $data[1]==0)
	{
		if($db_type==0)
		{
		   $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1)	;
		}
	}
	else
	{
		if($db_type==0)
		{
		   $txt_costing_date=change_date_format($data[1], "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_costing_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
		}
	}
	
	/*if($db_type==0)
		{
		   $txt_costing_date=change_date_format($data[1], "yyyy-mm-dd", "-");	
		}
		if($db_type==2)
		{
			$txt_costing_date=change_date_format($data[1], "yyyy-mm-dd", "-",1)	;
		}*/
	
	$monthly_cm_expense=0;
	$no_factory_machine=0;
	$working_hour=0;
	$cost_per_minute=0;
	$depreciation_amorti=0;
	if($db_type==0)
	{
	$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,depreciation_amorti,operating_expn from lib_standard_cm_entry where company_id=$data[0] and '$txt_costing_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0 LIMIT 1";
	}
	if($db_type==2)
	{
	$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,depreciation_amorti,operating_expn from lib_standard_cm_entry where company_id=$data[0] and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0";
	}
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		if($row[csf("monthly_cm_expense")] !="")
		{
		  $monthly_cm_expense=$row[csf("monthly_cm_expense")];
		}
		if($row[csf("no_factory_machine")] !="")
		{
		  $no_factory_machine=$row[csf("no_factory_machine")];
		}
		if($row[csf("working_hour")] !="")
		{
		  $working_hour=$row[csf("working_hour")];
		}
		if($row[csf("cost_per_minute")] !="")
		{
		  $cost_per_minute=$row[csf("cost_per_minute")];
		}
		if($row[csf("depreciation_amorti")] !="")
		{
		  $depreciation_amorti=$row[csf("depreciation_amorti")];
		}
		if($row[csf("operating_expn")] !="")
		{
		  $operating_expn=$row[csf("operating_expn")];
		}
	}
	$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;
	echo $data;
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
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
	<table width="1200" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead> 
                    <tr>
                     <th width="150" colspan="3"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="3"> </th>
                    </tr> 
                    <tr>              	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref </th>
                         <th width="100">Internal Ref </th>
                        <th width="100">File No </th>
                        <th width="150">Order No</th>
                        <th width="200">Date Range</th><th></th>  
                        </tr>         
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'pre_cost_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                     <td>
                     <input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px">
                     </td>
                       <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                       <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                       <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                       <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                     <td>
                     <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px">
                     </td>
                     <td>
                     <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					 <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'pre_cost_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	$job_cond="";
	$order_cond="";
	$style_cond=""; 
	if($data[8]==1)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'"; //else  $style_cond=""; 
	}
	if($data[8]==2)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond=""; 
	}
	if($data[8]==3)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'"; //else  $style_cond=""; 
	}
	if($data[8]==4 || $data[8]==0)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'"; //else  $style_cond=""; 
	}
	$internal_ref = str_replace("'","",$data[9]);
	$file_no = str_replace("'","",$data[10]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);
	if($db_type==0)
	{
	$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond order by a.job_no";
	}
	if($db_type==2)
	{
	$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $style_cond $order_cond order by a.job_no";
	}
	//echo $sql;
	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Internal Ref,File No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "90,80,120,100,100,100,100,90,90,90,80,100","1080","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0,0", $arr , "year,job_no_prefix_num,company_name,buyer_name,style_ref_no,grouping,file_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,0,0,1,0,1,3,0') ;
}

if ($action=="populate_data_from_job_table")
{
	$company_name="";
	$copy_quotation_id="";
	$quotation_id='';
	$avg_unit_price='';
	$price_costing_per='';
	if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping, 
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
	else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
	$job_data_arr=array();
	$data_array=sql_select("select a.job_no,$group_concat_all from wo_po_details_master a, wo_po_break_down b where a.job_no='$data' and a.job_no=b.job_no_mst group by a.job_no");
	foreach($data_array as $row)
	{
		$job_data_arr[$row[csf('job_no')]]['file']=$row[csf('file_no')];
		$job_data_arr[$row[csf('job_no')]]['ref']=$row[csf('grouping')];
	}
	$data_array=sql_select("select id,garments_nature,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,style_description,product_dept,product_code,currency_id,agent_name,order_repeat_no,region,team_leader,dealing_marchant,packing,remarks,job_quantity,avg_unit_price,ship_mode,order_uom,set_break_down,gmts_item_id,total_set_qnty,set_smv,quotation_id from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "reset_form('quotationdtls_2','','');\n";  
		echo "load_drop_down( 'requires/pre_cost_entry_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/pre_cost_entry_controller', '".$row[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/pre_cost_entry_controller', '".$row[csf("job_no")]."', 'load_drop_down_po', 'po_td' ); ;\n";
		echo "change_caption_cost_dtls('".$row[csf(order_uom)]."','change_caption_pcs')\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		$grouping=implode(",",array_unique(explode(",",$job_data_arr[$row[csf('job_no')]]['ref'])));
		$file_no=implode(",",array_unique(explode(",",$job_data_arr[$row[csf('job_no')]]['file'])));
		echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('cbo_pord_dept').value = '".$row[csf("product_dept")]."';\n"; 
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('txt_offer_qnty').value = '".$row[csf("job_quantity")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_agent').attr('disabled','true')".";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_region').attr('disabled','true')".";\n";
		echo "$('#cbo_order_uom').attr('disabled','true')".";\n";
		$quotation_id=$row[csf("quotation_id")];
		$company_name=$row[csf("company_name")];
		$sql_vari_seting_copy_quotation=sql_select("select copy_quotation from variable_order_tracking where company_name='$company_name' and variable_list=20 and status_active=1 and is_deleted=0");
		list($copy_quotation)= $sql_vari_seting_copy_quotation;
		$copy_quotation_id=$copy_quotation[csf('copy_quotation')];
		echo "document.getElementById('copy_quatation_id').value = '".$copy_quotation_id."';\n"; 
		// calculation for Dtls Table
		$avg_unit_price=$row[csf("avg_unit_price")];
			
		echo "document.getElementById('txt_final_price_pcs_pre_cost').value = '".$avg_unit_price."';\n";
        echo "calculate_confirm_price_dzn()\n";
		echo "cm_cost_predefined_method('".$row[csf("company_name")]."')\n";
		echo "set_multiselect('txt_po_breack_down_id','0','0','0','0');\n";  

	   // calculation for Dtls Table End
	}
	$cbo_approved_status=="";
	$data_array=sql_select("select costing_date,incoterm,incoterm_place,machine_line,prod_line_hr,costing_per,copy_quatation,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent, 	cut_effi_percent,efficiency_wastage_percent,remarks,approved,ready_to_approved from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	if (count($data_array)>0)
	{
		foreach ($data_array as $row)
		{
			echo "document.getElementById('txt_costing_date').value = '".change_date_format($row[csf("costing_date")],'dd-mm-yyyy','-')."';\n";  
			echo "document.getElementById('cbo_inco_term').value = '".$row[csf("incoterm")]."';\n"; 
			echo "document.getElementById('txt_incoterm_place').value = '".$row[csf("incoterm_place")]."';\n";
			echo "document.getElementById('txt_machine_line').value = '".$row[csf("machine_line")]."';\n";
			echo "document.getElementById('txt_prod_line_hr').value = '".$row[csf("prod_line_hr")]."';\n";  
			echo "document.getElementById('cbo_costing_per').value = '".$row[csf("costing_per")]."';\n";
			echo "document.getElementById('copy_quatation_id').value = '".$row[csf("copy_quatation")]."';\n";
			echo "document.getElementById('cm_cost_predefined_method_id').value = '".$row[csf("cm_cost_predefined_method_id")]."';\n";
			if($row[csf("cm_cost_predefined_method_id")]==0)
			{
				echo "cm_cost_predefined_method('".$company_name."')\n";
			}
			echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
			echo "document.getElementById('txt_sew_smv').value = '".$row[csf("sew_smv")]."';\n";
			echo "document.getElementById('txt_cut_smv').value = '".$row[csf("cut_smv")]."';\n";
			echo "document.getElementById('txt_sew_efficiency_per').value = '".$row[csf("sew_effi_percent")]."';\n";
			echo "document.getElementById('txt_cut_efficiency_per').value = '".$row[csf("cut_effi_percent")]."';\n";
			echo "document.getElementById('txt_efficiency_wastage').value = '".$row[csf("efficiency_wastage_percent")]."';\n";
			echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n"; 
			echo "document.getElementById('cbo_approved_status').value = '".$row[csf("approved")]."';\n"; 
			echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n"; 
			echo "change_caption_cost_dtls('".$row[csf("costing_per")]."','change_caption_dzn')\n";
			$cbo_approved_status= $row[csf("approved")];
			if($row[csf("costing_per")]==1)
			{
				$price_costing_per=$avg_unit_price*1*12;	
			}
			if($row[csf("costing_per")]==2)
			{
				$price_costing_per=$avg_unit_price*1*1;	
			}
			if($row[csf("costing_per")]==3)
			{
				$price_costing_per=$avg_unit_price*2*12;	
			}
			if($row[csf("costing_per")]==4)
			{
				$price_costing_per=$avg_unit_price*3*12;	
			}
			if($row[csf("costing_per")]==5)
			{
				$price_costing_per=$avg_unit_price*4*12;	
			}
			if($cbo_approved_status==1)
			{
				echo "document.getElementById('approve1').value = 'Un-Approved';\n"; 
				echo "$('#txt_costing_date').attr('disabled','true')".";\n";
				echo "$('#cbo_inco_term').attr('disabled','true')".";\n";
				echo "$('#txt_incoterm_place').attr('disabled','true')".";\n";
				echo "$('#txt_machine_line').attr('disabled','true')".";\n";
				echo "$('#txt_prod_line_hr').attr('disabled','true')".";\n";
				echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
				echo "$('#copy_quatation_id').attr('disabled','true')".";\n";
				echo "$('#txt_remarks').attr('disabled','true')".";\n";
				echo "$('#save1').attr('disabled','true')".";\n";
				echo "$('#update1').attr('disabled','true')".";\n";
				echo "$('#Delete1').attr('disabled','true')".";\n";
			}
			else
			{
				echo "document.getElementById('approve1').value = 'Approved';\n";
				echo "$('#txt_costing_date').removeAttr('disabled')".";\n";
				echo "$('#cbo_inco_term').removeAttr('disabled')".";\n";
				echo "$('#txt_incoterm_place').removeAttr('disabled')".";\n";
				echo "$('#txt_machine_line').removeAttr('disabled')".";\n";
				echo "$('#txt_prod_line_hr').removeAttr('disabled')".";\n";
				echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
				echo "$('#copy_quatation_id').removeAttr('disabled')".";\n";
				echo "$('#txt_remarks').removeAttr('disabled')".";\n";
				echo "$('#save1').removeAttr('disabled')".";\n";
				echo "$('#update1').removeAttr('disabled')".";\n";
				echo "$('#Delete1').removeAttr('disabled')".";\n";
			}
			echo "set_button_status(1, permission, 'fnc_precosting_entry',1)\n";
		}
	}
	else
	{
		if($copy_quotation_id==1)
		{
			$data_array=sql_select("select quot_date,incoterm,incoterm_place,machine_line,prod_line_hr,costing_per,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,remarks from  wo_price_quotation where id='$quotation_id' and is_deleted=0 and status_active=1");
			foreach ($data_array as $row)
			{
				echo "document.getElementById('cbo_inco_term').value = '".$row[csf("incoterm")]."';\n"; 
				echo "document.getElementById('txt_incoterm_place').value = '".$row[csf("incoterm_place")]."';\n";
				echo "document.getElementById('txt_machine_line').value = '".$row[csf("machine_line")]."';\n";
				echo "document.getElementById('txt_prod_line_hr').value = '".$row[csf("prod_line_hr")]."';\n";  
				echo "document.getElementById('cbo_costing_per').value = '".$row[csf("costing_per")]."';\n"; 
				echo "document.getElementById('cm_cost_predefined_method_id').value = '".$row[csf("cm_cost_predefined_method_id")]."';\n";
				if($row[csf("cm_cost_predefined_method_id")]==0)
				{
					echo "cm_cost_predefined_method('".$company_name."')\n";
				}
				echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
				echo "document.getElementById('txt_sew_smv').value = '".$row[csf("sew_smv")]."';\n";
				echo "document.getElementById('txt_cut_smv').value = '".$row[csf("cut_smv")]."';\n";
				echo "document.getElementById('txt_sew_efficiency_per').value = '".$row[csf("sew_effi_percent")]."';\n";
				echo "document.getElementById('txt_cut_efficiency_per').value = '".$row[csf("cut_effi_percent")]."';\n";
				echo "document.getElementById('txt_efficiency_wastage').value = '".$row[csf("efficiency_wastage_percent")]."';\n";
				echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n"; 
				echo "change_caption_cost_dtls('".$row[csf(costing_per)]."','change_caption_dzn')\n";
				echo "set_button_status(0, permission, 'fnc_precosting_entry',1)\n";
				if($row[csf("costing_per")]==1)
				{
					$price_costing_per=$avg_unit_price*1*12;	
				}
				if($row[csf("costing_per")]==2)
				{
					$price_costing_per=$avg_unit_price*1*1;	
				}
				if($row[csf("costing_per")]==3)
				{
					$price_costing_per=$avg_unit_price*2*12;	
				}
				if($row[csf("costing_per")]==4)
				{
					$price_costing_per=$avg_unit_price*3*12;	
				}
				if($row[csf("costing_per")]==5)
				{
					$price_costing_per=$avg_unit_price*4*12;	
				}
			}
		}
	}
	$data_array=sql_select("select id, job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent,wash_cost,wash_cost_percent, comm_cost, comm_cost_percent,commission,commission_percent, lab_test,lab_test_percent,inspection, 	inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh, common_oh_percent,depr_amor_pre_cost,depr_amor_po_price, total_cost, total_cost_percent,price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche  from  wo_pre_cost_dtls where  job_no='$data' and status_active=1 and is_deleted=0");
	if (count($data_array)>0)
	{
		foreach ($data_array as $row)
		{
			echo "reset_form('quotationdtls_2','','');\n";  
			echo "document.getElementById('txt_fabric_pre_cost').value = '".$row[csf("fabric_cost")]."';\n";  
			echo "document.getElementById('txt_fabric_po_price').value = '".$row[csf("fabric_cost_percent")]."';\n";  
			echo "document.getElementById('txt_trim_pre_cost').value = '".$row[csf("trims_cost")]."';\n";  
			echo "document.getElementById('txt_trim_po_price').value = '".$row[csf("trims_cost_percent")]."';\n";  
			echo "document.getElementById('txt_embel_pre_cost').value = '".$row[csf("embel_cost")]."';\n";  
			echo "document.getElementById('txt_embel_po_price').value = '".$row[csf("embel_cost_percent")]."';\n";
			echo "document.getElementById('txt_wash_pre_cost').value = '".$row[csf("wash_cost")]."';\n";  
			echo "document.getElementById('txt_wash_po_price').value = '".$row[csf("wash_cost_percent")]."';\n"; 
			echo "document.getElementById('txt_comml_pre_cost').value = '".$row[csf("comm_cost")]."';\n";  
			echo "document.getElementById('txt_comml_po_price').value = '".$row[csf("comm_cost_percent")]."';\n"; 
			echo "document.getElementById('txt_commission_pre_cost').value = '".$row[csf("commission")]."';\n";  
			echo "document.getElementById('txt_commission_po_price').value = '".$row[csf("commission_percent")]."';\n"; 
			echo "document.getElementById('txt_lab_test_pre_cost').value = '".$row[csf("lab_test")]."';\n";  
			echo "document.getElementById('txt_lab_test_po_price').value = '".$row[csf("lab_test_percent")]."';\n";  
			echo "document.getElementById('txt_inspection_pre_cost').value = '".$row[csf("inspection")]."';\n";  
			echo "document.getElementById('txt_inspection_po_price').value = '".$row[csf("inspection_percent")]."';\n";  
			echo "document.getElementById('txt_cm_pre_cost').value = '".$row[csf("cm_cost")]."';\n";  
			echo "document.getElementById('txt_cm_po_price').value = '".$row[csf("cm_cost_percent")]."';\n";  
			echo "document.getElementById('txt_freight_pre_cost').value = '".$row[csf("freight")]."';\n";  
			echo "document.getElementById('txt_freight_po_price').value = '".$row[csf("freight_percent")]."';\n";  
			echo "document.getElementById('txt_currier_pre_cost').value = '".$row[csf("currier_pre_cost")]."';\n";
			echo "document.getElementById('txt_currier_po_price').value = '".$row[csf("currier_percent")]."';\n";  
			echo "document.getElementById('txt_certificate_pre_cost').value = '".$row[csf("certificate_pre_cost")]."';\n";
			echo "document.getElementById('txt_certificate_po_price').value = '".$row[csf("certificate_percent")]."';\n";  
			echo "document.getElementById('txt_common_oh_pre_cost').value = '".$row[csf("common_oh")]."';\n";  
			echo "document.getElementById('txt_common_oh_po_price').value = '".$row[csf("common_oh_percent")]."';\n";  
			
			echo "document.getElementById('txt_depr_amor_pre_cost').value = '".$row[csf("depr_amor_pre_cost")]."';\n";  
			echo "document.getElementById('txt_depr_amor_po_price').value = '".$row[csf("depr_amor_po_price")]."';\n"; 
			
			echo "document.getElementById('txt_total_pre_cost').value = '".$row[csf("total_cost")]."';\n";  
			echo "document.getElementById('txt_total_po_price').value = '".$row[csf("total_cost_percent")]."';\n"; 
			echo "document.getElementById('txt_final_price_dzn_pre_cost').value = '".$row[csf("price_dzn")]."';\n";
			echo "document.getElementById('txt_final_price_dzn_po_price').value = '".$row[csf("price_dzn_percent")]."';\n";
			echo "document.getElementById('txt_margin_dzn_pre_cost').value = '".$row[csf("margin_dzn")]."';\n";
			echo "document.getElementById('txt_margin_dzn_po_price').value = '".$row[csf("margin_dzn_percent")]."';\n";
			echo "document.getElementById('txt_total_pre_cost_psc_set').value = '".$row[csf("cost_pcs_set")]."';\n";
			echo "document.getElementById('txt_total_pre_cost_psc_set_po_price').value = '".$row[csf("cost_pcs_set_percent")]."';\n";
			echo "document.getElementById('txt_final_price_pcs_pre_cost').value = '".$row[csf("price_pcs_or_set")]."';\n";
			echo "document.getElementById('txt_final_price_pcs_po_price').value = '".$row[csf("price_pcs_or_set_percent")]."';\n";
			echo "document.getElementById('txt_margin_pcs_pre_cost').value = '".$row[csf("margin_pcs_set")]."';\n";
			echo "document.getElementById('txt_margin_pcs_po_price').value = '".$row[csf("margin_pcs_set_percent")]."';\n";
			echo "document.getElementById('update_id_dtls').value = '".$row[csf("id")]."';\n"; 
			if($row[csf("margin_dzn")]<0)
			{
				echo "document.getElementById('txt_margin_dzn_pre_cost').style.backgroundColor = '#F00';\n";	
			}
			else
			{
				echo "document.getElementById('txt_margin_dzn_pre_cost').style.backgroundColor = '';\n";	

			}
			
			if($row[csf("margin_pcs_set")]<0)
			{
				echo "document.getElementById('txt_margin_pcs_pre_cost').style.backgroundColor = '#F00';\n";	
			}
			else
			{
				echo "document.getElementById('txt_margin_pcs_pre_cost').style.backgroundColor = '';\n";	
			}
			if($cbo_approved_status==1)
			{
				echo "$('#txt_lab_test_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_inspection_pre_cost').attr('disabled','true')".";\n";
				//echo "$('#txt_cm_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_freight_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_common_oh_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_1st_quoted_price_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_first_quoted_price_date').attr('disabled','true')".";\n";
				echo "$('#txt_revised_price_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_revised_price_date').attr('disabled','true')".";\n";
				echo "$('#txt_confirm_price_pre_cost').attr('disabled','true')".";\n";
				echo "$('#txt_confirm_date_pre_cost').attr('disabled','true')".";\n";
				echo "$('#save2').attr('disabled','true')".";\n";
				echo "$('#update2').attr('disabled','true')".";\n";
				echo "$('#Delete2').attr('disabled','true')".";\n";
			}
			else
			{
				echo "$('#txt_lab_test_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_inspection_pre_cost').removeAttr('disabled')".";\n";
				//echo "$('#txt_cm_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_freight_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_common_oh_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_1st_quoted_price_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_first_quoted_price_date').removeAttr('disabled')".";\n";
				echo "$('#txt_revised_price_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_revised_price_date').removeAttr('disabled')".";\n";
				echo "$('#txt_confirm_price_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#txt_confirm_date_pre_cost').removeAttr('disabled')".";\n";
				echo "$('#save2').removeAttr('disabled')".";\n";
				echo "$('#update2').removeAttr('disabled')".";\n";
				echo "$('#Delete2').removeAttr('disabled')".";\n";
			}
			echo "set_button_status(1, permission, 'fnc_quotation_entry_dtls',2)\n";
		}
		if($copy_quotation_id==1)
		{
		$data_array=sql_select("select id, quotation_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent,wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test,lab_test_percent,inspection,inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent,currier_pre_cost, 	currier_percent,certificate_pre_cost,certificate_percent, common_oh, common_oh_percent,depr_amor_pre_cost,depr_amor_po_price, total_cost, total_cost_percent, commission,commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs,final_cost_set_pcs_rate, a1st_quoted_price,a1st_quoted_price_date, revised_price,revised_price_date, confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent,  margin_dzn, margin_dzn_percent,confirm_date, inserted_by, insert_date, updated_by,update_date,status_active,is_deleted from wo_price_quotation_costing_mst where quotation_id='$quotation_id' and status_active=1 and is_deleted=0");
		
			if (count($data_array)>0)
	    {
			foreach ($data_array as $row)
			{
				//echo "document.getElementById('txt_fabric_pre_cost').value = '".$row[csf("fabric_cost")]."';\n";  
				//echo "document.getElementById('txt_fabric_po_price').value = '".$row[csf("fabric_cost_percent")]."';\n";  
				echo "$('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost','".$row[csf("fabric_cost")]."')".";\n";

				//echo "document.getElementById('txt_trim_pre_cost').value = '".$row[csf("trims_cost")]."';\n";
				//echo "document.getElementById('txt_trim_po_price').value = '".$row[csf("trims_cost_percent")]."';\n"; 
				echo "$('#txt_trim_pre_cost').attr('pri_trim_pre_cost','".$row[csf("trims_cost")]."')".";\n";

				//echo "document.getElementById('txt_embel_pre_cost').value = '".$row[csf("embel_cost")]."';\n"; 
				//echo "document.getElementById('txt_embel_po_price').value = '".$row[csf("embel_cost_percent")]."';\n"; 
				echo "$('#txt_embel_pre_cost').attr('pri_embel_pre_cost','".$row[csf("embel_cost")]."')".";\n";

				//echo "document.getElementById('txt_wash_pre_cost').value = '".$row[csf("wash_cost")]."';\n"; 
				//echo "document.getElementById('txt_wash_po_price').value = '".$row[csf("wash_cost_percent")]."';\n"; 
				echo "$('#txt_wash_pre_cost').attr('pri_wash_pre_cost','".$row[csf("wash_cost")]."')".";\n";

				//echo "document.getElementById('txt_comml_pre_cost').value = '".$row[csf("comm_cost")]."';\n";  
				//echo "document.getElementById('txt_comml_po_price').value = '".$row[csf("comm_cost_percent")]."';\n"; 
				echo "$('#txt_comml_pre_cost').attr('pri_comml_pre_cost','".$row[csf("comm_cost")]."')".";\n";

				//echo "document.getElementById('txt_commission_pre_cost').value = '".$row[csf("commission")]."';\n"; 
				//echo "document.getElementById('txt_commission_po_price').value = '".$row[csf("commission_percent")]."';\n"; 
				echo "$('#txt_commission_pre_cost').attr('pri_commission_pre_cost','".$row[csf("commission")]."')".";\n";

				//echo "document.getElementById('txt_lab_test_pre_cost').value = '".$row[csf("lab_test")]."';\n";  
				//echo "document.getElementById('txt_lab_test_po_price').value = '".$row[csf("lab_test_percent")]."';\n"; 
				echo "$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost','".$row[csf("lab_test")]."')".";\n";

				//echo "document.getElementById('txt_inspection_pre_cost').value = '".$row[csf("inspection")]."';\n";
				//echo "document.getElementById('txt_inspection_po_price').value = '".$row[csf("inspection_percent")]."';\n";  
				echo "$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost','".$row[csf("inspection")]."')".";\n";

				//echo "document.getElementById('txt_cm_pre_cost').value = '".$row[csf("cm_cost")]."';\n";  
				//echo "document.getElementById('txt_cm_po_price').value = '".$row[csf("cm_cost_percent")]."';\n";
				echo "$('#txt_cm_pre_cost').attr('pri_cm_pre_cost','".$row[csf("cm_cost")]."')".";\n";
				//echo "document.getElementById('txt_freight_pre_cost').value = '".$row[csf("freight")]."';\n"; 
				//echo "document.getElementById('txt_freight_po_price').value = '".$row[csf("freight_percent")]."';\n";
				echo "$('#txt_freight_pre_cost').attr('pri_freight_pre_cost','".$row[csf("freight")]."')".";\n";

				//echo "document.getElementById('txt_currier_pre_cost').value = '".$row[csf("currier_pre_cost")]."';\n";  
				//echo "document.getElementById('txt_currier_po_price').value = '".$row[csf("currier_percent")]."';\n";
				echo "$('#txt_currier_pre_cost').attr('pri_currier_pre_cost','".$row[csf("currier_pre_cost")]."')".";\n";

				//echo "document.getElementById('txt_certificate_pre_cost').value = '".$row[csf("certificate_pre_cost")]."';\n"; 
				//echo "document.getElementById('txt_certificate_po_price').value = '".$row[csf("certificate_percent")]."';\n";
				echo "$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost','".$row[csf("certificate_pre_cost")]."')".";\n";

				//echo "document.getElementById('txt_common_oh_pre_cost').value = '".$row[csf("common_oh")]."';\n"; 
				//echo "document.getElementById('txt_common_oh_po_price').value = '".$row[csf("common_oh_percent")]."';\n"; 
				echo "$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost','".$row[csf("common_oh")]."')".";\n";

				//echo "document.getElementById('txt_depr_amor_pre_cost').value = '".$row[csf("depr_amor_pre_cost")]."';\n";  
				//echo "document.getElementById('txt_depr_amor_po_price').value = '".$row[csf("depr_amor_po_price")]."';\n"; 
				echo "$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost','".$row[csf("depr_amor_pre_cost")]."')".";\n";
				//echo "document.getElementById('txt_total_pre_cost').value = '".$row[csf("total_cost")]."';\n"; 
				//echo "document.getElementById('txt_total_po_price').value = '".$row[csf("total_cost_percent")]."';\n"; 
				echo "$('#txt_total_pre_cost').attr('pri_total_pre_cost','".$row[csf("total_cost")]."')".";\n";

				//echo "document.getElementById('txt_final_price_pcs_pre_cost').value = '".$avg_unit_price."';\n";
				//echo "document.getElementById('txt_final_price_dzn_pre_cost').value = '".$price_costing_per."';\n";
			}
		}
		else
		{
		        echo "$('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost','0')".";\n";
				echo "$('#txt_trim_pre_cost').attr('pri_trim_pre_cost','0')".";\n";
				echo "$('#txt_embel_pre_cost').attr('pri_embel_pre_cost','0')".";\n";
				echo "$('#txt_wash_pre_cost').attr('pri_wash_pre_cost','0')".";\n";
				echo "$('#txt_comml_pre_cost').attr('pri_comml_pre_cost','0')".";\n";
				echo "$('#txt_commission_pre_cost').attr('pri_commission_pre_cost','0')".";\n";
				echo "$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost','0')".";\n";
				echo "$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost','0')".";\n";
				echo "$('#txt_cm_pre_cost').attr('pri_cm_pre_cost','0')".";\n";
				echo "$('#txt_freight_pre_cost').attr('pri_freight_pre_cost','0')".";\n";
				echo "$('#txt_currier_pre_cost').attr('pri_currier_pre_cost','0')".";\n";
				echo "$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost','0')".";\n";
				echo "$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost','0')".";\n";
				echo "$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost','0')".";\n";
				echo "$('#txt_total_pre_cost').attr('pri_total_pre_cost','0')".";\n";	
		}
		}
	}
	else
	{
		if($copy_quotation_id==1)
		{
		$data_array=sql_select("select id, quotation_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent,wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test,lab_test_percent,inspection,inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent,currier_pre_cost, 	currier_percent,certificate_pre_cost,certificate_percent, common_oh, common_oh_percent,depr_amor_pre_cost,depr_amor_po_price, total_cost, total_cost_percent, commission,commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs,final_cost_set_pcs_rate, a1st_quoted_price,a1st_quoted_price_date, revised_price,revised_price_date, confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent,  margin_dzn, margin_dzn_percent,confirm_date, inserted_by, insert_date, updated_by,update_date,status_active,is_deleted from wo_price_quotation_costing_mst where quotation_id='$quotation_id' and status_active=1 and is_deleted=0");
		if(count($data_array)>0)
		{
			foreach ($data_array as $row)
			{
				echo "reset_form('quotationdtls_2','','');\n";  
				echo "document.getElementById('txt_fabric_pre_cost').value = '".$row[csf("fabric_cost")]."';\n";  
				echo "document.getElementById('txt_fabric_po_price').value = '".$row[csf("fabric_cost_percent")]."';\n";  
				echo "document.getElementById('txt_trim_pre_cost').value = '".$row[csf("trims_cost")]."';\n";  
				echo "document.getElementById('txt_trim_po_price').value = '".$row[csf("trims_cost_percent")]."';\n";  
				echo "document.getElementById('txt_embel_pre_cost').value = '".$row[csf("embel_cost")]."';\n";  
				echo "document.getElementById('txt_embel_po_price').value = '".$row[csf("embel_cost_percent")]."';\n"; 
				echo "document.getElementById('txt_wash_pre_cost').value = '".$row[csf("wash_cost")]."';\n";  
				echo "document.getElementById('txt_wash_po_price').value = '".$row[csf("wash_cost_percent")]."';\n"; 
				echo "document.getElementById('txt_comml_pre_cost').value = '".$row[csf("comm_cost")]."';\n";  
				echo "document.getElementById('txt_comml_po_price').value = '".$row[csf("comm_cost_percent")]."';\n"; 
				echo "document.getElementById('txt_commission_pre_cost').value = '".$row[csf("commission")]."';\n";  
				echo "document.getElementById('txt_commission_po_price').value = '".$row[csf("commission_percent")]."';\n"; 
				echo "document.getElementById('txt_lab_test_pre_cost').value = '".$row[csf("lab_test")]."';\n";  
				echo "document.getElementById('txt_lab_test_po_price').value = '".$row[csf("lab_test_percent")]."';\n";  
				echo "document.getElementById('txt_inspection_pre_cost').value = '".$row[csf("inspection")]."';\n";  
				echo "document.getElementById('txt_inspection_po_price').value = '".$row[csf("inspection_percent")]."';\n";  
				echo "document.getElementById('txt_cm_pre_cost').value = '".$row[csf("cm_cost")]."';\n";  
				echo "document.getElementById('txt_cm_po_price').value = '".$row[csf("cm_cost_percent")]."';\n";
				echo "document.getElementById('txt_freight_pre_cost').value = '".$row[csf("freight")]."';\n";  
				echo "document.getElementById('txt_freight_po_price').value = '".$row[csf("freight_percent")]."';\n";
				echo "document.getElementById('txt_currier_pre_cost').value = '".$row[csf("currier_pre_cost")]."';\n";  
				echo "document.getElementById('txt_currier_po_price').value = '".$row[csf("currier_percent")]."';\n";
				echo "document.getElementById('txt_certificate_pre_cost').value = '".$row[csf("certificate_pre_cost")]."';\n";  
				echo "document.getElementById('txt_certificate_po_price').value = '".$row[csf("certificate_percent")]."';\n";
				echo "document.getElementById('txt_common_oh_pre_cost').value = '".$row[csf("common_oh")]."';\n";  
				echo "document.getElementById('txt_common_oh_po_price').value = '".$row[csf("common_oh_percent")]."';\n"; 
				
				echo "document.getElementById('txt_depr_amor_pre_cost').value = '".$row[csf("depr_amor_pre_cost")]."';\n";  
				echo "document.getElementById('txt_depr_amor_po_price').value = '".$row[csf("depr_amor_po_price")]."';\n";
				
				echo "document.getElementById('txt_total_pre_cost').value = '".$row[csf("total_cost")]."';\n";  
				echo "document.getElementById('txt_total_po_price').value = '".$row[csf("total_cost_percent")]."';\n"; 
				echo "document.getElementById('txt_final_price_pcs_pre_cost').value = '".$avg_unit_price."';\n";
				echo "document.getElementById('txt_final_price_dzn_pre_cost').value = '".$price_costing_per."';\n";
				echo "calculate_confirm_price_dzn()\n";
				echo "set_button_status(0, permission, 'fnc_quotation_entry_dtls',2)\n";
				
				echo "$('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost','".$row[csf("fabric_cost")]."')".";\n";
				echo "$('#txt_trim_pre_cost').attr('pri_trim_pre_cost','".$row[csf("trims_cost")]."')".";\n";
				echo "$('#txt_embel_pre_cost').attr('pri_embel_pre_cost','".$row[csf("embel_cost")]."')".";\n";
				echo "$('#txt_wash_pre_cost').attr('pri_wash_pre_cost','".$row[csf("wash_cost")]."')".";\n";
				echo "$('#txt_comml_pre_cost').attr('pri_comml_pre_cost','".$row[csf("comm_cost")]."')".";\n";
				echo "$('#txt_commission_pre_cost').attr('pri_commission_pre_cost','".$row[csf("commission")]."')".";\n";
				echo "$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost','".$row[csf("lab_test")]."')".";\n";
				echo "$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost','".$row[csf("inspection")]."')".";\n";
				echo "$('#txt_cm_pre_cost').attr('pri_cm_pre_cost','".$row[csf("cm_cost")]."')".";\n";
				echo "$('#txt_freight_pre_cost').attr('pri_freight_pre_cost','".$row[csf("freight")]."')".";\n";
				echo "$('#txt_currier_pre_cost').attr('pri_currier_pre_cost','".$row[csf("currier_pre_cost")]."')".";\n";
				echo "$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost','".$row[csf("certificate_pre_cost")]."')".";\n";
				echo "$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost','".$row[csf("common_oh")]."')".";\n";
				echo "$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost','".$row[csf("depr_amor_pre_cost")]."')".";\n";
				echo "$('#txt_total_pre_cost').attr('pri_total_pre_cost','".$row[csf("total_cost")]."')".";\n";
			}
			}
		else
		{
		        echo "$('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost','0')".";\n";
				echo "$('#txt_trim_pre_cost').attr('pri_trim_pre_cost','0')".";\n";
				echo "$('#txt_embel_pre_cost').attr('pri_embel_pre_cost','0')".";\n";
				echo "$('#txt_wash_pre_cost').attr('pri_wash_pre_cost','0')".";\n";
				echo "$('#txt_comml_pre_cost').attr('pri_comml_pre_cost','0')".";\n";
				echo "$('#txt_commission_pre_cost').attr('pri_commission_pre_cost','0')".";\n";
				echo "$('#txt_lab_test_pre_cost').attr('pri_lab_test_pre_cost','0')".";\n";
				echo "$('#txt_inspection_pre_cost').attr('pri_inspection_pre_cost','0')".";\n";
				echo "$('#txt_cm_pre_cost').attr('pri_cm_pre_cost','0')".";\n";
				echo "$('#txt_freight_pre_cost').attr('pri_freight_pre_cost','0')".";\n";
				echo "$('#txt_currier_pre_cost').attr('pri_currier_pre_cost','0')".";\n";
				echo "$('#txt_certificate_pre_cost').attr('pri_certificate_pre_cost','0')".";\n";
				echo "$('#txt_common_oh_pre_cost').attr('pri_common_oh_pre_cost','0')".";\n";
				echo "$('#txt_depr_amor_pre_cost').attr('pri_depr_amor_pre_cost','0')".";\n";
				echo "$('#txt_total_pre_cost').attr('pri_total_pre_cost','0')".";\n";	
		}
		}
	}
}

if($action=="check_data_mismass")
{
	$user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

	//Mysql
	/*$sql_check=sql_select("select Max(a.update_date) as cst_update_date, a.updated_by as cst_updated_by,   Max(b.update_date) as fct_update_date, b.updated_by as fct_updated_by  from wo_po_color_size_breakdown a, wo_pre_cost_fabric_cost_dtls b where a.job_no_mst=b.job_no and a.job_no_mst='$data'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no_mst");*/
	//Oracle
	$sql_check=sql_select("select Max(a.update_date) as cst_update_date, Max(a.updated_by) as cst_updated_by,   Max(b.update_date) as fct_update_date, Max(b.updated_by) as fct_updated_by  from wo_po_color_size_breakdown a, wo_pre_cost_fabric_cost_dtls b where a.job_no_mst=b.job_no and a.job_no_mst='$data'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no_mst");
	
		foreach($sql_check as $check_row)
		{
		if($check_row[csf("cst_update_date")] > $check_row[csf("fct_update_date")])
		{
			$check_input=1;
			
			$cst_update_date=date('d-m-Y',strtotime($check_row[csf("cst_update_date")]));
			$cst_update_time = date('g:i:s A',strtotime($check_row[csf("cst_update_date")]));
			
			$fct_update_date=date('d-m-Y',strtotime($check_row[csf("fct_update_date")]));
			$fct_update_time = date('g:i:s A',strtotime($check_row[csf("fct_update_date")]));
			
			$sms="Mr.".ucfirst($user_library[$check_row[csf("cst_updated_by")]])." have changed  Color Size Break Down at ".$cst_update_time.", on ".$cst_update_date.", PreCost is updated by Mr. ".ucfirst($user_library[$check_row[csf("fct_updated_by")]])." at ".$fct_update_time.", on ".$fct_update_date.". So You have to update Pre-cost";
			
			echo "document.getElementById('check_sms').innerHTML = '".$sms."';\n";
			echo "document.getElementById('check_input').value = '".$check_input."';\n";
		}
		else
		{
			$sms="";
			$check_input=0;
			echo "document.getElementById('check_input').value = '".$check_input."';\n";
			echo "document.getElementById('check_sms').innerHTML = '".$sms."';\n";
		}
		}
	
}

?>
<?
if($action=="open_set_list_view")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
extract($_REQUEST);
?>
<script>
function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var smv_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		if(smv_breck_down=="")
		{
			smv_breck_down+=$('#cboitem_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val();
		}
		else
		{
			smv_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val();
		}
	}
	var job_no=document.getElementById('job_no').value;
	var booking=return_global_ajax_value(job_no+"**"+smv_breck_down, 'update_smv', '', 'pre_cost_entry_controller');
	document.getElementById('set_breck_down').value=smv_breck_down;
	parent.emailwindow.hide();
}

function calculate_set_smv(i)
{
	var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
	var smv=document.getElementById('smv_'+i).value;
	var set_smv=txtsetitemratio*smv;
	document.getElementById('smvset_'+i).value=set_smv;
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
}

function set_sum_value_set(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	if(des_fil_id=="tot_set_qnty")
	{
	math_operation( des_fil_id, field_id, '+', rowCount );
	}
	if(des_fil_id=="tot_smv_qnty")
	{
	var ddd={ dec_type:1, comma:0, currency:1}
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}
}
</script>
</head>
<body>
       <div id="set_details"  align="center">            
    	<fieldset>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" />
             <input type="hidden" id="job_no" value="<? echo $txt_job_no; ?>" />
            <table width="400" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="230">Item</th><th width="40">Set Ratio</th><th width="40">SMV/ Pcs</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$tot_set_qnty=0;
					$tot_smv_qnty=0;
					$data_array=explode("__",$set_breck_down);
					
					$data_array=sql_select("Select gmts_item_id,set_item_ratio,smv_pcs,smv_set,smv_pcs_precost,smv_set_precost from wo_po_details_mas_set_details where job_no='$txt_job_no'");

					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							//$data=explode('_',$row);
							
							$smv_pcs_precost=0;
							$smv_set_precost=0;
							$tot_set_qnty=$tot_set_qnty+$row[csf("set_item_ratio")];
							if($row[csf("smv_set_precost")]>0 || $row[csf("smv_pcs_precost")]>0)
							{
							$smv_pcs_precost=$row[csf("smv_pcs_precost")];
							$smv_set_precost=$row[csf("smv_set_precost")];
							$tot_smv_qnty=$tot_smv_qnty+$row[csf("smv_set_precost")];
							}
							else
							{
								$smv_pcs_precost=$row[csf("smv_pcs")];
							    $smv_set_precost=$row[csf("smv_set")];
								$tot_smv_qnty=$tot_smv_qnty+$row[csf("smv_set")];
							}
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
										echo create_drop_down( "cboitem_".$i, 230, $garments_item, "",1,"-- Select Item --", $row[csf("gmts_item_id")], "",1,'' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:80px"  class="text_boxes_numeric"   value="<? echo $row[csf("set_item_ratio")]; ?>"  readonly/> 
                                    </td>
                                    <td>
                                    <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $smv_pcs_precost; ?>" /> 
                                    <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $smv_set_precost; ?>" /> 
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
						
					?>
                    <tr id="settr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 230, $garments_item, "",1,"--Select--", 0, '',1,'' ); 
									?>
                                    </td>
                                     <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:80px" class="text_boxes_numeric"  readonly /> 
                                     </td>
                                     <td>
                                    <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"/> 
                                    <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric"   value=""  /> 
                                    </td>
                                     <td>
                                    </td> 
                                </tr>
                    <? 
					} 
					?>
                </tbody>
                </table>
                <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="230">Total</th>
                            <th width="40">
                            <input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:80px"  value="<? echo $tot_set_qnty; ?>" readonly  />
                            </th>
                             <th  width="40">
                                <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 1;} ?>" readonly />
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <table width="400" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
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
if($action=='update_smv')
{
	$data=explode("**",$data);
	$data_array=explode("__",$data[1]);
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$i++;
			$data_smv=explode('_',$row);
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$rID_de1=execute_query( "update wo_po_details_mas_set_details set smv_pcs_precost='$data_smv[1]',smv_set_precost='$data_smv[2]'  where  job_no ='".$data[0]."' and gmts_item_id=".$data_smv[0]."",0);
			if($db_type==0)
			{
				if($rID_de1==1){
					mysql_query("COMMIT");  
					//echo "0**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					mysql_query("ROLLBACK"); 
					//echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID_de1==1){
					oci_commit($con);
					//echo "0**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					oci_rollback($con);
					//echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			disconnect($con);
		}
	}
}

?>
<?
if($action=='is_used_costing_per')
{
	$costing_per=return_field_value("costing_per", "wo_pre_cost_fabric_cost_dtls","job_no='$data' and status_active =1 and is_deleted=0");
	echo $costing_per;
}
?>
<?
if($action=='check_is_master_part_saved')
{
	$job_no=return_field_value("job_no", "wo_pre_cost_mst","job_no='$data' and status_active =1 and is_deleted=0");
	echo $job_no;
}
?>
<?
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$date= date('Y-m-d');
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "job_no", "wo_pre_cost_mst", "job_no=$txt_job_no and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "wo_pre_cost_mst", 1 );
			$field_array="id,garments_nature,job_no,costing_date,incoterm,incoterm_place,machine_line,prod_line_hr,costing_per,copy_quatation,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,remarks,ready_to_approved,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$garments_nature.",".$txt_job_no.",".$txt_costing_date.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$copy_quatation_id.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$txt_remarks.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
			$rID=sql_insert("wo_pre_cost_mst",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID==1){
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 	
			$field_array="costing_date*incoterm*incoterm_place*machine_line*prod_line_hr*costing_per*copy_quatation*cm_cost_predefined_method_id*exchange_rate*sew_smv*cut_smv*sew_effi_percent*cut_effi_percent*efficiency_wastage_percent*remarks*ready_to_approved*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_costing_date."*".$cbo_inco_term."*".$txt_incoterm_place."*".$txt_machine_line."*".$txt_prod_line_hr."*".$cbo_costing_per."*".$copy_quatation_id."*".$cm_cost_predefined_method_id."*".$txt_exchange_rate."*".$txt_sew_smv."*".$txt_cut_smv."*".$txt_sew_efficiency_per."*".$txt_cut_efficiency_per."*".$txt_efficiency_wastage."*".$txt_remarks."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
			$rID=sql_update("wo_pre_cost_mst",$field_array,$data_array,"job_no","".$txt_job_no."",1);
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "1**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			    if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					oci_rollback($con); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			disconnect($con);
			die;
	}
	
	else if ($operation==2)   // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("wo_pre_cost_mst",$field_array,$data_array,"job_no","".$txt_job_no."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con);  
					echo "2**".$rID."**".str_replace("'","",$txt_job_no);
				}
				else{
					oci_rollback($con); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no);
				}
			}
			disconnect($con);
			die;
	}
	
	else if ($operation==3)   // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="approved*approved_by*approved_date";
			if(trim(str_replace("'","",$cbo_approved_status))==2) 
			{
				$data_array="'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
			else 
			{
				$data_array="'2'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				
			}						
		    $rID=sql_update("wo_pre_cost_mst",$field_array,$data_array,"job_no",$txt_job_no,1); 
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "3**".$rID."**".str_replace("'","",$txt_job_no)."**".trim(str_replace("'","",$cbo_approved_status));
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$txt_job_no)."**".trim(str_replace("'","",$cbo_approved_status));
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con);  
					echo "3**".$rID."**".str_replace("'","",$txt_job_no)."**".trim(str_replace("'","",$cbo_approved_status));
				}
				else{
					oci_rollback($con);  
					echo "10**".$rID."**".str_replace("'","",$txt_job_no)."**".trim(str_replace("'","",$cbo_approved_status));
				}
			}
			disconnect($con);
			die;
	}
}
//*************************************************Master Form End ***********************************************
//*************************************************Dtls Form Start ***********************************************
if ($action=="save_update_delete_quotation_entry_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cm_for_shipment_sche = str_replace("'","", $txt_margin_dzn_pre_cost) +str_replace("'","", $txt_cm_pre_cost);
	$date= date('Y-m-d');
	if ($operation==0)  // Insert Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "wo_pre_cost_dtls", 1 ) ;
			$field_array="id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost, 	wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$id.",".$update_id.",".$cbo_costing_per.",".$cbo_order_uom.",".$txt_fabric_pre_cost.",".$txt_fabric_po_price.",".$txt_trim_pre_cost.",".$txt_trim_po_price.",".$txt_embel_pre_cost.",".$txt_embel_po_price.",".$txt_wash_pre_cost.",".$txt_wash_po_price.",".$txt_comml_pre_cost.",".$txt_comml_po_price.",".$txt_commission_pre_cost.",".$txt_commission_po_price.",".$txt_lab_test_pre_cost.",".$txt_lab_test_po_price.",".$txt_inspection_pre_cost.",".$txt_inspection_po_price.",".$txt_cm_pre_cost.",".$txt_cm_po_price.",".$txt_freight_pre_cost.",".$txt_freight_po_price.",".$txt_currier_pre_cost.",".$txt_currier_po_price.",".$txt_certificate_pre_cost.",".$txt_certificate_po_price.",".$txt_common_oh_pre_cost.",".$txt_common_oh_po_price.",".$txt_depr_amor_pre_cost.",".$txt_depr_amor_po_price.",".$txt_total_pre_cost.",".$txt_total_po_price.",".$txt_final_price_dzn_pre_cost.",".$txt_final_price_dzn_po_price.",".$txt_margin_dzn_pre_cost.",".$txt_margin_dzn_po_price.",".$txt_total_pre_cost_psc_set.",".$txt_total_pre_cost_psc_set_po_price.",".$txt_final_price_pcs_pre_cost.",".$txt_final_price_pcs_po_price.",".$txt_margin_pcs_pre_cost.",".$txt_margin_pcs_po_price.",".$cm_for_shipment_sche.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("wo_pre_cost_dtls",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con);
					echo "0**".$rID."**".$id;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".$id;
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
				if(str_replace("'",'',$update_id_dtls)=="")
				{
					
					$id=return_next_id( "id", "wo_pre_cost_dtls", 1 ) ;
					$field_array="id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost, 	wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id.",".$update_id.",".$cbo_costing_per.",".$cbo_order_uom.",".$txt_fabric_pre_cost.",".$txt_fabric_po_price.",".$txt_trim_pre_cost.",".$txt_trim_po_price.",".$txt_embel_pre_cost.",".$txt_embel_po_price.",".$txt_wash_pre_cost.",".$txt_wash_po_price.",".$txt_comml_pre_cost.",".$txt_comml_po_price.",".$txt_commission_pre_cost.",".$txt_commission_po_price.",".$txt_lab_test_pre_cost.",".$txt_lab_test_po_price.",".$txt_inspection_pre_cost.",".$txt_inspection_po_price.",".$txt_cm_pre_cost.",".$txt_cm_po_price.",".$txt_freight_pre_cost.",".$txt_freight_po_price.",".$txt_currier_pre_cost.",".$txt_currier_po_price.",".$txt_certificate_pre_cost.",".$txt_certificate_po_price.",".$txt_common_oh_pre_cost.",".$txt_common_oh_po_price.",".$txt_depr_amor_pre_cost.",".$txt_depr_amor_po_price.",".$txt_total_pre_cost.",".$txt_total_po_price.",".$txt_final_price_dzn_pre_cost.",".$txt_final_price_dzn_po_price.",".$txt_margin_dzn_pre_cost.",".$txt_margin_dzn_po_price.",".$txt_total_pre_cost_psc_set.",".$txt_total_pre_cost_psc_set_po_price.",".$txt_final_price_pcs_pre_cost.",".$txt_final_price_pcs_po_price.",".$txt_margin_pcs_pre_cost.",".$txt_margin_pcs_po_price.",".$cm_for_shipment_sche.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("wo_pre_cost_dtls",$field_array,$data_array,1);
				}
				if(str_replace("'",'',$update_id_dtls)!="")
				{
					$field_array="costing_per_id*order_uom_id*fabric_cost*fabric_cost_percent*trims_cost*trims_cost_percent*embel_cost*embel_cost_percent*wash_cost* 	wash_cost_percent*comm_cost*comm_cost_percent*commission*commission_percent*lab_test*lab_test_percent*inspection*inspection_percent*cm_cost*cm_cost_percent*freight*freight_percent*currier_pre_cost*currier_percent*certificate_pre_cost*certificate_percent*common_oh*common_oh_percent*depr_amor_pre_cost*depr_amor_po_price*total_cost*total_cost_percent*price_dzn*price_dzn_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*price_pcs_or_set*price_pcs_or_set_percent*margin_pcs_set*margin_pcs_set_percent*cm_for_sipment_sche*updated_by*update_date*status_active*is_deleted";
					$data_array="".$cbo_costing_per."*".$cbo_order_uom."*".$txt_fabric_pre_cost."*".$txt_fabric_po_price."*".$txt_trim_pre_cost."*".$txt_trim_po_price."*".$txt_embel_pre_cost."*".$txt_embel_po_price."*".$txt_wash_pre_cost."*".$txt_wash_po_price."*".$txt_comml_pre_cost."*".$txt_comml_po_price."*".$txt_commission_pre_cost."*".$txt_commission_po_price."*".$txt_lab_test_pre_cost."*".$txt_lab_test_po_price."*".$txt_inspection_pre_cost."*".$txt_inspection_po_price."*".$txt_cm_pre_cost."*".$txt_cm_po_price."*".$txt_freight_pre_cost."*".$txt_freight_po_price."*".$txt_currier_pre_cost."*".$txt_currier_po_price."*".$txt_certificate_pre_cost."*".$txt_certificate_po_price."*".$txt_common_oh_pre_cost."*".$txt_common_oh_po_price."*".$txt_depr_amor_pre_cost."*".$txt_depr_amor_po_price."*".$txt_total_pre_cost."*".$txt_total_po_price."*".$txt_final_price_dzn_pre_cost."*".$txt_final_price_dzn_po_price."*".$txt_margin_dzn_pre_cost."*".$txt_margin_dzn_po_price."*".$txt_total_pre_cost_psc_set."*".$txt_total_pre_cost_psc_set_po_price."*".$txt_final_price_pcs_pre_cost."*".$txt_final_price_pcs_po_price."*".$txt_margin_pcs_pre_cost."*".$txt_margin_pcs_po_price."*".$cm_for_shipment_sche."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				   $rID=sql_update("wo_pre_cost_dtls",$field_array,$data_array,"id","".$update_id_dtls."",1);
				}
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'","",$update_id_dtls);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			if($rID ){
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$update_id_dtls);
				}
				else{
					oci_rpllback($con);
					echo "10**".$rID."**".str_replace("'","",$update_id_dtls);
				}
			}
			disconnect($con);
			die;
	}
	
	else if ($operation==2)//Update Here============================================================================================================================
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID=sql_delete("wo_pre_cost_dtls",$field_array,$data_array,"id","".$update_id_dtls."",1);
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "2**".$rID;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==3)//Update Here============================================================================================================================
	{
			echo "1**".$rID;
			disconnect($con);
			die;
	}
}
//*************************************************Dtls Form End ***********************************************
?>
<?
//*************************************************Fabric Cost, Yarn Cost, Coversion Cost Start ***********************************************
if($action=="check_booking")
{
	$booking_no="";
	$sql_data=sql_select("select booking_no from wo_booking_dtls where pre_cost_fabric_cost_dtls_id=$data and booking_type=1 and status_active=1 and is_deleted=0 group by  booking_no");
	foreach($sql_data as $row)
	{
		$booking_no=$row[csf("booking_no")];	
	}
	echo $booking_no;
}


if($action=="booking_no_with_approved_status")
{
	
	$data=explode("_",$data);
	//echo $data[0];
	//echo $data[1];
	if($data[1]=="")
	{
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		//Mysql
		/*$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no";*/
		//Oracle
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,a.is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row)
	{
		if($row[csf('is_approved')]==1)
		{
		  $approved_booking.=$row[csf('booking_no')].", ";	
		}
		else
		{
		  $un_approved_booking.=$row[csf('booking_no')].", ";	
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
}

if ($action=="show_fabric_cost_listview")
{
	$data=explode("_",$data);
	
	?>
       <h3 align="left" class="accordion_h" onClick="show_hide_content('fabric_cost', '')"> +Fabric Cost</h3>
       <div id="content_fabric_cost" style="display:none;">            
    	<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" /> 
             
            	<table width="1800" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Gmts Item</th><th  width="135">Body Part</th><th width="115">Fab Nature</th><th width="100">Color Type</th><th width="220">Fabric Description</th><th  width="120">Fabric Source</th><th id="" width="60">Width/Dia Type</th><th id="gsmweight_caption" width="40">GSM/ Weight</th><th id="" width="160">Color & Size Sensitive</th><th id="gsmweight_caption" width="75">Color</th><th width="100">Consumption Basis</th><th width="75">Avg. Grey Cons</th><th width="73">Rate</th><th width="90">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$arr_bo_app=array();
					$sql_bo_app=sql_select("select a.is_approved,b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and a.is_approved=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.pre_cost_fabric_cost_dtls_id,a.is_approved");
					foreach($sql_bo_app as $row_bo_app)
					{
					  $arr_bo_app[$row_bo_app[csf('pre_cost_fabric_cost_dtls_id')]]	=$row_bo_app[csf('is_approved')];
					}
					$save_update=1;
					$gmts_item_id=return_field_value("gmts_item_id", "wo_po_details_master", "job_no='$data[0]'");
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					
					if($db_type==0)
					{
					$data_array=sql_select("select id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,color,consumption_basis, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, status_active,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,process_loss_method,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty  from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]' order by id");
					}
					if($db_type==2)
					{
					$data_array=sql_select("select id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,color,consumption_basis, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, status_active,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,process_loss_method,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty  from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]' order by id");
					}
					
					if (count($data_array)<1 && $data[2]==1 )
					{
						if($db_type==0)
						{
						$data_array=sql_select("select id,quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id as lib_yarn_count_deter_id ,construction, composition,fabric_description, gsm_weight,fab_cons_in_quotat_varia, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss,yarn_breack_down,width_dia_type,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[1]' order by id");
						}
						if($db_type==2)
						{
						$data_array=sql_select("select id,quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id as lib_yarn_count_deter_id,construction, composition,fabric_description, gsm_weight,fab_cons_in_quotat_varia, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss,yarn_breack_down,width_dia_type,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='$data[1]' order by id");
						}
						$save_update=0;
					}
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							if($db_type==0)
							{
								$cons_breack_down=$row[csf('cons_breack_down')];
								$msmnt_break_down=$row[csf('msmnt_break_down')];
							}
							if($db_type==2)
							{
								if($row[csf('cons_breack_down')] !="")
								{
								$cons_breack_down=$row[csf('cons_breack_down')]->load();
							    }
								if($row[csf('msmnt_break_down')] !="")
								{
								$msmnt_break_down=$row[csf('msmnt_break_down')]->load();
								}
							}
							if($approved==1 || $arr_bo_app[$row[csf('id')]]==1)
							{
								$disabled=1;
							}
							else
							{
								$disabled=0;
							}
							if($approved==1)
							{
								$btn_disabled=1;
							}
							else
							{
								$btn_disabled=0;
							}
							?>
                            	<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
                                    <td>
									<? 
										echo create_drop_down( "cbogmtsitem_".$i, 150, $garments_item,"", 1, "-- Select Item --", $row[csf("item_number_id")], "",$disabled,$gmts_item_id ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "txtbodypart_".$i, 140, $body_part,"", 1, "-- Select --", $row[csf("body_part_id")], "",$disabled,"" ); ?></td>
                                    <td>
									<?  echo create_drop_down( "cbofabricnature_".$i, 115, $item_category,"", 0, "", $row[csf("fab_nature_id")], "change_caption( this.value, 'gsmweight_caption' );",$disabled,"2,3" ); ?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocolortype_".$i, 100, $color_type,"", 1, "-- Select --", $row[csf("color_type_id")], "",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="hidden" id="libyarncountdeterminationid_<? echo $i; ?>"  name="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px"  value="<? echo $row[csf("lib_yarn_count_deter_id")];  ?>" readonly />
                                    <input type="hidden" id="construction_<? echo $i; ?>"  name="construction_<? echo $i; ?>" class="text_boxes" style="width:10px"  value="<? echo $row[csf("construction")];  ?>"   readonly />
                                    <input type="hidden" id="composition_<? echo $i; ?>"  name="composition_<? echo $i; ?>" class="text_boxes" style="width:10px"  value="<? echo $row[csf("composition")];  ?>"  readonly />
                                   
                                    <input type="text" id="fabricdescription_<? echo $i; ?>"    name="fabricdescription_<? echo $i; ?>"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>   title="<? echo $row[csf("fabric_description")];  ?>" readonly/> 
                                    </td>
                                    <td>
									 <?
									 echo create_drop_down( "cbofabricsource_".$i, 120, $fabric_source, "", 0, "", $row[csf("fabric_source")], "enable_disable( this.value,'txtrate_*txtamount_', $i );",$disabled,"1,2,3" ); 
									 ?>
                                     </td>
                                     <td>
                                     <?  echo create_drop_down( "cbowidthdiatype_".$i, 100, $fabric_typee,"", 1, "-- Select --", $row[csf("width_dia_type")], "",$disabled,"" ); ?>
                                   
                                    </td> 
                                    <td>
                                    <input type="text" id="txtgsmweight_<? echo $i; ?>" name="txtgsmweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onBlur="sum_yarn_required()" value="<? echo $row[csf("gsm_weight")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?> avglvalue="<?  echo $row[csf("gsm_weight_yarn")]; ?>" title="<?  echo $row[csf("gsm_weight_yarn")]; ?>"/>
                                    <input type="hidden" id="avgtxtgsmweight_<? echo $i; ?>" name="avgtxtgsmweight_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $row[csf("gsm_weight_yarn")]; ?>"  readonly/> 
                                    </td>
                                    
                                    <td>
                                   <?  echo create_drop_down( "cbocolorsizesensitive_".$i, 160, $size_color_sensitive,"", 1, "--Select--", $row[csf("color_size_sensitive")], "control_color_field($i)",$disabled,"" ); ?>
                                    </td>
                                    <td> 
                                    <input type="text" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" style="width:75px" onClick="open_color_popup(<? echo $i; ?>)" value="<? echo $color_library[$row[csf("color")]];  ?>"  <? if($row[csf("color_size_sensitive")] ==3){echo "";}else{echo "disabled";}?>/> <? //if($disabled==0 && $row[csf("color_size_sensitive")] ==3){echo "";}else{echo "disabled";}?>
                                   </td>
                                   <td>
                                   <? 
									echo create_drop_down( "consumptionbasis_".$i, 100, $consumtion_basis,'', 0, '', $row[csf('consumption_basis')], "",$disabled,"" );
								   ?>
                                   </td>
                                    <td>
                                    <input type="text" id="txtconsumption_<? echo $i; ?>" name="txtconsumption_<? echo $i; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "  onDblClick="set_session_large_post_data('requires/pre_cost_entry_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtbodypart_<? echo $i; ?>','cbofabricnature_<? echo $i; ?>','txtgsmweight_<? echo $i; ?>','<? echo $i; ?>','updateid_<? echo $i; ?>')"   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_cons")]; ?>" avglvalue="<?  echo $row[csf("avg_cons_yarn")]; ?>" title="<?  echo $row[csf("avg_cons_yarn")]; ?>"  readonly/>
                                    <input type="hidden" id="avgtxtconsumption_<? echo $i; ?>" name="avgtxtconsumption_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_cons_yarn")]; ?>" readonly/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtrate_<? echo $i; ?>" name="txtrate_<? echo $i; ?>" onBlur="math_operation( 'txtamount_<? echo $i; ?>', 'txtconsumption_<? echo $i; ?>*txtrate_<? echo $i; ?>', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "   class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("rate")]; ?>" <? if($row[csf("fabric_source")]==2){echo "";}else{echo "disabled";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="txtamount_<? echo $i; ?>"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' ) "    name="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?> " <? if($row[csf("fabric_source")]==2 && $disabled==0){echo "";}else{echo "disabled";}?>  readonly/>
                                    </td>
                                    <td width="95"><? echo create_drop_down( "cbostatus_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?></td>  
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )"   <? if($btn_disabled==0){echo "";}else{echo "disabled";}?>/> 
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_fabric_cost' );"  <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="txtfinishconsumption_<? echo $i; ?>"  name="txtfinishconsumption_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_finish_cons")]; ?>" readonly/>
                                    <input type="hidden" id="txtavgprocessloss_<? echo $i; ?>"  name="txtavgprocessloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("avg_process_loss")]; ?>" readonly/>
                                     <input type="hidden" id="consbreckdown_<? echo $i; ?>" name="consbreckdown_<? echo $i; ?>"   class="text_boxes" style="width:90px" value="<? echo   $cons_breack_down; ?>" readonly/>                                     
                                    <input type="hidden" id="msmntbreackdown_<? echo $i; ?>" name="msmntbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $msmnt_break_down; ?>" readonly/> 
                                    <input type="hidden" id="markerbreackdown_<? echo $i; ?>" name="markerbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("marker_break_down")]; ?>" readonly/>
                                    <input type="hidden" id="colorbreackdown_<? echo $i; ?>" name="colorbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("color_break_down")]; ?>" readonly/> 
                                    <input type="hidden" id="yarnbreackdown_<? echo $i; ?>" name="yarnbreackdown_<? echo $i; ?>"  class="text_boxes" style="width:90px" value="<? echo  $row[csf("yarn_breack_down")]; ?>" readonly/> 
                                    <input type="hidden" id="processlossmethod_<? echo $i; ?>" name="processlossmethod_<? echo $i; ?>" value="<? echo  $row[csf("process_loss_method")]; ?>" readonly/>
                                    <input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  readonly/>   
                                   <!-- Price quatation fabric cost Id-->
                                    <input type="hidden" id="prifabcostdtlsid_<? echo $i; ?>" name="prifabcostdtlsid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  readonly/> 
                                     <input type="hidden" id="isclickedconsinput_<? echo $i; ?>" name="isclickedconsinput_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="1"  /> 
                                     <input type="hidden" id="plancutqty_<? echo $i; ?>" name="plancutqty_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("plan_cut_qty")];  ?> " readonly/>
                                       <input type="hidden" id="jobplancutqty_<? echo $i; ?>" name="jobplancutqty_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("job_plan_cut_qty")];  ?> " readonly/>
                                       <input type="hidden" id="precostapproved_<? echo $i; ?>" name="precostapproved_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $approved   ?> " readonly/>

                                    <!-- Price quatation fabric cost Id end-->                                                   
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					$selected_item=0;
					$gmts_item_id_arr=explode(",",$gmts_item_id);
					if(count($gmts_item_id_arr)==1)
					{
					$selected_item=	$gmts_item_id;
					}	
					?>
                    <tr id="fabriccosttbltr_1" align="center">
                                    <td><?  echo create_drop_down( "cbogmtsitem_1", 150, $garments_item,"", 1, "-- Select Item --", $selected_item, "" ,"",$gmts_item_id); ?></td>
                                    <td><?  echo create_drop_down( "txtbodypart_1", 140, $body_part,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    <td><?  echo create_drop_down( "cbofabricnature_1", 115, $item_category,"", 0, "", 2, "change_caption( this.value, 'gsmweight_caption')","","2,3" ); ?></td>
                                    <td><?  echo create_drop_down( "cbocolortype_1", 100, $color_type,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    <td>
                                    <input type="hidden" id="libyarncountdeterminationid_1" name="libyarncountdeterminationid_1" class="text_boxes" style="width:10px"  /> 
                                    <input type="hidden" id="construction_1" name="construction_1" class="text_boxes" style="width:10px"  /> 
                                    <input type="hidden" id="composition_1" name="composition_1" class="text_boxes" style="width:10px"  /> 
                                    <input type="text" id="fabricdescription_1" placeholder="Dobule Click To Search"  name="fabricdescription_1"  class="text_boxes" style="width:220px" onDblClick="open_fabric_decription_popup(1)" readonly /> 
                                    </td>
                                     <td><? echo create_drop_down( "cbofabricsource_1", 120, $fabric_source, "", 0, "", "", "enable_disable( this.value,'txtrate_*txtamount_', 1 );","","1,2,3" );  ?></td> 
                                     <td><?  echo create_drop_down( "cbowidthdiatype_1", 100, $fabric_typee,"", 1, "-- Select --", $selected, "","","" ); ?></td>
                                    <td>
                                    <input type="text" id="txtgsmweight_1" name="txtgsmweight_1" class="text_boxes_numeric" style="width:40px" onBlur="sum_yarn_required()"> 
                                    <input type="hidden" id="avgtxtgsmweight_1" name="avgtxtgsmweight_1" class="text_boxes_numeric" style="width:40px" onBlur="sum_yarn_required()">
                                    </td>
                                     
                                    <td><?  echo create_drop_down( "cbocolorsizesensitive_1", 160, $size_color_sensitive,"", 1, "-- Select --", $selected, "control_color_field(1)","","" ); ?> </td>
                                    <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes" style="width:75px"  onClick="open_color_popup(1)" value="<? //echo $row[csf("gsm_weight")];  ?>" /></td>
                                    <td>
                                   <? 
									echo create_drop_down( "consumptionbasis_1", 100, $consumtion_basis,'', 0, '', '', "","","" );
								   ?>
                                   </td>
                                    <td> 
                                    <input type="text" id="txtconsumption_1" name="txtconsumption_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required();set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost' )"  onDblClick="set_session_large_post_data('requires/pre_cost_entry_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtbodypart_1','cbofabricnature_1','txtgsmweight_1','1','updateid_1')"    class="text_boxes_numeric" style="width:60px" readonly  /> 
                                    
                                    <input type="hidden" id="avgtxtconsumption_1" name="avgtxtconsumption_1" class="text_boxes_numeric" style="width:60px"  readonly/>

                                    </td>
                                    <td><input type="text" id="txtrate_1" onBlur="math_operation( 'txtamount_1', 'txtconsumption_1*txtrate_1', '*','',{dec_type:1,comma:0, currency:document.getElementById('cbo_currercy').value});set_sum_value( 'txtamount_sum', 'txtamount_' ,'tbl_fabric_cost') "  name="txtrate_1" class="text_boxes_numeric" style="width:60px"  > </td>
                                    <td><input type="text" id="txtamount_1"  onBlur="set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost') " readonly  name="txtamount_1" class="text_boxes_numeric" style="width:80px"></td>
                                    <td width="95"><? echo create_drop_down( "cbostatus_1", 80, $row_status, "", 0, "", "", "","","" );  ?></td>  
                                    <td>
                                    <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                    <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_fabric_cost');" />
                                    <input type="hidden" id="txtfinishconsumption_1"  name="txtfinishconsumption_1" class="text_boxes_numeric" style="width:60px" readonly/>
                                    <input type="hidden" id="txtavgprocessloss_1"  name="txtavgprocessloss_1" class="text_boxes_numeric" style="width:60px"  readonly/>
                                    <input type="hidden" id="consbreckdown_1" name="consbreckdown_1" value=""  class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="msmntbreackdown_1" name="msmntbreackdown_1" value="" class="text_boxes" style="width:90px" />
                                    <input type="hidden" id="markerbreackdown_1" name="markerbreackdown_1" value="" class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="colorbreackdown_1" name="colorbreackdown_1" value="" class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="yarnbreackdown_1" name="yarnbreackdown_1" value="" class="text_boxes" style="width:90px" /> 
                                    <input type="hidden" id="processlossmethod_1" name="processlossmethod_1"/>
                                    <input type="hidden" id="updateid_1" name="updateid_1" value="" class="text_boxes" style="width:20px" />
                                    <input type="hidden" id="prifabcostdtlsid_1" name="prifabcostdtlsid_1"  class="text_boxes" style="width:20px"  /> 
                                    <input type="hidden" id="isclickedconsinput_1" name="isclickedconsinput_1"  class="text_boxes" style="width:20px" value="1"  /> 
                                    <input type="hidden" id="plancutqty_1" name="plancutqty_1"  class="text_boxes" style="width:20px"/>
                                    <input type="hidden" id="jobplancutqty_1" name="jobplancutqty_1"  class="text_boxes" style="width:20px"/>
                                    <input type="hidden" id="precostapproved_1" name="precostapproved_1"  class="text_boxes" style="width:20px" value="0" readonly/>

                                    </td> 
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                <br/>
                <table width="1800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                <?
				$yarn_needed=0;
				$data_array1=sql_select("select fab_yarn_req_kg , fab_woven_req_yds , fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg, fab_amount,avg,pro_woven_grey_fab_req_yds,pro_knit_grey_fab_req_kg,pro_woven_fin_fab_req_yds,pro_knit_fin_fab_req_kg,pur_woven_grey_fab_req_yds,pur_knit_grey_fab_req_kg,pur_woven_fin_fab_req_yds,pur_knit_fin_fab_req_kg,woven_amount,knit_amount   from wo_pre_cost_sum_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0 ");
				list($sum_data_array )=$data_array1;
				/*foreach ($data_array as $row)
				{*/
					$avg_value="";
					if($sum_data_array[csf("avg")]=="")
					{
						$avg_value="Make AVG";
					}
					else
					{
					 $avg_value=$sum_data_array[csf("avg")];	
					}
				?>
                    	<tr>
                        	<th width="291"> 
                            <input type="button" id="avg" style="width:80px" class="formbutton" value="<? echo $avg_value; ?>" onClick="sum_yarn_required_avg(this.value)" /> &nbsp; Yarn Required(Kg)  
                            <input type="text" id="tot_yarn_needed" name="tot_yarn_needed" class="text_boxes_numeric" style="width:60px;" value="<? echo $sum_data_array[csf("fab_yarn_req_kg")];$yarn_needed= $sum_data_array[csf("fab_yarn_req_kg")];?>" readonly/>
                            </th>
                           
                            <th width="973"> 
                            Woven Grey Fab Req.(Yds) 
                            <input type="text" id="txtwoven_sum" name="txtwoven_sum" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("fab_woven_req_yds")] ?>" readonly />
                            Knit Grey Fab Req.(Kg) <input type="text" id="txtknit_sum"  name="txtknit_sum" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("fab_knit_req_kg")] ?>"  readonly>
                            Woven Fin. Fab Req.(Yds) 
                            <input type="text" id="txtwoven_fin_sum" name="txtwoven_fin_sum" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("fab_woven_fin_req_yds")] ?>" readonly />
                            Knit Fin. Fab Req.(Kg) <input type="text" id="txtknit_fin_sum"  name="txtknit_fin_sum" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("fab_knit_fin_req_kg")] ?>"  readonly>
                            </th>
                             <th width="100"></th>
                            <th width="75"></th>
                            <th width="73"></th>
                            <th width="90"><input type="text" id="txtamount_sum" name="txtamount_sum" class="text_boxes_numeric" style="width:80px" value="<? echo $sum_data_array[csf("fab_amount")] ?>"  readonly></th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                        
                         <tr>
                      
                        	<th width="291"> 
                            </th>
                           
                            <th width="973"> 
                           Pro.  Woven Grey Fab Req.(Yds) 
                            <input type="text" id="txtwoven_sum_production" name="txtwoven_sum_production" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pro_woven_grey_fab_req_yds")] ?>" readonly />
                            Pro. Knit Grey Fab Req.(Kg) <input type="text" id="txtknit_sum_production"  name="txtknit_sum_production" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pro_knit_grey_fab_req_kg")] ?>"  readonly>
                            Pro. Woven Fin. Fab Req.(Yds) 
                            <input type="text" id="txtwoven_fin_sum_production" name="txtwoven_fin_sum_production" class="text_boxes_numeric" style="width:60px" value="<?  echo $sum_data_array[csf("pro_woven_fin_fab_req_yds")] ?>" readonly />
                            Pro. Knit Fin. Fab Req.(Kg) <input type="text" id="txtknit_fin_sum_production"  name="txtknit_fin_sum_production" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pro_knit_fin_fab_req_kg")] ?>"  readonly>
                            </th>
                             <th width="100"></th>
                            <th width="75"></th>
                            <th width="73"></th>
                            <th width="90"></th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                          <tr>
                        	<th width="291"> 
                            </th>
                           
                            <th width="973"> 
                            Pur. Woven Grey Fab Req.(Yds) 
                            <input type="text" id="txtwoven_sum_purchase" name="txtwoven_sum_purchase" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pur_woven_grey_fab_req_yds")] ?>" readonly />
                            Pur. Knit Grey Fab Req.(Kg) <input type="text" id="txtknit_sum_purchase"  name="txtknit_sum_purchase" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pur_knit_grey_fab_req_kg")] ?>"  readonly>
                            Pur. Woven Fin. Fab Req.(Yds) 
                            <input type="text" id="txtwoven_fin_sum_purchase" name="txtwoven_fin_sum_purchase" class="text_boxes_numeric" style="width:60px" value="<?  echo $sum_data_array[csf("pur_woven_fin_fab_req_yds")] ?>" readonly />
                            Pur. Knit Fin. Fab Req.(Kg) <input type="text" id="txtknit_fin_sum_purchase"  name="txtknit_fin_sum_purchase" class="text_boxes_numeric" style="width:60px" value="<? echo $sum_data_array[csf("pur_knit_fin_fab_req_kg")] ?>"  readonly>
                            </th>
                             <th width="100">Woven  Amt</th>
                            <th width="75"><input type="text" id="txtwoven_amount_sum_purchase" name="txtwoven_amount_sum_purchase" class="text_boxes_numeric" style="width:65px" value="<? echo $sum_data_array[csf("woven_amount")] ?>"  readonly></th>
                            <th width="73">Kint  Amt</th>
                            <th width="90"><input type="text" id="txtkint_amount_sum_purchase" name="txtkint_amount_sum_purchase" class="text_boxes_numeric" style="width:80px" value="<? echo $sum_data_array[csf("knit_amount")] ?>"  readonly></th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                        <? //}?>
                    </tfoot>
                </table>
                <table width="1800" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="50%"> </td>
                        <td align="center" height="15" width="50%"> </td>
                    </tr>
                	<tr>
                        <td align="right" width="50%" class="button_container">
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", $save_update,0,"reset_form('fabriccost_3','','','cbofabricnature_,3,$i')",3) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_cost_dtls", 0,0,"reset_form('fabriccost_3','','',0)",3) ;
						}
						?>  
                        </td> 
                        <td align="left" width="50%" class="button_container" valign="top">
						<?
						if ( count($data_array)>0)
					    {
							?>
                            <input type="button" class="formbutton" style="width:100px" name="stripe_color" id="stripe_color" value="Stripe Color"  onClick="open_stripe_color_popup()"/>
                            <?
					    }
						
						?>  
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
        </div>
        
        
        
        
       <h3 align="left" id="accordion_h_yarn" class="accordion_h" onClick="show_hide_content('yarn_cost', '');">+Yarn Cost &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Yarn Nedded:&nbsp;<span id="tot_yarn_needed_span"><? echo $yarn_needed; ?></span></h3>
       <div id="content_yarn_cost" style="display:none;">            
    	<fieldset>
        	<form id="yarnccost_4" autocomplete="off">
            	<table width="1150" cellspacing="0" class="rpt_table" border="0" id="tbl_yarn_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="60">Count</th><th  width="100">Comp 1</th><th  width="50">%</th><th width="100">Comp 2</th><th width="50">%</th><th width="110">Type</th><th width="75">Cons Qnty</th><th width="75">Avg. Cons Qnty</th><th width="75">Supplier</th><th width="50">Rate</th><th width="80">Amount</th><th width="80">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$save_update=1;
                    $data_array=sql_select("select id,fabric_cost_dtls_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, avg_cons_qnty,supplier_id, rate, amount,status_active from wo_pre_cost_fab_yarn_cost_dtls where job_no='$data[0]' order by id");
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty,supplier_id, rate, amount,status_active from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$data[1]' order by id");
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
						$tot_cons=0;
						foreach( $data_array as $row )
						{
							$i++;
							$tot_cons=$tot_cons+$row[csf("cons_qnty")];
							if($approved==1)//|| $arr_bo_app[$row[csf('fabric_cost_dtls_id')]]==1
							{
								$disabled=1;
							}
							else
							{
								$disabled=0;
							}
							?>
                            	<tr id="yarncost_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cbocount_".$i, 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select Item --", $row[csf("count_id")], "",$disabled,"" ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "control_composition($i,this.id,'percent_one')",$disabled,"" ); ?></td>
                                   <td>
                                    <input type="text" id="percentone_<? echo $i; ?>"  name="percentone_<? echo $i; ?>" class="text_boxes" style="width:50px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo $row[csf("percent_one")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_two_id")], "control_composition($i,this.id,'percent_two')",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes" style="width:50px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="<? echo $row[csf("percent_two")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td><?  echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')"  value="<? echo $row[csf("cons_qnty")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                     </td>
                                     <td>
                                    <input type="text" id="avgconsqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')"  value="<? echo $row[csf("avg_cons_qnty")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                     </td>
                                    <td>
										<? 
										echo create_drop_down( "supplier_".$i, 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data[3]  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name",1," -- Select --", $row[csf("supplier_id")], "set_yarn_rate($i)",'','' ); 
										?>
								</td>
                                    <td>
                                    <input type="text" id="txtrateyarn_<? echo $i; ?>" name="txtrateyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')" value="<? echo $row[csf("rate")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountyarn_<? echo $i; ?>"  name="txtamountyarn_1<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")]; ?>"  readonly/>
                                    </td>
                                   
                                    <td><? echo create_drop_down( "cbostatusyarn_".$i, 80, $row_status, "", 0, "", $row[csf("status_active")], "",$disabled,"" );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_yarn_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="updateidyarncost_<? echo $i; ?>" name="updateidyarncost_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                     
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$save_update=0;
						$data_array_p=sql_select("select id, count_id, copm_one_id, percent_one, type_id, cons_ratio, cons_qnty,avg_cons_qnty,supplier_id from wo_pre_cost_fab_yarnbreakdown where job_no='$data[0]' order by id");
						if ( count($data_array)>0)
					    {
						$i=0;
						$tot_cons=0;
							foreach( $data_array_p as $row_p )
							{
								$i++;
								$tot_cons=$tot_cons+$row_p[csf("cons_qnty")];
								
	
						?>
						<tr id="yarncost_1" align="center">
									   <td>
										<? 
										echo create_drop_down( "cbocount_".$i, 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Item --", $row_p[csf("count_id")], '','','' ); 
										?>
										</td>
										<td><?  echo create_drop_down( "cbocompone_".$i,100, $composition,"", 1, "-- Select --", $row_p[csf("copm_one_id")], "control_composition($i,this.id,'comp_one')",'','' ); ?></td>
									   <td>
										<input type="text" id="percentone_<? echo $i;?>"  name="percentone_<? echo $i;?>" class="text_boxes" style="width:50px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_one')" value="<? echo  $row_p[csf("percent_one")]; ?>" />
										</td>
										<td><?  echo create_drop_down( "cbocomptwo_".$i, 100, $composition,"", 1, "-- Select --", '', "control_composition($i,this.id,'comp_two')",'','' ); ?></td>
										<td>
										<input type="text" id="percenttwo_<? echo $i; ?>"  name="percenttwo_<? echo $i; ?>" class="text_boxes" style="width:50px" onChange="control_composition(<? echo $i; ?>,this.id,'percent_two')" value="" />
										</td>
										<td><?  echo create_drop_down( "cbotype_".$i, 110, $yarn_type,"", 1, "-- Select --", $row_p[csf("type_id")], '','','' ); ?></td>
										
										<td>
										<input type="text" id="consqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')"  value="<? echo $row_p[csf("cons_qnty")]; ?>"/>
										 </td>
                                         <td>
                                    <input type="text" id="avgconsqnty_<? echo $i; ?>" name="consqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')"  value="<? echo $row[csf("avg_cons_qnty")]; ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                     </td>
										<td>
										<? 
										echo create_drop_down( "supplier_".$i, 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data[3]  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name",1," -- Select --", $row_p[csf("supplier_id")], "set_yarn_rate($i)",'','' ); 
										?>
										</td>
										<td>
										<input type="text" id="txtrateyarn_<? echo $i; ?>"  name="txtrateyarn_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_yarn_consumption_ratio(<? echo $i; ?>,'calculate_amount')" value="" /> 
										</td>
										<td>
										<input type="text" id="txtamountyarn_<? echo $i; ?>" name="txtamountyarn_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value=""  readonly/>
										</td>
									   
										<td><? echo create_drop_down( "cbostatusyarn_".$i, 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
										<td>
										<input type="button" id="increaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(<? echo $i; ?>)" />
										<input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>,'tbl_yarn_cost' );" />
										<input type="hidden" id="updateidyarncost_<? echo $i; ?>" name="updateidyarncost_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
										
									</tr>
						<?
							}
						} 
						else
						{
						?>
                        <tr id="yarncost_1" align="center">
									   <td>
										<? 
										echo create_drop_down( "cbocount_1", 60, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Item --", $row_p[csf("count_id")], '','','' ); 
										?>
										</td>
										<td><?  echo create_drop_down( "cbocompone_1",100, $composition,"", 1, "-- Select --", $row_p[csf("copm_one_id")], "control_composition(1,this.id,'comp_one')",'','' ); ?></td>
									   <td>
										<input type="text" id="percentone_1"  name="percentone_1" class="text_boxes" style="width:50px" onChange="control_composition(1,this.id,'percent_one')" value="<? echo  $row_p[csf("percent_one")]; ?>" />
										</td>
										<td><?  echo create_drop_down( "cbocomptwo_1", 100, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_two')",'','' ); ?></td>
										<td>
										<input type="text" id="percenttwo_1"  name="percenttwo_1" class="text_boxes" style="width:50px" onChange="control_composition(1,this.id,'percent_two')" value="" />
										</td>
										<td><?  echo create_drop_down( "cbotype_1", 110, $yarn_type,"", 1, "-- Select --", $row_p[csf("type_id")], '','','' ); ?></td>
										
										<td>
										<input type="text" id="consqnty_1" name="consqnty_1" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(1,'calculate_amount')"  value="<? echo $row_p[csf("cons_qnty")]; ?>"/>
										 </td>
                                         <td>
										<input type="text" id="avgconsqnty_1" name="avgconsqnty_1" class="text_boxes_numeric" style="width:75px" onChange=" calculate_yarn_consumption_ratio(1,'calculate_amount')"  value="<? ///echo $row_p[csf("cons_qnty")]; ?>"/>
										 </td>
										<td>
										<? 
										echo create_drop_down( "supplier_1", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data[3]  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name",1," -- Select --", $row_p[csf("supplier_id")], "set_yarn_rate(1)",'','' ); 
										?>
										</td>
										<td>
										<input type="text" id="txtrateyarn_1"  name="txtrateyarn_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_yarn_consumption_ratio(1,'calculate_amount')" value="" /> 
										</td>
										<td>
										<input type="text" id="txtamountyarn_1" name="txtamountyarn_1" class="text_boxes_numeric" style="width:80px" value=""  readonly/>
										</td>
									   
										<td><? echo create_drop_down( "cbostatusyarn_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
										<td>
										<input type="button" id="increaseyarn_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_yarn_cost(1)" />
										<input type="button" id="decreaseyarn_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_yarn_cost' );" />
										<input type="hidden" id="updateidyarncost_1" name="updateidyarncost_1"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
										
									</tr>
                        <?
						}
					}
					?>
                </tbody>
                </table>
                <table width="1150" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th  style="width:499px;">SUM</th>
                            <th width="75"><input type="text" id="txtconsumptionyarn_sum" name="txtconsumptionyarn_sum" class="text_boxes_numeric" style="width:75px" value="<? echo $tot_cons; ?>" readonly></th>
                             <th width="75"><input type="text" id="txtavgconsumptionyarn_sum" name="txtavgconsumptionyarn_sum" class="text_boxes_numeric" style="width:75px" value="<? echo $tot_cons; ?>" readonly></th>
                            <th width="62"></th>
                            <th width="80"><input type="text" id="txtamountyarn_sum" name="txtamountyarn_sum" class="text_boxes_numeric" style="width:80px" readonly></th>
                            <th width="95" style=" border:none"></th>
                            <th width="" style=" border:none"></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="900" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if (count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", $save_update,0,"reset_form('yarnccost_4','','',0)",4) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_yarn_cost_dtls", $save_update,0,"reset_form('yarnccost_4','','',0)",4) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
               
            </form>
        </fieldset>
       
        </div>
        
        
        
       <h3 align="left" class="accordion_h" onClick="show_hide_content('conversion_cost', '')">+Conversion Cost</h3> 
       <div id="content_conversion_cost" style="display:none;" align="left">            
    	<fieldset>
        	<form id="conversionccost_5" autocomplete="off">
            	<table width="1035" cellspacing="0" class="rpt_table" border="0" id="tbl_conversion_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="380">Fabric Description</th> <th width="155">Process</th> <th width="50">Process Loss</th> <th width="50">Req. Qnty</th>  <th width="50"> Avg.Req. Qnty (Less Process Loss)</th> <th width="50">Charge/ Unit</th><th width="80">Amount</th> <th width="80">Status</th> <th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[3]'  and variable_list=21 and status_active=1 and is_deleted=0");
					if($conversion_from_chart=="")
					{
						$conversion_from_chart=2;
					}
					
					$fab_description=array();
					$fab_description_array=sql_select("select id, body_part_id, color_type_id, fabric_description from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]' and  	fabric_source=1 order by id");
					foreach( $fab_description_array as $row_fab_description_array )
					{
					  $fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("fabric_description")];
					}
					
					$save_update=1;
					$data_array=sql_select("select id, job_no, fabric_description, cons_process,process_loss, req_qnty,avg_req_qnty, charge_unit, amount,color_break_down,charge_lib_id, status_active from  wo_pre_cost_fab_conv_cost_dtls where job_no='$data[0]' order by id");
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, quotation_id, cost_head, cons_type, process_loss,req_qnty, charge_unit, amount, status_active from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$data[1]' order by id");
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$selected_fab_description="";
							if($row[csf("fabric_description")]!="")
							{
								$selected_fab_description=$row[csf("fabric_description")];
							}
							else
							{
								$selected_fab_description=$row[csf("cost_head")];
							}
							$selected_process="";
							if($row[csf("cons_process")]!="")
							{
								$selected_process=$row[csf("cons_process")];
							}
							else
							{
								$selected_process=$row[csf("cons_type")];
							}
							$i++;
							if($approved==1)//|| $arr_bo_app[$row[csf('fabric_description')]]==1
							{
								$disabled=1;
							}
							else
							{
								$disabled=0;
							}
							?>
                            	<tr id="conversion_1" align="center">
                                    <td>
                                   
									<? 
									
									echo create_drop_down( "cbocosthead_".$i, 380, $fab_description, "",1," -- Select--", $selected_fab_description, "set_conversion_qnty(".$i.")",$disabled,"" ); 
									?>
                                    
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypeconversion_".$i, 155, $conversion_cost_head_array,"", 1, "-- Select --", $selected_process, "set_conversion_charge_unit(".$i.",".$conversion_from_chart.")",$disabled,"" ); ?></td>
                                    <td>
                                    <input type="text" id="txtprocessloss_<? echo $i; ?>"  name="txtprocessloss_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px"   value="<? echo $row[csf("process_loss")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly/>
                                    </td>
                                   <td>
                                    <input type="text" id="txtreqqnty_<? echo $i; ?>"  name="txtreqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("req_qnty")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtavgreqqnty_<? echo $i; ?>"  name="txtavgreqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )"  value="<? echo $row[csf("avg_req_qnty")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly/>
                                    </td>
                                   <td>
                                    <input type="text" id="txtchargeunit_<? echo $i; ?>"  name="txtchargeunit_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( <? echo $i;?> )" onClick="set_conversion_charge_unit(<? echo $i;?>,<? echo $conversion_from_chart;?>)"  value="<? echo $row[csf("charge_unit")];?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountconversion_<? echo $i; ?>"  name="txtamountconversion_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("amount")];  ?>"  readonly/>
                                    </td>
                                    
                                    <td><? echo create_drop_down( "cbostatusconversion_".$i, 80, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(<? echo $i; ?>,<? echo $conversion_from_chart;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreaseconversion_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_conversion_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="hidden" id="updateidcoversion_<? echo $i; ?>" name="updateidcoversion_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />  
                                    <input type="hidden" id="colorbreakdown_<? echo $i; ?>" name="colorbreakdown_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("color_break_down")]; ?>"  /> 
                                    <input type="hidden" id="coversionchargelibraryid_<? echo $i; ?>" name="coversionchargelibraryid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"   readonly />                                       
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					$save_update=0;
					?>
                    <tr id="conversion_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cbocosthead_1", 380, $fab_description, "",1," -- All Fabrics--", "", "set_conversion_qnty(1)","","" ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypeconversion_1", 155, $conversion_cost_head_array,"", 1, "-- Select --", "", "set_conversion_charge_unit( 1,".$conversion_from_chart." )","","" ); ?></td>
                                     <td>
                                    <input type="text" id="txtprocessloss_1"  name="txtprocessloss_1" class="text_boxes_numeric" style="width:50px"  value="" />
                                    </td>
                                   <td>
                                    <input type="text" id="txtreqqnty_1"  name="txtreqqnty_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( 1 )" value="" />
                                    </td>
                                    <td>
                                    <input type="text" id="txtavgreqqnty_1"  name="txtavgreqqnty_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( 1 )" value="" />
                                    </td>
                                   <td>
                                    <input type="text" id="txtchargeunit_1"  name="txtchargeunit_1" class="text_boxes_numeric" style="width:50px" onChange="calculate_conversion_cost( 1 )" onClick="set_conversion_charge_unit(1,<? echo $conversion_from_chart;?>)"  value="" />
                                    </td>
                                    <td>
                                    <input type="text" id="txtamountconversion_1"  name="txtamountconversion_1" class="text_boxes_numeric" style="width:80px" value="" readonly />
                                    </td>
                                    
                                    <td><? echo create_drop_down( "cbostatusconversion_1", 80, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_conversion_cost(1,<? echo $conversion_from_chart;?>)" />
                                    <input type="button" id="decreaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_conversion_cost' );" />
                                    <input type="hidden" id="updateidcoversion_1" name="updateidcoversion_1"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="colorbreakdown_1" name="colorbreakdown_1"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="coversionchargelibraryid_1" name="coversionchargelibraryid_1"  class="text_boxes" style="width:20px" value="" readonly  />                                    </td> 
                                </tr>
                    <? } ?>
                </tbody>
                </table>
                <table width="1035" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="600">Sum</th>
                            <th  width="50">
                            <input type="text" id="txtconreqnty_sum"  name="txtconreqnty_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th  width="50">
                            <input type="text" id="txtavgconreqnty_sum"  name="txtavgconreqnty_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="50"> 
                            <input type="text" id="txtconchargeunit_sum"  name="txtconchargeunit_sum" class="text_boxes_numeric" style="width:50px"  readonly />
                            </th>
                            <th width="80">
                             <input type="text" id="txtconamount_sum"  name="txtconamount_sum" class="text_boxes_numeric" style="width:80px"  readonly />
                            </th>
                            <th width="80" style=" border:none"></th>
                            <th width="" style=" border:none"></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="1035" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {

						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", $save_update,0,"reset_form('fabriccost_3','','',0)",5) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_fabric_conversion_cost_dtls", $save_update,0,"reset_form('fabriccost_3','','',0)",5) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>
        
                     
                            
        
<?
}

if($action=="fabric_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );

?>
<script> 
function js_set_value(data)
{
	var data=data.split('_');
	var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller');
	var fabric_yarn_description_arr=fabric_yarn_description.split("**");
	var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
    document.getElementById('fab_des_id').value=data[0];
	document.getElementById('fab_nature_id').value=data[1];
	document.getElementById('construction').value=trim(data[2]);
	document.getElementById('fab_gsm').value=trim(data[3]);
	document.getElementById('process_loss').value=trim(data[4]);
	document.getElementById('fab_desctiption').value=trim(fabric_description);
	document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
	document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
    parent.emailwindow.hide();
}
function toggle( x, origColor ) 
{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="fab_des_id" name="fab_des_id" />
<input type="hidden" id="fab_nature_id" name="fab_des_id" />
<input type="hidden" id="construction" name="construction" />
<input type="hidden" id="composition" name="composition" />
<input type="hidden" id="fab_gsm" name="fab_gsm" />
<input type="hidden" id="process_loss" name="process_loss" />
<input type="hidden" id="fab_desctiption" name="fab_desctiption" />
<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
</form>
<?
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	
	//$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id";
	//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$lib_yarn_count,9=>$yarn_type);
	//echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stich Length,Process Loss,Composition", "100,100,100,100,90,50,300","950","350",0, $sql, "js_set_value", "id,fab_nature_id,construction,gsm_weight,process_loss", "",1, "fab_nature_id,0,0,color_range_id,0,0,id", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0') ;
?>
<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
<thead>
<tr>
<th width="50">SL No</th>
<th width="100">Fab Nature</th>
<th width="100">Construction</th>
<th width="100">GSM/Weight</th>
<th width="100">Color Range</th>
<th width="90">Stich Length</th>
<th width="50">Process Loss</th>
<th>Composition</th>
</tr>
</thead>
</table>
<div id="" style="max-height:350px; width:948px; overflow-y:scroll">
<table id="list_view" class="rpt_table" width="930" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
<tbody>
<?
//Mysql
 /* $sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id order by a.id");*/
 //oracle
 $sql_data=sql_select("select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss order by a.id");
$i=1;
foreach($sql_data as $row)
{
	if ($i%2==0)  
		$bgcolor="#E9F3FF";
	else
		$bgcolor="#FFFFFF";
?>
<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')] ?>')">
<td width="50"><? echo $i; ?></td>
<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
<td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
<td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
<td><? echo $composition_arr[$row[csf('id')]]; ?></td>
</tr>

<?
$i++;
}
?>
</tbody>
</table>
<script>
setFilterGrid("list_view",-1);
toggle( "tr_"+"<? echo $libyarncountdeterminationid; ?>", '#FFFFCC');
</script>
</div>
</div>
</body>
</html>
<?
}
if($action =="fabric_yarn_description")
{
	$fab_description="";
	$yarn_description="";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if($fab_description!="")
			{
				$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
			else
			{
				$fab_description=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				//.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].","
			}
			
			if($yarn_description!="")
			{
				//$yarn_description=$yarn_description."__".$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
			else
			{
				//$yarn_description=$lib_yarn_count[$row[csf('count_id')]]."_".$composition[$row[csf('copmposition_id')]]."_100_".$yarn_type[$row[csf('type_id')]]."_".$row[csf('percent')];
				$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];

			}
		}
	}
	echo $fab_description."**".$yarn_description;
	
}

if($action=="rate_amount")
{
	$result="";
	$data_array=sql_select("select rate,amount from  wo_pre_cost_fabric_cost_dtls where id='$data'");
	foreach( $data_array as $row )
	{
	  $result=$row["rate"]."_".$row["amount"];	
	}
	echo $result; 
}

if ($action=="consumption_popup")
{
  echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
  extract($_REQUEST);
	
	$cons_breck_downn= $_SESSION['logic_erp']['cons_breck_downn'];
	$msmnt_breack_downn= $_SESSION['logic_erp']['msmnt_breack_downn'];
	$marker_breack_down= $_SESSION['logic_erp']['marker_breack_down'];
	
?>
     
<script>
var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
function add_break_down_tr( i )
{
	var body_part_id=document.getElementById('body_part_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	var row_num=$('#tbl_consmption_cost tr').length-1;
	if (i==0)
	{
		i=1;
		 $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth 
		  }); 
		  return;
	}
	
	if (row_num!=i)
	{
		return false;
	}
	
	if (form_validation('gmtssizes_'+i+'*diawidth_'+i+'*cons_'+i+'*processloss_'+i+'*requirement_'+i+'*pcs_'+i+'*itemsizes_'+i+'*pono_'+i+'*pocolor_'+i,'Gmts Sizes*Width*Cons*Process Loss*Requirement*Pcs*Item Sizes*PO NO*PO Color')==false)
	{
		return;
	}
	
	if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
	{
		 return;
	}
	
	if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2 && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false)
	{
		   return;
	}
	else
	{
		i++;
	 
		 $("#tbl_consmption_cost tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_consmption_cost");
		  
		  $('#addrow_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+")");
		  $('#decreaserow_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_consmption_cost')");
		  $('#cons_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'cons_sum', 'cons_' )");
		  $('#cons_'+i).removeAttr("onChange").attr("onChange","set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement( "+i+")");
		  $('#diawidth_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
		  $('#processloss_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'processloss_sum', 'processloss_' )");
		  $('#processloss_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_')");
          $('#requirement_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'requirement_sum', 'requirement_')");
		  $('#requirement_'+i).removeAttr("onChange").attr("onChange","calculate_requirement( "+i+");set_sum_value( 'requirement_sum', 'requirement_')");
		  $('#pcs_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'pcs_sum', 'pcs_')");

		  var j=i-1;
		  $('#gmtssizes_'+i).val(''); 
		  $('#diawidth_'+i).val($('#diawidth_'+j).val());
		  $('#cons_'+i).val('');
		  $('#processloss_'+i).val($('#processloss_'+j).val());
		  $('#requirement_'+i).val('');
		  $('#pcs_'+i).val($('#pcs_'+j).val());
		  $('#updateidcb_'+i).val('');
		  $('#itemsizes_'+i).val('');
		  //-----------------------
		  $("#tbl_msmnt_cost tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_msmnt_cost");
		  if(body_part_id==1 )
		  {
			  $('#bodylength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodysewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodyhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevelength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#sleevehemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestlenght_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#chestsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#bodylength_'+i).val(''); 
			  $('#bodysewingmargin_'+i).val($('#bodysewingmargin_'+j).val());
			  $('#bodyhemmargin_'+i).val($('#bodyhemmargin_'+j).val());
			  $('#sleevelength_'+i).val('');
			  $('#sleevesewingmargin_'+i).val($('#sleevesewingmargin_'+j).val());
			  $('#sleevehemmargin_'+i).val($('#sleevehemmargin_'+j).val());
			  $('#chestlenght_'+i).val('');
			  $('#chestsewingmargin_'+i).val($('#chestsewingmargin_'+j).val());
		  }
		  if(body_part_id==20)
		  {
			  $('#frontriselength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#frontrisesewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#westbandsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamlength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamsewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#inseamhemmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthailength_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#halfthaisewingmargin_'+i).removeAttr("onChange").attr("onChange","calculate_measurement_top("+i+")");
			  $('#frontriselength_'+i).val($('#frontriselength_'+j).val()); 
			  $('#frontrisesewingmargin_'+i).val($('#frontrisesewingmargin_'+j).val());
			  $('#westbandlength_'+i).val($('#westbandlength_'+j).val());
			  $('#westbandsewingmargin_'+i).val($('#westbandsewingmargin_'+j).val());
			  $('#inseamlength_'+i).val($('#inseamlength_'+j).val());
			  $('#inseamsewingmargin_'+i).val($('#inseamsewingmargin_'+j).val());
			  $('#inseamhemmargin_'+i).val($('#inseamhemmargin_'+j).val());
			  $('#halfthailength_'+i).val($('#halfthailength_'+j).val());
			  $('#halfthaisewingmargin_'+i).val($('#halfthaisewingmargin_'+j).val());
		  }
		  //------------------
		  set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
          set_sum_value( 'pcs_sum', 'pcs_');
		  $("#gmtssizes_"+i).autocomplete({
			 source: str_gmtssizes
		  });
		   $("#diawidth_"+i).autocomplete({
			 source:  str_diawidth 
		  });  
	}
}


function copy_value(value,field_id,i)
{
  var copy_val=document.getElementById('copy_val').checked;
  var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
  var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
  var pocolorid=document.getElementById('pocolorid_'+i).value;
  var rowCount = $('#tbl_consmption_cost tr').length-1;
 // var copy_basis=document.getElementById('copy_basis').value;
  var copy_basis=$('input[name="copy_basis"]:checked').val()
  //alert(copy_basis)
  if(hid_fab_cons_in_quotation_variable==1)
  {
	  /*if(copy_val==true)
	  {*/
	  for(var j=i; j<=rowCount; j++)
		{
		if(document.getElementById('approved_'+j).value==0)
		{
		if(copy_val==true)
	    {
			

			  if(field_id=='diawidth_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j) 
				}
			  }
			  if(field_id=='itemsizes_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j) 
				}
			  }
		}
		  if(field_id=='cons_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_requirement(j)  
			  }
		  }
		  if(field_id=='processloss_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_requirement(j)  
			  }
		  }
		}//if(var approved=document.getElementById('approved_'+j).value==0)
	  }
	 // }
  }
  
  if(hid_fab_cons_in_quotation_variable==2)
  {
	  /*if(copy_val==true)
	  {*/
		  for(var j=i; j<=rowCount; j++)
		  {
		  if(document.getElementById('approved_'+j).value==0)
		  {
		  if(copy_val==true)
		  {
			  if(field_id=='diawidth_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				  document.getElementById(field_id+j).value=value;
				  calculate_measurement_top(j)
				}
			  }
			  else if(field_id=='itemsizes_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_measurement_top(j)
				}
			  }
		  }
		  if(field_id=='processloss_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_requirement(j)  
			  }
		  }
		  
		  if(field_id=='bodylength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  
		  if(field_id=='bodysewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='bodyhemmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='sleevelength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='sleevesewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='sleevehemmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='chestlenght_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='chestsewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  // main fabric Bootom start
		  if(field_id=='frontriselength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='frontrisesewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='westbandlength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='westbandsewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  
		  if(field_id=='inseamlength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		   if(field_id=='inseamsewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  
		  if(field_id=='inseamhemmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  if(field_id=='halfthailength_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
		  
		  if(field_id=='halfthaisewingmargin_')
		  {
			  if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				 calculate_measurement_top(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_measurement_top(j)
			  }
		  }
			  
			  /*else
			  {
				  alert(field_id+j)
			  document.getElementById(field_id+j).value=value;
			  calculate_measurement_top(j)
			  }*/
		  }//if(document.getElementById('approved_'+j).value==0)
	      }
	  //}
  }
  if(hid_fab_cons_in_quotation_variable==3)
  {
	  /*if(copy_val==true)
	  {*/
	  for(var j=i; j<=rowCount; j++)
	  {
	  if(document.getElementById('approved_'+j).value==0)
	  {
		  if(copy_val==true)
		  {
			  if(field_id=='diawidth_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j) 
				}
			  }
			  if(field_id=='itemsizes_')
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j) 
				}
			  }
		  }
		  if(field_id=='cons_')
		  {
			if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_requirement(j)  
			  }
		  }
		  if(field_id=='processloss_')
		  {
			if(copy_basis==1)
			  {
				if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			 else if(copy_basis==2)
			  {
				if( pocolorid==document.getElementById('pocolorid_'+j).value)
				{
				document.getElementById(field_id+j).value=value;
				calculate_requirement(j)
				}
			  }
			  else if(copy_val==true)
			  {
				  document.getElementById(field_id+j).value=value;
			      calculate_requirement(j)  
			  }
		  }
		}//if(document.getElementById('approved_'+j).value==0)
	  }
	  //}
  }
}
function check_width_is_null_or_not(i)
{
	var cbofabricnature_id=document.getElementById('cbofabricnature_id').value;
	if(cbofabricnature_id==3)
	{
		 if(document.getElementById('diawidth_'+i).value=='' || document.getElementById('diawidth_'+i).value==0)
		 {
			 alert("Fill up Width")
			 document.getElementById('diawidth_'+i).focus();
		 }
	}
}

function fn_delete_down_tr(rowNo,table_id) 
{   
	if(table_id=='tbl_consmption_cost')
	{
		var numRow = $('table#tbl_consmption_cost tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_consmption_cost tbody tr:last').remove();
			$('#tbl_msmnt_cost tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
          set_sum_value( 'pcs_sum', 'pcs_');
	}
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='cons_sum')
	{
	var ddd={dec_type:6,comma:0,currency:1};
	}
	if(des_fil_id=='processloss_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	
	if(des_fil_id=='requirement_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	if(des_fil_id=='pcs_sum')
	{
	var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	/*document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
	claculate_avg()
}
	
function js_set_value()
{
	var body_part_id=document.getElementById('body_part_id').value;
	var cbofabricnature_id=document.getElementById('cbofabricnature_id').value;
	var hid_fab_cons_in_quotation_variable=document.getElementById('hid_fab_cons_in_quotation_variable').value;
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	var cons_breck_down="";
	var msmnt_breack_down="";
	var marker_breack_down="";
	if(hid_fab_cons_in_quotation_variable==3)
	{
		if(form_validation('txt_marker_dia*txt_marker_yds*txt_marker_inch*txt_gmt_pcs*txt_marker_length_yds*txt_marker_gsm*txt_marker_net_fab_cons','Marker Dia*Marker Yds*Marker Inch*Gmt Pcs*Marker Length*Marker Gsm*Marker Net Fabric')==false)
		{
			return;
		}
		else
		{
			var txt_marker_dia=$('#txt_marker_dia').val();
			var txt_marker_yds=$('#txt_marker_yds').val();
			var txt_marker_inch=$('#txt_marker_inch').val();
			var txt_gmt_pcs=$('#txt_gmt_pcs').val();
			var txt_marker_length_yds=$('#txt_marker_length_yds').val();
			var txt_marker_net_fab_cons=$('#txt_marker_net_fab_cons').val();
			marker_breack_down+=txt_marker_dia+'_'+txt_marker_yds+'_'+txt_marker_inch+'_'+txt_gmt_pcs+'_'+txt_marker_length_yds+'_'+txt_marker_net_fab_cons;
		}
	}
	for(var i=1; i<=rowCount; i++)
	{
		var ponoid=$('#ponoid_'+i).val()
			if(ponoid=='')
			{
				 ponoid=0;
			}
		var pocolorid=$('#pocolorid_'+i).val()
			if(pocolorid=='')
			{
				 pocolorid=0;
			}
		var gmtssizesid=$('#gmtssizesid_'+i).val()
			if(gmtssizesid=='')
			{
				 gmtssizesid=0;
			}
		var diawidth=$('#diawidth_'+i).val()
			if(diawidth=='')
			{
				 diawidth=0;
			}
		var itemsizes=$('#itemsizes_'+i).val();
			if(itemsizes=='')
			{
				 itemsizes=0;
			}
		var cons=$('#cons_'+i).val()
			if(cons=='')
			{
				 cons=0;
			}
		var processloss=$('#processloss_'+i).val()
			if(processloss=='')
			{
				 processloss=0;
			}
		var requirement=$('#requirement_'+i).val()
			if(requirement=='')
			{
				 requirement=0;
			}
	    var pcs=$('#pcs_'+i).val()
			if(pcs=='')
			{
				 pcs=0;
			}
	    var colorsizetableid=$('#colorsizetableid_'+i).val()
			if(colorsizetableid=='')
			{
				 pcs=0;
			}
			
			
			
			if(cbofabricnature_id==3 && diawidth !='' && cons!='' && processloss=='' )
			{
				alert("Fill up  Process Loss")
				return;
			}
			if(cbofabricnature_id==3 && diawidth !='' && cons=='' && processloss!='' )
			{
				alert("Fill up  Cons")
				return;
			}
			if(cbofabricnature_id==3 && diawidth =='' && cons!='' && processloss!='' )
			{
				alert("Fill up Width")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth !='' && cons=='' && processloss=='' )
			{
				alert("Fill up Cons And  Process Loss")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth =='' && cons=='' && processloss!='' )
			{
				alert("Fill up  Cons and Width")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth =='' && cons!='' && processloss=='' )
			{
				alert("Fill up Width Process Loss")
				return;
			}
			
			//================================================
			if(cbofabricnature_id==3 && diawidth !=0 && cons!=0 && processloss=='' )
			{
				alert("Fill up  Process Loss")
				return;
			}
			if(cbofabricnature_id==3 && diawidth !=0 && cons==0 && processloss!=0 )
			{
				alert("Fill up  Cons")
				return;
			}
			if(cbofabricnature_id==3 && diawidth ==0 && cons!=0 && processloss!=0 )
			{
				alert("Fill up Width")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth !=0 && cons==0 && processloss==0 )
			{
				alert("Fill up Cons And  Process Loss")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth ==0 && cons==0 && processloss!=0 )
			{
				alert("Fill up  Cons and Width")
				return;
			}
			
			if(cbofabricnature_id==3 && diawidth ==0 && cons!=0 && processloss==0 )
			{
				alert("Fill up Width Process Loss")
				return;
			}
			//=======================================================
			
			
			if(cbofabricnature_id==2  && cons!='' && processloss=='')
			{
				alert("Fill up  Process Loss")
				return;
			}
			if(cbofabricnature_id==2  && cons=='' && processloss!='')
			{
				alert("Fill up Cons")
				return;
			}
			//========================================================
			if(cbofabricnature_id==2  && cons!=0 && processloss<0)
			{
				alert("Fill up  Process Loss")
				return;
			}
			if(cbofabricnature_id==2  && cons==0 && processloss!=0)
			{
				alert("Fill up Cons")
				return;
			}
			//======================================================
			
			
			if(body_part_id==1 && hid_fab_cons_in_quotation_variable==2 && cons!='' && processloss!='' && form_validation('bodylength_'+i+'*bodysewingmargin_'+i+'*bodyhemmargin_'+i+'*sleevelength_'+i+'*sleevesewingmargin_'+i+'*sleevehemmargin_'+i+'*chestlenght_'+i+'*chestsewingmargin_'+i,'Body Length*Body Sewing Margin*Body Hem Margin*Sleeve Length*Sleeve Sewing Margin*Sleeve Hem Margin*Chest Length*Chest Sewing Margin')==false)
		    {
				return;
		     }			 
			 if(body_part_id==20 && hid_fab_cons_in_quotation_variable==2  && cons!='' && processloss!='' && form_validation('frontriselength_'+i+'*frontrisesewingmargin_'+i+'*westbandlength_'+i+'*westbandsewingmargin_'+i+'*inseamlength_'+i+'*inseamsewingmargin_'+i+'*inseamhemmargin_'+i+'*halfthailength_'+i+'*halfthaisewingmargin_'+i,'Front Rise Length*Front Rise Sewing Margin*West Band Length*West Band Sewing Margin*Inseam Length*Inseam Sewing Margin*Inseam Hem Margin*Half Thai Length* Half Thai Sewing Margin')==false )
		    {
				 return;
		    }
			//if(cons !="")
			//{
				if(cons_breck_down=="")
				{
					cons_breck_down+=ponoid+'_'+pocolorid+'_'+gmtssizesid+'_'+diawidth+'_'+itemsizes+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+colorsizetableid;
				}
				else
				{
					cons_breck_down+="__"+ponoid+'_'+pocolorid+'_'+gmtssizesid+'_'+diawidth+'_'+itemsizes+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+colorsizetableid;
				}
			//}
			
		if(hid_fab_cons_in_quotation_variable==2)
		{
			if(body_part_id==1)
			{
				var bodylength=$('#bodylength_'+i).val();
				if(bodylength=='')
				{
					bodylength=0;
				}
				var bodysewingmargin=$('#bodysewingmargin_'+i).val();
				if(bodysewingmargin=='')
				{
					bodysewingmargin=0;
				}
				var bodyhemmargin=$('#bodyhemmargin_'+i).val();
				if(bodyhemmargin=='')
				{
					bodyhemmargin=0;
				}
				var sleevelength=$('#sleevelength_'+i).val();
				if(sleevelength=='')
				{
					sleevelength=0;
				}
				var sleevesewingmargin=$('#sleevesewingmargin_'+i).val();
				if(sleevesewingmargin=='')
				{
					sleevesewingmargin=0;
				}
				var sleevehemmargin=$('#sleevehemmargin_'+i).val();
				if(sleevehemmargin=='')
				{
					sleevehemmargin=0;
				}
				var chestlenght=$('#chestlenght_'+i).val();
				if(chestlenght=='')
				{
					chestlenght=0;
				}
				var chestsewingmargin= $('#chestsewingmargin_'+i).val();
				if(chestsewingmargin=='')
				{
					chestsewingmargin=0;
				}
				var totalcons=$('#totalcons_'+i).val();
				if(totalcons=='')
				{
					totalcons=0;
				}
			}
			
			if(body_part_id==20)
			{
				var frontriselength= $('#frontriselength_'+i).val();
				if(frontriselength=='')
				{
					frontriselength=0;
				}
				var frontrisesewingmargin=$('#frontrisesewingmargin_'+i).val();
				if(frontrisesewingmargin=='')
				{
					frontrisesewingmargin=0;
				}
				var westbandlength=$('#westbandlength_'+i).val();
				if(westbandlength=='')
				{
					westbandlength=0;
				}
				var westbandsewingmargin=$('#westbandsewingmargin_'+i).val();
				if(westbandsewingmargin=='')
				{
					westbandsewingmargin=0;
				}
				var inseamlength=$('#inseamlength_'+i).val();
				if(inseamlength=='')
				{
					inseamlength=0;
				}
				var inseamsewingmargin=$('#inseamsewingmargin_'+i).val();
				if(inseamsewingmargin=='')
				{
					inseamsewingmargin=0;
				}
				var inseamhemmargin=$('#inseamhemmargin_'+i).val();
				if(inseamhemmargin=='')
				{
					inseamhemmargin=0;
				}
				var halfthailength=$('#halfthailength_'+i).val();
				if(halfthailength=='')
				{
					halfthailength=0;
				}
				var halfthaisewingmargin=$('#halfthaisewingmargin_'+i).val();
				if(halfthaisewingmargin=='')
				{
					halfthaisewingmargin=0;
				}
				var totalcons=$('#totalcons_'+i).val();
				if(totalcons=='')
				{
					totalcons=0;
				}
			}
			
			//if(cons !="")
			//{
			if(msmnt_breack_down=="")
			{
				
				if(body_part_id==1)
				{
				msmnt_breack_down+=bodylength+'_'+bodysewingmargin+'_'+bodyhemmargin+'_'+sleevelength+'_'+sleevesewingmargin+'_'+sleevehemmargin+'_'+chestlenght+'_'+chestsewingmargin+'_'+totalcons;
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+=frontriselength+'_'+frontrisesewingmargin+'_'+westbandlength+'_'+westbandsewingmargin+'_'+inseamlength+'_'+inseamsewingmargin+'_'+inseamhemmargin+'_'+halfthailength+'_'+halfthaisewingmargin+'_'+totalcons;
				}
			}
			else
			{
				if(body_part_id==1)
				{
				msmnt_breack_down+="__"+bodylength+'_'+bodysewingmargin+'_'+bodyhemmargin+'_'+sleevelength+'_'+sleevesewingmargin+'_'+sleevehemmargin+'_'+chestlenght+'_'+chestsewingmargin+'_'+totalcons;
				}
				if(body_part_id==20)
				{
				msmnt_breack_down+="__"+frontriselength+'_'+frontrisesewingmargin+'_'+westbandlength+'_'+westbandsewingmargin+'_'+inseamlength+'_'+inseamsewingmargin+'_'+inseamhemmargin+'_'+halfthailength+'_'+halfthaisewingmargin+'_'+totalcons;
				}
			}
			//}
		}
	}
	document.getElementById('cons_breck_down').value=cons_breck_down;
	document.getElementById('msmnt_breack_down').value=msmnt_breack_down;
    document.getElementById('marker_breack_down').value=marker_breack_down;

	//alert(cons_breck_down)
	claculate_avg()
	parent.emailwindow.hide();
}



function calculate_measurement_top(i)
{
	var body_part_id=document.getElementById('body_part_id').value;
	var cbofabricnature_id=document.getElementById('cbofabricnature_id').value;
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	if (cbo_costing_per_id==1) // knit type
	{
		var dzn_mult=1*12;
	}
	else if (cbo_costing_per_id==2) // knit type
	{
		var dzn_mult=1*1;
	}
	else if (cbo_costing_per_id==3) // knit type
	{
		var dzn_mult=2*12;
	}
	else if (cbo_costing_per_id==4) // knit type
	{
		var dzn_mult=3*12;
	}
	else if (cbo_costing_per_id==5) // knit type
	{
		var dzn_mult=4*12;
	}
	else
	{
		dzn_mult=0;
	}
	
//------------------------------------Knit------------------------------------
	if(cbofabricnature_id==2)//Knit
	{
	var txt_required_gsm_top=(document.getElementById('txt_gsm').value)*1;
		if (body_part_id==1)//main fabric top 
		{
			var txt_body_length_measurement_top=0;
			var txt_body_length_sewing_top=0;
			var txt_body_length_hem_top=0;
			var txt_sleeve_length_measurement_top=0;
			var txt_sleeve_length_sewing_top=0;
			var txt_sleeve_length_hem_top=0;
			var txt_chest_measurement_top=0;
			var txt_chest_sew_top=0;
			txt_body_length_measurement_top=(document.getElementById('bodylength_'+i).value)*1;
			txt_body_length_sewing_top=(document.getElementById('bodysewingmargin_'+i).value)*1;
			txt_body_length_hem_top=(document.getElementById('bodyhemmargin_'+i).value)*1;
			txt_sleeve_length_measurement_top=(document.getElementById('sleevelength_'+i).value)*1;
			txt_sleeve_length_sewing_top=(document.getElementById('sleevesewingmargin_'+i).value)*1;
			txt_sleeve_length_hem_top=(document.getElementById('sleevehemmargin_'+i).value)*1;
			txt_chest_measurement_top=(document.getElementById('chestlenght_'+i).value)*1;
			txt_chest_sew_top=(document.getElementById('chestsewingmargin_'+i).value)*1;
			//[{(Body Lentg +Sleeve Lenth + Sewing Margin + Hem) x (Half Chest + Sewing Margin)} x 2] x 12 x GSM / 10000000
			var dbl_total=(((txt_body_length_measurement_top +txt_sleeve_length_measurement_top+txt_body_length_sewing_top + txt_sleeve_length_sewing_top+txt_body_length_hem_top+txt_sleeve_length_hem_top) * (txt_chest_measurement_top + txt_chest_sew_top)) * 2) * 12 * txt_required_gsm_top / 10000000;
			
            //var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;	
		}
		if (body_part_id==20)//main fabric bottom
		{
			var txt_front_rise_measurement_bottom=0;
			var txt_front_rise_sewing_bottom=0;
			var txt_west_band_measurement_bottom=0;
			var txt_west_band_sewing_bottom=0;
			var txt_in_seam_measurement_bottom=0;
			var txt_in_seam_sew_bottom=0;
			var txt_in_seam_hem_bottom=0;
			var txt_half_thai_measurement_bottom=0;
			var txt_half_thai_sew_bottom=0;
			var txt_front_rise_measurement_bottom=(document.getElementById('frontriselength_'+i).value)*1;
			var txt_front_rise_sewing_bottom=(document.getElementById('frontrisesewingmargin_'+i).value)*1;
			var txt_west_band_measurement_bottom=(document.getElementById('westbandlength_'+i).value)*1;
			var txt_west_band_sewing_bottom=(document.getElementById('westbandsewingmargin_'+i).value)*1;
			var txt_in_seam_measurement_bottom=(document.getElementById('inseamlength_'+i).value)*1;
			var txt_in_seam_sew_bottom=(document.getElementById('inseamsewingmargin_'+i).value)*1;
			var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
			var txt_half_thai_measurement_bottom=(document.getElementById('halfthailength_'+i).value)*1;
 			var txt_half_thai_sew_bottom=(document.getElementById('halfthaisewingmargin_'+i).value)*1;
			//[{(Front Rise + In Seam + West Band + Sewing Margin + Hem) x (Half Thai + Sewing Margin)} x 4] x 12  x GSM / 10000000
			var dbl_total=(((txt_front_rise_measurement_bottom +txt_west_band_measurement_bottom +txt_in_seam_measurement_bottom+txt_front_rise_sewing_bottom + txt_west_band_sewing_bottom+txt_in_seam_sew_bottom+txt_in_seam_hem_bottom) * (txt_half_thai_measurement_bottom+txt_half_thai_sew_bottom)) * 4) * 12  * txt_required_gsm_top/ 10000000;
			//var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1)*(txt_required_gsm_top*1))/10000000;
		}
	}
//------------------------------------End Knit------------------------------------
//----------------------------------- Woven---------------------------------------
	if(cbofabricnature_id==3)//woven
	{
	 var txt_required_weight_top=document.getElementById('diawidth_'+i).value;
		if (body_part_id==1)//main fabric top 
		{
			var txt_body_length_measurement_top=0;
			var txt_body_length_sewing_top=0;
			var txt_body_length_hem_top=0;
			var txt_sleeve_length_measurement_top=0;
			var txt_sleeve_length_sewing_top=0;
			var txt_sleeve_length_hem_top=0;
			var txt_chest_measurement_top=0;
			var txt_chest_sew_top=0;
			txt_body_length_measurement_top=document.getElementById('bodylength_'+i).value;
			txt_body_length_sewing_top=document.getElementById('bodysewingmargin_'+i).value;
			txt_body_length_hem_top=document.getElementById('bodyhemmargin_'+i).value;
			txt_sleeve_length_measurement_top=document.getElementById('sleevelength_'+i).value;
			txt_sleeve_length_sewing_top=document.getElementById('sleevesewingmargin_'+i).value;
			txt_sleeve_length_hem_top=document.getElementById('sleevehemmargin_'+i).value;
			txt_chest_measurement_top=document.getElementById('chestlenght_'+i).value;
			txt_chest_sew_top=document.getElementById('chestsewingmargin_'+i).value;
			//[{(Body Lentg +Sleeve Lenth + Sewing Margin + Hem) x (Half Chest + Sewing Margin)} x 2] x 12 / (Width x 36)
			var dbl_total=(((txt_body_length_measurement_top*1)+(txt_body_length_sewing_top*1)+(txt_body_length_hem_top*1)+(txt_sleeve_length_measurement_top*1)+(txt_sleeve_length_sewing_top*1)+(txt_sleeve_length_hem_top*1))*((txt_chest_measurement_top*1)+(txt_chest_sew_top*1))*2*(dzn_mult*1))/((txt_required_weight_top*1)*36);
			
		}
		if (body_part_id==20)//main fabric bottom 
		{
			var txt_front_rise_measurement_bottom=0;
			var txt_front_rise_sewing_bottom=0;
			var txt_west_band_measurement_bottom=0;
			var txt_west_band_sewing_bottom=0;
			var txt_in_seam_measurement_bottom=0;
			var txt_in_seam_sew_bottom=0;
			var txt_in_seam_hem_bottom=0;
			var txt_half_thai_measurement_bottom=0;
			var txt_half_thai_sew_bottom=0;
			var txt_front_rise_measurement_bottom=document.getElementById('frontriselength_'+i).value;
			var txt_front_rise_sewing_bottom=document.getElementById('frontrisesewingmargin_'+i).value;
			var txt_west_band_measurement_bottom=document.getElementById('westbandlength_'+i).value;
			var txt_west_band_sewing_bottom=document.getElementById('westbandsewingmargin_'+i).value;
			var txt_in_seam_measurement_bottom=document.getElementById('inseamlength_'+i).value;
			var txt_in_seam_sew_bottom=document.getElementById('inseamsewingmargin_'+i).value;
			var txt_in_seam_hem_bottom=document.getElementById('inseamhemmargin_'+i).value;
			var txt_half_thai_measurement_bottom=document.getElementById('halfthailength_'+i).value;
 			var txt_half_thai_sew_bottom=document.getElementById('halfthaisewingmargin_'+i).value;
			//[{(Front Rise + In Seam + West Band + Sewing Margin + Hem) x (Half Thai + Sewing Margin)} x 4] x 12  / Width x 36
			var dbl_total=(((txt_front_rise_measurement_bottom*1)+(txt_front_rise_sewing_bottom*1)+(txt_west_band_measurement_bottom*1)+(txt_west_band_sewing_bottom*1)+(txt_in_seam_measurement_bottom*1)+(txt_in_seam_sew_bottom*1)+(txt_in_seam_hem_bottom*1))*((txt_half_thai_measurement_bottom*1)+(txt_half_thai_sew_bottom*1))*4*(dzn_mult*1))/((txt_required_weight_top*1)*36);
			
		}
	}
	//----------------------------------- End Woven---------------------------------------
    dbl_total= number_format_common( dbl_total, 5, 0) ;	
	document.getElementById('totalcons_'+i).value=dbl_total;
	document.getElementById('cons_'+i).value=dbl_total;
	set_sum_value( 'cons_sum', 'cons_'  );
	set_sum_value( 'requirement_sum', 'requirement_' )
	calculate_requirement(i)
	claculate_avg()
	/*var rowCount = $('#tbl_consmption_cost tr').length-1;
	document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
}

function calculate_requirement(i)
{
	//alert(i)
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('cons_'+i).value)*1;
	var processloss=(document.getElementById('processloss_'+i).value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('requirement_'+i).value= WastageQty;
		set_sum_value( 'requirement_sum', 'requirement_' )
		claculate_avg()
	/*var rowCount = $('#tbl_consmption_cost tr').length-1;
	document.getElementById('calculated_cons').value=(document.getElementById('requirement_sum').value*1)/rowCount;
	document.getElementById('avg_cons').value=(document.getElementById('cons_sum').value*1)/rowCount;
	document.getElementById('calculated_procloss').value=(document.getElementById('processloss_sum').value*1)/rowCount;
    document.getElementById('calculated_pcs').value=(document.getElementById('pcs_sum').value*1)/rowCount;*/
}

function claculate_avg()
{
	
	var tot_plancut_qty=document.getElementById('tot_plancut_qty').value*1
	var calculated_cons=0;
	var avg_cons=0;
	var plancutqty=0;
	var significant_row=0;
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	
	for(var j=1; j<=rowCount; j++)
	{
		if(document.getElementById('cons_'+j).value =='' || document.getElementById('cons_'+j).value ==0)
		{
			continue;
		}
		else
		{
			//significant_row=significant_row+1;
			//var pcsgmts=document.getElementById('pcsgmts_'+i).value*1
			//var plan_cut_percent=(pcsgmts/tot_plancut_qty)*100;
			
			//var cons=document.getElementById('cons_'+i).value*1
			//var avg_cons_percent=(cons*plan_cut_percent)/100;
			//avg_cons+=avg_cons_percent;
			
			//var requirement=document.getElementById('requirement_'+i).value*1
			//var calculated_cons_percent=(requirement*plan_cut_percent)/100;
			//calculated_cons+=calculated_cons_percent;
			
			plancutqty+=document.getElementById('pcsgmts_'+j).value*1;
		}
	}
		
	for(var i=1; i<=rowCount; i++)
	{
		if(document.getElementById('cons_'+i).value =='' || document.getElementById('cons_'+i).value ==0)
		{
			continue;
		}
		else
		{
			significant_row=significant_row+1;
			var pcsgmts=document.getElementById('pcsgmts_'+i).value*1
			var plan_cut_percent=(pcsgmts/plancutqty)*100;
			
			var cons=document.getElementById('cons_'+i).value*1
			var avg_cons_percent=(cons*plan_cut_percent)/100;
			avg_cons+=avg_cons_percent;
			
			var requirement=document.getElementById('requirement_'+i).value*1
			var calculated_cons_percent=(requirement*plan_cut_percent)/100;
			calculated_cons+=calculated_cons_percent;
			
			//plancutqty+=document.getElementById('pcsgmts_'+i).value*1;
		}
		
	}
	//alert(plancutqty)
	//var calculated_cons=(document.getElementById('requirement_sum').value*1)/significant_row;
	//var avg_cons=(document.getElementById('cons_sum').value*1)/significant_row;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/significant_row;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/significant_row;
	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 5, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 5, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 5, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;
	document.getElementById('calculated_plancutqty').value=plancutqty;
	
}

function claculate_avg_old()
{
	var plancutqty=0;
	var significant_row=0;
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	for(var i=1; i<=rowCount; i++)
	{
		if(document.getElementById('cons_'+i).value =='' || document.getElementById('cons_'+i).value ==0)
		{
			continue;
		}
		else
		{
			significant_row=significant_row+1;
			plancutqty+=document.getElementById('pcsgmts_'+i).value*1;

		}
		
	}
	var calculated_cons=(document.getElementById('requirement_sum').value*1)/significant_row;
	var avg_cons=(document.getElementById('cons_sum').value*1)/significant_row;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/significant_row;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/significant_row;
	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 5, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 5, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 5, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;
    document.getElementById('calculated_plancutqty').value=plancutqty;

}


function calculate_marker_length()
{
	var cbo_costing_per_id=document.getElementById('cbo_costing_per_id').value;
	if (cbo_costing_per_id==1) // knit type
	{
		var dzn_mult=1*12;
	}
	else if (cbo_costing_per_id==2) // knit type
	{
		var dzn_mult=1*1;
	}
	else if (cbo_costing_per_id==3) // knit type
	{
		var dzn_mult=2*12;
	}
	else if (cbo_costing_per_id==4) // knit type
	{
		var dzn_mult=3*12;
	}
	else if (cbo_costing_per_id==5) // knit type
	{
		var dzn_mult=4*12;
	}
	else
	{
		dzn_mult=0;
	}
	var txt_marker_yds= (document.getElementById('txt_marker_yds').value)*1;
	var txt_marker_inch= (document.getElementById('txt_marker_inch').value)*1;
	var txt_gmt_pcs= (document.getElementById('txt_gmt_pcs').value)*1;
	var txt_marker_dia= (document.getElementById('txt_marker_dia').value)*1;
	var txt_marker_gsm= (document.getElementById('txt_marker_gsm').value)*1;

	//if(txt_marker_yds !="" && txt_marker_inch !="" && txt_gmt_pcs !="")
	//{
		//alert(txt_marker_inch);
		//alert(txt_gmt_pcs);
		//alert(dzn_mult);
		var txt_marker_length_yds=(txt_marker_inch/36)+txt_marker_yds;
		//alert(txt_marker_length_yds);
		var txt_marker_length_yds2=(txt_marker_length_yds/txt_gmt_pcs)*dzn_mult;
		txt_marker_length_yds3= number_format_common( txt_marker_length_yds2, 5, 0) ;	
        document.getElementById('txt_marker_length_yds').value=txt_marker_length_yds3;
		var txt_marker_net_fab_cons=((txt_marker_length_yds3*36*2.54)/dzn_mult)*(txt_marker_dia*2*2.54*dzn_mult*txt_marker_gsm);
		var txt_marker_net_fab_cons2=txt_marker_net_fab_cons/10000000;
		document.getElementById('txt_marker_net_fab_cons').value=number_format_common(txt_marker_net_fab_cons2,5,0);
		//document.getElementById('cons_1').value=number_format_common(txt_marker_net_fab_cons2,1,0);
		copy_value(number_format_common(txt_marker_net_fab_cons2,5,0),'cons_',1)

	//}
}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<fieldset>
            <legend><? echo $body_part_id.'.'.$body_part[$body_part_id].'   Costing '.$costing_per[$cbo_costing_per] ;?></legend>
        	<form id="consumptionform_1" autocomplete="off">
            <input type="hidden" id="cbo_company_id" name="cbo_company_id" value="<? echo $cbo_company_id; ?>"/> 
            <input type="hidden" id="cbo_costing_per_id" name="cbo_costing_per_id" value="<? echo $cbo_costing_per; ?>"/>
            <input type="hidden" id="hid_fab_cons_in_quotation_variable" name="hid_fab_cons_in_quotation_variable" value="<? echo $hid_fab_cons_in_quotation_variable; ?>" width="500" /> 
            <input type="hidden" id="body_part_id" name="body_part_id" value="<? echo $body_part_id; ?>"/>
            <input type="hidden" id="cbofabricnature_id" name="cbofabricnature_id" value="<? echo $cbofabricnature_id; ?>"/> 
            <input type="hidden" id="cons_breck_down" name="cons_breck_down"  width="500"  value="<? echo $cons_breck_downn;?>"/> 
            <input type="hidden" id="msmnt_breack_down" name="msmnt_breack_down"  value="<? echo $msmnt_breack_downn;?>"/>
            <input type="hidden" id="marker_breack_down" name="marker_breack_down"  value="<? echo $marker_breack_down;?>"/>
            <input type="hidden" id="txt_gsm" name="txt_gsm" value="<? echo $txtgsmweight; ?>"/>
            <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val" checked/>  
            <form>
            <input type="radio" name="copy_basis" value="1">Size Wise
<input type="radio" name="copy_basis" value="2">Color Wise
<input type="reset" value="Reset">
</form>
			<?
			//$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$txt_job_no'");
			$arr_bo_app_po=array();
			$sql_bo_app_po=sql_select("select a.is_approved,b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$txt_job_no' and a.booking_type=1 and a.is_short=2 and a.is_approved=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.pre_cost_fabric_cost_dtls_id,a.is_approved,b.po_break_down_id");
			foreach($sql_bo_app_po as $row_bo_app_po)
			{
			  $arr_bo_app_po[$row_bo_app_po[csf('po_break_down_id')]][$row_bo_app_po[csf('pre_cost_fabric_cost_dtls_id')]]	=$row_bo_app_po[csf('is_approved')];
			}
			$pcs_value=0;
			$set_item_ratio=return_field_value("set_item_ratio", "wo_po_details_mas_set_details", "job_no='$txt_job_no'  and gmts_item_id='$cbogmtsitem'");
			if($set_item_ratio==0 || $set_item_ratio=="")
			{
				$set_item_ratio=1;
			}
			
			if($cbo_costing_per==1)
			{
				$pcs_value=1*12*$set_item_ratio;
			}
			if($cbo_costing_per==2)
			{
				$pcs_value=1*1*$set_item_ratio;
			}
			if($cbo_costing_per==3)
			{
				$pcs_value=2*12*$set_item_ratio;
			}
			if($cbo_costing_per==4)
			{
				$pcs_value=3*12*$set_item_ratio;
			}
			if($cbo_costing_per==5)
			{
				$pcs_value=4*12*$set_item_ratio;
			}
			if($body_part_id==3)
			{
				$pcs_value=$pcs_value*2;
			}
			$tot_plancut_qty=0;
			$data_array_tot_po_qty=sql_select("select b.id, b.po_number,b.po_quantity,b.shipment_date,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'  and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id order by b.id,color_size_table_id");
			foreach($data_array_tot_po_qty as $data_array_tot_po_qty_row )
			{
				$tot_plancut_qty+=$data_array_tot_po_qty_row[csf('plan_cut_qnty')];
			}
			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=18 and item_category_id=$cbofabricnature_id and status_active=1 and is_deleted=0");
            ?>
           <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
           <input type="hidden" id="tot_plancut_qty" name="tot_plancut_qty" value="<? echo $tot_plancut_qty; ?>"/>

           <?
		   if($hid_fab_cons_in_quotation_variable==3){
		   ?>
          <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_marker_cost" rules="all">
              <thead>
                    <tr>
                        <th  width="100"  rowspan="2">Marker Dia (Inch)</th><th  width="100" colspan="2">Marker Length</th><th  width="110"  rowspan="2">Gmts. Size Ratio (Pcs)</th><th width="90"  rowspan="2">Marker Length -Yds (1Dzn Gmts)</th><th width="110"  rowspan="2">GSM</th><th width="110"  rowspan="2">Net Fab Cons</th><th></th>
                        
                    </tr>
                    <tr>
                        <th  width="100">Yds</th><th  width="100">Inch</th><th></th>
                        
                    </tr>
              </thead>
              <tbody>
              <?
			  $marker_breack_down_arr=explode("_",$marker_breack_down);
			  ?>
              <tr>
              <td><input type="text" id="txt_marker_dia"  name="txt_marker_dia" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[0];  ?>"> </td>
              <td><input type="text" id="txt_marker_yds"  name="txt_marker_yds" class="text_boxes_numeric" style="width:90px"  onChange="calculate_marker_length()" value="<? echo $marker_breack_down_arr[1];  ?>"></td>
              <td><input type="text" id="txt_marker_inch"  name="txt_marker_inch" class="text_boxes_numeric" style="width:90px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[2];  ?>"></td>
              <td><input type="text" id="txt_gmt_pcs"  name="txt_gmt_pcs" class="text_boxes_numeric" style="width:110px" onChange="calculate_marker_length()"  value="<? echo $marker_breack_down_arr[3];  ?>"></td>
              <td><input type="text" id="txt_marker_length_yds"  name="txt_marker_length_yds" class="text_boxes_numeric" style="width:110px" readonly  value="<? echo $marker_breack_down_arr[4];  ?>"></td>
              <td><input type="text" id="txt_marker_gsm"  name="txt_marker_gsm" class="text_boxes_numeric" readonly style="width:110px"  value="<? echo $txtgsmweight; ?>"></td>
              <td><input type="text" id="txt_marker_net_fab_cons"  name="txt_marker_net_fab_cons" class="text_boxes_numeric" style="width:110px"  value="<? echo $marker_breack_down_arr[5];  ?>"></td>
              <td></td>
              </tr>
              </tbody>
          </table>
           <?
		   }
		   ?>
<br/>
            	<table width="1010" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">SL</th><th  width="100">PO NO</th><th  width="100">Color</th><th  width="100">Gmts sizes</th><th  width="110"><? if($cbofabricnature_id==2){echo "Dia"; }else{ echo "Width";}?></th><th width="90">Item Sizes</th><th width="110">Finish Cons <? if($cbofabricnature_id==2){echo "(Kg)";} else{echo "(Yds)";} ?></th><th width="110">Process Loss %</th><th width="105">Grey Cons <? if($cbofabricnature_id==2){echo "(Kg)";} else{echo "(Yds)";} ?></th><th width="90">Pcs</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$po_no_library=return_library_array( "select id,po_number from wo_po_break_down where job_no_mst ='$txt_job_no'", "id", "po_number"  );
					$data_array=explode("__",$cons_breck_downn);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					
					//else
					//{
						if($pri_fab_cost_dtls_id !="")
						{
						$price_quatation_dia=return_library_array("select gmts_sizes,dia_width from  wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","dia_width");
						$price_quatation_cons=return_library_array("select gmts_sizes,cons from  wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","cons");
						$price_quatation_cons_process_loss=return_library_array("select gmts_sizes,process_loss_percent from  wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","process_loss_percent");
						$price_quatation_requirment=return_library_array("select gmts_sizes,requirment from  wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","requirment");
						}
						//Mysql
						/*$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,c.id as color_size_table_id,c.color_number_id,c.size_number_id,d.color_name from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, lib_color d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.color_number_id=d.id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.color_number_id,c.size_number_id  order by b.job_no_mst"); */
						//Oracle
						$data_array=sql_select("select b.id, b.po_number,b.po_quantity,b.shipment_date,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id order by b.id,color_size_table_id"); 
						//=======================================
						if($pre_cost_fabric_cost_dtls_id != "")
						{
							$row_arr_data="";
							$wo_pre_cos_fab_co_avg_con_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,dia_width,item_size,cons,process_loss_percent,requirment,pcs,color_size_table_id  from wo_pre_cos_fab_co_avg_con_dtls where job_no='$txt_job_no' and pre_cost_fabric_cost_dtls_id=$pre_cost_fabric_cost_dtls_id order by po_break_down_id,color_size_table_id");
							
							foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $wo_pre_cos_fab_co_avg_con_dtls_data_row)
							{
								$row_arr=$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('po_break_down_id')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('color_number_id')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('gmts_sizes')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('dia_width')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('item_size')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('cons')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('process_loss_percent')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('requirment')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('pcs')]."_".$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('color_size_table_id')];
								$row_arr_data.=$row_arr."__";
							}
							$row_arr_data=rtrim($row_arr_data,"__");
							//echo $row_arr_data;
							$data_array_cons=explode("__",$row_arr_data);
						}
						
						if($pre_cost_fabric_cost_dtls_id == "")
						{
						$data_array_cons=explode("__",$cons_breck_downn);
						}
						//=======================================
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								if($precostapproved==1 || $arr_bo_app_po[$row[csf('id')]][$pre_cost_fabric_cost_dtls_id]==1 )
								{
									$disabled=1;
								}
								else
								{
									$disabled=0;
								}
								
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="pono_<? echo $i;?>"  name="pono_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('po_number')]; ?>" readonly/>
                                    <input type="hidden" id="ponoid_<? echo $i;?>"  name="ponoid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" readonly/>

                                    </td>
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" readonly />
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" readonly>
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('size_number_id')]; ?>" readonly>
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_<? echo $i;?>"  value="<? if($data[3]==""){echo $price_quatation_dia[$row[csf('size_number_id')]];}else{ echo $data[3];} ?>"  name="diawidth_<? echo $i;?>"  class="<? if($cbofabricnature_id==2){echo "text_boxes"; }else{ echo "text_boxes_numeric";}?>" style="width:95px" onChange="<? if($hid_fab_cons_in_quotation_variable==2){ echo "calculate_measurement_top( $i)";} ?>;copy_value(this.value,'diawidth_',<? echo $i;?>)" <? if($disabled==1){ echo "disabled";} else{ echo "";} ?> />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:75px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $data[4]; ?>" <? if($disabled==1){ echo "disabled";} else{ echo "";} ?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>" onBlur="set_sum_value( 'cons_sum', 'cons_' )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'cons_',<? echo $i;?>)"  name="cons_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" <? if(($hid_fab_cons_in_quotation_variable==2 || $hid_fab_cons_in_quotation_variable==3) && ($body_part_id==1 || $body_part_id==20)){ echo "readonly";} else{ echo "";} ?> value="<? if ($data[5]==""){echo $price_quatation_cons[$row[csf('size_number_id')]];} else {echo $data[5];} ?>" <? if($disabled==1){ echo "disabled";} else{ echo "";} ?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="processloss_<? echo $i;?>" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' );copy_value(this.value,'processloss_',<? echo $i;?>) " value="<? if ($data[6]==""){echo $price_quatation_cons_process_loss[$row[csf('size_number_id')]];} else{echo $data[6];} ?>"  <? if($disabled==1){ echo "disabled";} else{ echo "";} ?>/> 
                                    </td>
                                    <td>
                                    <input type="text" id="requirement_<? echo $i;?>" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) " onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" value="<? if ($data[7]==""){ echo $price_quatation_requirment[$row[csf('size_number_id')]];} else { echo $data[7]; } ?>" readonly /> 
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:75px"  value="<? echo $pcs_value; ?>" readonly />
                                    <input type="hidden" id="pcsgmts_<? echo $i;?>"  name="pcsgmts_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:75px"  value="<? echo $row[csf('plan_cut_qnty')]; ?>" readonly>
                                    </td>
                                    <td id="add_1">
                                   <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>"  readonly/>

                                     <!--<input type="button" id="addrow_<? //echo $i;?>"  name="addrow_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? // echo $i;?>)" />-->
                                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?>,'tbl_consmption_cost');" disabled />
                                     <input type="hidden" id="approved_<? echo $i;?>"  name="approved_<? echo $i;?>"  class="text_boxes_numeric" style="width:75px"  value="<? echo $disabled; ?>">

                                    </td>
                                </tr>
                    <?
							}
						}
					//} 
					?>
                </tbody>
                </table>
               
                <table width="1010" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th style="width:460px;">SUM</th>
                            <th width="89"></th>
                            <th width="110"><input type="text" id="cons_sum" name="cons_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="110"><input type="text" id="processloss_sum"  name="processloss_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="105"><input type="text" id="requirement_sum"  name="requirement_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                            <th width="90">
                            <input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:75px" readonly>
                            <input type="hidden" id="plancutqty_sum"    name="plancutqty_sum" class="text_boxes_numeric" style="width:75px" readonly>
                            </th>
                            

                            <th width=""></th>
                        </tr>
                        <tr>
                        	<th style="width:463px;">AVG</th>
                            <th width="90"></th>
                            <th width="110"><input type="text" id="avg_cons" name="avg_cons" class="text_boxes_numeric" style="width:95px" value="<? //echo $calculated_conss;?>" readonly></th>
                            <th width="110"><input type="text" id="calculated_procloss"  name="calculated_procloss" class="text_boxes_numeric" style="width:95px" readonly></th>
                           <th width="105"><input type="text" id="calculated_cons" name="calculated_cons" class="text_boxes_numeric" style="width:90px" value="<? echo $calculated_conss;?>" readonly></th>
                            <th width="90">
                            <input type="text" id="calculated_pcs"    name="calculated_pcs" class="text_boxes_numeric" style="width:75px" readonly>
                            <input type="hidden" id="calculated_plancutqty"    name="calculated_plancutqty" class="text_boxes_numeric" style="width:75px" readonly>

                            </th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
				<script>
                set_sum_value( 'cons_sum', 'cons_'  );
                set_sum_value( 'processloss_sum', 'processloss_'  );
                set_sum_value( 'requirement_sum', 'requirement_');
                set_sum_value( 'pcs_sum', 'pcs_');
                </script>
            </form>
        </fieldset>
   </div>
<?
if ($hid_fab_cons_in_quotation_variable==2)
{
	if ($body_part_id==1)
    {
?>
     

<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="3">Body</th><th colspan="3">Sleeve </th><th colspan="2">1/2 Chest</th><th width="">Total</th>
                        </tr>
                    	<tr>
                        	<th width="80">Length</th><th  width="80">Sewing Margin</th><th  width="80">Hem Margin</th><th width="80"> Length</th><th width="80">Sewing Margin</th><th width="80">Hem Margin</th><th width="80">Length</th><th width="80">Sewing Margin</th> <th width="">Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array_msn=explode('__',$msmnt_breack_downn);
					//Mysql
					/*$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id,d.color_name from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, lib_color d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.color_number_id=d.id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.color_number_id,c.po_break_down_id,c.size_number_id  order by a.job_no");*/
				//Oracle	
				$data_array=sql_select("select b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id,min(c.id) as color_size_table_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id  order by b.id");

					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							
							$data=explode('_',$data_array_msn[$i]);
							$i++;
							?>
                            	<tr id="break_1" align="center">
                                    <td>
                                    <input type="text" id="bodylength_<? echo $i;?>"  name="bodylength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'bodylength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[0]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="bodysewingmargin_<? echo $i;?>"    name="bodysewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'bodysewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[1]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/>    
                                    </td>
                                    <td>
                                    <input type="text" id="bodyhemmargin_<? echo $i;?>" name="bodyhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'bodyhemmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value=" <? echo $data[2]; ?> "  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevelength_<? echo $i;?>"  name="sleevelength_<? echo $i;?>" class="text_boxes_numeric" style="width:65px"  onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'sleevelength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[3]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevesewingmargin_<? echo $i;?>"  name="sleevesewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'sleevesewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[4]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="sleevehemmargin_<? echo $i;?>"  name="sleevehemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'sleevehemmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[5]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="chestlenght_<? echo $i;?>"  name="chestlenght_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'chestlenght_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[6]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="chestsewingmargin_<? echo $i;?>"  name="chestsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:65px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'chestsewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[7]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:150px" readonly value="<? echo $data[8]; ?>" />
                                    </td>
                                </tr>
                            
                            <?
							 
						}
					}
					
					?>
                </tbody>
                </table>
            </form>
            
        </fieldset>
   </div>
<?
	}
	if($body_part_id==20)
	{
	?>
		<div align="center" style="width:100%;" >
<fieldset>
        	<form id="fabriccost_3" autocomplete="off">
            	<table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_msmnt_cost" rules="all">
                	<thead>
                        <tr>
                        	<th  colspan="2">Front Rise</th><th colspan="2">West Band</th><th colspan="3">In Seam</th><th colspan="2"> Half Thai</th><th >Total</th>
                        </tr>
                    	<tr>
                        	<th width="70">Length</th><th  width="70">Sewing Margin</th><th  width="70">Length</th><th width="70"> Sewing Margin</th><th width="70">Length</th><th width="70">Sewing Margin</th><th width="70">Hem Margin</th><th width="70">Length</th> <th width="70">Sewing Margin</th><th width="">Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array_msn=explode('__',$msmnt_breack_downn);
					//Mysql
					/*$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id,d.color_name from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, lib_color d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.color_number_id=d.id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.color_number_id,c.po_break_down_id,c.size_number_id  order by a.job_no");*/
				//Oracle	
					$data_array=sql_select("select b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id,min(c.id) as color_size_table_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'   and a.garments_nature='$garments_nature' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id  order by b.id");
					if (count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$data=explode('_',$data_array_msn[$i]);
							$i++;
							?>
                            	<tr id="break_1" align="center">
                                    <td> 
                                    <input type="text" id="frontriselength_<? echo $i;?>"  name="frontriselength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'frontriselength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[0]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="frontrisesewingmargin_<? echo $i;?>"    name="frontrisesewingmargin_<? echo $i;?>"  class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'frontrisesewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[1]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />    
                                    </td>
                                    <td>
                                    <input type="text" id="westbandlength_<? echo $i;?>" name="westbandlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'westbandlength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[2]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="westbandsewingmargin_<? echo $i;?>"  name="westbandsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px"  onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'westbandsewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[3]; ?>"   <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/ > 
                                    </td>
                                    <td>
                                    <input type="text" id="inseamlength_<? echo $i;?>"  name="inseamlength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'inseamlength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[4]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="inseamsewingmargin_<? echo $i;?>"  name="inseamsewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'inseamsewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[5]; ?>"   <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="inseamhemmargin_<? echo $i;?>"  name="inseamhemmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'inseamhemmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[6]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="halfthailength_<? echo $i;?>"  name="halfthailength_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'halfthailength_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[7]; ?>"   <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="halfthaisewingmargin_<? echo $i;?>"  name="halfthaisewingmargin_<? echo $i;?>" class="text_boxes_numeric" style="width:55px"   onChange="calculate_measurement_top(<? echo $i;?>);copy_value(this.value,'halfthaisewingmargin_',<? echo $i;?>)" onClick="check_width_is_null_or_not(<? echo $i;?>)" value="<? echo $data[8]; ?>"   <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="totalcons_<? echo $i;?>"  name="totalcons_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" readonly value="<? echo $data[9]; ?>">
                                    </td>
                                </tr>
                            
                            <?
						}
					}
					
					?>
                </tbody>
                </table>
            </form>
        </fieldset>
   </div>
   <?
	}
}
?>
<div align="center" style="width:100%;" >
<fieldset>
                <table width="810" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td> 
                    </tr>
                </table>
                </fieldset>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="open_color_list_view")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
extract($_REQUEST);
?>
<script>
function js_set_value_color()
{
	var rowCount = $('#tbl_color_details tr').length-1;
	var color_breck_down="";
	for(var i=1; i<=rowCount; i++)
	{
		/*if (form_validation('concolor_'+i,'Contrast Color')==false)
		{
			return;
		}*/
		    var concolor=$('#concolor_'+i).val()
			if(concolor=='')
			{
				 concolor=$('#gmtscolor_'+i).val();
			}
		if(color_breck_down=="")
		{
			//color_breck_down=$('#gmtscolorid_'+i).val()+'_'+$('#gmtscolor_'+i).val()+'_'+$('#concolor_'+i).val();
			color_breck_down=$('#gmtscolorid_'+i).val()+'_'+$('#gmtscolor_'+i).val()+'_'+concolor;
		}
		else
		{
			//color_breck_down+="__"+$('#gmtscolorid_'+i).val()+'_'+$('#gmtscolor_'+i).val()+'_'+$('#concolor_'+i).val();
			color_breck_down+="__"+$('#gmtscolorid_'+i).val()+'_'+$('#gmtscolor_'+i).val()+'_'+concolor;
		}
	}
	document.getElementById('color_breck_down').value=color_breck_down;
	parent.emailwindow.hide();
}
function color_select_popup(buyer_name,texbox_id)
{
	//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
	//alert(texbox_id)
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'pre_cost_entry_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
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
</script>
</head>
<body>
       <div id="color_details"  align="center">            
    	<fieldset>
        	<form id="contrastcolor_1" autocomplete="off">
            <input type="hidden" id="color_breck_down" />     
            <input type="hidden" id="item_id" />        	
            <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_color_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="200">Gmts Color</th><th>Contrast Color</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					/*$tot_set_qnty=0;
					$data_array=explode("__",$color_breck_down);
					
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							$tot_set_qnty=$tot_set_qnty+$data[1];
							?>
                            	<tr id="color_1" align="center">
                                    <td>
                                    <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $data[0]; ?>"  readonly/> 
									<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:150px"  class="text_boxes"   value="<? echo $data[1]; ?>"  readonly/> 
                                    
                                    </td>
                                    <td>
                                    <input type="text" id="concolor_<? echo $i;?>"   name="concolor_<? echo $i;?>" style="width:130px"  class="text_boxes"   value="<? echo $data[2]; ?>"  /> 
                                    </td>
                                    
                                </tr>
                            
                            <?
							 
						}
					}*/
					
					//else
					//{
						$arr_bo_app=array();
						$sql_bo_app=sql_select("select a.is_approved,b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$txt_job_no' and a.booking_type=1 and a.is_short=2 and a.is_approved=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.pre_cost_fabric_cost_dtls_id,a.is_approved");
						foreach($sql_bo_app as $row_bo_app)
						{
						  $arr_bo_app[$row_bo_app[csf('pre_cost_fabric_cost_dtls_id')]]	=$row_bo_app[csf('is_approved')];
						}
						if($precostapproved==1 || $arr_bo_app[$pre_cost_fabric_cost_dtls_id])
						{
							$disabled=1;
						}
						else
						{
							$disabled=0;
						}
						$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=23  and status_active=1 and is_deleted=0");
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
						$data_array_color=explode("__",$color_breck_down);
						//Mysql
					/*$data_array=sql_select("select a.color_number_id from  wo_po_color_size_breakdown a, wo_po_break_down b where a.job_no_mst=b.job_no_mst and a.po_break_down_id=b.id and a.job_no_mst='$txt_job_no' and a.item_number_id='$cbogmtsitem' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.color_number_id order by b.id");*/
					//Oracle
					$data_array=sql_select("select a.color_number_id from  wo_po_color_size_breakdown a, wo_po_break_down b where a.job_no_mst=b.job_no_mst and a.po_break_down_id=b.id and a.job_no_mst='$txt_job_no' and a.item_number_id='$cbogmtsitem' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.color_number_id order by a.color_number_id");
					
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								
								$data=explode('_',$data_array_color[$i]);
								//print_r($data);
								$i++;
		
						?>
						<tr id="color_1" align="center">
									   <td>
										<input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $row[csf('color_number_id')]; ?>"  readonly/> 
										<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:150px"  class="text_boxes"   value="<? echo $color_library[$row[csf('color_number_id')]]; ?>"  readonly/> 
										</td>
										 <td>
										<input type="text" id="concolor_<? echo $i;?>" name="concolor_<? echo $i;?> " style="width:130px" class="text_boxes" value="<? echo $data[2]; ?>" <? echo $readonly." ".$onClick." ".$plachoder?> <? if($disabled==1){ echo "disabled";} else{ echo "";} ?> /> 
										 </td>
									</tr>
						<? 
							}
						}
					//} 
					?>
                </tbody>
                </table>
                <table width="350" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_color()"/>

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
if($buyer_name=="" || $buyer_name=="")
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
if($action=="get_yarn_rate")
{
	$data=explode("_",$data);
	if($db_type==0)
	{
		$effective_date=change_date_format($data[5],'yyyy-mm-dd','-');
		$sql="select  rate from lib_yarn_rate where supplier_id=$data[4] and yarn_count=$data[0] and composition=$data[1] and  percent=$data[2] and yarn_type=$data[3] and effective_date='$effective_date' and status_active=1 and is_deleted=0  order by id desc limit 1";

	}
	if($db_type==2)
	{
		$effective_date=change_date_format($data[5],'yyyy-mm-dd','-',1);
		 $sql="select  rate from lib_yarn_rate where supplier_id=$data[4] and yarn_count=$data[0] and composition=$data[1] and  percent=$data[2] and yarn_type=$data[3] and effective_date='$effective_date' and status_active=1 and is_deleted=0 and rownum <=1 order by id desc";

	}
	$sql_data=sql_select($sql);
	//print_r($sql_data);
    echo trim($sql_data[0][csf('rate')]);
}
if($action=="conversion_chart_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(id,rate)
{
	//var data=data.split("_");
	document.getElementById('charge_id').value=id;
	document.getElementById('charge_value').value=rate;
	parent.emailwindow.hide();

}
function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="charge_id" name="charge_id" />
<input type="hidden" id="charge_value" name="charge_value" />



<?
if($cbotypeconversion==1)
{
	 $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 //$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	 $arr=array (0=>$company_arr,1=>$body_part,5=>$unit_of_measurement,7=>$row_status);
	// echo  create_list_view ( "list_view", "Company Name,Body Part,Construction & Composition,GSM,Yarn Description,UOM,In-House Rate,Status", "150,120,180,60,150,70,100,60","980","220",1, "select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,body_part,0,0,0,uom_id,0,status_active", $arr , "comapny_id,body_part,const_comp,gsm,yarn_description,uom_id,in_house_rate,status_active", "../sub_contract_bill/requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,0,0,2,0' ) ;
	 ?>
     <table width="963" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="150">Company Name</th>
     <th width="120">Body Part</th>
     <th width="180">Construction & Composition</th>
     <th width="60">GSM</th>
     <th width="150">Yarn Description</th>
     <th width="70">UOM</th>
     <th width="100">In-House Rate</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:980; overflow:scroll-y; max-height:300px">
     <table width="963" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="150"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="120"><? echo $body_part[$row[csf("body_part")]]; ?></td>
     <td width="180"><? echo $row[csf("const_comp")]; ?></td>
     <td width="60"><? echo $row[csf("gsm")]; ?></td>
     <td width="150"><? echo $row[csf("yarn_description")]; ?></td>
     <td width="70"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="100"><? echo $row[csf("in_house_rate")]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
	 toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
     
     <?
}
else
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
	//echo "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Status", "100,150,70,70,70,80,60,80,60,50","900","250",1, "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,0' );
	?>
    <table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="100">Company Name</th>
     <th width="150">Const. Compo.</th>
     <th width="70">Process Type</th>
     <th width="70">Process Name</th>
     <th width="70">Color</th>
     <th width="80">Width/Dia type</th>
     
     <th width="60">In House Rate</th>
     <th width="80">UOM</th>
     <th width="60">Rate type</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:917; overflow:scroll-y; max-height:300px">
     <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="150"><? echo $row[csf("const_comp")]; ?></td>
     <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
      <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
     <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
     <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
     
     <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
     <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
	 toggle( "tr_"+"<? echo $coversionchargelibraryid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
    <?
}
?>
</form>
</div>
</body>
</html>
<?
	
}

if ($action=="set_conversion_charge")
{
	//extract($_REQUEST);
	$rate=return_field_value("rate", "lib_cost_component", "cost_component_name=$data");
	echo $rate; die; 
}

if ($action=="set_conversion_qnty")
{
	$data=explode("_",$data);
	
	//=============================
	/*$txt_job_no="";
	$cbogmtsitem="";
	$lib_yarn_count_deter_id=0;
	$avg_cons_yarn=0;
	$sql_pre_cost_fabric_cost=sql_select("select job_no,	item_number_id, lib_yarn_count_deter_id, avg_cons, avg_cons_yarn from wo_pre_cost_fabric_cost_dtls where id=$data[0]");
	foreach($sql_pre_cost_fabric_cost as $row_pre_cost_fabric_cost)
	{
	  $txt_job_no=$row_pre_cost_fabric_cost[csf('job_no')];
	  $cbogmtsitem=$row_pre_cost_fabric_cost[csf('item_number_id')];
	  $lib_yarn_count_deter_id=$row_pre_cost_fabric_cost[csf('lib_yarn_count_deter_id')];
	  $avg_cons_yarn=$row_pre_cost_fabric_cost[csf('avg_cons_yarn')];
	}
	
	$po_color_size_qty=array();
	$data_array_po=sql_select("select b.id, b.po_number,b.po_quantity,b.shipment_date,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and b.job_no_mst='$txt_job_no' and c.item_number_id='$cbogmtsitem'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,c.size_number_id order by b.id,color_size_table_id"); 
	foreach($data_array_po as $data_array_po_row)
	{
		$po_color_size_qty[$data_array_po_row[csf('id')]][$data_array_po_row[csf('color_number_id')]][$data_array_po_row[csf('size_number_id')]]=$data_array_po_row[csf('plan_cut_qnty')];
	}
	
	

    $plancutqty=0;
	$wo_pre_cos_fab_co_avg_con_dtls_data_arr=array();
	$wo_pre_cos_fab_co_avg_con_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,dia_width,item_size,cons,process_loss_percent,requirment,pcs,color_size_table_id  from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=$data[0] order by po_break_down_id,color_size_table_id");
	foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $wo_pre_cos_fab_co_avg_con_dtls_data_row)
	{
		if($wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('cons')]>0)
		{
			$plancutqty+=$po_color_size_qty[$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('po_break_down_id')]][$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('color_number_id')]][$wo_pre_cos_fab_co_avg_con_dtls_data_row[csf('gmts_sizes')]];
			
			
		}
	}
	$processloss=return_field_value("process_loss", "conversion_process_loss", "mst_id=$lib_yarn_count_deter_id and process_id=$data[1]");
	$avg_cons=0;
	foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $wo_pre_cos_fab_co_avg_con_dtls_data_row1)
	{
		if($wo_pre_cos_fab_co_avg_con_dtls_data_row1[csf('cons')]>0)
		{
			$pcsgmts=$po_color_size_qty[$wo_pre_cos_fab_co_avg_con_dtls_data_row1[csf('po_break_down_id')]][$wo_pre_cos_fab_co_avg_con_dtls_data_row1[csf('color_number_id')]][$wo_pre_cos_fab_co_avg_con_dtls_data_row1[csf('gmts_sizes')]];
			$plan_cut_percent=($pcsgmts/$plancutqty)*100;
			$cons=$wo_pre_cos_fab_co_avg_con_dtls_data_row1[csf('requirment')];
			$processloss_precent=($cons*$processloss)/100;
			$avg_cons_percent=(($cons-$processloss_precent)*$plan_cut_percent)/100;
			$avg_cons+=$avg_cons_percent;
		}
		
	}
	
	echo $avg_cons."_".$avg_cons_yarn."_".$processloss; die; */

	
	//============================
	$lib_yarn_count_deter_id=return_field_value("lib_yarn_count_deter_id", "wo_pre_cost_fabric_cost_dtls", "id=$data[0]");
	$processloss=return_field_value("process_loss", "conversion_process_loss", "mst_id=$lib_yarn_count_deter_id and process_id=$data[1]");
	$avg_cons=return_field_value("avg_cons", "wo_pre_cost_fabric_cost_dtls", "id=$data[0]");
	$avg_cons=$avg_cons-($avg_cons*$processloss)/100;
	$avg_cons_yarn=return_field_value("avg_cons_yarn", "wo_pre_cost_fabric_cost_dtls", "id=$data[0]");
	$avg_cons_yarn=$avg_cons_yarn-($avg_cons_yarn*$processloss)/100;
	echo $avg_cons."_".$avg_cons_yarn."_".$processloss; die; 
}

if($action=="conversion_color_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	/*function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}*/
	
	
	function set_conversion_charge_unit(i,conversion_from_chart)
	{
		if(conversion_from_chart==1)
		{
			document.getElementById('unitcharge_'+i).readOnly=true
		    set_conversion_charge_unit_pop_up(i)
		}
		else
		{
		document.getElementById('unitcharge_'+i).readOnly=false	
		}
				
				
	}
function set_conversion_charge_unit_pop_up(i)
{
    var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbotypeconversion=document.getElementById('cbotypeconversion').value
	var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
	var coversionchargelibraryid=document.getElementById('coversionchargelibraryid_'+i).value
	if(cbo_company_name==0)
	{
	alert("Select Company");
	return;
	}
	if(cbotypeconversion==0)
	{
	alert("Select Process");
	return;
	}
	if(txt_exchange_rate==0 || txt_exchange_rate=="")
	{
	alert("Select Exchange Rate");
	return;
	}
	
	else
	{
	var page_link='pre_cost_entry_controller.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
		{
			var charge_id=this.contentDoc.getElementById("charge_id");
			var charge_value=this.contentDoc.getElementById("charge_value");
			
			document.getElementById('coversionchargelibraryid_'+i).value=charge_id.value;
			document.getElementById('unitcharge_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
			claculate_avg()
			
		}
	}
}
	
	
	
	function js_set_value_color()
	{
		var rowCount = $('#tbl_color_details tr').length-3;
		var color_breck_down="";
		var coversionchargelibraryid_baeck_down="";
		for(var i=1; i<=rowCount; i++)
		{
			/*if (form_validation('unitcharge_'+i,'Charge Unit')==false)
			{
				return;
			}*/
			var unitcharge = $('#unitcharge_'+i).val();
				if(trim(unitcharge) =='')
				{
					unitcharge=0;
				}
				
			var coversionchargelibraryid= $('#coversionchargelibraryid_'+i).val();
				if(coversionchargelibraryid=='')
				{
					coversionchargelibraryid=0;
				}
			
			if(color_breck_down=="")
			{
				///alert($('#unitcharge_'+i).val())
				color_breck_down=$('#gmtscolorid_'+i).val()+'_'+unitcharge+'_'+coversionchargelibraryid;
			}
			else
			{
				color_breck_down+="__"+$('#gmtscolorid_'+i).val()+'_'+unitcharge+'_'+coversionchargelibraryid;
			}
			
			if(coversionchargelibraryid_baeck_down=="")
			{
				coversionchargelibraryid_baeck_down=$('#coversionchargelibraryid_'+i).val()
			}
			else
			{
				coversionchargelibraryid_baeck_down+="_"+$('#coversionchargelibraryid_'+i).val();
			}
		}
		//alert()
		document.getElementById('color_breck_down').value=color_breck_down;
		document.getElementById('chargelibid_breck_down').value=coversionchargelibraryid_baeck_down;
		
		parent.emailwindow.hide();
	}
	
function claculate_avg()
{
	var significant_row=0;
	var total_value=0;
	var rowCount = $('#tbl_color_details tr').length-3;
	for(var i=1; i<=rowCount; i++)
	{
		if(document.getElementById('unitcharge_'+i).value =='' || document.getElementById('unitcharge_'+i).value ==0)
		{
			continue;
		}
		else
		{
			significant_row=significant_row+1;
			total_value+=(document.getElementById('unitcharge_'+i).value)*1
		}
		
	}
	var avg_total_value=total_value/significant_row;
	document.getElementById('total_value').value=total_value
	document.getElementById('avg_total_value').value=number_format_common(avg_total_value, 1, 0);
	
	/*var avg_cons=(document.getElementById('cons_sum').value*1)/significant_row;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/significant_row;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/significant_row;
	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 1, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 1, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 1, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;*/
}


	
    </script>

</head>

<body>

<form id="contrastcolor_1" autocomplete="off">
            <input type="hidden" id="color_breck_down" />
            <input type="hidden" id="chargelibid_breck_down" />   
             <input type="hidden" id="cbo_company_name" value="<? echo $cbo_company_name;?>" /> 
              <input type="hidden" id="cbotypeconversion" value="<? echo $cbotypeconversion;?>" /> 
               <input type="hidden" id="txt_exchange_rate" value="<? echo $txt_exchange_rate;?>" /> 
               <input type="hidden" id="cbo_currercy" value="<? echo $cbo_currercy;?>" />    
            <table width="300" cellspacing="0" class="rpt_table" border="0" id="tbl_color_details" rules="all">
<thead>
    <tr>
        <th width="30">Sl</th><th width="120">Color</th><th>Charge/Unit</th>
    </tr>
    </thead>
    <tbody>
    <?
	$data_array_update=array();
    $data_array=explode("__",$colorbreakdown);
	if($data_array[0]=="")
	{
		$data_array=array();
	}
	if ( count($data_array)>0)
	{
		$i=0;
		$total_value=0;
		foreach( $data_array as $row )
		{
			$i++;
			$data=explode('_',$row);
			$data_array_update[$data[0]][unitcharge]=$data[1];
			$data_array_update[$data[0]][coversionchargelibraryid]=$data[2];
		}
	}
	
	
	
	/*else
	{*/
	if($cbocosthead !=0)
	{
		$color_size_sensitive=return_field_value("color_size_sensitive", "wo_pre_cost_fabric_cost_dtls", "id=$cbocosthead");
		if($color_size_sensitive==1)
		{
			$data_array=sql_select("select distinct a.color_number_id from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='$cbocosthead' and a.status_active=1 and  a.is_deleted=0 ");
		}
		if($color_size_sensitive==3)
		{
			//echo "select distinct contrast_color_id from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='$cbocosthead'";
		 	$data_array=sql_select("select  contrast_color_id as color_number_id from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='$cbocosthead'");

		}
		if($color_size_sensitive==0)
		{
			$data_array=sql_select("select  color as color_number_id  from wo_pre_cost_fabric_cost_dtls where id='$cbocosthead'");
		}
	$i=0;
	$avg_num=0;
	$total_value=0;
	foreach( $data_array as $row )
	{
		$unitcharge=0;
		if($data_array_update[$row[csf('color_number_id')]][unitcharge]=="")
		{
		$unitcharge=$conversion_qnty;	
		}
		else
		{
		$unitcharge=$data_array_update[$row[csf('color_number_id')]][unitcharge];
		}
		
		
		$total_value+=$unitcharge;
		
		if($unitcharge > 0)
		{
		$avg_num+=1;
		}
		
		$i++;
	?>
		<tr>
			<td><? echo $i; ?></td>
			<td>
            <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $row[csf('color_number_id')]; ?>"  readonly/> 
			<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:120px"  class="text_boxes"   value="<? echo $color_library[$row[csf('color_number_id')]]; ?>"  readonly/>
            </td>
			<td><input type="text" id="unitcharge_<? echo $i;?>"   name="unitcharge_<? echo $i;?>" value="<? echo $unitcharge; ?>"  onChange="claculate_avg()" onClick="set_conversion_charge_unit(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" style="width:150px"  class="text_boxes_numeric"/>
            <input type="hidden" id="coversionchargelibraryid_<? echo $i;?>"   name="coversionchargelibraryid_<? echo $i;?>" value="<? echo $data_array_update[$row[csf('color_number_id')]][coversionchargelibraryid]; ?>"  readonly/>
            </td>
		</tr>
    <?
	}
	}
	else
	{
		$color_array=array();
		$color_size_sensitive_array=sql_select("select  id,color_size_sensitive  from wo_pre_cost_fabric_cost_dtls where job_no='".$job_no."' and fabric_source=1");
		foreach( $color_size_sensitive_array as $color_size_sensitive_row )
		{
			if($color_size_sensitive_row[csf('color_size_sensitive')]==1)
			{
				$data_array1=sql_select("select distinct a.color_number_id as color_number_id  from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."' and a.status_active=1 and  a.is_deleted=0 ");
				//echo "select distinct a.color_number_id as color_number_id  from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."' and a.status_active=1 and  a.is_deleted=0 ";
				foreach( $data_array1 as $row1 )
				{
					if (array_key_exists($row1[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row1[csf('color_number_id')]]=$row1[csf('color_number_id')];
					}
				}
				
				
			}
			if($color_size_sensitive_row[csf('color_size_sensitive')]==3)
			{
				 
				$data_array2=sql_select("select  contrast_color_id as color_number_id  from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."'");
				//echo "select  contrast_color_id as color_number_id  from wo_pre_cos_fab_co_color_dtls where id='".$color_size_sensitive_row[csf('id')]."'";
				foreach( $data_array2 as $row2 )
				{
					if (array_key_exists($row2[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row2[csf('color_number_id')]]=$row2[csf('color_number_id')];
					}
				}
	
			}
			if($color_size_sensitive_row[csf('color_size_sensitive')]==0)
			{
				$data_array3=sql_select("select  color as color_number_id  from wo_pre_cost_fabric_cost_dtls where id='".$color_size_sensitive_row[csf('id')]."'");
				foreach( $data_array3 as $row3 )
				{
					if (array_key_exists($row3[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row3[csf('color_number_id')]]=$row3[csf('color_number_id')];
					}
				}
			}
		}
		$i=0;
		$avg_num=0;
		$total_value=0;
		foreach( $color_array as $key=>$value )
		{
		$unitcharge=0;
		if($data_array_update[$value][unitcharge]=="")
		{
		$unitcharge=$conversion_qnty;	
		}
		else
		{
		$unitcharge=$data_array_update[$value][unitcharge];
		}
		$total_value+=$unitcharge;
		
		if($unitcharge > 0)
		{
		$avg_num+=1;
		}
		
		$i++;
		?>
        <tr>
			<td><? echo $i; ?></td>
			<td> <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $value; ?>"  readonly/> 
			<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:120px"  class="text_boxes"   value="<? echo $color_library[$value]; ?>"  readonly/></td>
			<td><input type="text" id="unitcharge_<? echo $i;?>"   name="unitcharge_<? echo $i;?>" style="width:150px" onChange="claculate_avg()" onClick="set_conversion_charge_unit(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" value="<? echo $unitcharge; ?>"  class="text_boxes_numeric"/>
            <input type="hidden" id="coversionchargelibraryid_<? echo $i;?>"   name="coversionchargelibraryid_<? echo $i;?>" value="<? echo $data_array_update[$row[csf('color_number_id')]][coversionchargelibraryid]; ?>"  readonly/>
            </td>
		</tr>
        
        <?
		}
	}
	//}
	?>
</tbody>
<tfoot>
<tr>
			<th><? echo $avg_num;  ?></th>
			<th> 
            Total
			</th>
			<th><input type="text" id="total_value"   name="total_value" style="width:150px" value="<? echo $total_value; ?>"  class="text_boxes_numeric"/></th>
		</tr>
        <tr>
			<th></th>
			<th> 
            Avg Total
			</th>
			<th><input type="text" id="avg_total_value"   name="avg_total_value" style="width:150px" value="<? echo def_number_format(($total_value/$avg_num),$cbo_currercy,0); ?>"  class="text_boxes_numeric"/></th>
		</tr>
</tfoot>
</table>
 <table width="300" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_color()"/>

                        </td> 
                    </tr>
                </table>
</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    
    <?
}

if($action=="conversion_color_popup_old")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	/*function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}*/
	
	
	function set_conversion_charge_unit(i,conversion_from_chart)
	{
		if(conversion_from_chart==1)
		{
			document.getElementById('unitcharge_'+i).readOnly=true
		    set_conversion_charge_unit_pop_up(i)
		}
		else
		{
		document.getElementById('unitcharge_'+i).readOnly=false	
		}
				
				
	}
function set_conversion_charge_unit_pop_up(i)
{
    var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbotypeconversion=document.getElementById('cbotypeconversion').value
	var txt_exchange_rate=document.getElementById('txt_exchange_rate').value
	var coversionchargelibraryid=document.getElementById('coversionchargelibraryid_'+i).value
	if(cbo_company_name==0)
	{
	alert("Select Company");
	return;
	}
	if(cbotypeconversion==0)
	{
	alert("Select Process");
	return;
	}
	if(txt_exchange_rate==0 || txt_exchange_rate=="")
	{
	alert("Select Exchange Rate");
	return;
	}
	
	else
	{
	var page_link='pre_cost_entry_controller.php?action=conversion_chart_popup&cbo_company_name='+cbo_company_name+'&cbotypeconversion='+cbotypeconversion+'&coversionchargelibraryid='+coversionchargelibraryid;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Conversion Chart', 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var charge_id=this.contentDoc.getElementById("charge_id");
		var charge_value=this.contentDoc.getElementById("charge_value");
		document.getElementById('coversionchargelibraryid_'+i).value=charge_id.value;
		document.getElementById('unitcharge_'+i).value=number_format_common(charge_value.value/txt_exchange_rate,5,0,document.getElementById('cbo_currercy').value);
		claculate_avg()
		
	}
	}
}
	
	
	
	function js_set_value_color()
	{
		var rowCount = $('#tbl_color_details tr').length-3;
		var color_breck_down="";
		var coversionchargelibraryid_baeck_down="";
		for(var i=1; i<=rowCount; i++)
		{
			/*if (form_validation('unitcharge_'+i,'Charge Unit')==false)
			{
				return;
			}*/
			var unitcharge = $('#unitcharge_'+i).val();
				if(trim(unitcharge) =='')
				{
					unitcharge=0;
				}
			var coversionchargelibraryid= $('#coversionchargelibraryid_'+i).val();
				if(coversionchargelibraryid=='')
				{
					coversionchargelibraryid=0;
				}
			
			if(color_breck_down=="")
			{
				///alert($('#unitcharge_'+i).val())
				color_breck_down=$('#gmtscolorid_'+i).val()+'_'+unitcharge+'_'+coversionchargelibraryid;
			}
			else
			{
				color_breck_down+="__"+$('#gmtscolorid_'+i).val()+'_'+unitcharge+'_'+coversionchargelibraryid;
			}
			
			if(coversionchargelibraryid_baeck_down=="")
			{
				coversionchargelibraryid_baeck_down=$('#coversionchargelibraryid_'+i).val()
			}
			else
			{
				coversionchargelibraryid_baeck_down+="_"+$('#coversionchargelibraryid_'+i).val();
			}
		}
		//alert()
		document.getElementById('color_breck_down').value=color_breck_down;
		document.getElementById('chargelibid_breck_down').value=coversionchargelibraryid_baeck_down;
		parent.emailwindow.hide();
	}
	
function claculate_avg()
{
	var significant_row=0;
	var total_value=0;
	var rowCount = $('#tbl_color_details tr').length-3;
	for(var i=1; i<=rowCount; i++)
	{
		if(document.getElementById('unitcharge_'+i).value =='' || document.getElementById('unitcharge_'+i).value ==0)
		{
			continue;
		}
		else
		{
			significant_row=significant_row+1;
			total_value+=(document.getElementById('unitcharge_'+i).value)*1
		}
	}
	var avg_total_value=total_value/significant_row;
	document.getElementById('total_value').value=total_value
	document.getElementById('avg_total_value').value=number_format_common(avg_total_value, 1, 0);
	/*var avg_cons=(document.getElementById('cons_sum').value*1)/significant_row;
	var calculated_procloss=(document.getElementById('processloss_sum').value*1)/significant_row;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/significant_row;
	document.getElementById('calculated_cons').value=number_format_common(calculated_cons, 1, 0);
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 1, 0);
	document.getElementById('calculated_procloss').value=number_format_common(calculated_procloss, 1, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;*/
}
</script>
</head>
<body>

<form id="contrastcolor_1" autocomplete="off">
<input type="hidden" id="color_breck_down" />
<input type="hidden" id="chargelibid_breck_down" />   
<input type="hidden" id="cbo_company_name" value="<? echo $cbo_company_name;?>" /> 
<input type="hidden" id="cbotypeconversion" value="<? echo $cbotypeconversion;?>" /> 
<input type="hidden" id="txt_exchange_rate" value="<? echo $txt_exchange_rate;?>" /> 
<input type="hidden" id="cbo_currercy" value="<? echo $cbo_currercy;?>" />    
<table width="300" cellspacing="0" class="rpt_table" border="0" id="tbl_color_details" rules="all">
<thead>
    <tr>
        <th width="30">Sl</th><th width="120">Color</th><th>Charge/Unit</th>
    </tr>
    </thead>
    <tbody>
    <?
	$data_array=explode("__",$colorbreakdown);
	if($data_array[0]=="")
	{
		$data_array=array();
	}
	if ( count($data_array)>0)
	{
		$i=0;
		$total_value=0;
		foreach( $data_array as $row )
		{
			$i++;
			$data=explode('_',$row);
			$total_value+=$data[1];
		?>
		<tr>
			<td><? echo $i; ?></td>
			<td>
            <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $data[0]; ?>"  readonly/> 
			<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:120px"  class="text_boxes"   value="<? echo $color_library[$data[0]]; ?>"  readonly/>
            </td>
			<td><input type="text" id="unitcharge_<? echo $i;?>"   name="unitcharge_<? echo $i;?>" value="<? echo $data[1]; ?>"  onChange="claculate_avg()" onClick="set_conversion_charge_unit(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" style="width:150px"  class="text_boxes_numeric"/> 
            <input type="hidden" id="coversionchargelibraryid_<? echo $i;?>"   name="coversionchargelibraryid_<? echo $i;?>" value="<? echo $data[2]; ?>"  readonly/>
            </td>
		</tr>
        <?
		}
	}
	else
	{
	if($cbocosthead !=0)
	{
		$color_size_sensitive=return_field_value("color_size_sensitive", "wo_pre_cost_fabric_cost_dtls", "id=$cbocosthead");
		if($color_size_sensitive==1)
		{
			$data_array=sql_select("select distinct a.color_number_id from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='$cbocosthead' and a.status_active=1 and  a.is_deleted=0 ");
		}
		if($color_size_sensitive==3)
		{
			//echo "select distinct contrast_color_id from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='$cbocosthead'";
		 	$data_array=sql_select("select  contrast_color_id as color_number_id from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='$cbocosthead'");

		}
		if($color_size_sensitive==0)
		{
			$data_array=sql_select("select  color as color_number_id  from wo_pre_cost_fabric_cost_dtls where id='$cbocosthead'");
		}
	$i=0;
	$total_value=0;
	foreach( $data_array as $row )
	{
		$i++;
		$total_value+=$conversion_qnty;
	?>
		<tr>
			<td>
			<? 
			echo $i; 
			?>
            </td>
			<td>
            <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $row[csf('color_number_id')]; ?>"  readonly/> 
			<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:120px"  class="text_boxes"   value="<? echo $color_library[$row[csf('color_number_id')]]; ?>"  readonly/>
            </td>
			<td>
            <input type="text" id="unitcharge_<? echo $i;?>"   name="unitcharge_<? echo $i;?>" value="<? echo $conversion_qnty; ?>"  onChange="claculate_avg()" onClick="set_conversion_charge_unit(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" style="width:150px"  class="text_boxes_numeric"/>
            <input type="hidden" id="coversionchargelibraryid_<? echo $i;?>"   name="coversionchargelibraryid_<? echo $i;?>" value=""  readonly/>
            </td>
		</tr>
    <?
	}
	}
	else
	{
		$color_array=array();
		$color_size_sensitive_array=sql_select("select  id,color_size_sensitive  from wo_pre_cost_fabric_cost_dtls where job_no='".$job_no."' and fabric_source=1");
		foreach( $color_size_sensitive_array as $color_size_sensitive_row )
		{
			if($color_size_sensitive_row[csf('color_size_sensitive')]==1)
			{
				$data_array1=sql_select("select distinct a.color_number_id as color_number_id  from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."' and a.status_active=1 and  a.is_deleted=0 ");
				//echo "select distinct a.color_number_id as color_number_id  from  wo_po_color_size_breakdown a,wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."' and a.status_active=1 and  a.is_deleted=0 ";
				foreach( $data_array1 as $row1 )
				{
					if (array_key_exists($row1[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row1[csf('color_number_id')]]=$row1[csf('color_number_id')];
					}
				}
				
				
			}
			if($color_size_sensitive_row[csf('color_size_sensitive')]==3)
			{
				 
				$data_array2=sql_select("select  contrast_color_id as color_number_id  from wo_pre_cos_fab_co_color_dtls where pre_cost_fabric_cost_dtls_id='".$color_size_sensitive_row[csf('id')]."'");
				//echo "select  contrast_color_id as color_number_id  from wo_pre_cos_fab_co_color_dtls where id='".$color_size_sensitive_row[csf('id')]."'";
				foreach( $data_array2 as $row2 )
				{
					if (array_key_exists($row2[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row2[csf('color_number_id')]]=$row2[csf('color_number_id')];
					}
				}
	
			}
			if($color_size_sensitive_row[csf('color_size_sensitive')]==0)
			{
				$data_array3=sql_select("select  color as color_number_id  from wo_pre_cost_fabric_cost_dtls where id='".$color_size_sensitive_row[csf('id')]."'");
				foreach( $data_array3 as $row3 )
				{
					if (array_key_exists($row3[csf('color_number_id')], $color_array)) 
					{
						continue;
					}
					else
					{
						$color_array[$row3[csf('color_number_id')]]=$row3[csf('color_number_id')];
					}
				}
			}
		}
		$i=0;
		$total_value=0;
		foreach( $color_array as $key=>$value )
		{
			$i++;
			$total_value+=$conversion_qnty;
		?>
        <tr>
			<td><? echo $i; ?></td>
			<td> <input type="hidden" id="gmtscolorid_<? echo $i;?>"   name="gmtscolorid_<? echo $i;?>" style="width:50px"  class="text_boxes"   value="<? echo $value; ?>"  readonly/> 
			<input type="text" id="gmtscolor_<? echo $i;?>"   name="gmtscolor_<? echo $i;?>" style="width:120px"  class="text_boxes"   value="<? echo $color_library[$value]; ?>"  readonly/></td>
			<td><input type="text" id="unitcharge_<? echo $i;?>"   name="unitcharge_<? echo $i;?>" style="width:150px" onChange="claculate_avg()" onClick="set_conversion_charge_unit(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" value="<? echo $conversion_qnty; ?>"  class="text_boxes_numeric"/>
            <input type="hidden" id="coversionchargelibraryid_<? echo $i;?>"   name="coversionchargelibraryid_<? echo $i;?>" value=""  readonly/>
            </td>
		</tr>
        
        <?
		}
	}
	}
	?>
</tbody>
<tfoot>
<tr>
			<th></th>
			<th> 
            Total
			</th>
			<th><input type="text" id="total_value"   name="total_value" style="width:150px" value="<? echo $total_value; ?>"  class="text_boxes_numeric"/></th>
		</tr>
        <tr>
			<th></th>
			<th> 
            Avg Total
			</th>
			<th><input type="text" id="avg_total_value"   name="avg_total_value" style="width:150px" value="<? echo def_number_format(($total_value/$i),$cbo_currercy,0); ?>"  class="text_boxes_numeric"/></th>
		</tr>
</tfoot>
</table>
 <table width="300" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_color()"/>

                        </td> 
                    </tr>
                </table>
</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    
    <?
}

/*if($action=="check_is_booking_insert")
{
	//echo $data;
	$fabric_booking_no_sql=sql_select("select distinct booking_no 
							FROM 
						    wo_booking_dtls
						    WHERE 
						    pre_cost_fabric_cost_dtls_id =$data 
						    and  	booking_type=1 and  status_active=1 and  is_deleted=0");
	
							
	$fabric_booking_no="";
	foreach($fabric_booking_no_sql as $fabric_booking_no_sql_row)
	{
		$fabric_booking_no.=$fabric_booking_no_sql_row[booking_no].",";
	}
		
	$service_booking_no_sql=sql_select("select distinct booking_no 
						FROM 
						wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls b
						WHERE 
						a.pre_cost_fabric_cost_dtls_id =b.id and
						b.fabric_description =$data and
						a.booking_type=3 and  a.status_active=1 and  a.is_deleted=0 and  b.status_active=1 and  b.is_deleted=0");
	$service_booking_no="";
	foreach($service_booking_no_sql as $service_booking_no_sql_row)
	{
		$service_booking_no.=$service_booking_no_sql_row[booking_no].",";
	}
	
	echo  $fabric_booking_no."_".$service_booking_no;
	
}*/

if($action=="delete_row_fabric_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_fabric_cost_dtls where  id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cos_fab_co_avg_con_dtls where  pre_cost_fabric_cost_dtls_id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cos_fab_co_color_dtls where  pre_cost_fabric_cost_dtls_id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cost_fab_yarn_cost_dtls where  fabric_cost_dtls_id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cost_fab_yarnbreakdown where  fabric_cost_dtls_id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cost_fab_conv_cost_dtls where  fabric_description =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}
if($action=="delete_row_yarn_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_fab_yarn_cost_dtls where  id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}

if($action=="conversion_from_chart")
{
	
	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="")
	{
		$conversion_from_chart=2;
	}
	echo trim($conversion_from_chart);

}

if($action=="delete_row_conversion_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_fab_conv_cost_dtls where  id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}

if($action=="delete_row_trim_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	if (is_duplicate_field( "pre_cost_fabric_cost_dtls_id", "wo_booking_dtls", "pre_cost_fabric_cost_dtls_id=$data and booking_type=2 and is_deleted=0 and status_active=1" ) == 1)
	{
			echo "11"; die;
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_trim_cost_dtls where  id =".$data."",0);
	$rID_de1=execute_query( "delete from  wo_pre_cost_trim_co_cons_dtls where  wo_pre_cost_trim_cost_dtls_id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}

if($action=="delete_row_embellishment_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_embe_cost_dtls where  id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}

if($action=="delete_row_wash_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from  wo_pre_cost_embe_cost_dtls where  id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}
if($action=="delete_row_comarcial_cost")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID_de1=execute_query( "delete from   wo_pre_cost_comarci_cost_dtls where  id =".$data."",0);
	if($db_type==0)
		{
			
				if($rID_de1)
				{
					mysql_query("COMMIT");  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					//echo "10**".$new_job_no[0]."**".$rID;
				}
			
		}
		
		if($db_type==2 || $db_type==1 )
		{
				if($rID_de1)
				{
					oci_commit($con);  
					//echo "0**".$new_job_no[0]."**".$rID;
				}
				else
				{
					oci_rollback($con);  
					//echo "10**".$new_job_no[0]."**".$rID;
				}
		}
		disconnect($con);
}

if ($action=="save_update_delet_fabric_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if($db_type==0)
		{
		$field_array="id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty";
		}
		if($db_type==2)
		{
		$field_array="id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty";
		}
		
		$field_array1="id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id,color_number_id, gmts_sizes, dia_width,item_size, cons, process_loss_percent, requirment, pcs,color_size_table_id, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		
		$field_array2="id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id";
		
		$field_array3="id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,inserted_by,insert_date,status_active,is_deleted"; 
		
		$add_comma=0;
		$add_comma_yarn=0;
		$add_comma_color=0;
		
		$id=return_next_id( "id", "wo_pre_cost_fabric_cost_dtls", 1 ) ;
		$id1=return_next_id( "id", "wo_pre_cos_fab_co_avg_con_dtls", 1 ) ;
		$wo_pre_cos_fab_co_color_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_color_dtls", 1 ) ;
		$wo_pre_cost_fab_yarn_cost_dtls_id=return_next_id( "id", " wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;
		$wo_pre_cost_fab_yarnbreakdown_id=return_next_id( "id", "wo_pre_cost_fab_yarnbreakdown", 1 ) ;
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbogmtsitem="cbogmtsitem_".$i;
			$txtbodypart="txtbodypart_".$i;
			$cbofabricnature="cbofabricnature_".$i;
			$cbocolortype="cbocolortype_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
			$construction="construction_".$i;
			$composition="composition_".$i;
			$fabricdescription="fabricdescription_".$i;
			$txtgsmweight="txtgsmweight_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtcolor="txtcolor_".$i;
			$txtconsumption="txtconsumption_".$i;
			$cbofabricsource="cbofabricsource_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtfinishconsumption="txtfinishconsumption_".$i;
			$txtavgprocessloss="txtavgprocessloss_".$i;
			$cbostatus="cbostatus_".$i;
			$consbreckdown="consbreckdown_".$i;
			$msmntbreackdown="msmntbreackdown_".$i;
			$processlossmethod="processlossmethod_".$i;
			$colorbreackdown="colorbreackdown_".$i;
			$yarnbreackdown="yarnbreackdown_".$i;
			$consumptionbasis="consumptionbasis_".$i;
			$markerbreackdown="markerbreackdown_".$i;
            $cbowidthdiatype="cbowidthdiatype_".$i;		
			$avgtxtconsumption="avgtxtconsumption_".$i;
			$avgtxtgsmweight="avgtxtgsmweight_".$i;
			$plancutqty="plancutqty_".$i;
			$jobplancutqty="jobplancutqty_".$i;
			$cons_breckdown=substr(str_replace("'","",$$consbreckdown),0,100);
			//color Id;
			//$cons_break_down="8_18_8_78_0_defult";
			if( str_replace("'","",$$cbocolorsizesensitive)==0)
			{
				if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name");  
					$new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				}
				else
				{
					$color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color); 
				}
			}
			else
			{
				$color_id=0;
			}
			//color Id End
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$construction.",".$$composition.",".$$fabricdescription.",".$$txtgsmweight.",".$$cbocolorsizesensitive.",".$color_id.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$colorbreackdown.",".$$yarnbreackdown.",".$$markerbreackdown.",".$$cbowidthdiatype.",".$$avgtxtconsumption.",".$$avgtxtgsmweight.",".$$plancutqty.",".$$jobplancutqty.")";
//	msmnt break down===============================================================================================	
			$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
			$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
			$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
			for($c=0;$c < count($consbreckdown_array);$c++)
			{
				$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
				$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
				if ($add_comma!=0) $data_array1 .=",";
				if(str_replace("'",'',$$txtbodypart)*1==1)
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				else if(str_replace("'",'',$$txtbodypart)*1==20)
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				else 
				{
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				}
				$id1=$id1+1;
				$add_comma++;
			}
//Msmnt break down end===============================================================================================
//Yarn break down ===================================================================================================
				if(str_replace("'",'',$$cbofabricsource)==1)
				{
					$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					for($c=0;$c < count($yarnbreckdown_array);$c++)
					{
						$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
						if(str_replace("'",'',$$cbofabricnature)==2)

						{
							$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtconsumption)*$yarnbreckdownarr[4])/100),5,"");

						}
						if(str_replace("'",'',$$cbofabricnature)==3)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtgsmweight)*$yarnbreckdownarr[4])/100),5,"");

						}
						if ($add_comma_yarn!=0) 
						{
							$data_array3 .=",";
							$data_array4 .=",";
						}
						$data_array3 .="(".$wo_pre_cost_fab_yarn_cost_dtls_id.",".$id.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
						$data_array4 .="(".$wo_pre_cost_fab_yarnbreakdown_id.",".$id.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
						$wo_pre_cost_fab_yarn_cost_dtls_id=$wo_pre_cost_fab_yarn_cost_dtls_id+1;
						$wo_pre_cost_fab_yarnbreakdown_id=$wo_pre_cost_fab_yarnbreakdown_id+1;
						$add_comma_yarn++;
					}
				}
// Yarn break down end ===============================================================================================
// Color break down ==================================================================================================	
			if(str_replace("'",'',$$colorbreackdown)!="")
			{
				$colorbreckdown_array=explode('__',str_replace("'",'',$$colorbreackdown));
				for($c=0;$c < count($colorbreckdown_array);$c++)
				{
					$colorbreckdownarr=explode('_',$colorbreckdown_array[$c]);
					if (!in_array(str_replace("'","",$colorbreckdownarr[2]),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$colorbreckdownarr[2]), $color_library, "lib_color", "id,color_name");   
						$new_array_color[$color_id]=str_replace("'","",$colorbreckdownarr[2]);
					}
					else 
					{
						$color_id =  array_search(str_replace("'","",$colorbreckdownarr[2]), $new_array_color); 
					}
					if ($add_comma_color!=0) $data_array2 .=",";
					$data_array2 .="(".$wo_pre_cos_fab_co_color_dtls_id.",".$id.",".$update_id.",".$colorbreckdownarr[0].",'".$colorbreckdownarr[1]."','".$color_id."')";
					$wo_pre_cos_fab_co_color_dtls_id=$wo_pre_cos_fab_co_color_dtls_id+1;
					$add_comma_color++;
				}
			}
//  color break down end ===============================================================================================	
			$id=$id+1;
		}// end master for loop
		//print_r($markerbreackdownarr);
		//echo "insert into wo_pre_cost_fabric_cost_dtls (".$field_array.") values ".$data_array;
			//echo "insert into wo_pre_cos_fab_co_avg_con_dtls (".$field_array1.") values ".$data_array1;die;	

		 $rID_in=sql_insert("wo_pre_cost_fabric_cost_dtls",$field_array,$data_array,0,1);
		 $rID_in1=sql_insert("wo_pre_cos_fab_co_avg_con_dtls",$field_array1,$data_array1,1,0); 
		
		if ($data_array2!="")
		{
			$rID_in2=sql_insert("wo_pre_cos_fab_co_color_dtls",$field_array2,$data_array2,1);
		}
		if ($data_array3!="")
		{
			$rID_in3=sql_insert("wo_pre_cost_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
	
			$rID_in4=sql_insert("wo_pre_cost_fab_yarnbreakdown",$field_array3,$data_array4,0);
		}
		//=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array5="id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,pro_woven_grey_fab_req_yds,pro_knit_grey_fab_req_kg,pro_woven_fin_fab_req_yds,pro_knit_fin_fab_req_kg,pur_woven_grey_fab_req_yds,pur_knit_grey_fab_req_kg,pur_woven_fin_fab_req_yds,pur_knit_fin_fab_req_kg,woven_amount,knit_amount";
			$data_array5="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtwoven_fin_sum.",".$txtknit_fin_sum.",".$txtamount_sum.",".$avg.",".$txtwoven_sum_production.",".$txtknit_sum_production.",".$txtwoven_fin_sum_production.",".$txtknit_fin_sum_production.",".$txtwoven_sum_purchase.",".$txtknit_sum_purchase.",".$txtwoven_fin_sum_purchase.",".$txtknit_fin_sum_purchase.",".$txtwoven_amount_sum_purchase.",".$txtkint_amount_sum_purchase.")";
			$rID_id5=sql_insert("wo_pre_cost_sum_dtls",$field_array5,$data_array5,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_woven_fin_req_yds*fab_knit_fin_req_kg*fab_amount*avg*pro_woven_grey_fab_req_yds*pro_knit_grey_fab_req_kg*pro_woven_fin_fab_req_yds*pro_knit_fin_fab_req_kg*pur_woven_grey_fab_req_yds*pur_knit_grey_fab_req_kg*pur_woven_fin_fab_req_yds*pur_knit_fin_fab_req_kg*woven_amount*knit_amount";
			$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtwoven_fin_sum."*".$txtknit_fin_sum."*".$txtamount_sum."*".$avg."*".$txtwoven_sum_production."*".$txtknit_sum_production."*".$txtwoven_fin_sum_production."*".$txtknit_fin_sum_production."*".$txtwoven_sum_purchase."*".$txtknit_sum_purchase."*".$txtwoven_fin_sum_purchase."*".$txtknit_fin_sum_purchase."*".$txtwoven_amount_sum_purchase."*".$txtkint_amount_sum_purchase."";
			$rID_in5=sql_update("wo_pre_cost_sum_dtls",$field_array5,$data_array5,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if ($data_array2!="")
			{
				if($rID_in && $rID_in1 &&  $rID_in2)
				{
					mysql_query("COMMIT");  
					echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
			}
			else
			{
				if($rID_in && $rID_in1 )
				{
					mysql_query("COMMIT");  
					echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if ($data_array2!="")
			{
				if($rID_in && $rID_in1 &&  $rID_in2)
				{
					oci_commit($con);  
					echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
				else
				{
					oci_rollback($con);  
					echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
			}
			else
			{
				if($rID_in && $rID_in1 )
				{
					oci_commit($con);    
					echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
				else
				{
					oci_rollback($con);   
					echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
				}
			}
		}
		disconnect($con);
		die;
	}
// Insert  end ====================================================================================	
// Update here ====================================================================================	
	if($operation==1)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		if($db_type==0)
		{
		$field_array="id, job_no, item_number_id, body_part_id,fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty";
		$field_array_up="job_no*item_number_id*body_part_id*fab_nature_id*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*color_size_sensitive* 	color*avg_cons*fabric_source*rate*amount*avg_finish_cons*avg_process_loss*updated_by*update_date*status_active*is_deleted*company_id*costing_per*consumption_basis*process_loss_method*cons_breack_down*msmnt_break_down*color_break_down*yarn_breack_down*marker_break_down*width_dia_type*avg_cons_yarn*gsm_weight_yarn*plan_cut_qty*job_plan_cut_qty";

		}
		if($db_type==2)
		{
		$field_array="id, job_no, item_number_id, body_part_id,fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty";
		$field_array_up="job_no*item_number_id*body_part_id*fab_nature_id*color_type_id*lib_yarn_count_deter_id*construction*composition*fabric_description*gsm_weight*color_size_sensitive* 	color*avg_cons*fabric_source*rate*amount*avg_finish_cons*avg_process_loss*updated_by*update_date*status_active*is_deleted*company_id*costing_per*consumption_basis*process_loss_method*cons_breack_down*msmnt_break_down*color_break_down*yarn_breack_down*marker_break_down*width_dia_type*avg_cons_yarn*gsm_weight_yarn*plan_cut_qty*job_plan_cut_qty";

		}
		
		$field_array1="id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id,color_number_id, gmts_sizes, dia_width,item_size, cons, process_loss_percent, requirment, pcs,color_size_table_id, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons";
		
		$field_array2="id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id";
		$field_array3="id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,inserted_by,insert_date,status_active,is_deleted"; 
		$add_co=0;
		$add_comma=0;
		$add_comma_yarn=0;
		$add_comma_color=0;
		$id=return_next_id( "id", "wo_pre_cost_fabric_cost_dtls", 1 ) ;
		$id1=return_next_id( "id", "wo_pre_cos_fab_co_avg_con_dtls", 1 ) ;
		$wo_pre_cos_fab_co_color_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_color_dtls", 1 ) ;
		$wo_pre_cost_fab_yarn_cost_dtls_id=return_next_id( "id", " wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;
		$wo_pre_cost_fab_yarnbreakdown_id=return_next_id( "id", "wo_pre_cost_fab_yarnbreakdown", 1 ) ;
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbogmtsitem="cbogmtsitem_".$i;
			$txtbodypart="txtbodypart_".$i;
			$cbofabricnature="cbofabricnature_".$i;
			$cbocolortype="cbocolortype_".$i;
			$libyarncountdeterminationid="libyarncountdeterminationid_".$i;
            $construction="construction_".$i;
			$composition="composition_".$i;			
			$fabricdescription="fabricdescription_".$i;
			$txtgsmweight="txtgsmweight_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtcolor="txtcolor_".$i;
			$txtconsumption="txtconsumption_".$i;
			$cbofabricsource="cbofabricsource_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtfinishconsumption="txtfinishconsumption_".$i;
			$txtavgprocessloss="txtavgprocessloss_".$i;
			$cbostatus="cbostatus_".$i;
			$consbreckdown="consbreckdown_".$i;
			$msmntbreackdown="msmntbreackdown_".$i;
			$updateid="updateid_".$i;
			$processlossmethod="processlossmethod_".$i;
			$colorbreackdown="colorbreackdown_".$i;
			$yarnbreackdown="yarnbreackdown_".$i;
			$consumptionbasis="consumptionbasis_".$i;
			$markerbreackdown="markerbreackdown_".$i;
			$cbowidthdiatype="cbowidthdiatype_".$i;	
			$avgtxtconsumption="avgtxtconsumption_".$i;
			$avgtxtgsmweight="avgtxtgsmweight_".$i;
			$plancutqty="plancutqty_".$i;
			$jobplancutqty="jobplancutqty_".$i;

			//color Id
			if( str_replace("'","",$$cbocolorsizesensitive)==0)
			 {
				 if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name");  
					  $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
				 }
				 else 
				 {
					 $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color); 
				 }
			 }
			 else
			 {
				 $color_id=0;
			 }
			//color Id End
			

			if(str_replace("'",'',$$updateid)!="")
			{
				/*$field_array="job_no*item_number_id*body_part_id*fab_nature_id*color_type_id*lib_yarn_count_deter_id*fabric_description*gsm_weight*color_size_sensitive* 	color*avg_cons*fabric_source*rate*amount*avg_finish_cons*avg_process_loss*updated_by*update_date*status_active*is_deleted*company_id*costing_per*consumption_basis*process_loss_method*cons_breack_down*msmnt_break_down*color_break_down*yarn_breack_down";
				
				$data_array ="".$update_id."*".$$cbogmtsitem."*".$$txtbodypart."*".$$cbofabricnature."*".$$cbocolortype."*".$$libyarncountdeterminationid."*".$$fabricdescription."*".$$txtgsmweight."*".$$cbocolorsizesensitive."*".$color_id."*".$$txtconsumption."*".$$cbofabricsource."*".$$txtrate."*".$$txtamount."*".$$txtfinishconsumption."*".$$txtavgprocessloss."*".$_SESSION['logic_erp']['user_id']."*'".$date."'*".$$cbostatus."*0*".$cbo_company_name."*".$cbo_costing_per."*".$$consumptionbasis."*".$$processlossmethod."*".$$consbreckdown."*".$$msmntbreackdown."*".$$colorbreackdown."*".$$yarnbreackdown."";
				$rID=sql_update("wo_pre_cost_fabric_cost_dtls",$field_array,$data_array,"id","".$$updateid."",0);*/
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$update_id."*".$$cbogmtsitem."*".$$txtbodypart."*".$$cbofabricnature."*".$$cbocolortype."*".$$libyarncountdeterminationid."*".$$construction."*".$$composition."*".$$fabricdescription."*".$$txtgsmweight."*".$$cbocolorsizesensitive."*".$color_id."*".$$txtconsumption."*".$$cbofabricsource."*".$$txtrate."*".$$txtamount."*".$$txtfinishconsumption."*".$$txtavgprocessloss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbostatus."*0*".$cbo_company_name."*".$cbo_costing_per."*".$$consumptionbasis."*".$$processlossmethod."*".$$consbreckdown."*".$$msmntbreackdown."*".$$colorbreackdown."*".$$yarnbreackdown."*".$$markerbreackdown."*".$$cbowidthdiatype."*".$$avgtxtconsumption."*".$$avgtxtgsmweight."*".$$plancutqty."*".$$jobplancutqty.""));
				
// msmnt break down ==================================================================================================				
				$rID_de1=execute_query( "delete from wo_pre_cos_fab_co_avg_con_dtls where  pre_cost_fabric_cost_dtls_id =".$$updateid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else 
					{
						$data_array1 .="(".$id1.",".$$updateid.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
				    }
					$id1=$id1+1;
					$add_comma++;
				}
// msmnt break down end ==================================================================================================	
// yarn break down =======================================================================================================	
                $rID_de2=execute_query( "delete from wo_pre_cost_fab_yarn_cost_dtls where  fabric_cost_dtls_id =".$$updateid."",0);
			    $rID_de3=execute_query( "delete from wo_pre_cost_fab_yarnbreakdown where  fabric_cost_dtls_id =".$$updateid."",0);
                if(str_replace("'",'',$$cbofabricsource)==1)
				{
					$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					for($c=0;$c < count($yarnbreckdown_array);$c++)
					{
						$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
						if(str_replace("'",'',$$cbofabricnature)==2)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtconsumption)*$yarnbreckdownarr[4])/100),5,"");
						}
						if(str_replace("'",'',$$cbofabricnature)==3)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
						}
						if ($add_comma_yarn!=0)
						{
							$data_array3 .=",";
							$data_array4 .=",";
						}
						$data_array3 .="(".$wo_pre_cost_fab_yarn_cost_dtls_id.",".$$updateid.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
                        $data_array4 .="(".$wo_pre_cost_fab_yarnbreakdown_id.",".$$updateid.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$date."',".$$cbostatus.",0)";						                        $wo_pre_cost_fab_yarn_cost_dtls_id=$wo_pre_cost_fab_yarn_cost_dtls_id+1;
						$wo_pre_cost_fab_yarnbreakdown_id=$wo_pre_cost_fab_yarnbreakdown_id+1;
						$add_comma_yarn++;
					}
				}
// yarn break down end ==================================================================================================	
// Color break down =====================================================================================================
				if(str_replace("'",'',$$colorbreackdown)!="")
				{
					$rID_de4=execute_query( "delete from wo_pre_cos_fab_co_color_dtls where  pre_cost_fabric_cost_dtls_id =".$$updateid."",0);
					$colorbreckdown_array=explode('__',str_replace("'",'',$$colorbreackdown));
					for($c=0;$c < count($colorbreckdown_array);$c++)
					{
						$colorbreckdownarr=explode('_',$colorbreckdown_array[$c]);
						// color ID
						if (!in_array(str_replace("'","",$colorbreckdownarr[2]),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$colorbreckdownarr[2]), $color_library, "lib_color", "id,color_name");   
							$new_array_color[$color_id]=str_replace("'","",$colorbreckdownarr[2]);
						}
						else
						{
							$color_id =  array_search(str_replace("'","",$colorbreckdownarr[2]), $new_array_color); 
						}
						// Color ID end
						if ($add_comma_color!=0) $data_array2 .=",";
						$data_array2 .="(".$wo_pre_cos_fab_co_color_dtls_id.",".$$updateid.",".$update_id.",".$colorbreckdownarr[0].",'".$colorbreckdownarr[1]."','".$color_id."')";
						$wo_pre_cos_fab_co_color_dtls_id=$wo_pre_cos_fab_co_color_dtls_id+1;
						$add_comma_color++;
					}
				}
// Color break down end====================================
			}// end if(str_replace("'",'',$$updateid)!="")
			
			if(str_replace("'",'',$$updateid)=="")
			{
				/*$id=return_next_id( "id", "wo_pre_cost_fabric_cost_dtls", 1 ) ;
				$field_array="id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,	lib_yarn_count_deter_id, fabric_description, gsm_weight,color_size_sensitive,color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down";
				$data_array="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$fabricdescription.",".$$txtgsmweight.",".$$cbocolorsizesensitive.",".$color_id.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$date."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$colorbreackdown.",".$$yarnbreackdown.")";
				$rID=sql_insert("wo_pre_cost_fabric_cost_dtls",$field_array,$data_array,0);*/
				if ($add_co!=0) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbogmtsitem.",".$$txtbodypart.",".$$cbofabricnature.",".$$cbocolortype.",".$$libyarncountdeterminationid.",".$$construction.",".$$composition.",".$$fabricdescription.",".$$txtgsmweight.",".$$cbocolorsizesensitive.",".$color_id.",".$$txtconsumption.",".$$cbofabricsource.",".$$txtrate.",".$$txtamount.",".$$txtfinishconsumption.",".$$txtavgprocessloss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0,".$cbo_company_name.",".$cbo_costing_per.",".$$consumptionbasis.",".$$processlossmethod.",".$$consbreckdown.",".$$msmntbreackdown.",".$$colorbreackdown.",".$$yarnbreackdown.",".$$markerbreackdown.",".$$cbowidthdiatype.",".$$avgtxtconsumption.",".$$avgtxtgsmweight.",".$$plancutqty.",".$$jobplancutqty.")";
	           // $id=$id+1;
// msmnt break down=================================================================================				
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				$msmntbreackdown_array=explode('__',str_replace("'",'',$$msmntbreackdown));
				$markerbreackdownarr=explode('_',str_replace("'",'',$$markerbreackdown));
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$msmntbreackdownarr=explode('_',$msmntbreackdown_array[$c]);
					// size ID
					/*if (!in_array(str_replace("'","",$consbreckdownarr[0]),$new_array_size))
					{
					$size_id = return_id( str_replace("'","",$consbreckdownarr[0]), $size_library, "lib_size", "id,size_name");   
					$new_array_size[$size_id]=str_replace("'","",$consbreckdownarr[0]);
					}
					else
					{
						$size_id =  array_search(str_replace("'","",$consbreckdownarr[0]), $new_array_size);
					}*/
					// size ID end
					if ($add_comma!=0) $data_array1 .=",";
					if(str_replace("'",'',$$txtbodypart)*1==1)
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."',0,0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[8]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else if(str_replace("'",'',$$txtbodypart)*1==20)
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,'".$msmntbreackdownarr[0]."','".$msmntbreackdownarr[1]."','".$msmntbreackdownarr[2]."','".$msmntbreackdownarr[3]."','".$msmntbreackdownarr[4]."','".$msmntbreackdownarr[5]."','".$msmntbreackdownarr[6]."','".$msmntbreackdownarr[7]."','".$msmntbreackdownarr[8]."','".$msmntbreackdownarr[9]."','".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					else 
					{
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'".$markerbreackdownarr[0]."','".$markerbreackdownarr[1]."','".$markerbreackdownarr[2]."','".$markerbreackdownarr[3]."','".$markerbreackdownarr[4]."','".$markerbreackdownarr[5]."')";
					}
					$id1=$id1+1;
					$add_comma++;
				}
// msmnt break down end =================================================================================
// Yarn break down ==================================================================================================
                if(str_replace("'",'',$$cbofabricsource)==1)
				{
					$yarnbreckdown_array=explode('__',str_replace("'",'',$$yarnbreackdown));
					for($c=0;$c < count($yarnbreckdown_array);$c++)
					{
						$yarnbreckdownarr=explode('_',$yarnbreckdown_array[$c]);
						if(str_replace("'",'',$$cbofabricnature)==2)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtconsumption)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtconsumption)*$yarnbreckdownarr[4])/100),5,"");
						}
						if(str_replace("'",'',$$cbofabricnature)==3)
						{
							$cons=def_number_format(((str_replace("'",'',$$txtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
							$avg_cons=def_number_format(((str_replace("'",'',$$avgtxtgsmweight)*$yarnbreckdownarr[4])/100),5,"");
						}
						if ($add_comma_yarn!=0)
						{
							$data_array3 .=",";
							$data_array4 .=",";
						}
						$data_array3 .="(".$wo_pre_cost_fab_yarn_cost_dtls_id.",".$id.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
						$data_array4 .="(".$wo_pre_cost_fab_yarnbreakdown_id.",".$id.",".$update_id.",".$yarnbreckdownarr[0].",'".$yarnbreckdownarr[1]."','".$yarnbreckdownarr[2]."','".$yarnbreckdownarr[3]."','".$yarnbreckdownarr[4]."','".$cons."','".$avg_cons."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatus.",0)";
						$wo_pre_cost_fab_yarn_cost_dtls_id=$wo_pre_cost_fab_yarn_cost_dtls_id+1;
						$wo_pre_cost_fab_yarnbreakdown_id=$wo_pre_cost_fab_yarnbreakdown_id+1;
						$add_comma_yarn++;
					}
				}
				//echo $data_array3; die;
// Yarn break down end ===============================================================================================
// color break down ==================================================================================================	
				if(str_replace("'",'',$$colorbreackdown)!="")
				{
					$colorbreckdown_array=explode('__',str_replace("'",'',$$colorbreackdown));
					for($c=0;$c < count($colorbreckdown_array);$c++)
					{
						$colorbreckdownarr=explode('_',$colorbreckdown_array[$c]);
						// color ID
						if (!in_array(str_replace("'","",$colorbreckdownarr[2]),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$colorbreckdownarr[2]), $color_library, "lib_color", "id,color_name");   
							$new_array_color[$color_id]=str_replace("'","",$colorbreckdownarr[2]);
						}
						else
						{
							$color_id =  array_search(str_replace("'","",$colorbreckdownarr[2]), $new_array_color); 
						}
						// color ID end
						if ($add_comma_color!=0) $data_array2 .=",";
						$data_array2 .="(".$wo_pre_cos_fab_co_color_dtls_id.",".$id.",".$update_id.",".$colorbreckdownarr[0].",'".$colorbreckdownarr[1]."','".$color_id."')";
						$wo_pre_cos_fab_co_color_dtls_id=$wo_pre_cos_fab_co_color_dtls_id+1;
						$add_comma_color++;
					}
				}
// color break down end ==========================================================	
			 $id=$id+1;
			 $add_co++;			
			}//if(str_replace("'",'',$$updateid)=="")
		}// end master for loop
							
//echo bulk_update_sql_statement( "wo_pre_cost_fabric_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ); die;
		$rID_up=execute_query(bulk_update_sql_statement( "wo_pre_cost_fabric_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if ($data_array!="")
		{
		$rID_in=sql_insert("wo_pre_cost_fabric_cost_dtls",$field_array,$data_array,0,1);
		}
		
		$rID_in1=sql_insert("wo_pre_cos_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		
		if ($data_array2!="")
		{
			$rID_in2=sql_insert("wo_pre_cos_fab_co_color_dtls",$field_array2,$data_array2,0);
		}
		if ($data_array3!="")
		{
			$rID_in3=sql_insert("wo_pre_cost_fab_yarn_cost_dtls",$field_array3,$data_array3,0);
			$rID_in4=sql_insert("wo_pre_cost_fab_yarnbreakdown",$field_array3,$data_array4,0);
		}
		 //=======================sum=================
		 $wo_pre_cost_sum_dtls_job_no="";
		 $queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		 $nameArray=sql_select( $queryText );
	     foreach ($nameArray as $result)
		 {
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		 }
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array5="id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,pro_woven_grey_fab_req_yds,pro_knit_grey_fab_req_kg,pro_woven_fin_fab_req_yds,pro_knit_fin_fab_req_kg,pur_woven_grey_fab_req_yds,pur_knit_grey_fab_req_kg,pur_woven_fin_fab_req_yds,pur_knit_fin_fab_req_kg,woven_amount,knit_amount";
		$data_array5="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$tot_yarn_needed.",".$txtwoven_sum.",".$txtknit_sum.",".$txtwoven_fin_sum.",".$txtknit_fin_sum.",".$txtamount_sum.",".$avg.",".$txtwoven_sum_production.",".$txtknit_sum_production.",".$txtwoven_fin_sum_production.",".$txtknit_fin_sum_production.",".$txtwoven_sum_purchase.",".$txtknit_sum_purchase.",".$txtwoven_fin_sum_purchase.",".$txtknit_fin_sum_purchase.",".$txtwoven_amount_sum_purchase.",".$txtkint_amount_sum_purchase.")";
		$rID_in5=sql_insert("wo_pre_cost_sum_dtls",$field_array5,$data_array5,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array5="fab_yarn_req_kg*fab_woven_req_yds*fab_knit_req_kg*fab_woven_fin_req_yds*fab_knit_fin_req_kg*fab_amount*avg*pro_woven_grey_fab_req_yds*pro_knit_grey_fab_req_kg*pro_woven_fin_fab_req_yds*pro_knit_fin_fab_req_kg*pur_woven_grey_fab_req_yds*pur_knit_grey_fab_req_kg*pur_woven_fin_fab_req_yds*pur_knit_fin_fab_req_kg*woven_amount*knit_amount";
		$data_array5 ="".$tot_yarn_needed."*".$txtwoven_sum."*".$txtknit_sum."*".$txtwoven_fin_sum."*".$txtknit_fin_sum."*".$txtamount_sum."*".$avg."*".$txtwoven_sum_production."*".$txtknit_sum_production."*".$txtwoven_fin_sum_production."*".$txtknit_fin_sum_production."*".$txtwoven_sum_purchase."*".$txtknit_sum_purchase."*".$txtwoven_fin_sum_purchase."*".$txtknit_fin_sum_purchase."*".$txtwoven_amount_sum_purchase."*".$txtkint_amount_sum_purchase."";
		$rID_in5=sql_update("wo_pre_cost_sum_dtls",$field_array5,$data_array5,"job_no","".$update_id."",1);
		}
		//$tot_com_amount=update_comarcial_cost($update_id);
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$update_id." and booking_type=1 and is_short=2 ",1);
		//=======================sum End =================
		
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if($rID_up )
			{
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID_up."**".$tot_com_amount;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID_up."**".$tot_com_amount;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID_up)
			{
				oci_commit($con);  
				echo "1**".$new_job_no[0]."**".$rID_up."**".$tot_com_amount;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID_up."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
// Update End ==========================================================================================
}
?>

<?
if ($action=="save_update_delet_fabric_yarn_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", " wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;
		 $field_array="id,job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_qnty,avg_cons_qnty,supplier_id, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			 $cbocomptwo="cbocomptwo_".$i;
			 $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 //$consratio="consratio_".$i;
			 $consqnty="consqnty_".$i;
			 $avgconsqnty="avgconsqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			 $supplier="supplier_".$i;

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consqnty.",".$$avgconsqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
			$id=$id+1;

		 }
		 $rID=sql_insert(" wo_pre_cost_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array1="id,job_no,yarn_cons_qnty,yarn_amount";
		$data_array1="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
		$rID1=sql_insert("wo_pre_cost_sum_dtls",$field_array1,$data_array1,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array1="yarn_cons_qnty*yarn_amount";
		$data_array1 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
		$rID1=sql_update("wo_pre_cost_sum_dtls",$field_array1,$data_array1,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array_up="job_no*count_id*copm_one_id*percent_one*copm_two_id*percent_two*type_id*cons_qnty*avg_cons_qnty*supplier_id*rate*amount*updated_by*update_date*status_active*is_deleted";
		$field_array="id,job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_qnty,avg_cons_qnty,supplier_id, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;
		$id=return_next_id( "id", " wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocount="cbocount_".$i;
			 $cbocompone="cbocompone_".$i;
			 $percentone="percentone_".$i;
			 $cbocomptwo="cbocomptwo_".$i;
			 $percenttwo="percenttwo_".$i;
			 $cbotype="cbotype_".$i;
			 $consqnty="consqnty_".$i;
			 $avgconsqnty="avgconsqnty_".$i;
			 $txtrateyarn="txtrateyarn_".$i;
			 $txtamountyarn="txtamountyarn_".$i;
			 $cbostatusyarn="cbostatusyarn_".$i;
			 $updateidyarncost="updateidyarncost_".$i;
			 $supplier="supplier_".$i;

			if(str_replace("'",'',$$updateidyarncost)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidyarncost);
				$data_array_up[str_replace("'",'',$$updateidyarncost)] =explode(",",("".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consqnty.",".$$avgconsqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0"));
			}
			if(str_replace("'",'',$$updateidyarncost)=="")
			{
				if ($add_comma!=0) $data_array .=",";
			    $data_array .="(".$id.",".$update_id.",".$$cbocount.",".$$cbocompone.",".$$percentone.",".$$cbocomptwo.",".$$percenttwo.",".$$cbotype.",".$$consqnty.",".$$avgconsqnty.",".$$supplier.",".$$txtrateyarn.",".$$txtamountyarn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusyarn.",0)";
			    $id=$id+1;
			    $add_comma++;
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_fab_yarn_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			$rID1=sql_insert("wo_pre_cost_fab_yarn_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="id,job_no,yarn_cons_qnty,yarn_amount";
		$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconsumptionyarn_sum.",".$txtamountyarn_sum.")";
		$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="yarn_cons_qnty*yarn_amount";
		$data_array2 ="".$txtconsumptionyarn_sum."*".$txtamountyarn_sum."";
		$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		//=======================sum End =================
 		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
}
?>

<?
if ($action=="save_update_delet_fabric_conversion_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pre_cost_fab_conv_cost_dtls", 1 ) ;
		 $field_array="id,job_no,fabric_description,cons_process,process_loss,req_qnty,avg_req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtprocessloss="txtprocessloss_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtavgreqqnty="txtavgreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $colorbreakdown="colorbreakdown_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtavgreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$colorbreakdown.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_fab_conv_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array1="id,job_no,conv_req_qnty,conv_charge_unit,conv_amount";
		$data_array1="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
		$rID1=sql_insert("wo_pre_cost_sum_dtls",$field_array1,$data_array1,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array1="conv_req_qnty*conv_charge_unit*conv_amount";
		$data_array1 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
		$rID1=sql_update("wo_pre_cost_sum_dtls",$field_array1,$data_array1,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		 check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$field_array_up ="job_no*fabric_description*cons_process*process_loss*req_qnty*avg_req_qnty*charge_unit*amount*color_break_down*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		$field_array="id,job_no,fabric_description,cons_process,process_loss,req_qnty,avg_req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		$add_comma=0;
		$id=return_next_id( "id", "wo_pre_cost_fab_conv_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbocosthead="cbocosthead_".$i;
			 $cbotypeconversion="cbotypeconversion_".$i;
			 $txtprocessloss="txtprocessloss_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtavgreqqnty="txtavgreqqnty_".$i;
			 $txtchargeunit="txtchargeunit_".$i;
			 $txtamountconversion="txtamountconversion_".$i;
			 $cbostatusconversion="cbostatusconversion_".$i;
			 $updateidcoversion="updateidcoversion_".$i;
			 $colorbreakdown="colorbreakdown_".$i;
			 $coversionchargelibraryid="coversionchargelibraryid_".$i;
			if(str_replace("'",'',$$updateidcoversion)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidcoversion);
				$data_array_up[str_replace("'",'',$$updateidcoversion)] =explode(",",("".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtavgreqqnty.",".$$txtchargeunit.",".$$txtamountconversion.",".$$colorbreakdown.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0"));
			}
			if(str_replace("'",'',$$updateidcoversion)=="")
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cbocosthead.",".$$cbotypeconversion.",".$$txtprocessloss.",".$$txtreqqnty.",".$$txtreqqnty.",".$$txtavgreqqnty.",".$$txtamountconversion.",".$$colorbreakdown.",".$$coversionchargelibraryid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbostatusconversion.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_fab_conv_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID1=sql_insert("wo_pre_cost_fab_conv_cost_dtls",$field_array,$data_array,0);
		 }

 		//=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,conv_req_qnty,conv_charge_unit,conv_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconreqnty_sum.",".$txtconchargeunit_sum.",".$txtconamount_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="conv_req_qnty*conv_charge_unit*conv_amount";
			$data_array2 ="".$txtconreqnty_sum."*".$txtconchargeunit_sum."*".$txtconamount_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	
	
}
//*************************************************Fabric Cost, Yarn Cost, Coversion Cost End ***********************************************
?>
<?
//*************************************************Trim Cost Start ***********************************************
if ($action=="show_trim_cost_listview")
{
	$data=explode("*",$data);
	//print_r($data);
	
?>
<h3 align="left" class="accordion_h">+Trim Cost</h3> 
       <div id="content_trim_cost"  align="center">            
    	<fieldset>
        	<form id="trimccost_6" autocomplete="off">
            	<table width="1170" cellspacing="0" class="rpt_table" border="0" id="tbl_trim_cost" rules="all">
                	<thead>
                    	<tr>
                        	 <th width="115">Group</th> <th width="150">Description</th><th width="90">Brand/Sup Ref</th><th width="130">Remarks</th> <th width="95">Nominated Supp</th> <th  width="60">Cons UOM</th> <th  width="70">Cons/Unit Gmts</th> <th width="70">Rate</th> <th width="100">Amount</th> <th width="50">Apvl Req.</th> <th width="95">Status</th> <th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$save_update=1;
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					//$permission=0;
					}
					else
					{
					$disabled=0;
					//$permission=$permission;
					}
					$data_array=sql_select("select id,remark, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down, status_active from  wo_pre_cost_trim_cost_dtls where job_no='$data[0]' order by id");// quotation_id='$data'
					if(count($data_array)<1 && $data[3]==1)
					{
						$data_array=sql_select("select id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from  wo_pri_quo_trim_cost_dtls where quotation_id='$data[2]' order by id");// quotation_id='$data'
						$save_update=0;
					}
					
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							if($db_type==0)
							{
								$cons_breack_down=$row[csf('cons_breack_down')];
							}
							if($db_type==2)
							{
								if($row[csf('cons_breack_down')] !="")
								{
								 $cons_breack_down=$row[csf('cons_breack_down')];
								//$cons_breack_down=$row[csf('cons_breack_down')]->load();
								}
							}
							?>
                            	<tr id="trim_1" align="center">
                                   
                                    <td>
									<? 
									echo create_drop_down( "cbogroup_".$i, 115, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trim_group")], "set_trim_cons_uom(this.value, ".$i.")",$disabled,"" ); 
									?>
                                    
                                    </td>
                                    <td>
                                    <input type="text" id="txtdescription_<? echo $i; ?>"  name="txtdescription_<? echo $i; ?>" class="text_boxes" style="width:150px" value="<? echo $row[csf("description")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsupref_<? echo $i; ?>"  name="txtsupref_<? echo $i; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("brand_sup_ref")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                     <td>
                                          <input class="text_boxes" type="text" style="width:120px;" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>" value="<? echo $row[csf("remark")];  ?>"  maxlength="500" title="Maximum 500 Character" <? if($disabled==0){echo "";}else{echo "disabled";}?> />  
                                     </td>
                                     <td>
									<?
									//echo create_drop_down( "cbonominasupplier_".$i, 80, $row_status,"", 0, "0", $row[csf("nominated_supp")], '','','' ); 
									echo create_drop_down( "cbonominasupplier_".$i,95, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("nominated_supp")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')",$disabled,"" );
									?>
                                    </td> 
                                    <td>
									<?  echo create_drop_down( "cboconsuom_".$i, 60, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?>
                                    </td>
                                    
                                   <td>
                                    <input type="text" id="txtconsdzngmts_<? echo $i; ?>"  name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_trim_cost( <? echo $i;?> )" onDblClick="open_calculator(<? echo $i;?> )" onClick="open_consumption_popup_trim('requires/pre_cost_entry_controller.php?action=consumption_popup_trim', 'Consumtion Entry Form','<? echo $i; ?>')"  readonly/>
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_<? echo $i; ?>"  name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_trim_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_<? echo $i; ?>"  name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $row[csf("amount")];  ?>"  readonly  />
                                    </td>
                                    <td >
									<? echo create_drop_down( "cboapbrequired_".$i, 50, $yes_no,"", 0, "0", $row[csf("apvl_req")], '',$disabled,'' );  ?>
                                    </td>  
                                    
                                    
                                    
                                    <td>
									<? echo create_drop_down( "cbotrimstatus_".$i, 95, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    
                                    <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />  
                                    <input type="hidden" id="consbreckdown_<? echo $i; ?>" name="consbreckdown_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $cons_breack_down; ?>"  />

  
                                                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$trim_rate_from_library=return_library_array( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 group by a.id", "id", "rate");
						$save_update=0;
						$data_array=sql_select("select a.trims_group, a.cons_uom, a.cons_dzn_gmts, a.purchase_rate, a.amount, a.apvl_req, a.supplyer from  lib_trim_costing_temp a,lib_trim_costing_temp_dtls b  where a.id=b.lib_trim_costing_temp_id and b.buyer_id='$data[1]' and a.status_active=1 and  a.is_deleted=0 group by a.id,a.trims_group, a.cons_uom, a.cons_dzn_gmts, a.purchase_rate, a.amount, a.apvl_req, a.supplyer");
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$rate=$trim_rate_from_library[$row[csf('trims_group')]];
							$amount=$row[csf('cons_dzn_gmts')]*$trim_rate_from_library[$row[csf('trims_group')]];
							if($rate=="" || $rate==0)
							{
							 $rate=$row[csf('purchase_rate')];	
							 $amount=$row[csf('amount')];	
							}
							$i++;
					?>
                    <tr id="trim_1" align="center">
                                
                                   <td>
									<? 
									echo create_drop_down( "cbogroup_".$i, 115, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trims_group")], "set_trim_cons_uom(this.value,".$i.")",'',"" ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtdescription_<? echo $i; ?>"  name="txtdescription_<? echo $i; ?>" class="text_boxes" style="width:150px" value="<? //echo $row[csf("cons_dzn_gmts")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsupref_<? echo $i; ?>"  name="txtsupref_<? echo $i; ?>" class="text_boxes" style="width:90px" value="<? //echo $row[csf("cons_dzn_gmts")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                     <td>
                                      <input class="text_boxes" type="text" style="width:120px;" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>"  maxlength="500" title="Maximum 500 Character" <? if($disabled==0){echo "";}else{echo "disabled";}?> />  
                                 </td>
                                      <td>
									<? //echo create_drop_down( "cbonominasupplier_1", 80, $row_status,"", 0, "0", $row[csf("supplyer")], '','','' );  
                                    echo create_drop_down( "cbonominasupplier_".$i,95, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("supplyer")],"set_trim_rate_amount(this.value,".$i.",'supplier_change')","","" );
									?>
                                    </td> 
                                    <td><?  echo create_drop_down( "cboconsuom_".$i, 60, $unit_of_measurement,"", 1, "-- Select --", $row[csf("cons_uom")], "",1,"" ); ?></td>
                                   <td>
                                    <input type="text" id="txtconsdzngmts_<? echo $i; ?>"  name="txtconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( <? echo $i;?> )" onDblClick="open_calculator(<? echo $i;?> )" onClick="open_consumption_popup_trim('requires/pre_cost_entry_controller.php?action=consumption_popup_trim', 'Consumtion Entry Form','<? echo $i; ?>')" readonly/>
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_<? echo $i; ?>"  name="txttrimrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $rate;?>" onChange="calculate_trim_cost( <? echo $i;?> )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_<? echo $i; ?>"  name="txttrimamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $amount;?>"  readonly />
                                    </td>
                                    <td>
									<? echo create_drop_down( "cboapbrequired_".$i, 50, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' );  ?>
                                    </td>  
                                    
                                   
                                  
                                    <td>
									<? echo create_drop_down( "cbotrimstatus_".$i, 95, $row_status,"", 0, "0", '', '','','' );  ?>
                                    </td>  
                                    <td>
                                    <input type="button" id="increasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i; ?> )" />
                                    <input type="button" id="decreasetrim_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_trim_cost' );" />
                                    <input type="hidden" id="updateidtrim_<? echo $i; ?>" name="updateidtrim_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                     <input type="hidden" id="consbreckdown_<? echo $i; ?>" name="consbreckdown_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                    
                                   </td> 
                                </tr>
                    <? 
						}
					}
					else
					{
						$save_update=0;
					?>
                    
                    <tr id="trim_1" align="center">
                    			
                                   <td>
									<? 
									echo create_drop_down( "cbogroup_1", 115, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
											status_active=1 order by item_name", "id,item_name",1," -- Select Item --", $row[csf("trims_group")], "set_trim_cons_uom(this.value,1)",'',"" ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtdescription_1"  name="txtdescription_1" class="text_boxes" style="width:150px" value="<? //echo $row[csf("cons_dzn_gmts")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsupref_1"  name="txtsupref_1" class="text_boxes" style="width:90px" value="<? //echo $row[csf("cons_dzn_gmts")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td>
                                      <input class="text_boxes" type="text" style="width:120px;" name="txtremark_1" id="txtremark_1"  maxlength="500" title="Maximum 500 Character" <? if($disabled==0){echo "";}else{echo "disabled";}?> />  
                                 </td>
                                     <td>
									<? //echo create_drop_down( "cbonominasupplier_1", 80, $row_status,"", 0, "0", $row[csf("supplyer")], '','','' );  
                                    echo create_drop_down( "cbonominasupplier_1",95, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, '-Select-', $row[csf("supplyer")],"set_trim_rate_amount(this.value,1,'supplier_change')","","" );
									?>
                                    </td> 
                                    <td><?  echo create_drop_down( "cboconsuom_1", 60, $unit_of_measurement,"", 1, "-- Select --", '', "",1,"" ); ?></td>
                                   <td>
                                    <input type="text" id="txtconsdzngmts_1"  name="txtconsdzngmts_1" class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("cons_dzn_gmts")];?>" onChange="calculate_trim_cost( 1)" onDblClick="open_calculator(1)" onClick="open_consumption_popup_trim('requires/pre_cost_entry_controller.php?action=consumption_popup_trim', 'Consumtion Entry Form',1)" readonly/>
                                    </td>
                                   <td>
                                    <input type="text" id="txttrimrate_1"  name="txttrimrate_1" class="text_boxes_numeric" style="width:70px" value="<? echo $row[csf("purchase_rate")];?>" onChange="calculate_trim_cost( 1 )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txttrimamount_1"  name="txttrimamount_1" class="text_boxes_numeric" style="width:100px" value="<? echo $row[csf("amount")];?>"  readonly />
                                    </td>
                                    <td>
									<? echo create_drop_down( "cboapbrequired_1", 50, $yes_no,"", 0, "0", $row[csf("apvl_req")], '','','' );  ?>
                                    </td>  
                                    
                                  
                                  
                                    <td>
									<? echo create_drop_down( "cbotrimstatus_1", 95, $row_status,"", 0, "0", '', '','','' );  ?>
                                    </td>  
                                    <td>
                                    <input type="button" id="increasetrim_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(1 )" />
                                    <input type="button" id="decreasetrim_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_trim_cost' );" />
                                    <input type="hidden" id="updateidtrim_1" name="updateidtrim_1"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="consbreckdown_1" name="consbreckdown_1"  class="text_boxes" style="width:20px" value=""  />                                   </td> 
                                </tr>
                    <?
					}
					}
					?>
                </tbody>
                </table>
                <table width="1170" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="668">
                            Sum
                            </th>
                            <th  width="72">
                            <input type="text" id="txtconsdzntrim_sum"  name="txtconsdzntrim_sum" class="text_boxes_numeric" style="width:70px"  readonly />
                            </th>
                            <th width="72"> 
                            <input type="text" id="txtratetrim_sum"  name="txtratetrim_sum" class="text_boxes_numeric" style="width:70px"  readonly />
                            </th>
                            <th width="100">
                             <input type="text" id="txttrimamount_sum"  name="txttrimamount_sum" class="text_boxes_numeric" style="width:100px"  readonly />
                            </th>
                            <th width="50">
                            </th>
                            <th width="">
                            </th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="1170" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
							echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
					    }
						else
						{
							echo load_submit_buttons( $permission, "fnc_trim_cost_dtls", $save_update,0,"reset_form('trimccost_6','','',0)",6) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}
?>
<?
if ($action=="set_cons_uom")
{
	//extract($_REQUEST);
	$cons_uom=return_field_value("trim_uom", "lib_item_group", "id=$data");
	echo $cons_uom; die;
}
if ($action=="rate_from_library")
{
	//extract($_REQUEST);
	$data=explode("_",$data);
	$rate=0;
	if($data[1]==0)
	{
	$trim_rate_from_library=sql_select( "select a.id, min(b.rate) as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0]  and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 group by a.id");
	}
	else
	{
	$trim_rate_from_library=sql_select( "select a.id, b.rate as rate from lib_item_group a, lib_item_group_rate b where a.id=b.mst_id and a.id=$data[0] and b.supplier_id=$data[1] and a.item_category=4 and a.status_active=1 and	a.is_deleted=0 ");	
	}
	foreach($trim_rate_from_library as $trim_rate_from_library_row)
	{
		if($trim_rate_from_library_row[csf('rate')] !="")
		{
	     $rate=$trim_rate_from_library_row[csf('rate')];	
		}
	}
	echo $rate; die;
}

if ($action=="consumption_popup_trim")
{
  echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
  extract($_REQUEST);

?>
     
<script>
function copy_value(value,field_id,i)
{
  var copy_val=document.getElementById('copy_val').checked;
  var rowCount = $('#tbl_consmption_cost tr').length-3;
  
	  if(copy_val==true)
	  {
		  for(var j=i; j<=rowCount; j++)
			{
			  
				  if(field_id=='itemsizes_')
				  {
					
					document.getElementById(field_id+j).value=value;
					
				  }
				  if(field_id=='cons_')
				  {
					document.getElementById(field_id+j).value=value;
				  }
				  if(field_id=='place_')
				  {
					document.getElementById(field_id+j).value=value;
				  }
			}
	  }
	  set_sum_value( 'cons_sum', 'cons_'  );
      set_sum_value( 'pcs_sum', 'pcs_');
}

function fn_delete_down_tr(rowNo,table_id) 
{   
	if(table_id=='tbl_consmption_cost')
	{
		var numRow = $('table#tbl_consmption_cost tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_consmption_cost tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
          set_sum_value( 'pcs_sum', 'pcs_');
	}
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='cons_sum')
	{
		var ddd={dec_type:5,comma:0};
	}
	
	if(des_fil_id=='pcs_sum')
	{
		var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-3;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	claculate_avg()
}
	
function js_set_value()
{
	var rowCount = $('#tbl_consmption_cost tr').length-3;
	var cons_breck_down="";
	for(var i=1; i<=rowCount; i++)
	{
		var ponoid=$('#ponoid_'+i).val()
			if(ponoid=='')
			{
				 ponoid=0;
			}
		var itemsizes=$('#itemsizes_'+i).val();
			if(itemsizes=='')
			{
				 itemsizes=0;
			}
		var cons=$('#cons_'+i).val()
			if(cons=='')
			{
				 cons=0;
			}
	 var place=$('#place_'+i).val()
			if(place=='')
			{
				 place=0;
			}
		
	    var pcs=$('#pcs_'+i).val()
			if(pcs=='')
			{
				 pcs=0;
			}
		var cbocountry=$('#cbocountry_'+i).val()
			if(cbocountry=='')
			{
				 cbocountry=0;
			}
			
			if(cons_breck_down=="")
			{
					cons_breck_down+=ponoid+'_'+itemsizes+'_'+cons+'_'+place+'_'+pcs+'_'+cbocountry;
			}
			else
			{
				    cons_breck_down+="__"+ponoid+'_'+itemsizes+'_'+cons+'_'+place+'_'+pcs+'_'+cbocountry;
			}
	}
	document.getElementById('cons_breck_down').value=cons_breck_down;
	claculate_avg()
	parent.emailwindow.hide();
}
function claculate_avg()
{
	var significant_row=0;
	var rowCount = $('#tbl_consmption_cost tr').length-3;
	for(var i=1; i<=rowCount; i++)
	{
		if(document.getElementById('cons_'+i).value =='' || document.getElementById('cons_'+i).value ==0)
		{
			continue;
		}
		else
		{
			significant_row=significant_row+1;
			document.getElementById('pcs_'+i).value=document.getElementById('pcs').value
		}
	}
	var avg_cons=(document.getElementById('cons_sum').value*1)/significant_row;
    var calculated_pcs=(document.getElementById('pcs_sum').value*1)/significant_row;
	document.getElementById('avg_cons').value=number_format_common(avg_cons, 5, 0);
    document.getElementById('calculated_pcs').value=calculated_pcs;
}

function clculate_cons_for_mtr()
{
  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
  var txt_costing_per=document.getElementById('txt_costing_per').value;	
  if(txt_costing_per==1)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*12;	
  }
  if(txt_costing_per==2)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;	
  }
  if(txt_costing_per==3)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*2*12;	
  }
  if(txt_costing_per==4)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*3*12;	
  }
  if(txt_costing_per==5)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*4*12;	
  }
  var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1
  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
  document.getElementById('txt_caculated_value').value=txt_cons_for_mtr/txt_cons_length;	
}

function clculate_req_carton()
{
  var txt_gmts_per_catton=(document.getElementById('txt_gmts_per_catton').value)*1;	
  var txt_cost_for=document.getElementById('txt_cost_for').value;
  var txt_caculated_value=(1/txt_gmts_per_catton)*txt_cost_for;
  document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);
}

function clculate_req_carton_stiker()
{
  var txt_stiker_per_catton=(document.getElementById('txt_stiker_per_catton').value)*1;	
  var txt_req_carton=document.getElementById('txt_req_carton').value;
  var txt_caculated_value=txt_stiker_per_catton*txt_req_carton;
  document.getElementById('txt_caculated_value').value= number_format_common(txt_caculated_value,5,0);	
}

function clculate_elastic()
{
		  var txt_costing_per=document.getElementById('txt_costing_per').value;	
		  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
		  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
		  var cons_dzn=0;
		  if(txt_costing_per==1)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*12
		  }
		  if(txt_costing_per==2)
		  {
		   cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1
		  }
		  if(txt_costing_per==3)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*24	
		  }
		  if(txt_costing_per==4)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*36
		  }
		  if(txt_costing_per==5)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*48	
		  }
		 
		  document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);	
}

function clculate_gumtap()
{
	
		  var txt_costing_per=document.getElementById('txt_costing_per').value;	
		  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;
		  var txt_pcs_per_carton=(document.getElementById('txt_pcs_per_carton').value)*1;
		  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
		  var cons_dzn=0;
		  if(txt_costing_per==1)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
		  cons_dzn=(cons_dzn/txt_cons_length)*12;
		  }
		  if(txt_costing_per==2)
		  {
		   cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
		   cons_dzn=(cons_dzn/txt_cons_length)*1;
		  }
		  if(txt_costing_per==3)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
		  cons_dzn=(cons_dzn/txt_cons_length)*24;
		  }
		  if(txt_costing_per==4)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
		  cons_dzn=(cons_dzn/txt_cons_length)*36;
		  }
		  if(txt_costing_per==5)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_pcs_per_carton);
		  cons_dzn=(cons_dzn/txt_cons_length)*48;
		  }
		 
		  document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);	
}

function clculate_tagpin()
{
		  var txt_costing_per=document.getElementById('txt_costing_per').value;	
		  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
		  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
		  var cons_dzn=0;
		  if(txt_costing_per==1)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*12
		  }
		  if(txt_costing_per==2)
		  {
		   cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1
		  }
		  if(txt_costing_per==3)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*24	
		  }
		  if(txt_costing_per==4)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*36
		  }
		  if(txt_costing_per==5)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*48	
		  }
		 
		  document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);	
}

function clculate_sequines()
{
var txt_costing_per=document.getElementById('txt_costing_per').value;	
		  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
		  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
		  var cons_dzn=0;
		  if(txt_costing_per==1)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*12
		  }
		  if(txt_costing_per==2)
		  {
		   cons_dzn=(txt_cons_per_gmts/txt_cons_length)*1
		  }
		  if(txt_costing_per==3)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*24	
		  }
		  if(txt_costing_per==4)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*36
		  }
		  if(txt_costing_per==5)
		  {
		  cons_dzn=(txt_cons_per_gmts/txt_cons_length)*48	
		  }
		 
		  document.getElementById('txt_caculated_value').value=number_format_common(cons_dzn,5,0);		
}

function calculator_value_set(i)
{
	var txt_caculated_value=document.getElementById('txt_caculated_value').value
	document.getElementById('tr_order').value=i;
	if(txt_caculated_value !='')
	{
		document.getElementById('cons_'+i).value=txt_caculated_value;
		copy_value(txt_caculated_value,'cons_',i)
	}
	
}

function open_country_popup(i)
{
	var txt_po_id=document.getElementById('ponoid_'+i).value;
	var txt_country=document.getElementById('cbocountry_'+i).value
	var txt_country_name=document.getElementById('cbocountryname_'+i).value
	var page_link='pre_cost_entry_controller.php?action=open_country_popup';
	var title='Country';
	page_link=page_link+'&txt_po_id='+txt_po_id+'&txt_country='+txt_country+'&txt_country_name='+txt_country_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=350px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("txt_selected_id");
		var theemailname=this.contentDoc.getElementById("txt_selected_name");
		//if (trim(theemail.value)!="")
		//{
			//freeze_window(5);
			document.getElementById('cbocountry_'+i).value=trim(theemail.value);
			document.getElementById('cbocountryname_'+i).value=trim(theemailname.value);
			//release_freezing();
		//}
	}
}
</script>
</head>
<body>
<div align="center" style="width:100%;">
<?
	if($cbo_costing_per==1)
	{
		$pcs_value=1*12;
	}
	if($cbo_costing_per==2)
	{
		$pcs_value=1*1;
	}
	if($cbo_costing_per==3)
	{
		$pcs_value=2*12;
	}
	if($cbo_costing_per==4)
	{
		$pcs_value=3*12;
	}
	if($cbo_costing_per==5)
	{
		$pcs_value=4*12;
	}
	$job_order_uom=return_field_value("order_uom","wo_po_details_master","job_no='$txt_job_no'");
?>
 
 <table class="rpt_table" border="0">
 <tr>
 <th><b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/><input type="hidden" id="pcs"  name="pcs"  class="text_boxes_numeric" style="width:75px"  value="<? echo $pcs_value; ?>"> <input type="hidden" id="tr_order"  name="tr_order"  class="text_boxes_numeric" style="width:75px"  value="" /> <input type="hidden" id="cons_breck_down"  name="cons_breck_down"  class="text_boxes_numeric" style="width:75px"  value="" /></th> <th></th>
 </tr>
 <tr>
 <td>
<table width="570" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">SL</th><th  width="100">PO NO</th><th width="90">Item Sizes</th><th width="50">Cons</th><th width="80">Placement</th><th width="90"><? if ($job_order_uom==1){echo "Pcs";} if($job_order_uom==58){echo "Set";} ?></th><th width="80">Country</th>
                            
                            <th width=""></th>  
                        </tr>
                    </thead>
                    <tbody>
                        <?
						if($cbo_costing_per==1)
						{
							$pcs_value=1*12;
						}
						if($cbo_costing_per==2)
						{
							$pcs_value=1*1;
						}
						if($cbo_costing_per==3)
						{
							$pcs_value=2*12;
						}
						if($cbo_costing_per==4)
						{
							$pcs_value=3*12;
						}
						if($cbo_costing_per==5)
						{
							$pcs_value=4*12;
						}
                        $data_array=sql_select("select id, po_number,po_quantity,shipment_date from   wo_po_break_down  where  job_no_mst='$txt_job_no'  and status_active=1   order by id");
						$cons_breck_downnarr=explode('__',$cons_breck_downn);
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$cons_breck_downnarr[$i]);
								$i++;
								//$po_country=return_library_array( "select country_id from wo_po_color_size_breakdown where po_break_down_id=".$row[csf('id')]." group by country_id", "country_id","country_id");
								//$country_string=implode(",",$po_country);
								$countrydata=explode(",",$data[5]);
								$country_name="";
								for($countryindex=0; $countryindex < count($countrydata);$countryindex++)
								{
								$country_name.=	$country_library[$countrydata[$countryindex]].",";
								}
							

					?>
                    <tr id="break_1" align="center">
                                    <td>
                                     <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="pono_<? echo $i;?>"  name="pono_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('po_number')]; ?>" readonly/>
                                    <input type="hidden" id="ponoid_<? echo $i;?>"  name="ponoid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>"  readonly/>

                                    </td>
                                    
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:75px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $data[1]; ?>" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>"  onChange="set_sum_value( 'cons_sum', 'cons_' ); copy_value(this.value,'cons_',<? echo $i;?>)"  name="cons_<? echo $i;?>" onClick="calculator_value_set(<? echo $i;?>)" class="text_boxes_numeric" style="width:50px" value="<? echo $data[2]; ?>" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
                                    </td>
                                     <td>
                                    <input type="text" id="place_<? echo $i;?>"  name="place_<? echo $i;?>" class="text_boxes" style="width:80px" onChange="copy_value(this.value,'place_',<? echo $i;?>)"  value="<? echo $data[3]; ?>"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:75px"  value="<? echo $data[4]; ?>" readonly />
                                    </td>
                                    <td>
                                    <input type="hidden" id="cbocountry_<? echo $i;?>"  name="cbocountry_<? echo $i;?>"  onClick="" class="text_boxes_numeric" style="width:75px"  value="<? echo $data[5]; ?>" readonly />
                                    <input type="text" id="cbocountryname_<? echo $i;?>"  name="cbocountryname_<? echo $i;?>"  onClick="open_country_popup(<? echo $i;?>)" class="text_boxes_numeric" style="width:75px"  value="<? echo rtrim($country_name,','); ?>" readonly />
                                     <? 
                                       // echo create_drop_down( "cbocountry_".$i, 80, $country_library,"", 1, "-- Select Country --", $data[5], "","",$country_string);
                                     ?>
                                    </td>
                                    <td id="add_1">
                                     <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?>,'tbl_consmption_cost' );" />
                                     </td>
                                </tr>
                    <?
							}
						}
						?>
                        </tbody>
                        <tfoot>
                    	<tr>
                        <th width="50"></th><th  width="100"></th><th width="90">Sum:</th><th width="50"><input type="text" id="cons_sum" name="cons_sum" class="text_boxes_numeric" style="width:50px" readonly></th><th width="80"></th><th width="90"><input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:75px" readonly></th><th width="80"></th>
                        <th width=""></th>  
                            
                        	
                        </tr>
                        <tr>
                        <th width="50"></th><th  width="100"></th><th width="90">Avg:</th><th width="50"><input type="text" id="avg_cons" name="avg_cons" class="text_boxes_numeric" style="width:50px" value="<? //echo $calculated_conss;?>" readonly></th><th width="80"></th><th width="90"><input type="text" id="calculated_pcs" name="calculated_pcs" class="text_boxes_numeric" style="width:75px" readonly></th><th width="80"></th>
                        <th width=""></th>  
                        
                        	
                        </tr>
                        </tfoot>
                        </table>
						<script>
                        set_sum_value( 'cons_sum', 'cons_'  );
                        set_sum_value( 'pcs_sum', 'pcs_');
                        </script>
                        </div>
                        
     <div align="center" style="width:100%;" >
         <fieldset>
            <table width="470" cellspacing="0" class="" border="0" rules="all">
                 <tr>
                    <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td> 
                </tr>
            </table>
        </fieldset>
   </div>
   </td>
   <td>
<?
                                    if($calculator_parameter==1)
									{
										
										?>
										<fieldset>
										<legend>Sewing Thread-Comsumption Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="300">
										<tr>
										<td width="120">
										Cons Per Garment
										</td>
										<td width="">
										<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_cons_for_mtr()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Mtr
										</td>
										</tr>
										<tr>
										<td>
										Cons <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric"  readonly/> Mtr
										</td>
										</tr>
										<tr>
										<td>
										Cone Length
										</td>
										<td>
										<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_cons_for_mtr()" value="4000" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> Mtr
										</td>
										</tr>
										<tr>
										<td>
										Cons  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> Cone
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    <?
                                    if($calculator_parameter==2)
									{
										
										
										?>
										<fieldset>
										<legend>Carton-Comsumption Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="260">
										<tr>
										<td width="120">
										Gmts Per Carton
										</td>
										<td width="">
										<input type="text" id="txt_gmts_per_catton" name="txt_gmts_per_catton" class="text_boxes_numeric" onChange="clculate_req_carton()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
										</td>
										</tr>
										<tr>
										<td>
										Costting <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_cost_for" name="txt_cost_for" class="text_boxes_numeric" value="<? echo $pcs_value;?>"  readonly/> 
										</td>
										</tr>
										<tr>
										
										</tr>
										<tr>
										<td>
										Required Carton
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> 
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    
                                     <?
                                    if($calculator_parameter==3)
									{
										//echo "select a.cons_dzn_gmts from wo_pre_cost_trim_cost_dtls a,lib_item_group b where a.trim_group=b.id and b.cal_parameter=2 and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
										$data_array=sql_select("select a.cons_dzn_gmts from wo_pre_cost_trim_cost_dtls a,lib_item_group b where a.trim_group=b.id and b.cal_parameter=2 and a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
										//print_r( $data_array);
										$total_row=count($data_array);
										$req_carton=0;
										if($total_row==1)
										{
										list($data)	= $data_array;
										$req_carton=$data[cons_dzn_gmts];
										}
										?>
										<fieldset>
										<legend>Carton Stiker-Comsumption Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="260">
										<tr>
										<td width="120">
										Stiker Per Carton
										</td>
										<td width="">
										<input type="text" id="txt_stiker_per_catton" name="txt_stiker_per_catton" class="text_boxes_numeric" onChange="clculate_req_carton_stiker()"  <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> 
										</td>
										</tr>
										<tr>
										<td>
										Req Car  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_req_carton" name="txt_req_carton" class="text_boxes_numeric" onChange="clculate_req_carton_stiker()" value="<? echo $req_carton;?>" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>  /> 
										</td>
										</tr>
										<tr>
										
										</tr>
										<tr>
										<td>
										Required Stiker
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> 
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    <?
                                    if($calculator_parameter==4)
									{
										
										
										?>
										<fieldset>
										<legend>Poly-Comsumption Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="260">
										<tr>
										<td width="120">
										Gmts Per Poly
										</td>
										<td width="">
										<input type="text" id="txt_gmts_per_catton" name="txt_gmts_per_catton" class="text_boxes_numeric" onChange="clculate_req_carton()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> 
										</td>
										</tr>
										<tr>
										<td>
										Costting <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_cost_for" name="txt_cost_for" class="text_boxes_numeric" value="<? echo $pcs_value;?>"  readonly/> 
										</td>
										</tr>
										<tr>
										
										</tr>
										<tr>
										<td>
										Required Poly
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> 
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    <?
                                    if($calculator_parameter==5)
									{
										?>
										<fieldset>
										<legend>Elastic Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="300">
										<tr>
										<td width="120">
										Cons Per Garment
										</td>
										<td width="">
										<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_elastic()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Yds
										</td>
										</tr>
										<tr>
										<td>
										Roll Length
										</td>
										<td>
										<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_elastic()" value="48" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> Yds
										</td>
										</tr>
										<tr>
										<td>
										Cons  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> Roll
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    
                                    <?
                                    if($calculator_parameter==6)
									{
										?>
										<fieldset>
										<legend>Gum Tap Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="300">
										<tr>
										<td width="120">
										Cons Per Carton
										</td>
										<td width="">
										<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_gumtap()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Yds
										</td>
										</tr>
										<tr>
                                        <tr>
										<td width="120">
										Pcs Per Carton
										</td>
										<td width="">
										<input type="text" id="txt_pcs_per_carton" name="txt_pcs_per_carton" class="text_boxes_numeric" onChange="clculate_gumtap()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Pcs
										</td>
										</tr>
										<tr>
										<td>
										Roll Length
										</td>
										<td>
										<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_gumtap()" value="48" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> Yds
										</td>
										</tr>
										<tr>
										<td>
										Cons  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> Roll
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    
                                    <?
                                    if($calculator_parameter==7)
									{
										?>
										<fieldset>
										<legend>Tag Pin Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="300">
										<tr>
										<td width="120">
										Cons Per Garments
										</td>
										<td width="">
										<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_tagpin()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Pcs
										</td>
										</tr>
										<tr>
										<td>
										Qty Per Box
										</td>
										<td>
										<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_tagpin()" value="4800" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> Pcs
										</td>
										</tr>
										<tr>
										<td>
										Cons  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> Box
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
                                    <?
                                    if($calculator_parameter==8)
									{
										?>
										<fieldset>
										<legend>Tag Pin Calculator</legend>
										<table cellpadding="0" cellspacing="0" align="center" width="300">
										<tr>
										<td width="120">
										Cons Per Garments
										</td>
										<td width="">
										<input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_sequines()" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?> /> Pcs
										</td>
										</tr>
										<tr>
										<td>
										Roll Length
										</td>
										<td>
										<input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_sequines()" value="10000" <? if($cbo_approved_status==1){echo "disabled";} else { echo "";}?>/> Pcs
										</td>
										</tr>
										<tr>
										<td>
										Cons  <? echo $costing_per[$cbo_costing_per];?>
										</td>
										<td>
										<input type="text" id="txt_caculated_value" name="txt_caculated_value" class="text_boxes_numeric" readonly /> Roll
										</td>
										</tr>
										 <tr>
										
										<td colspan="3" align="center">
										<input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 										<input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 
										</td>
										</tr>
										
										</table>
										</fieldset>
										<?
										
									}
									?>
   </td>
   <tr>
   </table>
   
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="calculator_parameter")
{
	extract($_REQUEST);
	$cal_parameter_type=return_field_value("cal_parameter", "lib_item_group", "id='$data'");
	echo trim($cal_parameter_type); die;
	
}

if($action=="calculator_type")
{
   extract($_REQUEST);
   echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
?>
<script>
function clculate_cons_for_mtr()
{
  var txt_cons_per_gmts=(document.getElementById('txt_cons_per_gmts').value)*1;	
  var txt_costing_per=document.getElementById('txt_costing_per').value;	
  if(txt_costing_per==1)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*12;	
  }
  if(txt_costing_per==2)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*1;	
  }
  if(txt_costing_per==3)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*2*12;	
  }
  if(txt_costing_per==4)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*3*12;	
  }
  if(txt_costing_per==5)
  {
  document.getElementById('txt_cons_for_mtr').value=txt_cons_per_gmts*4*12;	
  }
  var txt_cons_for_mtr= (document.getElementById('txt_cons_for_mtr').value)*1
  var txt_cons_length= (document.getElementById('txt_cons_length').value)*1
  document.getElementById('txt_cons_for_cone').value=txt_cons_for_mtr/txt_cons_length;	
}
function js_set_value_calculator(type)
{
	if(type=='sewing_thread')
	{
		var clacolator_param_value=document.getElementById('txt_cons_per_gmts').value+'*'+document.getElementById('txt_cons_for_mtr').value+'*'+document.getElementById('txt_cons_length').value+'*'+document.getElementById('txt_cons_for_cone').value+'*'+document.getElementById('txt_costing_per').value;
		
		document.getElementById('txt_clacolator_param_value').value=clacolator_param_value;
		
	}
	
		parent.emailwindow.hide();

	
}
</script>
</head>
<body>
<?
	if($calculator_parameter==1)
	{
		
		?>
        <fieldset>
        <legend>Sewing Thread</legend>
        <table cellpadding="0" cellspacing="2" align="center" width="300">
        <tr>
        <td width="120">
        Cons Per Garment
        </td>
        <td width="">
        <input type="text" id="txt_cons_per_gmts" name="txt_cons_per_gmts" class="text_boxes_numeric" onChange="clculate_cons_for_mtr()" /> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_mtr" name="txt_cons_for_mtr" class="text_boxes_numeric"  readonly/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cone Length
        </td>
        <td>
        <input type="text" id="txt_cons_length" name="txt_cons_length" class="text_boxes_numeric"  onChange="clculate_cons_for_mtr()" value="4000"/> Mtr
        </td>
        </tr>
        <tr>
        <td>
        Cons  <? echo $costing_per[$cbo_costing_per];?>
        </td>
        <td>
        <input type="text" id="txt_cons_for_cone" name="txt_cons_for_cone" class="text_boxes_numeric" readonly /> Cone
        </td>
        </tr>
         <tr>
        
        <td colspan="3" align="center">
        <input type="button" class="formbutton" value="Close" onClick="js_set_value_calculator('sewing_thread')"/> 
        <input type="hidden" id="txt_costing_per" name="txt_costing_per" class="text_boxes_numeric" value="<? echo $cbo_costing_per; ?>" readonly /> 
        <input type="hidden" id="txt_clacolator_param_value" name="txt_clacolator_param_value" class="text_boxes_numeric" value="" readonly /> 

        </td>
        </tr>
        
        </table>
        </fieldset>
        <?
		
	}
	?>
 </body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    
<?
}

if($action=="open_country_popup")
{
echo load_html_head_contents("Country","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script> 
/*function js_set_value(data)
{
	document.getElementById('country_name').value=data;
    parent.emailwindow.hide();
}*/

 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			/*var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}*/
			$("#tbl_list_search tr").each(function() {
			    var valTP=$(this).attr("id");
				$("#"+valTP).click();
				
																				  
		});
		}
		
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual_name' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = '';
			var name='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			//alert(id)
			$('#txt_selected_id').val( id );
			$('#txt_selected_name').val( name );
		}
</script> 
</head>
<body>
<body>
<div align="center">
<form>
<input type="hidden" id="txt_selected_id" name="txt_selected_id" value="<? if($txt_country !=0){echo $txt_country;}?> " />
<input type="hidden" id="txt_selected_name" name="txt_selected_name" value="<?  if($txt_country !=0){echo $txt_country_name;}?> " />
<?
$sql_data=sql_select("select country_id  from wo_po_color_size_breakdown   WHERE po_break_down_id=$txt_po_id and  status_active=1 and is_deleted=0 group by country_id"); 
?>
<table width="500" cellspacing="0" class="rpt_table" border="0" id="tbl_list_search" rules="all">
<?
$i=1;
foreach($sql_data as $row)
{
	if ($i%2==0)  
	$bgcolor="#E9F3FF";
else
	$bgcolor="#FFFFFF";
?>
<tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $row[csf('country_id')];?>" onClick="js_set_value(<? echo $row[csf('country_id')];?>)"> 
<td width="50"><? echo $i ?></td>
<td width="100">
<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $row[csf('country_id')]; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
<input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $row[csf('country_id')]; ?>" value="<? echo $country_library[$row[csf('country_id')]]; ?>"/>		
<? echo $country_library[$row[csf('country_id')]]; ?>
</td>
<td width="100">
<? echo $country_library_short[$row[csf('country_id')]]; ?>
</td>
</tr>
<?
$i++;
}
?>
</table>
<table width="500" cellspacing="0" cellpadding="0" style="border:none" align="center">
<tr>
<td align="center" height="30" valign="bottom">
<div style="width:100%"> 
<div style="width:50%; float:left" align="left">
<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
</div>
<div style="width:50%; float:left" align="left">
<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
</div>
</div>
</td>
</tr>
</table>
</form>
</div>
</body>
<script>

var txt_country='<? if($txt_country !=0){echo $txt_country;} ?>'
var txt_country_name='<?  if($txt_country !=0){echo $txt_country_name;} ?>'
if(txt_country !="")
{
selected_id=txt_country.split(",");
selected_name=txt_country_name.split(",");
}

for(var i=0; i<selected_id.length;i++)
{
   if(selected_id[i] !="")
   {
	toggle( document.getElementById( 'search' + selected_id[i] ), '#FFFFCC' );
   }
}
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
}
function create_consbreak_down($job_no,$cons_value)
{
$cbo_costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no=$job_no");
$data_array=sql_select("select id, po_number from   wo_po_break_down  where  job_no_mst=$job_no  and status_active=1   order by id");
						if ( count($data_array)>0)
						{
							$cons_breck_down="";
							if($cbo_costing_per==1)
							{
								$pcs_value=1*12;
							}
							if($cbo_costing_per==2)
							{
								$pcs_value=1*1;
							}
							if($cbo_costing_per==3)
							{
								$pcs_value=2*12;
							}
							if($cbo_costing_per==4)
							{
								$pcs_value=3*12;
							}
							if($cbo_costing_per==5)
							{
								$pcs_value=4*12;
							}
							$oth=0;
							foreach( $data_array as $row )
							{	
							    if($cons_breck_down=="")
								{
										$cons_breck_down.=$row[csf('id')].'_'.$oth.'_'.$cons_value.'_'.$oth.'_'.$pcs_value.'_'.$oth;
								}
								else
								{
										$cons_breck_down.="__".$row[csf('id')].'_'.$oth.'_'.$cons_value.'_'.$oth.'_'.$pcs_value.'_'.$oth;
								}
							}
							return $cons_breck_down;
						}
}
if ($action=="save_update_delet_trim_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pre_cost_trim_cost_dtls", 1 ) ;
		 $id1=return_next_id( "id", "wo_pre_cost_trim_co_cons_dtls", 1 ) ;
		 $field_array="id, job_no,remark, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down, inserted_by, insert_date, status_active,	is_deleted";
		 $field_array1="id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id,item_size, cons, place, pcs,country_id";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			  $txtremark="txtremark_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtsupref="txtsupref_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 if(str_replace("'",'',$$consbreckdown) !='')
			 {
				$consbreckdown= $$consbreckdown;
			 }
			 else
			 {
			 
				 $consbreckdown="'".create_consbreak_down($update_id,str_replace("'",'',$$txtconsdzngmts))."'";
			 }
			 
			 if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$txtremark.",".$$cbogroup.",".$$txtdescription.",".$$txtsupref.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$consbreckdown.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
			//	CONS break down===============================================================================================	
			if(str_replace("'",'',$consbreckdown) !='')
			{
				$consbreckdown_array=explode('__',str_replace("'",'',$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++)
				{
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."')";
					$id1=$id1+1;
					$add_comma++;
				}
			}
           //CONS break down end===============================================================================================
		   $id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_trim_cost_dtls",$field_array,$data_array,0);
		 $rID_in1=sql_insert("wo_pre_cost_trim_co_cons_dtls",$field_array1,$data_array1,1);

		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		check_table_status( $_SESSION['menu_id'],0);
		//=======================sum End =================
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);   
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="job_no*remark*trim_group*description*brand_sup_ref*cons_uom*cons_dzn_gmts*rate*amount*apvl_req*nominated_supp*cons_breack_down*updated_by*update_date*status_active*is_deleted";
		 $field_array="id, job_no,remark, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down, inserted_by, insert_date, status_active,	is_deleted";
		 $field_array1="id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size,cons,place,pcs,country_id";
		 $add_co=0;
		 $add_comma=0;
		 $id=return_next_id( "id","wo_pre_cost_trim_cost_dtls", 1 ) ;
		 $id1=return_next_id( "id", "wo_pre_cost_trim_co_cons_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbogroup="cbogroup_".$i;
			  $txtremark="txtremark_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtsupref="txtsupref_".$i;
			 $cboconsuom="cboconsuom_".$i;
			 $txtconsdzngmts="txtconsdzngmts_".$i;
			 $txttrimrate="txttrimrate_".$i;
			 $txttrimamount="txttrimamount_".$i;
			 $cboapbrequired="cboapbrequired_".$i;
			 $cbonominasupplier="cbonominasupplier_".$i;
			 $cbotrimstatus="cbotrimstatus_".$i;
			 $updateidtrim="updateidtrim_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 if(str_replace("'",'',$$consbreckdown) !='')
			 {
				$consbreckdown= $$consbreckdown;
			 }
			 else
			 {
				 $consbreckdown="'".create_consbreak_down($update_id,str_replace("'",'',$$txtconsdzngmts))."'";
			 }
			if(str_replace("'",'',$$updateidtrim)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateidtrim);
				$data_array_up[str_replace("'",'',$$updateidtrim)] =explode("*",("".$update_id."*".$$txtremark."*".$$cbogroup."*".$$txtdescription."*".$$txtsupref."*".$$cboconsuom."*".$$txtconsdzngmts."*".$$txttrimrate."*".$$txttrimamount."*".$$cboapbrequired."*".$$cbonominasupplier."*".$consbreckdown."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$cbotrimstatus."*0"));
				//	CONS break down===============================================================================================	
				if(str_replace("'",'',$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_pre_cost_trim_co_cons_dtls where  wo_pre_cost_trim_cost_dtls_id =".$$updateidtrim."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						//		 $field_array1="id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id,item_size, cons, pcs";
		
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if ($add_comma!=0) $data_array1 .=",";
						$data_array1 .="(".$id1.",".$$updateidtrim.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
				
			}
			if(str_replace("'",'',$$updateidtrim)=="")
			{
				if ($add_co!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$txtremark.",".$$cbogroup.",".$$txtdescription.",".$$txtsupref.",".$$cboconsuom.",".$$txtconsdzngmts.",".$$txttrimrate.",".$$txttrimamount.",".$$cboapbrequired.",".$$cbonominasupplier.",".$consbreckdown.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbotrimstatus.",0)";
				 //$id=$id+1;
				// $add_co++;
				 //	CONS break down===============================================================================================
				if($$consbreckdown !='')
				{
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						if ($add_comma!=0) $data_array1 .=",";
						$data_array1 .="(".$id1.",".$id.",".$update_id.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
		    $id=$id+1;
		    $add_co++;
			}
		 }
		$rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_trim_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array !="")
		{
			$rID1=sql_insert("wo_pre_cost_trim_cost_dtls",$field_array,$data_array,0);
		}
		$rID_in1=sql_insert("wo_pre_cost_trim_co_cons_dtls",$field_array1,$data_array1,1);
 		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,trim_cons,trim_rate,trim_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtconsdzntrim_sum.",".$txtratetrim_sum.",".$txttrimamount_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="trim_cons*trim_rate*trim_amount";
			$data_array2 ="".$txtconsdzntrim_sum."*".$txtratetrim_sum."*".$txttrimamount_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
}
//*************************************************Trim Cost End ***********************************************
?>
<?
//*************************************************Embellishment Cost Start ***********************************************
if ($action=="show_embellishment_cost_listview")
{
	$data=explode("_",$data);
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$data[0]'");
	if($costing_per==1)
	{
		$header_td="Cons/ 1 Dzn Gmts";
	}
	else if($costing_per==2)
	{
	$header_td="Cons/ 1 Pcs Gmts";	
	}
	else if($costing_per==3)
	{
	$header_td="Cons/ 2 Dzn Gmts";	
	}
	
	else if($costing_per==4)
	{
	$header_td="Cons/ 3 Dzn Gmts";	
	}
	else if($costing_per==5)
	{
	$header_td="Cons/ 4 Dzn Gmts";	
	}
?>
<h3 align="left" class="accordion_h" >+Embellishment Cost</h3> 
       <div id="content_embellishment_cost"  align="center">            
    	<fieldset>
        	<form id="embellishment_7" autocomplete="off">
            	<table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_embellishment_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th  width="100">Type</th><th  width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$save_update=1;
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					$data_array=sql_select("select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pre_cost_embe_cost_dtls where  emb_name!=3 and job_no='$data[0]' order by id");
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pri_quo_embe_cost_dtls where emb_name!=3 and quotation_id='$data[1]' order by id");// quotation_id='$data'
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
					$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);

						
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 150, $emblishment_name_array, "",1," -- Select Item --", $row[csf("emb_name")], "cbotype_loder(".$i.")",$disabled,'1,2,4,5' ); 
									?>
                                    
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 100, $type_array[$row[csf("cboembtype_")]],"", 1, "-- Select --", $row[csf("emb_type")], "check_duplicate(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_emb_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo $row[csf("amount")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 95, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$save_update=0;
						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						$i=0;
						foreach( $emblishment_name_array as $row => $value )
						{
							$i++;
							if($i==3)
							{
								continue;
								
							}
							else
							{
								if($i>3)
								{
									$i=$i-1;
								}
					?>
                    <tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 150, $emblishment_name_array, "",0,"", $row, "cbotype_loder(".$i.")",'','1,2,4,5' ); 
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>">
									<?  
									echo create_drop_down( "cboembtype_".$i, 100, $type_array[$row],"", 1, "-- Select --", "", "check_duplicate(".$i.")","","" ); 
									?>
                                    </td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_emb_cost( <? echo $i;?> )"/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value=""   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 95, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_embellishment_cost(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_embellishment_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                    <? 
					           if($i==3)
								{
									$i++;
								}
							}
						}
					} 
					?>
                </tbody>
                </table>
                <table width="750" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="102" style="border:none"> 
                            </th>
                            <th width="103" style="border:none">
                            </th>
                            <th width="120">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:110px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="750" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", $save_update,0,"reset_form('embellishment_7','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_embellishment_cost_dtls", $save_update,0,"reset_form('embellishment_7','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}

if ($action=="load_drop_down_embtype")
{
	$data=explode('_',$data);
	if($data[0]==1)
	{
		echo create_drop_down( "cboembtype_".$data[1], 95,$emblishment_print_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==2)
	{
		echo create_drop_down( "cboembtype_".$data[1], 95,$emblishment_embroy_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==3)
	{
		echo create_drop_down( "cboembtype_".$data[1], 95,$emblishment_wash_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	if($data[0]==4)
	{
		echo create_drop_down( "cboembtype_".$data[1], 95,$emblishment_spwork_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
	
	if($data[0]==5)
	{
		echo create_drop_down( "cboembtype_".$data[1], 95,$emblishment_gmts_type,"", 1, "-- Select --", "", "check_duplicate(".$data[1].")","","" ); 
		die;
	}
}

if ($action=="save_update_delet_embellishment_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;
		 $field_array="id,job_no,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_embe_cost_dtls",$field_array,$data_array,0);
		//=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,emb_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="emb_amount";
			$data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		check_table_status( $_SESSION['menu_id'],0);
		//=======================sum End =================
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="job_no*emb_name*emb_type*cons_dzn_gmts*rate*amount*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,job_no,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
                $id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));
			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID1=sql_insert("wo_pre_cost_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="id,job_no,emb_amount";
		$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
		$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="emb_amount";
		$data_array2 ="".$txtamountemb_sum."";
		$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
        check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	
	
}
//*************************************************Embellishment Cost End ***********************************************
//*************************************************Wash Cost Start ***********************************************

if ($action=="show_wash_cost_listview")
{
	$data=explode("_",$data);
	
	//$data=explode("_",$data);
	$header_td="";
	$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$data[0]'");
	if($costing_per==1)
	{
		$header_td="Cons/ 1 Dzn Gmts";
	}
	else if($costing_per==2)
	{
	$header_td="Cons/ 1 Pcs Gmts";	
	}
	else if($costing_per==3)
	{
	$header_td="Cons/ 2 Dzn Gmts";	
	}
	
	else if($costing_per==4)
	{
	$header_td="Cons/ 3 Dzn Gmts";	
	}
	else if($costing_per==5)
	{
	$header_td="Cons/ 4 Dzn Gmts";	
	}

	$conversion_from_chart=return_field_value("conversion_from_chart", "variable_order_tracking", "company_name='$data[3]'  and variable_list=21 and status_active=1 and is_deleted=0");
	if($conversion_from_chart=="")
	{
		$conversion_from_chart=2;
	}
?>
<h3 align="left" class="accordion_h" >+Embellishment Cost</h3> 
       <div id="content_embellishment_cost"  align="center">            
    	<fieldset>
        	<form id="wash_7" autocomplete="off">
            	<table width="750" cellspacing="0" class="rpt_table" border="0" id="tbl_wash_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Name</th><th  width="100">Type</th><th  width="90"><? echo $header_td; ?></th><th width="90">Rate</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$save_update=1;
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					$data_array=sql_select("select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pre_cost_embe_cost_dtls where  emb_name=3 and job_no='$data[0]' order by id");
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from  wo_pri_quo_embe_cost_dtls where emb_name=3 and quotation_id='$data[1]' order by id");// quotation_id='$data'
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
					//$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);

						
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="embellishment_1" align="center">
                                    <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 150, $emblishment_name_array, "",1," -- Select Item --", $row[csf("emb_name")], "",1,'' ); 
									?>
                                    
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>"><?  echo create_drop_down( "cboembtype_".$i, 100, $emblishment_wash_type,"", 1, "-- Select --", $row[csf("emb_type")], "check_duplicate_wash(".$i.")",$disabled,"" ); ?></td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("cons_dzn_gmts")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_wash_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up('".$i."')";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo $row[csf("amount")];  ?>"  <? if($disabled==0){echo "";}else{echo "disabled";}?>   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 95, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?>  )"  <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />  
                                    <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("charge_lib_id")]; ?>"  />                                    
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$save_update=0;
						//$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$blank_array);
						$i=0;
						$i++;
						/*foreach( $emblishment_name_array as $row => $value )
						{
							
							if($i==3)
							{
								continue;
								
							}
							else
							{
								if($i>3)
								{
									$i=$i-1;
								}*/
					?>
                    <tr id="embellishment_1" align="center">
                                   <td id="cboembnametd_<? echo $i;?>">
									<? 
									echo create_drop_down( "cboembname_".$i, 150, $emblishment_name_array, "",1,"--Select--", 3, "",1,'' ); 
									?>
                                    </td>
                                    <td id="embtypetd_<? echo $i;?>">
									<?  
									echo create_drop_down( "cboembtype_".$i, 100, $emblishment_wash_type,"", 1, "-- Select --", "", "check_duplicate_wash(".$i.")","","" ); 
									?>
                                    </td>
                                   <td id="txtembconsdzngmtstd_<? echo $i;?>">
                                    <input type="text" id="txtembconsdzngmts_<? echo $i; ?>"  name="txtembconsdzngmts_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_wash_cost( <? echo $i;?> )"/>
                                    </td>
                                   <td id="txtembratetd_<? echo $i;?>">
                                    <input type="text" id="txtembrate_<? echo $i; ?>"  name="txtembrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_wash_cost( <? echo $i;?> )" onClick="<? if($conversion_from_chart==1){ echo "set_wash_charge_unit_pop_up(1)";}else{echo '';}?>" <? if($conversion_from_chart==1){echo "redonly";}else{echo "";}?>/>
                                    </td>
                                    <td id="txtembamounttd_<? echo $i;?>">
                                    <input type="text" id="txtembamount_<? echo $i; ?>"  name="txtembamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value=""   />
                                    </td>
                                    
                                    <td id="cboembstatustd_<? echo $i;?>"><? echo create_drop_down( "cboembstatus_".$i, 95, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td id="buttontd_<? echo $i;?>">
                                    <input type="button" id="increaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_wash_cost(<? echo $i; ?>,<? echo $conversion_from_chart; ?>)" />
                                    <input type="button" id="decreaseemb_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_wash_cost' );" />
                                    <input type="hidden" id="embupdateid_<? echo $i; ?>" name="embupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value=""  />
                                    <input type="hidden" id="embratelibid_<? echo $i; ?>" name="embratelibid_<? echo $i; ?>"  class="text_boxes" style="width:20px"  readonly/>                                    </td> 
                                </tr>
                    <? 
					           /*if($i==3)
								{
									$i++;
								}*/
							//}
						//}
					} 
					?>
                </tbody>
                </table>
                <table width="750" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="251">Sum</th>
                            <th  width="102" style="border:none"> 
                            </th>
                            <th width="103" style="border:none">
                            </th>
                            <th width="120">
                            <input type="text" id="txtamountemb_sum"  name="txtamountemb_sum" class="text_boxes_numeric" style="width:110px"  readonly />
                            </th>
                            <th width="95"></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="750" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", $save_update,0,"reset_form('wash_7','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_wash_cost_dtls", $save_update,0,"reset_form('wash_7','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}

if($action=="wash_chart_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(id,rate)
{
	//var data=data.split("_");
	document.getElementById('charge_id').value=id;
	document.getElementById('charge_value').value=rate;
	parent.emailwindow.hide();

}
function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="charge_id" name="charge_id" />
<input type="hidden" id="charge_value" name="charge_value" />



<?


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
	$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,9=>$row_status);
	//echo "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,8) and process_id=$cbotypeconversion and comapny_id=$cbo_company_name";
	//echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Width/Dia type,In House Rate,UOM,Rate type,Status", "100,150,70,70,70,80,60,80,60,50","900","250",1, "select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id =7  and comapny_id=$cbo_company_name", "js_set_value", "id,in_house_rate","", 1, "comapny_id,0,process_type_id,process_id,color_id,width_dia_id,0,uom_id,rate_type_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,0,0,0' );
	?>
	<table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
     <thead>
     <th width="50">SL</th>
     <th width="100">Company Name</th>
     <th width="150">Const. Compo.</th>
     <th width="70">Process Type</th>
     <th width="70">Process Name</th>
     <th width="70">Color</th>
     <th width="80">Width/Dia type</th>
     
     <th width="60">In House Rate</th>
     <th width="80">UOM</th>
     <th width="60">Rate type</th>
     <th>Status</th>
     </thead>
     </table>
     <div style=" width:917; overflow:scroll-y; max-height:300px">
     <table width="900" class="rpt_table" border="1" rules="all" id="list_view" cellpadding="0" cellspacing="0">
     <?
	 $sql_data=sql_select("select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id =7  and comapny_id=$cbo_company_name");
	 $i=1;
	 foreach($sql_data as $row)
	 {
		 if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		
	 ?>
     <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]?>, <? echo $row[csf("in_house_rate")]; ?>)" id="tr_<? echo $row[csf("id")];  ?>">
     <td width="50"><? echo $i; ?></td>
     <td width="100"><? echo $company_arr[$row[csf("comapny_id")]]; ?></td>
     <td width="150"><? echo $row[csf("const_comp")]; ?></td>
     <td width="70"><? echo $process_type[$row[csf("process_type_id")]]; ?></td>
      <td width="70"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
     <td width="70"><? echo $color_library_arr[$row[csf("color_id")]]; ?></td>
     <td width="80"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
     
     <td width="60"><? echo $row[csf("in_house_rate")]; ?></td>
     <td width="80"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
     <td width="60"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
     <td><? echo $row_status[$row[csf("status_active")]]; ?></td>
     </tr>
     <?
	  $i++;
	 }
	 ?>
     <script>
	 setFilterGrid("list_view",-1)
	 toggle( "tr_"+"<? echo $embratelibid ?>", '#FFFFCC');
	 </script>
     </table>
     </div>
    <?

?>
</form>
</div>
</body>
</html>
<?
}

if ($action=="save_update_delet_wash_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	     $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
         if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;
		 $field_array="id,job_no,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_embe_cost_dtls",$field_array,$data_array,0);
		//=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,emb_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="emb_amount";
			$data_array2 ="".$txtamountemb_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		check_table_status( $_SESSION['menu_id'],0);
		//=======================sum End =================
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="job_no*emb_name*emb_type*cons_dzn_gmts*rate*amount*charge_lib_id*updated_by*update_date*status_active*is_deleted";
		 $field_array="id,job_no,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboembname="cboembname_".$i;
			 $cboembtype="cboembtype_".$i;
			 $txtembconsdzngmts="txtembconsdzngmts_".$i;
			 $txtembrate="txtembrate_".$i;
			 $txtembamount="txtembamount_".$i;
			 $cboembstatus="cboembstatus_".$i;
			 $embupdateid="embupdateid_".$i;
			 $embratelibid="embratelibid_".$i;
			if(str_replace("'",'',$$embupdateid)!="")
			{
                $id_arr[]=str_replace("'",'',$$embupdateid);
				$data_array_up[str_replace("'",'',$$embupdateid)] =explode(",",("".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0"));
			}
			if(str_replace("'",'',$$embupdateid)=="")
			{
				
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$$cboembname.",".$$cboembtype.",".$$txtembconsdzngmts.",".$$txtembrate.",".$$txtembamount.",".$$embratelibid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboembstatus.",0)";
				$id=$id+1;
				$add_comma++;
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_embe_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID1=sql_insert("wo_pre_cost_embe_cost_dtls",$field_array,$data_array,0);
		 }

		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="id,job_no,emb_amount";
		$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtamountemb_sum.")";
		$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="emb_amount";
		$data_array2 ="".$txtamountemb_sum."";
		$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
        check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}
//**************************************************************************Wash Cost End**********************************************
?>

<?
//*************************************************Commision Cost Start ***********************************************
if ($action=="show_commission_cost_listview")
{
	$data=explode("_",$data);
	
?>
<h3 align="left" class="accordion_h">+Commission Cost</h3> 
       <div id="content_commission_cost" align="center">            
    	<fieldset>
        	<form id="commission_8" autocomplete="off">
            	<table width="580" cellspacing="0" class="rpt_table" border="0" id="tbl_commission_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Particulars</th> <th width="100">Commn. Base</th> <th width="90">Commn Rate</th> <th width="110">Amount</th> <th width="">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$save_update=1;
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					$data_array=sql_select("select id, job_no, particulars_id, commission_base_id, commision_rate,commission_amount,status_active from  wo_pre_cost_commiss_cost_dtls where job_no='$data[0]' order by id");// quotation_id='$data'
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, quotation_id, particulars_id, commission_base_id, commision_rate,commission_amount,status_active from  wo_pri_quo_commiss_cost_dtls where quotation_id='$data[1]' order by id");// quotation_id='$data'
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="commissiontr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboparticulars_".$i, 150, $commission_particulars, "",1," -- Select Item --", $row[csf("particulars_id")], "",$disabled,'' ); 
									//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index )
									?>
                                    
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_".$i, 100, $commission_base_array,"", 1, "-- Select --", $row[csf("commission_base_id")], "calculate_commission_cost(".$i.")",$disabled,"" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_<? echo $i; ?>"  name="txtcommissionrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("commision_rate")];  ?>" onChange="calculate_commission_cost( <? echo $i;?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_<? echo $i; ?>"  name="txtcommissionamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo $row[csf("commission_amount")];  ?>" <? if($disabled==0){echo "";}else{echo "disabled";}?> readonly />
                                    </td>
                                    
                                    <td>
									<? echo create_drop_down( "cbocommissionstatus_".$i, 95, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?>
                                    <input type="hidden" id="commissionupdateid_<? echo $i; ?>" name="commissionupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />   
                                    </td>  
                                    
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						$save_update=0;
					?>
                    <tr id="commissiontr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboparticulars_1", 150, $commission_particulars, "",0,"", $row, '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_1", 100, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(1)","","" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_1"  name="txtcommissionrate_1" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_commission_cost(1 )"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_1"  name="txtcommissionamount_1" class="text_boxes_numeric" style="width:110px" value=""  readonly />
                                    </td>
                                    
                                    <td>
									<? echo create_drop_down( "cbocommissionstatus_1", 95, $row_status,"", 0, "0", '', '','','' );  ?>
                                    <input type="hidden" id="commissionupdateid_1" name="commissionupdateid_1"  class="text_boxes" style="width:20px" value=""  />
                                    </td>  
                                    
                                </tr>
                                <tr id="commissiontr_2" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboparticulars_2", 150, $commission_particulars, "",0,"", $row, '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbocommissionbase_2", 100, $commission_base_array,"", 1, "-- Select --", "", "calculate_commission_cost(2)","","" ); ?></td>
                                   
                                   <td>
                                    <input type="text" id="txtcommissionrate_2"  name="txtcommissionrate_2" class="text_boxes_numeric" style="width:90px" value="" onChange="calculate_commission_cost(2)"/>
                                    </td>
                                    <td>
                                    <input type="text" id="txtcommissionamount_2"  name="txtcommissionamount_2" class="text_boxes_numeric" style="width:110px" value=""   />
                                    </td>
                                    
                                    <td>
									<? echo create_drop_down( "cbocommissionstatus_2", 95, $row_status,"", 0, "0", '', '','','' );  ?>
                                    <input type="hidden" id="commissionupdateid_2" name="commissionupdateid_2"  class="text_boxes" style="width:20px" value=""  />
                                    </td>  
                                    
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="580" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="150" style="border:none"></th> 
                            <th  width="100" align="right" style="border:none">SUM</th>
                            <th width="90">
                            <input type="text" id="txtratecommission_sum"  name="txtratecommission_sum" class="text_boxes_numeric" style="width:90px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcommission_sum"  name="txtamountcommission_sum" class="text_boxes_numeric" style="width:110px"  readonly />
                            </th>
                            <th></th>
                            
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="580" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", $save_update,0,"reset_form('commission_8','','',0)",7) ;
					    }
						else
						{
						echo load_submit_buttons( $permission, "fnc_commission_cost_dtls", $save_update,0,"reset_form('commission_8','','',0)",7) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}


if ($action=="save_update_delet_commission_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pre_cost_commiss_cost_dtls", 1 ) ;
		 $field_array="id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_commiss_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="id,job_no,commis_rate,commis_amount";
		$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
		$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		//$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="commis_rate*commis_amount";
		$data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
		$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="particulars_id*commission_base_id*commision_rate*commission_amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pre_cost_commiss_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			 $cboparticulars="cboparticulars_".$i;
			 $cbocommissionbase="cbocommissionbase_".$i;
			 $txtcommissionrate="txtcommissionrate_".$i;
			 $txtcommissionamount="txtcommissionamount_".$i;
			 $cbocommissionstatus="cbocommissionstatus_".$i;
			 $commissionupdateid="commissionupdateid_".$i;
			if(str_replace("'",'',$$commissionupdateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$commissionupdateid);
			    $data_array_up[str_replace("'",'',$$commissionupdateid)]=explode(",",("".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0"));
			}
			if(str_replace("'",'',$$commissionupdateid)=="")
			{
			    if ($add_comma!=0) $data_array .=",";
			    $data_array .="(".$id.",".$update_id.",".$$cboparticulars.",".$$cbocommissionbase.",".$$txtcommissionrate.",".$$txtcommissionamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocommissionstatus.",0)";
			    $id=$id+1;
				$add_comma++;
			}
		 }
         $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_commiss_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID=sql_insert("wo_pre_cost_commiss_cost_dtls",$field_array,$data_array,0);
		 }
 		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="id,job_no,commis_rate,commis_amount";
		$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtratecommission_sum.",".$txtamountcommission_sum.")";
		$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
		$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
		$field_array2="commis_rate*commis_amount";
		$data_array2 ="".$txtratecommission_sum."*".$txtamountcommission_sum."";
		$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		$tot_com_amount=update_comarcial_cost($update_id,$cbo_company_name);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "1**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_job_no[0]."**".$rID."**".$tot_com_amount;
			}
		}
		disconnect($con);
		die;
	}
	
	
	
}

//*************************************************Commision Cost End ***********************************************
?>

<?
//*************************************************Comarcial Cost Start ***********************************************
if ($action=="show_comarcial_cost_listview")
{
$data=explode("_",$data);
$commercial_cost_method=return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$data[3]."  and variable_list=27 and status_active=1 and is_deleted=0");
if($commercial_cost_method=="" || $commercial_cost_method==0)
{
	$commercial_cost_method=1;
}

$commercial_cost_percent=return_field_value("commercial_cost_percent", "variable_order_tracking", "company_name=".$data[3]."  and variable_list=27 and status_active=1 and is_deleted=0");



if($commercial_cost_percent=="" || $commercial_cost_percent==0)
{
	$commercial_cost_percent=0;
}
$editable=return_field_value("editable", "variable_order_tracking", "company_name=".$data[3]."  and variable_list=27 and status_active=1 and is_deleted=0");

$price_dzn=$data[4];
$fob_value=$data[4]-$data[5];
$amount=0;
if($commercial_cost_method==1)
{
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=def_number_format($row[csf("amount")],5,"");
	}
}
if($commercial_cost_method==2)
{
	
		$amount=def_number_format($price_dzn,5,"");
	
}
if($commercial_cost_method==3)
{
	
		$amount=def_number_format($fob_value,5,"");
	
}
$com_amount=def_number_format(($amount*($commercial_cost_percent/100)),5,"");

?>
<h3 align="left" class="accordion_h" >+Commercial Cost</h3> 
       <div id="content_comarcial_cost"  align="center">            
    	<fieldset>
        	<form id="comarcial_9" autocomplete="off">
              <input type="text" id="txt_commercial_cost_method" value="<? echo $commercial_cost_method;?>"/>
            	<table width="540" cellspacing="0" class="rpt_table" border="0" id="tbl_comarcial_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="150">Item</th><th width="90"> Rate In %</th><th width="110">Amount</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$save_update=1;
					$approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$data[0]'");
					if($approved==1)
					{
					$disabled=1;
					}
					else
					{
					$disabled=0;
					}
					if($editable==1)
					{
					$readonly=0	;
					}
					else
					{
						$readonly=1	;
					}
					$data_array=sql_select("select id, job_no, item_id , rate,amount,status_active from  wo_pre_cost_comarci_cost_dtls where job_no='$data[0]' order by id");
					if(count($data_array)<1 && $data[2]==1)
					{
					$data_array=sql_select("select id, quotation_id, item_id, rate,amount,status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id='$data[1]' order by id");
					$save_update=0;
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="comarcialtr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboitem_".$i, 150, $camarcial_items, "",1," -- Select Item --", $row[csf("item_id")], "",$disabled,'' ); 
									//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index )
									?>
                                    </td>
                                   
                                   <td>
                                    <input type="text" id="txtcomarcialrate_<? echo $i; ?>"  name="txtcomarcialrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf("rate")];  ?>" onChange="calculate_comarcial_cost( <? echo $i;?>,'cal_amount' )" <? if($disabled==0){echo "";}else{echo "disabled";}?> <? if($readonly==0){echo "";}else{echo "readonly";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_<? echo $i; ?>"  name="txtcomarcialamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo $row[csf("amount")];  ?>" onChange="calculate_comarcial_cost(<? echo $i;?>,'cal_rate')"   <? if($disabled==0){echo "";}else{echo "disabled";}?>  <? if($readonly==0){echo "";}else{echo "readonly";}?> />
                                    </td>
                                    
                                    <td ><? echo create_drop_down( "cbocomarcialstatus_".$i, 95, $row_status,"", 0, "0", $row[csf("status_active")], '',$disabled,'' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(<? echo $i; ?> )" <? if($disabled==0){echo "";}else{echo "disabled";}?> />
                                    <input type="button" id="decreasecomarcial_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,'tbl_comarcial_cost' );" <? if($disabled==0){echo "";}else{echo "disabled";}?>/>
                                    <input type="hidden" id="comarcialupdateid_<? echo $i; ?>" name="comarcialupdateid_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo  $row[csf("id")]; ?>"  />                                      
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
					$save_update=0;
					?>
                    <tr id="comarcialtr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 150, $camarcial_items, "",0,"", 4, '','','' ); 
									?>
                                    </td>
                                   
                                   <td>
                                    <input type="text" id="txtcomarcialrate_1"  name="txtcomarcialrate_1" class="text_boxes_numeric" style="width:90px"  onChange="calculate_comarcial_cost(1,'cal_amount')" value="<? echo $commercial_cost_percent; ?>" <? if($readonly==0){echo "";}else{echo "readonly";}?> />
                                    </td>
                                    <td>
                                    <input type="text" id="txtcomarcialamount_1"  name="txtcomarcialamount_1" class="text_boxes_numeric" style="width:110px"   onChange="calculate_comarcial_cost(1,'cal_rate')"  value="<? echo $com_amount; ?>" <? if($readonly==0){echo "";}else{echo "readonly";}?> />
                                    </td>
                                    
                                    <td width="95"><? echo create_drop_down( "cbocomarcialstatus_1", 95, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="button" id="increasecomarcial_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_comarcial_cost(1 )" />
                                    <input type="button" id="decreasecomarcial_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_comarcial_cost' );" />
                                    <input type="hidden" id="comarcialupdateid_1" name="comarcialupdateid_1"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="540" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="150">Sum</th>
                            
                            <th width="90">
                            <input type="text" id="txtratecomarcial_sum"  name="txtratecomarcial_sum" class="text_boxes_numeric" style="width:90px"  readonly />
                            </th>
                            <th width="110">
                            <input type="text" id="txtamountcomarcial_sum"  name="txtamountcomarcial_sum" class="text_boxes_numeric" style="width:110px"  readonly />
                            </th>
                            <th width="95" style="border:none"></th>
                            <th width="" style="border:none"></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="540" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						<?
						if ( count($data_array)>0)
					    {
							echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", $save_update,0,"reset_form('comarcial_9','','',0)",9) ;
					    }
						else
						{
							echo load_submit_buttons( $permission, "fnc_comarcial_cost_dtls", $save_update,0,"reset_form('comarcial_9','','',0)",9) ;
						}
						?>  
                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>

<?
}

if($action=="sum_fab_yarn_trim_value")
{
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no ='$data' and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=$row[csf("amount")];
	}
	echo $amount;
	die;
}

/*function update_comarcial_cost($job_no)
{
	$amount=0;
	$data_array=sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
	foreach( $data_array as $row )
	{
		$amount=def_number_format($row[csf("amount")],5,"");
	}
	$tot_com_amount=0;
	
	$data_array1=sql_select("select id,rate from wo_pre_cost_comarci_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
	foreach( $data_array1 as $row1 )
	{
		$com_amount=def_number_format(($amount*($row1[csf("rate")]/100)),5,"");
		$tot_com_amount+=$com_amount;
		$rID_de=execute_query( "update  wo_pre_cost_comarci_cost_dtls set amount=$com_amount where id='".$row1[csf("id")]."'",1 );
	}
	//execute_query( "update  wo_pre_cost_dtls set comm_cost =$tot_com_amount where job_no =$job_no",1 );
	execute_query( "update  wo_pre_cost_sum_dtls set comar_amount  =$tot_com_amount where job_no =$job_no",1 );
	
	
}*/

if ($action=="save_update_delet_comarcial_cost_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_pre_cost_comarci_cost_dtls", 1 ) ;
		 $field_array="id,job_no,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			 if ($i!=1) $data_array .=",";
			 $data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
			 $id=$id+1;
		 }
		 $rID=sql_insert("wo_pre_cost_comarci_cost_dtls",$field_array,$data_array,0);
		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,comar_rate,comar_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);   
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)
	{
	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		 $field_array_up="item_id*rate*amount*updated_by*update_date*status_active*is_deleted ";
		 $field_array="id,job_no,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 $add_comma=0;
		 $id=return_next_id( "id", "wo_pre_cost_comarci_cost_dtls", 1 ) ;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			 $cboitem="cboitem_".$i;
			 $txtcomarcialrate="txtcomarcialrate_".$i;
			 $txtcomarcialamount="txtcomarcialamount_".$i;
			 $cbocomarcialstatus="cbocomarcialstatus_".$i;
			 $comarcialupdateid="comarcialupdateid_".$i;
			if(str_replace("'",'',$$comarcialupdateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$comarcialupdateid);
			    $data_array_up[str_replace("'",'',$$comarcialupdateid)] =explode(",",("".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0"));
			}
			if(str_replace("'",'',$$comarcialupdateid)=="")
			{
			    if ($add_comma!=0) $data_array .=",";
			    $data_array .="(".$id.",".$update_id.",".$$cboitem.",".$$txtcomarcialrate.",".$$txtcomarcialamount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbocomarcialstatus.",0)";
			    $id=$id+1;
			    $add_comma++;
			}
		 }
         $rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_comarci_cost_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			 $rID=sql_insert("wo_pre_cost_comarci_cost_dtls",$field_array,$data_array,0);
		 }
 		 //=======================sum=================
		$wo_pre_cost_sum_dtls_job_no="";
		$queryText= "select job_no from  wo_pre_cost_sum_dtls where job_no =$update_id";
		$nameArray=sql_select( $queryText );
	    foreach ($nameArray as $result)
		{
		    $wo_pre_cost_sum_dtls_job_no= $result[csf('job_no')];
		}
		if($wo_pre_cost_sum_dtls_job_no=="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="id,job_no,comar_rate,comar_amount";
			$data_array2="(".$wo_pre_cost_sum_dtls_id.",".$update_id.",".$txtratecomarcial_sum.",".$txtamountcomarcial_sum.")";
			$rID2=sql_insert("wo_pre_cost_sum_dtls",$field_array2,$data_array2,1);
		}
		if($wo_pre_cost_sum_dtls_job_no!="")
		{
			$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
			$field_array2="comar_rate*comar_amount";
			$data_array2 ="".$txtratecomarcial_sum."*".$txtamountcomarcial_sum."";
			$rID2=sql_update("wo_pre_cost_sum_dtls",$field_array2,$data_array2,"job_no","".$update_id."",1);
		}
		//=======================sum End =================
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "1**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);   
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	
	
}
//*************************************************Comarcial Cost End ***********************************************
?>

<?
/*if($action=="create_quotation_id_list_view")
{
	
	$data=explode('_',$data);
	//print_r($data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else { echo "Please Select Buyer First."; die; }
	
	//if ($data[2]!=0) $buyer=" and a.buyer_name='$data[2]'"; else { echo "Please Select CHEK First."; die; } die;
	
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from  wo_price_quotation a where status_active=1  and is_deleted=0 $company $buyer order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
	
	
} */
?>









<?php

// report generate here 
// report start

if($action=="preCostRpt")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	$po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
	 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id  and a.job_no =".$txt_job_no."   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
	$po_qty+=$sql_po_row[csf('order_quantity')];
	$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
	$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
	}
	
	$fab_knit_req_kg_avg=0;
	$fab_woven_req_yds_avg=0;
	if($db_type==0)
	{
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date order by a.job_no";  
	}
	if($db_type==2)
	{
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";  
	}
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Pre- Costing</div>
	<?
	$uom="";
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=0;
		
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
			$job_in_ref.=$val[csf('grouping')].",";
			$job_in_file.=$val[csf('file_no')].",";
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
		$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
		foreach ($job_ref as $ref)
		{
			$ref_cond.=", ".$ref;
		}
		$file_con='';
		foreach ($job_file as $file)
		{
			if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
		}
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td colspan="3"><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                        
                        <td>Job Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo $row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="3"><? echo $job_in_orders; ?></td>
                        <td>Plun Cut Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo $po_plun_cut_qty/$total_set_qnty." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_req_kg")];$fab_knit_req_kg_avg+=$row[csf("fab_knit_req_kg")]; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_req_yds")];$fab_woven_req_yds_avg+= $row[csf("fab_woven_req_yds")];?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $row[csf("fab_yarn_req_kg")] ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                    <tr>
                    <td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_fin_req_kg")] ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_fin_req_yds")]; ?>(Yds)</b></td>
                    	<td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                        
                    </tr>
                    <tr>
                    <td>Internal Ref</td>
                    <td colspan="2"><b><? echo ltrim($ref_cond,", "); ?> </b></td>
                    <td>File No</td>
                    <td colspan="2"><b><? echo $file_cond; ?></b></td>
                    </tr>
                </table>
            <?	
			
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			
	}//end first foearch
	
	
	//id, fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,comar_amount,commis_rate,commis_amount	 	
	// 	costing_per_id 	order_uom_id 	fabric_cost 	fabric_cost_percent 	trims_cost 	trims_cost_percent 	embel_cost 	
	//embel_cost_percent 	comm_cost 	comm_cost_percent 	commission 	commission_percent 	lab_test 	lab_test_percent 	inspection 	
	//inspection_percent 	cm_cost,cm_cost_percent 	freight,freight_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent
	// 	price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche	
	//margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
	
	
	//start	all summary report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$others_cost_value=0;
	$fabric_cost=0;
	$trims_cost=0;
	$embel_cost=0;
	$comm_cost=0;
	$commission=0;
	$lab_test=0;
	$inspection=0;
	$cm_cost=0;
	$freight=0;
	$currier_pre_cost=0;
	$certificate_pre_cost=0;
	$common_oh=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">SL</td>
                    <td width="300">Particulars</td>
                    <td width="100">Cost</td>
                    <td width="100">Amount (USD)</td>
                    <td width="180">% to Ord. Value</td>                     
                </tr>
            <?
            $sl=0;
            foreach( $data_array as $row )
            { 
			$fabric_cost=$row[csf("fabric_cost")];
			$trims_cost=$row[csf("trims_cost")];
			$embel_cost=$row[csf("embel_cost")];
			$comm_cost=$row[csf("comm_cost")];
			$commission=$row[csf("commission")];
			$lab_test=$row[csf("lab_test")];
			$inspection=$row[csf("inspection")];
			$cm_cost=$row[csf("cm_cost")];
			$freight=$row[csf("freight")];
			$currier_pre_cost=$row[csf("currier_pre_cost")];
			$certificate_pre_cost=$row[csf("certificate_pre_cost")];
			$common_oh=$row[csf("common_oh")];
				$sl=$sl+1;
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				if($zero_value==1)
				{
				
?>	
                <tr>
                    <td><? echo $sl; ?></td>
                    <td align="left"><b>Order Price/<? echo $costing_for; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right" rowspan="13">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); ?>%</td>
                </tr>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Gmts Wash</td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commission Cost</td>
                    <td align="right"><? echo number_format($row[csf("commission")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("commission_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); ?>%</td>
                 </tr>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); ?>%</td>
                 </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Currier Cost</td>
                    <td align="right"><? echo number_format($row[csf("currier_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("currier_percent")],2); ?>%</td>
                 </tr>
                  <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Certificate Cost</td>
                    <td align="right"><? echo number_format($row[csf("certificate_pre_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("certificate_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (2:11)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($row[csf("total_cost")],4); ?></b></td>
                    <td align="center"><b><? echo number_format($row[csf("total_cost_percent")],2); ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_for; ?> (1-12)</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_dzn")],4); ?></td>
                    <td align="center"><b><? $margin_dzn_percent=($row[csf("margin_dzn")]/$row[csf("price_dzn")])*100; echo number_format($margin_dzn_percent,2); ?>%</b></td>
                </tr>
                
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Price / <? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("price_pcs_or_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("total_cost")]/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_pcs_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
 
            <?
				}
				else
				{
					?>
                 <tr>
                    <td><? echo $sl; ?></td>
                    <td align="left"><b>Order Price/<? echo $costing_for; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? echo "100.00%"; ?></td>
                </tr>
                <?
				if($row[csf("fabric_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("trims_cost")]!=0)
				{
				?>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("embel_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("wash_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Gmts Wash</td>
                    <td align="right"><? echo number_format($row[csf("wash_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("wash_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("comm_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("commission")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commission Cost</td>
                    <td align="right"><? echo number_format($row[csf("commission")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("commission_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("lab_test")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); ?>%</td>
                 </tr>
                <?
				}
				if($row[csf("inspection")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				if($row[csf("cm_cost")]!=0)
				{
				?>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				if($row[csf("freight")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); ?>%</td>
                 </tr>
                <?
				}
				if($row[csf("common_oh")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (2:<? echo $sl-1;?>)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($row[csf("total_cost")],4); ?></b></td>
                    <td align="center"><b><? echo number_format($row[csf("total_cost_percent")],2); ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_for; ?> (1-<? echo $sl-1;?>)</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_dzn")],4); ?></td>
                    <td align="center"><b><? $margin_dzn_percent=($row[csf("margin_dzn")]/$row[csf("price_dzn")])*100; echo number_format($margin_dzn_percent,2); ?>%</b></td>
                </tr>
                
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Price / <? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("price_pcs_or_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("total_cost")]/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_pcs_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                    <?
				}
            }
            ?>
            </table>
      </div>
      <?
	//End all summary report here -------------------------------------------
	
	
	
	$sql = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons,avg_cons_yarn, fabric_source,gsm_weight, rate, amount,avg_finish_cons,status_active   
			from wo_pre_cost_fabric_cost_dtls 
			where job_no=".$txt_job_no."";
			
			
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					 <td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$knit_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					<td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$woven_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/Dzn</td>
							<td width="100">Avg. Fab. Cons/Dzn</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here
		$fab_knit_req_kg_avg_amount=$knit_subtotal_amount/$knit_subtotal_avg_cons*$fab_knit_req_kg_avg;
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
					/*$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_knit_req_kg_avg_amount,4).'</td>
					</tr>';*/
					if($zero_value==1)
					{
  		               echo $knit_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $knit_fab;
						}
						else
						{
							echo "";
						}
						
					}
		
		//woven fabrics table here 
		$fab_woven_req_yds_avg_amount=$woven_subtotal_amount/$woven_subtotal_avg_cons*$fab_woven_req_yds_avg;
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_woven_req_yds_avg_amount,4).'</td>
					</tr>';*/
   					$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total (Avg)</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($fab_woven_req_yds_avg_amount+$fab_knit_req_kg_avg_amount),4).'</td>
					</tr></table></div>';*/
        // echo $woven_fab; 
		 if($zero_value==1)
					{
  		               echo $woven_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $woven_fab;
						}
						else
						{
							echo "";
						}
						
					}
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//oracle 
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio , sum(cons_qnty) as cons_qnty, sum(avg_cons_qnty) as avg_cons_qnty,  rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		if($zero_value==1)
		{
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Avg.Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_avg_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                     <td align="right"><? echo number_format($row[csf("avg_cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				  $total_avg_qnty += $row[csf("avg_cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td align="right"><? echo number_format($total_avg_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	  }
	  else
	  {
		  if($fabric_cost>0)
		  {
		  ?>
           <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                     <td width="100">Avg. Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                     <td align="right"><? echo number_format($row[csf("avg_cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				  $total_avg_qnty += $row[csf("avg_cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td align="right"><? echo number_format($total_avg_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	      $grand_total_amount +=$total_amount;
		  }
		  else
		  {
			 echo ""; 
		  }
	  }
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id,a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no." ";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Avg. Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("avg_req_qnty")],4); ?></td>
                     <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="5">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="5">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	}
	else
	{
		if($fabric_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Avg. Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                     <td align="right"><? echo number_format($row[csf("avg_req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="5">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="5">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($trims_cost>0)
		{
	?>
    <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
	<?
		}
		else
		{
		   echo "";	
		}
	}
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($embel_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate In %</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($comm_cost>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($commission>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
		   echo "";	
		}
		
	}
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
   	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($lab_test>0 || $inspection>0 || $cm_cost>0 || $freight>0 || $common_oh>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
				if($row[csf("lab_test")]>0)
				{
  			?>	 


                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("inspection")]>0)
				{
				?>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("cm_cost")]>0)
				{
				?>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("freight")]>0)
				{
				?>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                 <?
				}
				if($row[csf("common_oh")]>0)
				{
				?>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
				}
                 $total_amount += $row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Other Components  Part report here -------------------------------------------	  
  
  
  	
  	//start	CM on Net Order Value part report here -------------------------------------------
    	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row );
	$order_net_value = 0;
	?> 
 
        <div style="margin-top:15px">
        <div style="float:left">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:420px;text-align:center;" rules="all">
            <label><b>CM on Net Order Value</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">%</td>
                </tr>            
                <tr>
                    <td align="left">Order Value </td>
                    <td align="right"><? echo number_format($order_values,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Commission </td>
                    <td align="right"><? $less_commission = $row[csf("commission")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_commission,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Commercial Cost </td>
                    <td align="right"><? $less_commercial = $row[csf("comm_cost")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_commercial,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Freight</td>
                    <td align="right"><? $less_freight = $row[csf("freight")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_freight,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Net Order Value</td>
                    <td align="right"><? $order_net_value=$order_values-($less_commission+$less_commercial+$less_freight);echo number_format($order_net_value,4); ?></td>
                    <td align="center"><? echo number_format($order_net_value/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">Other Cost</td>
                    <td align="right"><? $otherCost=$others_cost_value/$order_price_per_dzn*$order_job_qnty; echo number_format($otherCost,4); ?></td>
                    <td align="center"><? echo number_format($otherCost/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Value</td>
                    <td align="right"><? $cmValue = $order_net_value-$otherCost; echo number_format($cmValue,4); ?></td>
                    <td align="center"><? echo number_format($cmValue/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">CM /<? echo $costing_for; ?></td>
                    <td align="right"><? $cmPerDzn=$cmValue/$order_job_qnty*$order_price_per_dzn; echo number_format($cmPerDzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">CM Cost /<? echo $costing_for; ?></td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Margin /<? echo $costing_for; ?></td>
                    <td align="right"><? echo number_format($cmPerDzn-$row[csf("cm_cost")],4); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                                 
            </table>
      </div>
      <div style="margin-left:5px;float:left;">
      		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:420px;text-align:center;" rules="all">
            <label><b>Cost Summary for order quantity</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">%</td>
                </tr>            
                <tr>
                    <td align="left">Total Order Value </td>
                    <td align="right"><? $total_order_val = $order_job_qnty*$avg_unit_price; echo number_format($total_order_val,4); ?></td>
                    <td align="center"><? echo number_format($total_order_val/$total_order_val*100,4); ?></td>
                </tr> 
                 <tr>
                    <td align="left">Total Cost </td>
                    <td align="right"><?  $total_cost = $row[csf("total_cost")]/$order_price_per_dzn*$order_job_qnty; echo number_format($total_cost,4); ?></td>
                    <td align="center"><? echo number_format($total_cost/$total_order_val*100,2); ?></td>
                </tr>
                 <tr>
                    <td align="left">Margin </td>
                    <td align="right"><? $margin_val = $total_order_val-$total_cost; echo number_format($margin_val,4); ?></td>
                    <td align="center"><? echo number_format($margin_val/$total_order_val*100,2); ?></td>
                </tr>
                 <tr>
                    <td align="left">Margin /<? echo $costing_for; ?> </td>
                    <td align="right"><? echo number_format($margin_val/$order_job_qnty*$order_price_per_dzn,2); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                 
           </table>     
      </div>
      
       <?
     	 // image show here  -------------------------------------------
		$sql = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id=".$txt_job_no."";
		$data_array=sql_select($sql);
 	  ?> 
          <div style="margin:15px 5px;float:left;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='97' width='89' />
            <?  } ?>			
          </div>
      
      
      <div style="clear:both"></div>     
      </div>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>
  		
	
    <br>
    
    <?
     echo signature_table(109, $cbo_company_name, "850px");?>
    	
 	<?
  
}//end master if condition-------------------------------------------------------


if($action=="preCostRpt2")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	 $po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
	 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id  and a.job_no =".$txt_job_no."   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
	$po_qty+=$sql_po_row[csf('order_quantity')];
	$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
	$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
	}
	
	//echo $po_qty;
	
	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no =".$txt_job_no."");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
	{
	$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
	}
	$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where job_no =".$txt_job_no."", "job_no", "costing_per");// where job_no ='FAL-14-01157'
	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");	
	foreach($sql_std_para as $sql_std_row)
	{
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
	} 
	$fab_knit_req_kg_avg=0;
	$fab_woven_req_yds_avg=0;
	if($db_type==0)
	{
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.sew_smv,b.cut_smv,b.sew_effi_percent, 	b.cut_effi_percent,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date order by a.job_no";  
	}
	if($db_type==2)
	{
    $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.sew_smv,b.cut_smv,b.sew_effi_percent, 	b.cut_effi_percent,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";  
	}
	
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Pre- Costing</div>
	<?
	$uom="";
	$sew_smv=0;
	$cut_smv=0;
	$sew_effi_percent=0;
	$cut_effi_percent=0;
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
			$job_in_file .= $val[csf('file_no')].",";
			$job_in_ref .= $val[csf('grouping')].",";
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
		$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
		//$ref_con='';
		
		foreach ($job_ref as $ref)
		{
			$ref_cond.=", ".$ref;
		}
		$file_con='';
		foreach ($job_file as $file)
		{
			if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
		}
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td colspan=""><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                        
                        <td>Job Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo $row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                         <td>Plan Cut Qty</td>
                       <td><b><? echo $po_plun_cut_qty/$total_set_qnty." ". $unit_of_measurement[$row[csf("order_uom")]];?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_req_kg")];$fab_knit_req_kg_avg+=$row[csf("fab_knit_req_kg")]; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_req_yds")];$fab_woven_req_yds_avg+= $row[csf("fab_woven_req_yds")];?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $row[csf("fab_yarn_req_kg")] ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per")]];?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                    <tr>
                    <td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_fin_req_kg")] ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_fin_req_yds")]; ?>(Yds)</b></td>
                    	<td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                       
                    </tr>
                      <tr>
                     <td>Internal Ref</td>
                        <td colspan="2"><b><? echo ltrim($ref_cond,", "); ?></b></td>
                        <td>File No</td>
                        <td colspan="2"><b><? echo $file_cond; ?></b></td>
                    </tr>
                </table>
            <?	
			
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			
	}//end first foearch
	
	
	//id, fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,comar_amount,commis_rate,commis_amount	 	
	// 	costing_per_id 	order_uom_id 	fabric_cost 	fabric_cost_percent 	trims_cost 	trims_cost_percent 	embel_cost 	
	//embel_cost_percent 	comm_cost 	comm_cost_percent 	commission 	commission_percent 	lab_test 	lab_test_percent 	inspection 	
	//inspection_percent 	cm_cost,cm_cost_percent 	freight,freight_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent
	// 	price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche	
	//margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
	
	
	//start	all summary report here -------------------------------------------
	
 	?>
    
    
        
         
 
       
      
      <?
	
	 $sql_new = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
			$data_array_new=sql_select($sql_new);
			$summary_data=array();
            
            foreach( $data_array_new as $row_new )
            {
				$summary_data[price_dzn]=$row_new[csf("price_dzn")];
				$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
				
			    $summary_data[commission]=$row_new[csf("commission")];
				$summary_data[commission_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("commission")];
				
				
				$summary_data[trims_cost]=$row_new[csf("trims_cost")];
				
				$summary_data[emb_cost]=$row_new[csf("embel_cost")];
				$summary_data[emb_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("embel_cost")];
				
				$summary_data[lab_test]=$row_new[csf("lab_test")];
				$summary_data[lab_test_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("lab_test")];
				
				$summary_data[inspection]=$row_new[csf("inspection")];
				$summary_data[inspection_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("inspection")];
				
				$summary_data[freight]=$row_new[csf("freight")];
				$summary_data[freight_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("freight")];
				
				$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
				$summary_data[currier_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("currier_pre_cost")];

				$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
				$summary_data[certificate_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("certificate_pre_cost")];
				$summary_data[wash_cost]=$row_new[csf("wash_cost")];
				$summary_data[wash_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("wash_cost")];
				
				$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
				
				$summary_data[OtherDirectExpenses_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$summary_data[OtherDirectExpenses];
				 
				$summary_data[cm_cost]=$row_new[csf("cm_cost")];
				$summary_data[cm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("cm_cost")];
				
				$summary_data[comm_cost]=$row_new[csf("comm_cost")];
				$summary_data[comm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("comm_cost")];
				
				$summary_data[common_oh]=$row_new[csf("common_oh")];
				$summary_data[common_oh_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("common_oh")];
				$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
				$summary_data[depr_amor_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("depr_amor_pre_cost")];
				
				
				
				//$summary_data[total_cost]=$row_new[csf("total_cost")];
				//$summary_data[trims_cost_percent]=$row_new[csf("trims_cost_percent")];
				//$summary_data[embel_cost_percent]=$row_new[csf("embel_cost_percent")];
				//$summary_data[embel_cost_percent]=$row_new[csf("embel_cost_percent")];
			}
			


$yarn_data=array();
$sql_yarn="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as yarn_id,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes  and d.id=f.fabric_cost_dtls_id   and e.cons !=0  and  a.job_no =".$txt_job_no." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id";
$data_arr_yarn=sql_select($sql_yarn);
foreach($data_arr_yarn as $yarn_row)
{
    $costing_per_qty=0;
	$costing_per=$costing_per_arr[$yarn_row[csf('job_no')]];
	if($costing_per==1)
	{
	$costing_per_qty=12	;
	}
	if($costing_per==2)
	{
	$costing_per_qty=1;	
	}
	if($costing_per==3)
	{
	$costing_per_qty=24	;
	}
	if($costing_per==4)
	{
	$costing_per_qty=36	;
	}
	if($costing_per==5)
	{
	$costing_per_qty=48	;
	}
	
	$set_item_ratio=$gmtsitem_ratio_array[$yarn_row[csf('job_no')]][$yarn_row[csf('item_number_id')]];
	$reqyarnqnty =def_number_format(($yarn_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$yarn_row[csf("CONS_QNTY")],5,"");
	
	$yarnamount=def_number_format(($reqyarnqnty*$yarn_row[csf("RATE")]),5,"");
	//$yarnamount =def_number_format(($yarn_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$yarn_row[csf("amount")],5,"");

	
	$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]=$yarn_row[csf("amount")];
	$summary_data[yarn_cost_job]+=$yarnamount;
	//$summary_data[yarn_job_qty]+=$yarn_row[csf("plan_cut_qnty")];
	//$yarn_data[$yarn_row[csf('job_no')]][$yarn_row[csf('id')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('country_id')]][$yarn_row[csf('color_number_id')]][$yarn_row[csf('size_number_id')]]['req_yarn_qnty']+=$reqyarnqnty;
	//$yarn_data[$yarn_row[csf('job_no')]][$yarn_row[csf('id')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('country_id')]][$yarn_row[csf('color_number_id')]][$yarn_row[csf('size_number_id')]]['yarn_amount']+=$yarnamount;

}


// Conversion 
$conv_data=array();
$sql_conv="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as con_id,f.cons_process,f.req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and e.pre_cost_fabric_cost_dtls_id=f.fabric_description  and e.cons !=0 and  a.job_no =".$txt_job_no." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
UNION ALL
select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as con_id,f.cons_process,f.req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.fabric_description=0  and e.cons !=0  and  a.job_no =".$txt_job_no." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
$data_arr_conv=sql_select($sql_conv);
foreach($data_arr_conv as $conv_row)
{
    $costing_per_qty=0;
	$costing_per=$costing_per_arr[$conv_row[csf('job_no')]];
	if($costing_per==1)
	{
	$costing_per_qty=12	;
	}
	if($costing_per==2)
	{
	$costing_per_qty=1;	
	}
	if($costing_per==3)
	{
	$costing_per_qty=24	;
	}
	if($costing_per==4)
	{
	$costing_per_qty=36	;
	}
	if($costing_per==5)
	{
	$costing_per_qty=48	;
	}
	
	$set_item_ratio=$gmtsitem_ratio_array[$conv_row[csf('job_no')]][$conv_row[csf('item_number_id')]];
	$convcolorrate=array();
	if($conv_row[csf('color_break_down')] !="")
	{
		$arr_1=explode("__",$conv_row[csf('color_break_down')]);
		for($ci=0;$ci<count($arr_1);$ci++)
		{
		$arr_2=explode("_",$arr_1[$ci]);
		$convcolorrate[$arr_2[0]]=$arr_2[1];
			
		}
	}
	//print_r($convcolorrate);
	//echo "<br/>";
	$convrate=0;
    $convqnty =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("req_qnty")],5,"");

	/*if($conv_row[csf('color_break_down')] !="")
	{
	$convrate=$convcolorrate[$conv_row[csf('color_number_id')]];
	}
	else
	{
	$convrate=$conv_row[csf('charge_unit')];
	}*/
	$convrate=$conv_row[csf('charge_unit')];
	$convamount=def_number_format($convqnty*$convrate,5,"");
	//$convamount =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("amount")],5,"");
	
	//$conv_data[$conv_row[csf('job_no')]][$conv_row[csf('id')]][$conv_row[csf('item_number_id')]][$conv_row[csf('country_id')]][$conv_row[csf('color_number_id')]][$conv_row[csf('size_number_id')]]['conv_qnty']+=$convqnty;
	$conv_data[cons_process][$conv_row[csf('con_id')]]=$conv_row[csf('cons_process')];
	$conv_data[amount][$conv_row[csf('con_id')]]=$conv_row[csf('amount')];
	$conv_data[amount_job][$conv_row[csf('con_id')]]+=$convamount;
	$summary_data[conver_cost_job]+=$convamount;

}

//die;
//Conversion End

//start	Trims Cost part report here -------------------------------------------
   	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array_trim=sql_select($sql_trim);
	$total_trims_cost=0; 
            foreach( $data_array_trim as $row_trim )
            { 
			   $order_qty_tr=0;
			   $dtls_data=sql_select("select po_break_down_id,cons,country_id from wo_pre_cost_trim_co_cons_dtls where wo_pre_cost_trim_cost_dtls_id=".$row_trim[csf("id")]." and cons !=0");
			   foreach($dtls_data as $dtls_data_row )
			   {
				   if($dtls_data_row[csf('country_id')]==0)
					 {
						 $txt_country_cond="";
					 }
					 else
					 {
						 $txt_country_cond ="and c.country_id in (".$dtls_data_row[csf('country_id')].")";
					 }
					 
					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$dtls_data_row[csf('po_break_down_id')]."  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
	                 list($sql_po_qty_row)=$sql_po_qty;
	                 $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
					 $order_qty_tr+=$po_qty;
			   }
				//$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_qty_tr;
				$trim_amount = $row_trim[csf("amount")]/$order_price_per_dzn*$order_qty_tr;
				
 				//$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
				$total_trims_cost += $trim_amount;
				$summary_data[trims_cost_job]+=$trim_amount;
			}

//End	Trims Cost part report here -------------------------------------------

	  ?>
       <div style="margin-top:15px">
         <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
          <tr style="font-weight:bold">
                            
                            <td width="380" colspan="5">Order Porfitability</td>
                                               
                        </tr>
                        <tr style="font-weight:bold">
                            <td width="80">Line Items</td>
                            <td width="380">Particulars</td>
                            <td width="100">Amount (USD)/<? echo $costing_for; ?></td>
                            <td width="100">Total Value</td>
                            <td width="100">%</td>                     
                        </tr>
                        <tr>
                            <td width="80">1</td>
                            <td width="380" align="left" style="font-weight:bold">Gross FOB Value</td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[price_dzn],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[price_dzn_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[price_dzn_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">2</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: commission</td>
                            <td width="100" align="right"><? echo number_format($summary_data[commission],4); ?></td>
                            <td width="100" align="right"><? echo number_format($summary_data[commission_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[commission_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                           <td width="80">3</td>
                            <?
							$NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
							$NetFOBValue_job=$summary_data[price_dzn_job]-$summary_data[commission_job];
							?>
                            <td width="380" align="left" style="font-weight:bold"><b>Net FOB Value (1-2)</b></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($NetFOBValue,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($NetFOBValue_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($NetFOBValue_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">4</td>
                            <td width="380" align="left" style="font-weight:bold"><b>Less: Cost of Material & Services (5+6+7+8+9) </b></td>
                            <?
							$Less_Cost_Material_Services=array_sum($summary_data[yarn_cost])+array_sum($conv_data[amount])+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
							
							$Less_Cost_Material_Services_job=$summary_data[yarn_cost_job]+$summary_data[conver_cost_job]+$summary_data[trims_cost_job]+$summary_data[emb_cost_job]+$summary_data[OtherDirectExpenses_job];
							//+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Less_Cost_Material_Services,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Less_Cost_Material_Services_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Less_Cost_Material_Services_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">5</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold">Yarn Cost</td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format(array_sum($summary_data[yarn_cost]),4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[yarn_cost_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[yarn_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80" valign="top">6</td>
                            <td width="380" align="left" style=" padding-left:100px">
                            
                            <table>
                            <tr>
                            <td width="180" style="font-weight:bold">Conversion Cost</td>
                           
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            <td width="180" align="left"><? echo $conversion_cost_head_array[$conv_data[cons_process][$key]]; ?></td>
                           
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top"> 
							<? //echo number_format(array_sum($conv_data[amount]),4); ?>
                            
                             <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format(array_sum($conv_data[amount]),4); ?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($conv_data[amount][$key],4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
							
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[conver_cost_job],4); ?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($conv_data[amount_job][$key],4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="180" align="right" valign="top">
							
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format(($summary_data[conver_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="180" align="right"><? echo number_format(($conv_data[amount_job][$key]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>                     
                        </tr>
                        
                        <tr>
                            <td width="80">7</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold" ><b>Trim Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[trims_cost],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[trims_cost_job],4)?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[trims_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">8</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold"><b>Embelishment Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[emb_cost],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[emb_cost_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[emb_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                         <?
						 //$OtherDirectExpenses=$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
						  
							//$OtherDirectExpenses_job_qty=($po_qty/($total_set_qnty*$order_price_per_dzn))*$OtherDirectExpenses;
							
						 ?>
                            <td width="80" valign="top">9</td>
                            <td width="380" align="left" style=" padding-left:100px">
                            
                            <table>
                            <tr>
                            <td width="180" style="font-weight:bold">Other Direct Expenses</td>
                           
                            </tr>
                            </table>
                            
                
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            <td width="180" align="left">Lab Test</td>
                            
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Inspection</td>
                           
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Freight Cost</td>
                            
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Courier Cost</td>
                            
                            </tr>
                            
                             <tr>
                            <td width="180" align="left">Certificate Cost</td>
                           
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Garments Wash Cost</td>
                           
                            </tr>
                            
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[OtherDirectExpenses],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[lab_test],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[inspection],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[freight],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[currier_pre_cost],4);?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[certificate_pre_cost],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[wash_cost],4);?></td>
                            </tr>
                            
                            </table>
                            </td>
                            
                            
                            <td width="100" align="right" valign="top">
							<? //echo number_format($summary_data[OtherDirectExpenses_job],4); ?>
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[OtherDirectExpenses_job],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[lab_test_job],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[inspection_job],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[freight_job],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[currier_pre_cost_job],4);?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[certificate_pre_cost_job],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[wash_cost_job],4);?></td>
                            </tr>
                            
                            </table>
                            </td>
                            <td width="180" align="right" valign="top">
							
                            <table>
                            <tr>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[OtherDirectExpenses_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="180" align="right"><? echo number_format(($summary_data[lab_test_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="180" align="right"><? echo number_format(($summary_data[inspection_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="180" align="right"><? echo number_format(($summary_data[freight_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="180" align="right"><? echo number_format(($summary_data[currier_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="180" align="right"><? echo number_format(($summary_data[certificate_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="180" align="right"><? echo number_format(($summary_data[wash_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            
                            </table>
                            </td>                     
                        </tr>
                         <tr>
                            <td width="80">10</td>
                            <td width="380" align="left" style="font-weight:bold">Contributions/Value Additions (3-4)</td>
                            <?
							$Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
							$Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Contribution_Margin,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Contribution_Margin_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Contribution_Margin_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">11</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: CM Cost </td>
                            <td width="100" align="right"><? echo number_format($summary_data[cm_cost],4); ?> </td>
                            <td width="100" align="right"><? echo number_format($summary_data[cm_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[cm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">12</td>
                            <td width="380" align="left" style="font-weight:bold">Gross Profit (10-11)</td>
                            <?
							$Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
							$Gross_Profit_job=$Contribution_Margin_job-$summary_data[cm_cost_job];
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Gross_Profit,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Gross_Profit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Gross_Profit_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        
                        <tr>
                            <td width="80">13</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Commercial Cost</td>
                            
                            <td width="100" align="right"> <? echo number_format( $summary_data[comm_cost],4); ?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data[comm_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[comm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <td width="80">14</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Operating Expensees</td>
                            
                            <td width="100" align="right"><? echo number_format( $summary_data[common_oh],4); ?> </td>
                            <td width="100" align="right"><? echo number_format( $summary_data[common_oh_job],4); ?> </td>
                            <td width="180" align="right"><? echo number_format(($summary_data[common_oh_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        
                        <tr >
                            <td width="80">15</td>
                            <td width="380" align="left" style="font-weight:bold">Operating Profit/ Loss (12-(13+14))</td>
                            <?
							$OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
							$OperatingProfitLoss_job=$Gross_Profit_job-($summary_data[comm_cost_job]+$summary_data[common_oh_job]);
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($OperatingProfitLoss,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($OperatingProfitLoss_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($OperatingProfitLoss_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                         <tr>
                            <td width="80">16</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>
                            
                            <td width="100" align="right"> <? echo number_format( $summary_data[depr_amor_pre_cost],4); ?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data[depr_amor_pre_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[depr_amor_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        
                        <tr>
                        <?
						$interest_expense=$NetFOBValue*$financial_para[interest_expense]/100;
						$income_tax=$NetFOBValue*$financial_para[income_tax]/100;
						$interest_expense_job=$NetFOBValue_job*$financial_para[interest_expense]/100;
						$income_tax_job=$NetFOBValue_job*$financial_para[income_tax]/100;
						?>
                            <td width="80">17</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Interest </td>
                            
                            <td width="100" align="right"> <? echo number_format( $interest_expense,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $interest_expense_job,4); ?></td>
                            <td width="180" align="right"><? echo number_format(($interest_expense_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                         <tr>
                            <td width="80">18</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Income Tax</td>
                            
                            <td width="100" align="right"> <? echo number_format( $income_tax,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $income_tax_job,4); ?></td>
                            <td width="180" align="right"><? echo number_format(($income_tax_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        <tr>
                            <? 
							$Netprofit=$OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
							$Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
							?>
                            <td width="80">19</td>
                            <td width="380" align="left" style="font-weight:bold">Net Profit (15-(16+17+18))</td>
                            
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit,4); ?> </td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Netprofit_job/$summary_data[price_dzn_job])*100,4);?></td>                     
                        </tr>
                        </table>
         </div>
      <?
	//End all summary report here -------------------------------------------
	
	
	
	$sql = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons,avg_cons_yarn, fabric_source,gsm_weight, rate, amount,avg_finish_cons,status_active   
			from wo_pre_cost_fabric_cost_dtls 
			where job_no=".$txt_job_no."";
			
			
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					 <td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$knit_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					<td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$woven_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/Dzn</td>
							<td width="100">Avg. Fab. Cons/Dzn</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here
		$fab_knit_req_kg_avg_amount=$knit_subtotal_amount/$knit_subtotal_avg_cons*$fab_knit_req_kg_avg;
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
					/*$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_knit_req_kg_avg_amount,4).'</td>
					</tr>';*/
					if($zero_value==1)
					{
  		               echo $knit_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $knit_fab;
						}
						else
						{
							echo "";
						}
						
					}
		
		//woven fabrics table here 
		$fab_woven_req_yds_avg_amount=$woven_subtotal_amount/$woven_subtotal_avg_cons*$fab_woven_req_yds_avg;
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_woven_req_yds_avg_amount,4).'</td>
					</tr>';*/
   					$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total (Avg)</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($fab_woven_req_yds_avg_amount+$fab_knit_req_kg_avg_amount),4).'</td>
					</tr></table></div>';*/
        // echo $woven_fab; 
		 if($zero_value==1)
					{
  		               echo $woven_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $woven_fab;
						}
						else
						{
							echo "";
						}
						
					}
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//oracle 
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio , sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		if($zero_value==1)
		{
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	  }
	  else
	  {
		  if($fabric_cost>0)
		  {
		  ?>
           <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	      $grand_total_amount +=$total_amount;
		  }
		  else
		  {
			 echo ""; 
		  }
	  }
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id,a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no." ";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	}
	else
	{
		if($fabric_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($trims_cost>0)
		{
	?>
    <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
	<?
		}
		else
		{
		   echo "";	
		}
	}
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($embel_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate In %</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($comm_cost>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($commission>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
		   echo "";	
		}
		
	}
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:300px;" rules="all">
            <label><b>CM Details</b></label>
                <tr>
                    <td width="150">CPM (TK)</td>
                    <td width="150"><? echo $financial_para[cost_per_minute] ?></td>
                    
                 </tr>
                  <tr>
                    <td width="150">SMV</td>
                    <td width="150"><? echo $sew_smv .", ".$cut_smv ?></td>
                 </tr>
                 <tr>
                    <td width="150">EFF %</td>
                    <td width="150"><? echo $sew_effi_percent .", ".$cut_effi_percent; ?></td>
                 </tr>
                 
  </table>
    
    	<br/>
 	<? 
	echo signature_table(109, $cbo_company_name, "850px");
  
}//end master if condition-------------------------------------------------------


if($action=="preCostRptOrder")//Order Wise Pre Costing Used in Fabric Receive Status Report and Cost Break Down Report
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$order_id=$txt_job_no;
	
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{	
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg, a.total_set_qnty as ratio, d.po_number, d.po_quantity as po_qnty, d.unit_price, d.pub_shipment_date from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c, wo_po_break_down d where a.job_no=b.job_no and a.job_no=d.job_no_mst and b.job_no=c.job_no and a.status_active=1 and d.id=$order_id $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date";  
	}
	if($db_type==2)
	{	
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg, a.total_set_qnty as ratio, d.po_number, d.po_quantity as po_qnty, d.unit_price, d.pub_shipment_date from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c, wo_po_break_down d where a.job_no=b.job_no and a.job_no=d.job_no_mst and b.job_no=c.job_no and a.status_active=1 and d.id=$order_id $company_name $cbo_buyer_name $txt_style_ref";  
	}
		 
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Pre- Costing</div>
	<?
	$uom="";
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=0;
		$order_values = $row[csf("po_qnty")]*$row[csf("unit_price")];
		$job_in_orders = $row[csf("po_number")];
		$pulich_ship_date = $row[csf("pub_shipment_date")];
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
							
						?>
                        <td width="100"><b><? echo $gmts_item; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td colspan="3"><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                        
                        <td>Order Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo $row[csf("po_qnty")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_req_kg")]; ?> (Kg)</b></td>
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_req_yds")]; ?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("unit_price")]; ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $row[csf("fab_yarn_req_kg")] ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                </table>
            <?	
			
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("po_qnty")];
			$avg_unit_price=$row[csf("unit_price")];
			
	}//end first foearch
	
	
	//id, fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,comar_amount,commis_rate,commis_amount	 	
	// 	costing_per_id 	order_uom_id 	fabric_cost 	fabric_cost_percent 	trims_cost 	trims_cost_percent 	embel_cost 	
	//embel_cost_percent 	comm_cost 	comm_cost_percent 	commission 	commission_percent 	lab_test 	lab_test_percent 	inspection 	
	//inspection_percent 	cm_cost,cm_cost_percent 	freight,freight_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent
	// 	price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche	
	//margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
	
	$job_no=$row[csf('job_no')];
	//start	all summary report here -------------------------------------------
	$sql = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no='".$job_no."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$others_cost_value=0;
	$fabric_cost=0;
	$trims_cost=0;
	$embel_cost=0;
	$comm_cost=0;
	$commission=0;
	$lab_test=0;
	$inspection=0;
	$cm_cost=0;
	$freight=0;
	$common_oh=0;
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80">SL</td>
                    <td width="300">Particulars</td>
                    <td width="100">Cost</td>
                    <td width="100">Amount (USD)</td>
                    <td width="180">% to Ord. Value</td>                     
                </tr>
            <?
            $sl=0;
            foreach( $data_array as $row )
            { 
			$fabric_cost=$row[csf("fabric_cost")];
			$trims_cost=$row[csf("trims_cost")];
			$embel_cost=$row[csf("embel_cost")];
			$comm_cost=$row[csf("comm_cost")];
			$commission=$row[csf("commission")];
			$lab_test=$row[csf("lab_test")];
			$inspection=$row[csf("inspection")];
			$cm_cost=$row[csf("cm_cost")];
			$freight=$row[csf("freight")];
			$common_oh=$row[csf("common_oh")];
				$sl=$sl+1;
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				if($zero_value==1)
				{
				
?>	
                <tr>
                    <td><? echo $sl; ?></td>
                    <td align="left"><b>Order Price/<? echo $costing_for; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? echo "100.00%"; ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right" rowspan="10">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); ?>%</td>
                </tr>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commission Cost</td>
                    <td align="right"><? echo number_format($row[csf("commission")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("commission_percent")],2); ?>%</td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); ?>%</td>
                 </tr>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); ?>%</td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (2:11)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($row[csf("total_cost")],4); ?></b></td>
                    <td align="center"><b><? echo number_format($row[csf("total_cost_percent")],2); ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_for; ?> (1-12)</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_dzn")],4); ?></td>
                    <td align="center"><b><? $margin_dzn_percent=($row[csf("margin_dzn")]/$row[csf("price_dzn")])*100; echo number_format($margin_dzn_percent,2); ?>%</b></td>
                </tr>
                
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Price / <? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("price_pcs_or_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("total_cost")]/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_pcs_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
 
            <?
				}
				else
				{
					?>
                 <tr>
                    <td><? echo $sl; ?></td>
                    <td align="left"><b>Order Price/<? echo $costing_for; ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo number_format($row[csf("price_dzn")],4);//$avg_unit_price*$order_price_per_dzn ?></b></td>
                    <td align="center"><? echo "100.00%"; ?></td>
                </tr>
                <?
				if($row[csf("fabric_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">All Fabric Cost</td>
                    <td align="right"><? echo number_format($row[csf("fabric_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("fabric_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("trims_cost")]!=0)
				{
				?>
              
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Trims Cost</td>
                    <td align="right"><? echo number_format($row[csf("trims_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("trims_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("embel_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Embellishment Cost</td>
                    <td align="right"><? echo number_format($row[csf("embel_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("embel_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("comm_cost")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commercial Cost</td>
                    <td align="right"><? echo number_format($row[csf("comm_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("comm_cost_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("commission")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Commission Cost</td>
                    <td align="right"><? echo number_format($row[csf("commission")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("commission_percent")],2); ?>%</td>
                </tr>
                <?
				}
				if($row[csf("lab_test")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("lab_test_percent")],2); ?>%</td>
                 </tr>
                <?
				}
				if($row[csf("inspection")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("inspection_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				if($row[csf("cm_cost")]!=0)
				{
				?>
                <tr> 
                    <td><? echo ++$sl; ?></td>
                    <td align="left">CM Cost</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("cm_cost_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				if($row[csf("freight")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("freight_percent")],2); ?>%</td>
                 </tr>
                <?
				}
				if($row[csf("common_oh")]!=0)
				{
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Office OH </td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="center"><? echo number_format($row[csf("common_oh_percent")],2); ?>%</td>
                 </tr>
                 <?
				}
				?>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost (2:<? echo $sl-1;?>)</b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($row[csf("total_cost")],4); ?></b></td>
                    <td align="center"><b><? echo number_format($row[csf("total_cost_percent")],2); ?>%</b></td>
                 </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $costing_for; ?> (1-<? echo $sl-1;?>)</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_dzn")],4); ?></td>
                    <td align="center"><b><? $margin_dzn_percent=($row[csf("margin_dzn")]/$row[csf("price_dzn")])*100; echo number_format($margin_dzn_percent,2); ?>%</b></td>
                </tr>
                
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Price / <? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("price_pcs_or_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                 <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Cost /<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("total_cost")]/$order_price_per_dzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left">Margin/<? echo $unit_of_measurement[$uom];?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("margin_pcs_set")],4); ?></td>
                    <td align="right"></td>
                </tr>
                    <?
				}
            }
            ?>
            </table>
      </div>
      <?
		//End all summary report here -------------------------------------------
	
	
	
		//2	All Fabric Cost part here------------------------------------------- 	   	
		$sql = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active   
			from wo_pre_cost_fabric_cost_dtls 
			where job_no='".$job_no."'";
		$data_array=sql_select($sql);
		$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];	
				$knit_subtotal_amount += $row[csf("amount")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];	
				$woven_subtotal_amount += $row[csf("amount")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/Dzn</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="6">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here 
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
					if($zero_value==1)
					{
  		               echo $knit_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $knit_fab;
						}
						else
						{
							echo "";
						}
						
					}
		
		//woven fabrics table here 
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
        // echo $woven_fab; 
		 if($zero_value==1)
					{
  		               echo $woven_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $woven_fab;
						}
						else
						{
							echo "";
						}
						
					}
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//Mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no='".$job_no."' group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//Oracle
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no='".$job_no."' group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		if($zero_value==1)
		{
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	  }
	  else
	  {
		  if($fabric_cost>0)
		  {
		  ?>
           <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	      $grand_total_amount +=$total_amount;
		  }
		  else
		  {
			 echo ""; 
		  }
	  }
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id,a.fabric_description, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no='".$job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	}
	else
	{
		if($fabric_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no='".$job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($trims_cost>0)
		{
	?>
    <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
	<?
		}
		else
		{
		   echo "";	
		}
	}
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no='".$job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($embel_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no='".$job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($comm_cost>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no='".$job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($commission>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
		   echo "";	
		}
		
	}
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
   	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no='".$job_no."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($lab_test>0 || $inspection>0 || $cm_cost>0 || $freight>0 || $common_oh>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
				if($row[csf("lab_test")]>0)
				{
  			?>	 


                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($row[csf("lab_test")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("inspection")]>0)
				{
				?>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($row[csf("inspection")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("cm_cost")]>0)
				{
				?>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                </tr>
                <?
				}
				if($row[csf("freight")]>0)
				{
				?>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($row[csf("freight")],4); ?></td>
                </tr>
                 <?
				}
				if($row[csf("common_oh")]>0)

				{
				?>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($row[csf("common_oh")],4); ?></td>
                </tr>
            <?
				}
                 $total_amount += $row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$row[csf("common_oh")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Other Components  Part report here -------------------------------------------	  
  
  
  	
  	//start	CM on Net Order Value part report here -------------------------------------------
    	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no='".$job_no."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row );
	$order_net_value = 0;
	?> 
 
        <div style="margin-top:15px">
        <div style="float:left">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:420px;text-align:center;" rules="all">
            <label><b>CM on Net Order Value</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">%</td>
                </tr>            
                <tr>
                    <td align="left">Order Value </td>
                    <td align="right"><? echo number_format($order_values,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Commission </td>
                    <td align="right"><? $less_commission = $row[csf("commission")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_commission,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Commercial Cost </td>
                    <td align="right"><? $less_commercial = $row[csf("comm_cost")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_commercial,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Less Freight</td>
                    <td align="right"><? $less_freight = $row[csf("freight")]/$order_price_per_dzn*$order_job_qnty; echo number_format($less_freight,4); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Net Order Value</td>
                    <td align="right"><? $order_net_value=$order_values-($less_commission+$less_commercial+$less_freight);echo number_format($order_net_value,4); ?></td>
                    <td align="center"><? echo number_format($order_net_value/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">Other Cost</td>
                    <td align="right"><? $otherCost=$others_cost_value/$order_price_per_dzn*$order_job_qnty; echo number_format($otherCost,4); ?></td>
                    <td align="center"><? echo number_format($otherCost/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Value</td>
                    <td align="right"><? $cmValue = $order_net_value-$otherCost; echo number_format($cmValue,4); ?></td>
                    <td align="center"><? echo number_format($cmValue/$order_net_value*100,2); ?></td>
                </tr>
                <tr>
                    <td align="left">CM /<? echo $costing_for; ?></td>
                    <td align="right"><? $cmPerDzn=$cmValue/$order_job_qnty*$order_price_per_dzn; echo number_format($cmPerDzn,4); ?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">CM Cost /<? echo $costing_for; ?></td>
                    <td align="right"><? echo number_format($row[csf("cm_cost")],4); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">Margin /<? echo $costing_for; ?></td>
                    <td align="right"><? echo number_format($cmPerDzn-$row[csf("cm_cost")],4); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                                 
            </table>
      </div>
      <div style="margin-left:5px;float:left;">
      		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:420px;text-align:center;" rules="all">
            <label><b>Cost Summary for order quantity</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">%</td>
                </tr>            
                <tr>
                    <td align="left">Total Order Value </td>
                    <td align="right"><? $total_order_val = $order_job_qnty*$avg_unit_price; echo number_format($total_order_val,4); ?></td>
                    <td align="center"><? echo number_format($total_order_val/$total_order_val*100,4); ?></td>
                </tr> 
                 <tr>
                    <td align="left">Total Cost </td>
                    <td align="right"><?  $total_cost = $row[csf("total_cost")]/$order_price_per_dzn*$order_job_qnty; echo number_format($total_cost,4); ?></td>
                    <td align="center"><? echo number_format($total_cost/$total_order_val*100,2); ?></td>
                </tr>
                 <tr>
                    <td align="left">Margin </td>
                    <td align="right"><? $margin_val = $total_order_val-$total_cost; echo number_format($margin_val,4); ?></td>
                    <td align="center"><? echo number_format($margin_val/$total_order_val*100,2); ?></td>
                </tr>
                 <tr>
                    <td align="left">Margin /<? echo $costing_for; ?> </td>
                    <td align="right"><? echo number_format($margin_val/$order_job_qnty*$order_price_per_dzn,2); ?></td>
                    <td align="center">&nbsp;</td>
                </tr>
                 
           </table>     
      </div>
      
       <?
     	 // image show here  -------------------------------------------
		$sql = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id='".$job_no."'";
		$data_array=sql_select($sql);
 	  ?> 
          <div style="margin:15px 5px;float:left;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='97' width='89' />
            <?  } ?>			
          </div>
      
      
      <div style="clear:both"></div>     
      </div>
    <!--End CM on Net Order Value Part report here ------------------------------------------->
	<? echo "<br /><b>Note: Other Cost =  Fabric Cost + Trims Cost + Embellishment Cost + Lab Test + Inspection + Office OH</b><br /><br />"; ?>
  		
	
    <br/>

<?
	echo signature_table(109, $cbo_company_name, "850px");
}
//generate_report BOM--------------------------------------------------------------
if($action=="bomRpt_old")
{
	///extract($_REQUEST);
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
 	
 	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{	
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg order by a.job_no"; 
	}
	if($db_type==2)
	{	
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $company_name $cbo_buyer_name $txt_style_ref group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg  order by a.job_no"; //a.job_quantity as job_quantity,
	}
	//echo $sql;die;
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">BOM Report</div>
	<?
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$ord_qty=0;
		$avg_unit_price=0;
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Plan Cut Qnty</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="100"><b><? echo $row[csf("job_quantity")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                        <td>Order Qty </td>
                        <td><b><? echo $row[csf("ord_qty")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                       
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Garments Item</td>
                        <td colspan="3"><b><? echo $grmnt_items; ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                    
                </table>

            <?	
			
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			$ord_qty=$row[csf("ord_qty")];
			
	}//end first foearch
	
	
	
	
	//2 Fabric Cost part here------------------------------------------- 	   	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active   
			from wo_pre_cost_fabric_cost_dtls 
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
		
		$knit_fab="";$woven_fab=""; 
		$knit_subtotal_amount=0;
		$woven_subtotal_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
			 $set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
			   $fincons=0;
			   $greycons=0;
			   $order_qty_fab=0;
			   $fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and cons !=0");
			   foreach($fab_dtls_data as $fab_dtls_data_row )
			   {
					 $sql_po_qty_fab=sql_select("select sum(c.plan_cut_qnty) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$fab_dtls_data_row[csf('po_break_down_id')]." and item_number_id='".$row[csf('item_number_id')]."' and size_number_id='".$fab_dtls_data_row[csf('gmts_sizes')]."' and  color_number_id= '".$fab_dtls_data_row[csf('color_number_id')]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1");
					 
	                 list($sql_po_qty_row_fab)=$sql_po_qty_fab;
	                 $po_qty_fab=$sql_po_qty_row_fab[csf('order_quantity')];
					 $fincons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
					 $greycons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("requirment")];
					 $order_qty_fab+=$po_qty_fab;
			   }
            
			//$row[csf("avg_cons")] = $greycons;
			//$row[csf("avg_finish_cons")] = $fincons;
 			$row[csf("amount")] = ($row[csf("amount")]/($order_price_per_dzn*$set_item_ratio))*($order_qty_fab*$set_item_ratio);
			
			//$row[csf("avg_cons")] = ($row[csf("avg_cons")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
			//$row[csf("avg_finish_cons")] = ($row[csf("avg_finish_cons")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
 			//$row[csf("amount")] = ($row[csf("amount")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
				
			if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				
 				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';	
				$knit_subtotal_avg_cons+=$greycons;
				$knit_subtotal_avg_finish_cons+=$row[csf("avg_finish_cons")];
            	$knit_subtotal_amount+=$row[csf("amount")];
			}			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				$j++;
                 $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';	
				$woven_subtotal_avg_cons+=$greycons;
				$woven_subtotal_avg_finish_cons+=$fincons;
				$woven_subtotal_amount+=$row[csf("amount")];
			}
        }	
	 
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">					
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="300">Description</td>
							<td width="100">Source</td>
							<td width="100">Gray Fabric Qnty</td>	
							<td width="100">Finish Fab Qnty</td>
 							<td width="50">Rate</td>
							<td width="50">Amount</td>
						</tr>'.$knit_fab;
		$woven_fab = '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here 
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;
		
		//woven fabrics table here 
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					</table></div>';
        echo $woven_fab;           		
  		
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//Mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//oracle 
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql); 
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
			<?
            $total_yarn_amount = 0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
					
 				$row[csf("cons_qnty")] = $row[csf("cons_qnty")]/$order_price_per_dzn*$order_job_qnty;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?  
				 $total_yarn_amount +=$row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	 
	//End Yarn Cost part report here -------------------------------------------
	
	
	
	//start	Conversion Cost to Fabric report here -------------------------------------------
   	 $sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount,a.color_break_down, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
		
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Required</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_conversion_cost=0;
            foreach( $data_array as $row )
            { 
			
			    $color_id_string="";
				$color_break_down_arr=explode("__",$row[csf("color_break_down")]);
				for($co=0; $co<=count($color_break_down_arr); $co++)
				{
					$color_break_down_arr_row=explode("_",$color_break_down_arr[$co]);
					
					//for($cow=0; $cow<=count($color_break_down_arr_row);  $cow++)
					//{
						if($color_break_down_arr_row[1] !=0)
						{
				          $color_id_string.=$color_break_down_arr_row[0].",";
						}
					//}
				}
				$color_id_string=rtrim($color_id_string,",");
				if($color_id_string =="")
				{
					$color_cond="";
				}
				else
				{
				  $color_cond="and c.color_number_id in(".$color_id_string.")";	
				}
				
				$po_break_down_id_string="";
				if($row[csf("pre_cost_fabric_cost_dtls_id")] ==0)
				{
					//$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where job_no='".$row[csf("job_no")]."' and cons !=0");
					$po_data_array=sql_select("select c.plan_cut_qnty/a.total_set_qnty as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=f.job_no  and b.id=c.po_break_down_id and b.id=f.po_break_down_id and c.po_break_down_id=f.po_break_down_id and c.item_number_id=d.item_number_id and a.job_no='".$row[csf("job_no")]."' and f.cons !=0   $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.id,c.plan_cut_qnty,a.total_set_qnty");
					$po_qty_con=0;
					foreach($po_data_array as $po_data_array_row)
					{
					  $po_qty_con+=$po_data_array_row[csf('order_quantity')];	
					}
				 
 				$row[csf("req_qnty")] = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con);
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con);
				$item_descrition = "All Fabrics";
				}
				else
				{
					$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("pre_cost_fabric_cost_dtls_id")]." and cons !=0");
					foreach($po_data_array as $po_data_array_row)
					{
					$po_break_down_id_string.=$po_data_array_row[csf('po_break_down_id')].",";	
					}
					
					$sql_po_qty_con=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in(".rtrim($po_break_down_id_string,",").") and c.item_number_id='".$row[csf('item_number_id')]."' $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1");
				 list($sql_po_qty_row_con)=$sql_po_qty_con;
	             $po_qty_con=$sql_po_qty_row_con[csf('order_quantity')];
				 
				$set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
 				$row[csf("req_qnty")] = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con/$set_item_ratio);
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con/$set_item_ratio);
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				}
				
				
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_conversion_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_conversion_cost; ?></td>
                </tr>                
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
	
	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
 	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Consumption</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_trims_cost=0; 
            foreach( $data_array as $row )
            { 
			   $order_qty_tr=0;
			   $dtls_data=sql_select("select po_break_down_id,cons,country_id from wo_pre_cost_trim_co_cons_dtls where wo_pre_cost_trim_cost_dtls_id=".$row[csf("id")]." and cons !=0");
			   foreach($dtls_data as $dtls_data_row )
			   {
				   if($dtls_data_row[csf('country_id')]==0)
					 {
						 $txt_country_cond="";
					 }
					 else
					 {
						 $txt_country_cond ="and c.country_id in (".$dtls_data_row[csf('country_id')].")";
					 }
					 
					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$dtls_data_row[csf('po_break_down_id')]."  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
	                 list($sql_po_qty_row)=$sql_po_qty;
	                 $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
					 $order_qty_tr+=$po_qty;
			   }
			   //list($dtls_data_row)=$dtls_data;
			   
			     
			   
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_qty_tr;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_qty_tr;
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" );            	 
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_trims_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------	
	 
	 
	 
	 //start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Gmts. Qnty (Dzn)</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_embellishment_amt=0;  
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
				
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_job_qnty;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_embellishment_amt += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------	
	 
	 
	 
	 //start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_commercial_cost=0;
            foreach( $data_array as $row )
            { 
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_commercial_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_commission_cost=0;
            foreach( $data_array as $row )
            { 
				$row[csf("commission_amount")] = $row[csf("commission_amount")]/$order_price_per_dzn*$order_job_qnty;
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_commission_cost += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	//End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
   	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
         <table>
         <tr>
         <td>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_other_components=0;
			$lab_test = 0;
			$inspection = 0;
			$cm_cost = 0;
			$freight = 0;
			$common_oh = 0;
            foreach( $data_array as $row )
            { 
				$lab_test = $row[csf("lab_test")]/$order_price_per_dzn*$order_job_qnty;
				$inspection = $row[csf("inspection")]/$order_price_per_dzn*$order_job_qnty;
				$cm_cost = $row[csf("cm_cost")]/$order_price_per_dzn*$order_job_qnty;
				$freight = $row[csf("freight")]/$order_price_per_dzn*$order_job_qnty;
				$common_oh = $row[csf("common_oh")]/$order_price_per_dzn*$order_job_qnty;
   			?>	 

                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
            <?
                 $total_other_components += $lab_test+$inspection+$cm_cost+$freight+$common_oh;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_other_components,4); ?></td>
                </tr>                
            </table>
            </td>
            <td valign="top" rowspan="2">
             
            <?
     	 // image show here  -------------------------------------------
		 $sql = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id=$txt_job_no limit 1";
		$data_array=sql_select($sql);
 	  ?> 
          <div style="margin:15px 5px;float:right;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='400' width='300' />
            <?  } ?>			
          </div>
          </td>
          </tr>
          <tr>
          <td>
           <?
	
	 $total_summary_amount = 0;	
	 $total_summary_amount = $total_commission_cost+$total_commercial_cost+$total_embellishment_amt+$total_trims_cost+$total_conversion_cost+$total_yarn_amount +$woven_subtotal_amount+$knit_subtotal_amount+$lab_test+$inspection+$cm_cost+$freight+$common_oh;
	
	 ?>
	 <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b>Summary</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Cost Summary</td>
                    <td width="100">Total</td>
                 </tr>
                 <tr> 
                    <td align="left">Knit Fabric (Purchase) </td>
                    <td align="right"><? echo number_format($knit_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Woven Fabric (Purchase)</td>
                    <td align="right"><? echo number_format($woven_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Yarn</td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Conversion to Fabric</td>
                    <td align="right"><? echo number_format($total_conversion_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Trims</td>
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Embellishment</td>
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commercial</td>
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commission</td>
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_summary_amount,4); ?></td>
                </tr>                
            </table> 
          </td>
          </tr>
          </table>
      </div>
      <br/>
     <? 	 echo signature_table(109, $cbo_company_name, "850px"); ?>
      </div>
      
      <!--End CM on Net Order Value Part report here ------------------------------------------->
 	
	 <?

	
}


?>

<?
//generate_report BOM--------------------------------------------------------------
if($action=="bomRpt")
{
	///extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
	if(str_replace("'",'',$txt_po_breack_down_id)=="") 
	{
		$txt_po_breack_down_id_cond=''; 
		$txt_po_breack_down_id_cond1=''; 
		$txt_po_breack_down_id_cond2=''; 
		$txt_po_breack_down_id_cond3=''; 
	}
	else
	{
		$txt_po_breack_down_id_cond=" and d.id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond1=" and id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
	}
 	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$gmtsitem_ratio_array=array();
	$grmnt_items = "";
    $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
	foreach($grmts_sql as $key=>$val)
	{
		$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
		$gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];	
	}
	$grmnt_items = rtrim($grmnt_items,",");
	if($db_type==0)
	{	
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg order by a.job_no"; 
	}
	if($db_type==2)
	{	
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name $txt_style_ref group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg  order by a.job_no"; //a.job_quantity as job_quantity,
	}
	//echo $sql;die;
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">BOM Report</div>
	<?
	
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$ord_qty=0;
		$avg_unit_price=0;
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,file_no,grouping,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';$job_in_ref = '';$job_in_file = '';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
			$job_in_ref .= $val[csf('grouping')].",";
			$job_in_file .= $val[csf('file_no')].",";
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
		$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
		//$ref_con='';
		foreach ($job_ref as $ref)
		{
			$ref_cond.=", ".$ref;
		}
		$file_con='';
		foreach ($job_file as $file)
		{
			if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
		}
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Plan Cut Qnty</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="100"><b><? echo $row[csf("job_quantity")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                        <td>Order Qty</td>
                        <td><b><? echo $row[csf("ord_qty")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    	<td>Style Ref. No</td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                       
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Garments Item</td>
                        <td colspan="3"><b><? echo $grmnt_items; ?></b></td>
                        <td>Shipment Date</td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                    <tr>
                    	<td>Internal Ref</td>
                        <td colspan="2"><b><? echo ltrim($ref_cond,", "); ?></b></td>
                        <td>File No</td>
                        <td colspan="2"><b><? echo $file_cond; ?></b></td>
                    </tr>
                </table>

            <?	
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			$ord_qty=$row[csf("ord_qty")];
	}//end first foearch
	
	
	
	
	//2 Fabric Cost part here------------------------------------------- 	   	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active            from wo_pre_cost_fabric_cost_dtls 
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
		
		$knit_fab="";$woven_fab=""; 
		$knit_subtotal_amount=0;
		 $woven_subtotal_amount=0;$knit_subtotal_amount_dzn=0;$knit_subtotal_amount_kg=0;$woven_subtotal_amount_dzn=0;$woven_subtotal_amount_kg=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
			    $set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
			 
			   $fincons=0;
			   $greycons=0;
			   $order_qty_fab=0;
			   $fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." $txt_po_breack_down_id_cond2 and cons !=0");
			   foreach($fab_dtls_data as $fab_dtls_data_row )
			   {
					 $sql_po_qty_fab=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$fab_dtls_data_row[csf('po_break_down_id')]." and c.item_number_id='".$row[csf('item_number_id')]."' and size_number_id='".$fab_dtls_data_row[csf('gmts_sizes')]."' and  color_number_id= '".$fab_dtls_data_row[csf('color_number_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1");
					 
					 list($sql_po_qty_row_fab)=$sql_po_qty_fab;
	                 $po_qty_fab=$sql_po_qty_row_fab[csf('order_quantity')];
					 //$cons+=($fab_dtls_data_row[csf("cons")]/($order_price_per_dzn*$set_item_ratio))*($po_qty_fab*$set_item_ratio);
					 //echo "(".$po_qty_fab."/(".$order_price_per_dzn."*".$set_item_ratio."))*".$fab_dtls_data_row[csf("cons")]."<br/>";
					 $fincons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
					 $greycons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("requirment")];
					 $order_qty_fab+=$po_qty_fab;
			   }
			//$row[csf("avg_cons")] = $greycons;
			//$row[csf("avg_finish_cons")] = $fincons;
			$knit_cost_dzn=$row[csf("amount")];$woven_cost_dzn=$row[csf("amount")];
 			$row[csf("amount")] = ($row[csf("amount")]/($order_price_per_dzn*$set_item_ratio))*($order_qty_fab*$set_item_ratio);
			//$row[csf("avg_cons")] = ($row[csf("avg_cons")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
			//$row[csf("avg_finish_cons")] = ($row[csf("avg_finish_cons")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
 			//$row[csf("amount")] = ($row[csf("amount")]/($order_price_per_dzn*$set_item_ratio))*($order_job_qnty*$set_item_ratio);
				
			if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				
 				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';	
				$knit_subtotal_avg_cons+=$greycons;
				$knit_subtotal_avg_finish_cons+=$fincons;
            	$knit_subtotal_amount+=$row[csf("amount")];
				
				$knit_subtotal_amount_dzn+=$knit_cost_dzn;
				$knit_subtotal_amount_kg=$knit_subtotal_amount/$knit_subtotal_amount_dzn;
			}			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				$j++;
                 $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>  
                </tr>';	
				$woven_subtotal_avg_cons+=$greycons;
				$woven_subtotal_avg_finish_cons+=$fincons;
				$woven_subtotal_amount+=$row[csf("amount")];
				
				$woven_subtotal_amount_dzn+=$woven_cost_dzn;
				$woven_subtotal_amount_kg=$woven_subtotal_amount/$woven_subtotal_amount_dzn;
			}
        }	
	 
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">					
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="300">Description</td>
							<td width="100">Source</td>
							<td width="100">Gray Fabric Qnty</td>	
							<td width="100">Finish Fab Qnty</td>
 							<td width="50">Rate</td>
							<td width="50">Amount</td>
						</tr>'.$knit_fab;
		$woven_fab = '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here 
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;
		
		//woven fabrics table here 
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					</table></div>';
        echo $woven_fab;           		
  		
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$yarn_data_array=array();
		$sql_y=sql_select("select c.item_number_id,c.order_quantity ,c.plan_cut_qnty,f.job_no,f.count_id,f.copm_one_id,f.percent_one,f.copm_two_id,f.percent_two,f.type_id,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,e.requirment   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id  and c.size_number_id=e.gmts_sizes  and d.id= f.fabric_cost_dtls_id and a.job_no=".$txt_job_no." $txt_po_breack_down_id_cond3 and e.cons !=0    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");
		
		foreach($sql_y as $sql_y_r)
		{
			
			$set_item_ratio=$gmtsitem_ratio_array[$sql_y_r[csf('job_no')]][$sql_y_r[csf('item_number_id')]];
			$cons_qnty = def_number_format(($sql_y_r[csf("cons_qnty")]*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
			$avg_cons_qnty = def_number_format(($sql_y_r[csf("avg_cons_qnty")]*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
			//$amount = $sql_y_r[csf("amount")]*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio));
			 $amount = def_number_format(($cons_qnty*$sql_y_r[csf("rate")]),5,"");
			//$amount = def_number_format(($avg_cons_qnty*$sql_y_r[csf("rate")]),5,"");

			$yarn_data_array[$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_two")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("rate")]][qnty]+=$cons_qnty;
			$yarn_data_array[$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_two")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("rate")]][avg_qnty]+=$avg_cons_qnty;
			$yarn_data_array[$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_two")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("rate")]][amount]+=$amount;
		}
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//Mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//oracle 
				 $sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		        $data_array=sql_select($sql); 
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Avg.Yarn Qnty</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
			<?
			$total_yarn_qty = 0;
          $total_yarn_amount = 0; $total_yarn_cost_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
					
 				$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("copm_two_id")]][$row[csf("percent_two")]][$row[csf("type_id")]][$row[csf("rate")]][qnty];
				$rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("copm_two_id")]][$row[csf("percent_two")]][$row[csf("type_id")]][$row[csf("rate")]][avg_qnty];
				$rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("copm_two_id")]][$row[csf("percent_two")]][$row[csf("type_id")]][$row[csf("rate")]][amount];
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($rowcons_qnty,4); ?></td>
                    <td align="right"><? echo number_format($rowavgcons_qnty,4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($rowamount,4); ?></td>
                </tr>
            <?  
			      $total_yarn_qty+=$rowcons_qnty;
				   $total_avg_yarn_qty+=$rowavgcons_qnty;
				 $total_yarn_amount +=$rowamount;
				 
				  $total_yarn_cost_dzn+=$row[csf("amount")];
				  
				   $total_yarn_avg_cons_qty+=$rowavgcons_qnty;
				  $total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_yarn_qty,4); ?></td>
                    <td align="right"><? echo number_format($total_avg_yarn_qty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	 
	//End Yarn Cost part report here -------------------------------------------
	
	//start	Conversion Cost to Fabric report here -------------------------------------------
	
   	 $conv_data=array();
$sql_conv="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as fid,f.req_qnty,f.avg_req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0  and a.job_no=".$txt_job_no." $txt_po_breack_down_id_cond3  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
UNION ALL
select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as fid,f.req_qnty,f.avg_req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.fabric_description=0  and e.cons !=0  and a.job_no=".$txt_job_no." $txt_po_breack_down_id_cond3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
$data_arr_conv=sql_select($sql_conv);
foreach($data_arr_conv as $conv_row)
{
    $costing_per_qty=0;
	$costing_per=$costing_per_arr[$conv_row[csf('job_no')]];
	if($costing_per==1)
	{
	$costing_per_qty=12	;
	}
	if($costing_per==2)
	{
	$costing_per_qty=1;	
	}
	if($costing_per==3)
	{
	$costing_per_qty=24	;
	}
	if($costing_per==4)
	{
	$costing_per_qty=36	;
	}
	if($costing_per==5)
	{
	$costing_per_qty=48	;
	}
	
	$set_item_ratio=$gmtsitem_ratio_array[$conv_row[csf('job_no')]][$conv_row[csf('item_number_id')]];
	$convcolorrate=array();
	if($conv_row[csf('color_break_down')] !="")
	{
		$arr_1=explode("__",$conv_row[csf('color_break_down')]);
		for($ci=0;$ci<count($arr_1);$ci++)
		{
		$arr_2=explode("_",$arr_1[$ci]);
		$convcolorrate[$arr_2[0]]=$arr_2[1];
			
		}
	}
	$convrate=0;
	
	$convqnty =def_number_format(($conv_row[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))*$conv_row[csf("req_qnty")],5,"");
	$avgconvqnty =def_number_format(($conv_row[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))*$conv_row[csf("avg_req_qnty")],5,"");
	
	if($conv_row[csf('color_break_down')] !="")
	{
	$convrate=$convcolorrate[$conv_row[csf('color_number_id')]];
	}
	else
	{
	$convrate=$conv_row[csf('charge_unit')];
	}
	$convrate=$conv_row[csf('charge_unit')];
	$convamount=def_number_format($convqnty*$convrate,5,"");
	//$convamount=def_number_format($avgconvqnty*$convrate,5,"");


	
	$conv_data[$conv_row[csf('fid')]]['conv_qnty']+=$convqnty;
	$conv_data[$conv_row[csf('fid')]]['avg_conv_qnty']+=$avgconvqnty;
	$conv_data[$conv_row[csf('fid')]]['conv_amount']+=$convamount;

}
	
	
	
	
   	 $sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount,a.color_break_down, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no." order by  a.cons_process";
			
				//$sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit,a.amount,a.color_break_down, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id 
			//from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			//where a.job_no=".$txt_job_no." order by  a.cons_process";
			
			 $sql_count = "select a.cons_process as cons_process
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no." group by a.cons_process order by  a.cons_process";
			$tot_data_array=sql_select($sql_count);
			
			foreach( $tot_data_array as $row )
            {
				$process_id=$row[csf("cons_process")];
				 $process_row+=count($row[csf("cons_process")]);
			}
			
	$data_array=sql_select($sql);
		
 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+2+$process_row; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Required</td>
                    <td width="100">Avg.Required</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_conversion_cost=0;$total_conversion_cost_dzn=0;$total_conversion_cost_kg=0;
			$total_convsion_qty=0;
			$total_avg_convsion_qty=0;$grand_total_conv_qnty=0;$grand_total_avg_convsion_qty=0;$grand_total_conversion_cost=0;
			$process_array_check=array();$k=1;
            foreach( $data_array as $row )
            { 
			
			   /* $color_id_string="";
				$color_break_down_arr=explode("__",$row[csf("color_break_down")]);
				for($co=0; $co<=count($color_break_down_arr); $co++)
				{
					$color_break_down_arr_row=explode("_",$color_break_down_arr[$co]);
					
					//for($cow=0; $cow<=count($color_break_down_arr_row);  $cow++)
					//{
						if($color_break_down_arr_row[1] !=0)
						{
				          $color_id_string.=$color_break_down_arr_row[0].",";
						}
					//}
				}
				$color_id_string=rtrim($color_id_string,",");
				if($color_id_string =="")
				{
					$color_cond="";
				}
				else
				{
				  $color_cond="and c.color_number_id in(".$color_id_string.")";	
				}
				
				$po_break_down_id_string="";*/
				if($row[csf("pre_cost_fabric_cost_dtls_id")] ==0)
				{
				/*	//$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where job_no='".$row[csf("job_no")]."' and cons !=0");
					$po_data_array=sql_select("select c.plan_cut_qnty/a.total_set_qnty as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=f.job_no  and b.id=c.po_break_down_id and b.id=f.po_break_down_id and c.po_break_down_id=f.po_break_down_id and c.item_number_id=d.item_number_id and a.job_no='".$row[csf("job_no")]."' $txt_po_breack_down_id_cond3 and f.cons !=0   $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.id,c.plan_cut_qnty,a.total_set_qnty");
					$po_qty_con=0;
					foreach($po_data_array as $po_data_array_row)
					{
					  $po_qty_con+=$po_data_array_row[csf('order_quantity')];	
					}
				 
 				$req_qnty_c = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con);
				
				$amount_c = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con);*/
				$item_descrition = "All Fabrics";
				}
				else
				{
					/*$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("pre_cost_fabric_cost_dtls_id")]." $txt_po_breack_down_id_cond2 and cons !=0");
					foreach($po_data_array as $po_data_array_row)
					{
					$po_break_down_id_string.=$po_data_array_row[csf('po_break_down_id')].",";	
					}
					
					$sql_po_qty_con=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in(".rtrim($po_break_down_id_string,",").") and c.item_number_id='".$row[csf('item_number_id')]."' $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1");
				 list($sql_po_qty_row_con)=$sql_po_qty_con;
	             $po_qty_con=$sql_po_qty_row_con[csf('order_quantity')];
				 
				$set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
 				$req_qnty_c = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con);
				$amount_c = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con);*/
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				}
				
					$process_id=$row[csf("cons_process")];
				 
                if (!in_array($process_id,$process_array_check) )
						{
							if($k!=1)
							{
								?>
                               <tr>
                                   
                                      <td>&nbsp;</td>
                                    <td><strong>Sub. Total : </strong></td>
                                   
                                    <td align="right"><strong><? echo number_format($total_convsion_qty,4); ?></strong></td>
                                    <td align="right"><strong><? echo number_format($total_avg_convsion_qty,4); ?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><strong><? echo number_format($total_conversion_cost,4); ?></td>
                                </tr>
                                <?
							}
							?>
                            <?
							unset($total_convsion_qty);
							unset($total_avg_convsion_qty);
							unset($total_conversion_cost);
							$process_array_check[]=$process_id; 
							$k++;    
						}
				
				
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    
                    <td align="right"><? echo number_format($conv_data[$row[csf('id')]]['conv_qnty'],4); ?></td>
                     <td align="right"><? echo number_format($conv_data[$row[csf('id')]]['avg_conv_qnty'],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($conv_data[$row[csf('id')]]['conv_amount'],4); ?></td>
                </tr>
            <?
			$total_convsion_qty+=$conv_data[$row[csf('id')]]['conv_qnty'];
			$total_avg_convsion_qty+=$conv_data[$row[csf('id')]]['avg_conv_qnty'];
            $total_conversion_cost += $conv_data[$row[csf('id')]]['conv_amount'];
			
			$grand_total_conv_qnty+=$conv_data[$row[csf('id')]]['conv_qnty'];
			$grand_total_avg_convsion_qty+=$conv_data[$row[csf('id')]]['avg_conv_qnty'];
			$grand_total_conversion_cost+= $conv_data[$row[csf('id')]]['conv_amount'];
			
			$total_conversion_cost_dzn+=$row[csf('amount')];
			$total_conversion_cost_kg=$grand_total_conversion_cost/$total_avg_yarn_qty;//$grand_total_avg_convsion_qty;
            }
            ?>
                
                
                 <tr class="rpt_bottom" style="font-weight:bold">
                   <td>&nbsp;</td>
                    <td align="right">Sub. Total</td>
                     <td align="right"><? echo number_format($total_convsion_qty,4); ?></td>
                      <td align="right"><? echo number_format($total_avg_convsion_qty,4); ?></td> 
                      <td>&nbsp;</td>                   
                    <td align="right"><? echo number_format($total_conversion_cost,4); ?></td>
                </tr>   
                
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2" align="right">Grand Total</td>
                     <td align="right"><? echo number_format($grand_total_conv_qnty,4); ?></td>
                      <td align="right"><? echo number_format($grand_total_avg_convsion_qty,4); ?></td> 
                      <td>&nbsp;</td>                   
                    <td align="right"><? echo number_format($grand_total_conversion_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
	
	
	
	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
 	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Consumption</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_trims_cost=0; 
            foreach( $data_array as $row )
            { 
			   $order_qty_tr=0;
			   $dtls_data=sql_select("select po_break_down_id,cons,country_id from wo_pre_cost_trim_co_cons_dtls where wo_pre_cost_trim_cost_dtls_id=".$row[csf("id")]." $txt_po_breack_down_id_cond2 and cons !=0");
			   foreach($dtls_data as $dtls_data_row )
			   {
				   if($dtls_data_row[csf('country_id')]==0)
					 {
						 $txt_country_cond="";
					 }
					 else
					 {
						 $txt_country_cond ="and c.country_id in (".$dtls_data_row[csf('country_id')].")";
					 }
					 
					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$dtls_data_row[csf('po_break_down_id')]."  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
	                 list($sql_po_qty_row)=$sql_po_qty;
	                 $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
					 $order_qty_tr+=$po_qty;
			   }
				
				$total_trims_cost_dzn+= $row[csf("amount")];
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_qty_tr;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_qty_tr;
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" );            	 
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_trims_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------	
	 
	 
	 
	 //start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Gmts. Qnty (Dzn)</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
         
			   $total_embellishment_amt=0;$total_embellishment_amt_dzn=0;   
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				 $total_embellishment_amt_dzn += $row[csf("amount")];
				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_job_qnty;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_embellishment_amt += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------	
	 
	 
	 
	 //start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_commercial_cost=0;
            foreach( $data_array as $row )
            { 
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$ord_qty;
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_commercial_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
           $total_commercial_cost=0;$total_commercial_cost_dzn=0;
            foreach( $data_array as $row )
            { 
				
				$commission_amount = $row[csf("commission_amount")]/$order_price_per_dzn*$ord_qty;
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($commission_amount,4); ?></td>
                </tr>
            <?
                  $total_commission_cost += $commission_amount;
				 $total_commission_cost += $commission_amount;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	//End Commission Cost Part report here -------------------------------------------	
  
  
	//start	Other Components part report here -------------------------------------------
   	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche  
			from wo_pre_cost_dtls  
			where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	?> 
 
        <div style="margin-top:15px">
         <table>
         <tr>
         <td>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            
			  $total_other_components=0;$lab_test_dzn=0;
		
			$lab_test = 0;
			$inspection = 0;
			$cm_cost = 0;
			$freight = 0;
			$common_oh = 0;
            foreach( $data_array as $row )
            { 
				$lab_test = $row[csf("lab_test")]/$order_price_per_dzn*$ord_qty;
				$inspection = $row[csf("inspection")]/$order_price_per_dzn*$ord_qty;
				$cm_cost = $row[csf("cm_cost")]/$order_price_per_dzn*$ord_qty;
				$freight = $row[csf("freight")]/$order_price_per_dzn*$ord_qty;
				$common_oh = $row[csf("common_oh")]/$order_price_per_dzn*$ord_qty;
				
				$lab_test_dzn=$row[csf("lab_test")];
				$inspection_dzn=$row[csf("inspection")];
				$cm_cost_dzn =$row[csf("cm_cost")];
				$common_oh_dzn =$row[csf("common_oh")];
				$freight_dzn =$row[csf("freight")];
				
   			?>	 
                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
            <?
                 $total_other_components += $lab_test+$inspection+$cm_cost+$freight+$common_oh;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>                    
                    <td align="right"><? echo number_format($total_other_components,4); ?></td>
                </tr>                
            </table>
            </td>
            <td valign="top" rowspan="2">
             
            <?
     	 // image show here  -------------------------------------------
		 $sql = "select id,master_tble_id,image_location
				from common_photo_library  
				where master_tble_id=$txt_job_no limit 1";
		$data_array=sql_select($sql);
 	  ?> 
          <div style="margin:15px 5px;float:right;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='400' width='300' />
            <?  } ?>			
          </div>
          </td>
          </tr>
          <tr>
          <td>
           <?
	
	 $total_summary_amount = 0;	
	 $total_summary_amount = $total_commission_cost+$total_commercial_cost+$total_embellishment_amt+$total_trims_cost+$grand_total_conversion_cost+$total_yarn_amount +$woven_subtotal_amount+$knit_subtotal_amount+$lab_test+$inspection+$cm_cost+$freight+$common_oh;
	
	 ?>
	 <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:500px;text-align:center;" rules="all">
            <label><b>Summary</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Cost Summary</td>
                    <td width="100">Cost/DZN</td>
                    <td width="100">Cost/Kg</td>
                    <td width="100">Total</td>
                 </tr>
                 <tr> 
                    <td align="left">Knit Fabric (Purchase) </td>
                    <td align="right"><? echo number_format($knit_subtotal_amount_dzn,4); ?></td>
                     <td align="right"><? echo number_format($knit_subtotal_amount_kg,4); ?></td>
                    <td align="right"><? echo number_format($knit_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Woven Fabric (Purchase)</td>
                     <td align="right"><? echo number_format($woven_subtotal_amount_dzn,4); ?></td>
                     <td align="right"><? echo number_format($woven_subtotal_amount_kg,4); ?></td>
                    <td align="right"><? echo number_format($woven_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Yarn</td>
                    <td align="right"><? echo number_format($total_yarn_cost_dzn,4); ?></td>
                    <td align="right"><? echo number_format($total_yarn_cost_kg,4); ?></td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Conversion to Fabric</td>
                    <td align="right"><? echo number_format($total_conversion_cost_dzn,4); ?></td>
                      <td align="right"><? echo number_format($total_conversion_cost_kg,4); ?></td>
                    <td align="right"><? echo number_format($grand_total_conversion_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Trims</td>
                     <td align="right"><? echo number_format($total_trims_cost_dzn,4); ?></td>
                      <td align="right" rowspan="9"><? //echo number_format($total_trims_cost_kg,4); ?></td>
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Embellishment</td>
                     <td align="right"><? echo number_format($total_embellishment_amt_dzn,4); ?></td>
                     
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commercial</td>
                    <td align="right"><? echo number_format($total_commercial_cost_dzn,4); ?></td>
                   
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commission</td>
                     <td align="right"><? echo number_format($total_commission_cost_dzn,4); ?></td>
                   
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Lab Test</td>
                     <td align="right"><? echo number_format($lab_test_dzn,4); ?></td>
                     
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection_dzn,4); ?></td>
                    
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost_dzn,4); ?></td>
                    
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                     <td align="right"><? echo number_format($freight_dzn,4); ?></td>
                    
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh_dzn,4); ?></td>
                   
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="right" colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_summary_amount,4); ?></td>
                </tr>                
            </table> 
          </td>
          </tr>
          </table>
      </div>
      <br/>
      <?
       echo signature_table(109, $cbo_company_name, "850px"); ?>
     
      </div>
      
      <!--End CM on Net Order Value Part report here ------------------------------------------->
	
	 <?
	
}
?>







