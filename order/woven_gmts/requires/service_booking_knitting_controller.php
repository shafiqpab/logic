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
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');

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
    
         
				<table width="900"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="890" class="rpt_table" align="center" rules="all">
                                <thead>                	 
                                    <th width="150">Company Name</th>
                                    <th width="140">Buyer Name</th>
                                    <th width="100">Job No</th>
                                    <th width="130">Order No</th>
                                    <th width="200">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_knitting_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:85px" value="<? echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:85px" value="<? echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value, 'create_po_search_list_view', 'search_div', 'service_booking_knitting_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
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
		 $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.approved=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";  
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,120,90,120,70,80","900","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,1,0,1,3','','');
	}
	else
	{
		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer order by a.job_no";
		
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
		echo "load_drop_down( 'requires/service_booking_knitting_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=2 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		//echo "load_drop_down( 'requires/service_booking_knitting_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
}


// =================================
// program no 
if ($action=="programs_search_popup")
{
  	echo load_html_head_contents("Program Search","../../../", 1, 1, $unicode);
    ?>
     
	<script>	 
        function js_set_value(str_data)
        {         
			document.getElementById('selected_program_no_primary_id').value=str_data;
            parent.emailwindow.hide();
        }	
    </script>

    </head>

    <body>
        <div align="center" style="width:100%;" >            
            <input type="hidden" id="selected_program_no_primary_id">
            <?
                extract($_REQUEST);
               
                if ($orderNo!='') 
                {
                    $orderNumbers = explode(",",$orderNo);
                    $order_numbers = "";
                    //KNITTING_PARTY
                    
                    foreach($orderNumbers as $orderNumber)
                    {         
                        if($order_numbers=="") $order_numbers="'".$orderNumber."'"; else $order_numbers .=",'".$orderNumber."'";
                    }

                    if($supplier_id !=0) 
                    {
                        $supplier_cond = "and knitting_party='".$supplier_id."'";
                    }
                                     
                     //echo "SELECT id FROM wo_po_break_down WHERE job_no_mst='$jobNo' AND id IN($order_id)";
                   /* $wo_po_sql = sql_select("SELECT id FROM wo_po_break_down WHERE job_no_mst='$jobNo' AND id IN($order_id)");
					
                    $poids = "";
                    foreach($wo_po_sql as $row)
                    {
                        if($poids == "") 
                        {
                            $poids = $row[csf('id')];  
                        } 
                        else 
                        {
                            $poids .= ",".$row[csf('id')];  
                        }
                    }*/
					
					
                    $poids=$order_id;
                    $arr=array (2=>$body_part,3=>$color_type);
                    $sql = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,b.id as fabric_des_id FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and c.knitting_source = 3 and b.cons_process=1 and a.po_id in($poids) $supplier_cond";
					 
										  //$sql = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,b.id as fabric_des_id FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and c.knitting_source = 3 and a.po_id in($poids) $supplier_cond";

                    //create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn )
                    echo  create_list_view("list_view", "Plan Id,Program no,Body Part,Color Type,Fabric Desc,Fabric Gsm,Dia,Prog. Qnty", "70,80,100,100,200,100,100,80,80","930","320",0, $sql , "js_set_value", "id,fabric_des_id", "", 1, "0,0,body_part_id,color_type_id,0,0,0,0", $arr , "plan_id,program_no,body_part_id,color_type_id,fabric_desc,gsm_weight,dia,program_qnty", '','','0,0,0,0,0,0,0,0,0,0','',''); 
                    
                }
            ?>             
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action=="populate_data_from_program_popup")
{      
    if($data !='')
    {
        $sql = "SELECT id, mst_id as plan_id,dtls_id as program_no,booking_no,body_part_id,color_type_id,fabric_desc,gsm_weight,dia,program_qnty FROM ppl_planning_entry_plan_dtls WHERE id = $data";
        $data_array=sql_select($sql);
           
        foreach ($data_array as $row)
        {
            
            echo "document.getElementById('txt_program_no').value = '".$row[csf("program_no")]."';\n";    
            
            echo "$('#cbo_supplier_name').prop('disabled', true);";
        }  
    }
}
// =================================


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
} 

if ($action=="load_drop_down_fabric_description")
{	
	$data=explode("_",$data);
    
	$fabric_description_array=array();
	
	if($data[1] =="")
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no='$data[0]' and cons_process=1 ");
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0 and cons_process=1  ");
	}
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row)
	{
		if($row[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id, body_part_id, color_type_id, fabric_description, gsm_weight from wo_pre_cost_fabric_cost_dtls where  id='".$row[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			
			$fabric_description_array[$row[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			
		}
		
		if($row[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id, body_part_id, color_type_id, fabric_description, gsm_weight from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row[csf("cons_process")]].', '.$row[csf("gsm_weight")];
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description", 470, $fabric_description_array,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_booking_knitting_controller',this.value, 'load_drop_down_dia', 'dia_td'); set_process(this.value,'set_process')" );
	exit();
} 

if($action=='load_drop_down_dia')
{
	$exdata=explode("**",$data);    
	$fab_cost_dtls_id=return_library_array( "select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls where id='$exdata[0]'",'id','fabric_description');
	$dtls_id=$fab_cost_dtls_id[$exdata[0]];
	//echo "select dia_width from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id='$dtls_id' and dia_width!=0 group by dia_width order by dia_width ASC";
	echo create_drop_down( "cbo_dia", 80, "select dia_width from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id='$dtls_id' group by dia_width order by dia_width ASC","dia_width,dia_width", "", "", $exdata[1], "" );
    //echo "document.getElementById('txt_program_no').value = '".$exdata[2]."';\n";   
    exit();
}
 
if($action=="set_process")
{
	$process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	echo $process; die;
}
 
if($action=="lapdip_approval_list_view_edit")
{
	$data=explode("**",$data);  
    
	$job_no=$data[0];
	$type=$data[1];
	$process=$data[3];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
    $programNo=$data[9];
    
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
	if($type==0)
	{
        $sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	       sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
	       a.amount,b.size_number_id,b.color_number_id,a.lib_composition,a.lib_supplier_rate_id
		   from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and
		   a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id and a.job_no='$job_no' and a.booking_type=3 and a.process=1 and
		   a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and   a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 ";
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
	
		if($rate_from_library==1)
		{
			$rate_disable="disabled";	
		}
		else
		{
			$fab_mapping_disable="disabled";
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
								<input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-'); ?>" style="width:70px;" class="datepicker">
							</td>
                            <td>
								<input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[csf("delivery_end_date")],'dd-mm-yyyy','-'); ?>" style="width:70px;" class="datepicker">
							</td>
                            <td>
								<input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty'); calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $row[csf("wo_qnty")]; ?>"/>
							</td>
                            <td>
								<input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:50px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $row[csf("rate")]; ?>" <?php //echo $rate_disable; ?>>
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
		
		if($sensitivity==0 && $programNo =='')
		{
			$groupby="group by b.id,b.po_number";
            
            $sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnt ,
                d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per ,
                g.gmts_sizes,g.item_size,CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN 
                round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) WHEN 4 THEN 
                round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
                from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d,wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls f,wo_pre_cos_fab_co_avg_con_dtls g 
                where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and  f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no' and e.id in($fabric_description_id) and b.id  in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 
                group by b.id, b.po_number, d.costing_per, e.fabric_description, e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id, f.costing_per ,g.gmts_sizes,g.item_size";
     
			$sql2="select b.id as po_break_down_id, min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  
			from wo_po_break_down b, wo_po_color_size_breakdown c 
			where  b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='$job_no' and b.id  in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $groupby";


		}
		
		
		else if(($programNo =='') && $sensitivity==1 || $sensitivity==3 )
		{
			
			/* //query updte by jahid
			 $sql1="select b.id as po_break_down_id,b.po_number,min(c.id)as color_size_table_id,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty,
			 d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down,f.body_part_id,f.costing_per,
			 g.gmts_sizes,g.item_size,CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) WHEN 2 THEN
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
			 e.color_break_down,f.body_part_id,f.costing_per ,g.gmts_sizes,g.item_size";*/
			 
			 $pre_item_color_arr=return_library_array("select pre_cost_fabric_cost_dtls_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls group by pre_cost_fabric_cost_dtls_id, contrast_color_id","pre_cost_fabric_cost_dtls_id","contrast_color_id");
			 
			 $sql1="select b.id as po_break_down_id, b.po_number,min(c.id)as color_size_table_id, c.color_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty, d.costing_per, e.fabric_description, e.cons_process, e.req_qnty, e.charge_unit, e.amount, e.color_break_down, f.id as pre_cost_fabric_cost_dtls_id, f.body_part_id, f.costing_per,
			 CASE f.costing_per WHEN 1 THEN round((e.req_qnty/12)*sum(c.plan_cut_qnty),4) 
			 WHEN 2 THEN round((e.req_qnty/1)*sum(c.plan_cut_qnty),4)  
			 WHEN 3 THEN round((e.req_qnty/24)*sum(c.plan_cut_qnty),4) 
			 WHEN 4 THEN  round((e.req_qnty/36)*sum(c.plan_cut_qnty),4) 
			 WHEN 5 THEN round((e.req_qnty/48)*sum(c.plan_cut_qnty),4) ELSE 0 END as wo_req_qnty 
			 from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f, wo_pre_cos_fab_co_avg_con_dtls g  
			 where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes   and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and a.job_no='$job_no'  and e.id in($fabric_description_id) and b.id in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  
			 group by b.id, b.po_number, c.color_number_id, d.costing_per, e.fabric_description, e.cons_process, e.req_qnty, e.charge_unit, e.amount, e.color_break_down, f.id,  f.body_part_id, f.costing_per";
			 
			 $groupby="group by b.id,b.po_number,c.color_number_id";
			 
			 $sql2="select b.id as po_break_down_id, c.color_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty  
			 from wo_po_break_down b, wo_po_color_size_breakdown c 
			 where b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and  b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 
			 $groupby";
			 
			//echo $sql1."<br>".$sql2."<br>";

		}
		else if($sensitivity==2 && $programNo =='')
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
			and f.is_deleted=0 $groupby";
			
		    $sql2="select b.id as po_break_down_id, c.size_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty 
			from wo_po_break_down b, wo_po_color_size_breakdown c where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and 
			b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			$groupby";
			

		}
		else if($sensitivity==4 && $programNo =='')
		{
			
		 	$groupby="group by b.id,b.po_number,c.color_number_id,c.size_number_id";
			$sql1="select b.id as po_break_down_id,b.po_number,min(c.id) as color_size_table_id,c.size_number_id,c.color_number_id,
			sum(c.plan_cut_qnty) as plan_cut_qnty ,
			d.costing_per,e.fabric_description,e.cons_process,e.req_qnty,e.charge_unit,e.amount,e.color_break_down, f.id as pre_cost_fabric_cost_dtls_id,f.body_part_id,f.costing_per,
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
			e.amount,e.color_break_down, f.id, f.body_part_id,f.costing_per,g.gmts_sizes,g.item_size";
			
		 	$sql2="select b.id as po_break_down_id, c.color_number_id,c.size_number_id,min(c.id)as color_size_table_id,sum(c.plan_cut_qnty) as plan_cut_qnty
			from wo_po_break_down b, wo_po_color_size_breakdown c 
			
			where 	b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst='$job_no' and b.id in($txt_order_no_id) and b.status_active=1 
			and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $groupby";
			
		} 
        else if (($sensitivity == 1 || $sensitivity == 0) && $programNo!='')
        {
            $sql1 = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id as po_break_down_id, b.id as fabric_des_id,b.amount,b.charge_unit,min(d.id)as color_size_table_id,sum(d.plan_cut_qnty) as plan_cut_qnty
            FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c, wo_po_color_size_breakdown d WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and c.knitting_source = 3 and b.cons_process=1 and a.dtls_id=$programNo and a.po_id=d.po_break_down_id group by a.id,a.mst_id,a.dtls_id,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id , b.id,b.amount,b.charge_unit
            "; 
        }
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
					if ($sensitivity == 1 && $programNo!='')
                    {
                        $dataArray=sql_select($sql1);
                        $i = 1;
                        foreach($dataArray as $row)
                        {
                            
                            if($row[csf("body_part_id")]==2 || $row[csf("body_part_id")]==3 )
                            {
                                //$woqnty=$row[csf("plan_cut_qnty")]*2;
                                $uom_item="1,2";
                                $selected_uom="1";
                            }
                            else 
                            {
                                //$woqnty=$row[csf("wo_req_qnty")];
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
                                $amount=$rate*$row[csf("program_qnty")];
                            }
                            
                            if($rate_from_library==1)
                            {
                                $rate='';
                                $amount="";
                                $rate_disable="disabled";	
                            }
                            else
                            {
                                $fab_mapping_disable="disabled";
                            }
                            
                            /* $woqnty="";
                            $item_color_all="";
                            $item_color_id_all="";
                            
                            if($sensitivity==3)
                            { 
                                $item_color_all=$color_library[$pre_item_color_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]];
                                $item_color_id_all=$pre_item_color_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]];
                            } 
                            else if($sensitivity==1 || $sensitivity==4 ) 
                            { 
                                $item_color_all=$color_library[$row[csf("color_number_id")]];
                                $item_color_id_all=$row[csf("color_number_id")];
                            } */
                            
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
                                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color_all; ?>"/>
                                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id_all; ?>" disabled="disabled"/>
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
                                    <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $row[csf("program_qnty")]; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" <?php //echo $rate_disable; ?>>
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
                    else 
                    {
                        $dataArray=sql_select($sql1);
                        if(count($dataArray)==0)
                        {
                            $dataArray=sql_select($sql2);
                        }
                        $i=1;
                        //print_r($dataArray);
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
                            if($rate_from_library==1)
                            {
                                $rate='';
                                $amount="";
                                $rate_disable="disabled";	
                            }
                            else
                            {
                                $fab_mapping_disable="disabled";
                            }
                            $item_color_all="";
                            $item_color_id_all="";
                            
                            if($sensitivity==3)
                            { 
                                $item_color_all=$color_library[$pre_item_color_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]]];
                                $item_color_id_all=$pre_item_color_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]];
                            } 
                            else if($sensitivity==1 || $sensitivity==4 ) 
                            { 
                                $item_color_all=$color_library[$row[csf("color_number_id")]];
                                $item_color_id_all=$row[csf("color_number_id")];
                            }
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
                                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color_all; ?>"/>
                                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id_all; ?>" disabled="disabled"/>
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
                                    <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $woqnty; ?>"/>
                                </td>
                                <td>
                                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" <?php //echo $rate_disable; ?>>
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
	
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ");
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
	$sql="select a.id, a.job_no, b.booking_no, $group_concat, b.dia_width, b.pre_cost_fabric_cost_dtls_id, sum(b.amount) as amount, b.process, b.sensitivity,
	sum(b.wo_qnty) as wo_qnty, b.insert_date,b.program_no from wo_booking_dtls b, wo_booking_mst a
  	where b.booking_no=a.booking_no and a.booking_no='$data[1]'and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	and b.process=1
  	group by a.job_no,a.id, b.dia_width, b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date,b.program_no";
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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")]."_".$row[csf("dia_width")]."_".$row[csf("program_no")];?>","child_form_input_data","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
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
			if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
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
			pay_mode,source,attention,ready_to_approved,process,inserted_by,insert_date";//
			$data_array ="(".$id.",3,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_ready_to_approved.",".$cbo_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
		 disconnect($con);	die;
		}
		 
		 $field_array_up="booking_type*booking_month*booking_year*booking_no*company_id*buyer_id*job_no*po_break_down_id*
		 item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*ready_to_approved*updated_by*update_date";
		 $data_array_up ="3*".$cbo_booking_month."*".$cbo_booking_year."*".$txt_booking_no."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, pre_cost_fabric_cost_dtls_id, artwork_no, po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, item_size, gmts_size, description, dia_width, uom, process, sensitivity, wo_qnty, rate, amount, delivery_date, delivery_end_date,lib_composition,lib_supplier_rate_id,inserted_by, insert_date,program_no";
		 $new_array_color=array();
		// echo "10**jahid##$row_num";die;
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
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
             
			 
			 //echo "10**".$$item_color_id;die;
			 
			 $new_array_color=return_library_array( "select a.fabric_color_id, b.id, b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			/* if (!in_array(str_replace("'","",$$item_color),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$item_color);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }*/
			 
			 //echo "10**$color_id";die;
			 
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$$fabric_description_id.",".$$artworkno.",".$$po_id.",".$$color_size_table_id.",".$txt_job_no.",".$txt_booking_no.",3,".$$item_color_id.",".$$gmts_color_id.",".$$item_size.",".$$gmts_size_id.",".$$fabric_description_id.",".$cbo_dia.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$lib_composition.",".$$lib_supplier_rateId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$program_no."')";
		     $id_dtls=$id_dtls+1;
		 }
		//echo "10** insert into wo_booking_dtls ($field_array1) values $data_array1";die; 
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
		 $is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id
*gmts_color_id*item_size*gmts_size*description*dia_width*uom*process*sensitivity*wo_qnty*rate*amount*delivery_date* delivery_end_date* lib_composition* lib_supplier_rate_id*updated_by*update_date*program_no";
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
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$item_color);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }

			if(str_replace("'",'',$$updateid)!="")
			{
			$id_arr[]=str_replace("'",'',$$updateid);
			$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$po_id."*".$$color_size_table_id."*".$txt_job_no."*".$txt_booking_no."*3*".$$item_color_id."*".$$gmts_color_id."*".$$item_size."*".$$gmts_size_id."*".$$fabric_description_id."*".$cbo_dia."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$lib_composition."*".$$lib_supplier_rateId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"."*".$program_no));
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
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
 



	
		
function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";
			
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}
		
	   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	   //return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);
		
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";
	    
	}
    //return  $strQuery; die;
	echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360); 
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	        $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con); 
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	//else
		//return 0;
		
	die;
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
	<table width="1020" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th width="200">Company Name</th>
                <th width="200">Buyer Name</th>
                <th width="220">Booking No</th>
                <th width="220">Job No</th>
                <th width="220">Date Range</th>
                <th></th> 
            </tr>                	 
        </thead>
        <tr class="general">
            <td align="center"> <input type="hidden" id="selected_booking">
                <? 
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_knitting_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
            </td>
            <td id="buyer_td"  align="center">
                <? 
                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
                ?>
            </td>
            <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px"></td>            
            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
            <td  align="center">
            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
            </td> 
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_booking_search_list_view', 'search_div', 'service_booking_knitting_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
        </tr>
        <tr>
        	<td  align="center" height="40" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?> </td>
        </tr>
    </table>
    </form>
	<br>
    <div id="search_div"> </div>    

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
	//if ($data[4]!="") $jobNoCond=" and a.job_no='$data[4]'"; else $jobNoCond="";
   	if (str_replace("'","",$data[4])!="") $jobNoCond=" and a.job_no like '%$data[4]%'"; else  $jobNoCond=""; 
    if (str_replace("'","",$data[5])!="") $bookingNoCond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $bookingNoCond="";

    
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
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$po_no=return_library_array( "select id, job_no_prefix_num from wo_po_details_master",'id','job_no_prefix_num');
	
	$arr=array (2=>$comp,3=>$buyer_arr,6=>$item_category,7=>$fabric_source,8=>$suplier);
	//$arr=array (3=>$comp,3=>$buyer_arr,5=>$po_no,6=>$item_category,7=>$fabric_source,8=>$suplier);

    $sql= "select a.process,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
    a.job_no,a.po_break_down_id,b.id,a.item_category,a.fabric_source,a.supplier_id,b.po_number from wo_booking_mst a,wo_po_break_down b 
    where a.job_no = b.job_no_mst and $company $buyer $booking_date $jobNoCond $bookingNoCond and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.process=1  order by a.booking_no"; 
  
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO Number,Fabric Nature,Fabric Source,Supplier", "70,80,100,100,110,150,100,100","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,0,item_category,fabric_source,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_number,item_category,fabric_source,supplier_id", '','','0,0,0,0,0,0,0,0,0','','');
    
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
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
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
                <td width="100" style="font-size:12px"><b>Work order No</b>   </td>
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
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $txt_job_no=rtrim($job_no,',');
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
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
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('fabric_color_id')]." and wo_qnty>0 and is_deleted=0 and status_active=1");                          
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
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=1"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."'");  
	
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=1"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'");                          
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
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
           <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td>
           <?
		  	 echo get_spacial_instruction($txt_booking_no);
		  ?>
          </td>
          </tr>
           </table>
        <table  width="90%" class="rpt_table" style="border:1px solid black; display:none"   border="0" cellpadding="0" cellspacing="0">
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
					$po_qty_arr=array();$knit_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date order by  b.id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS knit_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$knit_data_arr[$row[csf('job_no')]]['knit']=$row[csf('knit_cost')];	
					}
					$i=1; $total_balance_knit=0;$tot_knit_cost=0;$tot_pre_cost=0;
					$sql_knit=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_knit );
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_knit=($knit_data_arr[$selectResult[csf('job_no')]]['knit']/$costing_per_qty)*$po_qty;
						$knit_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
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
                     <? echo number_format($pre_cost_knit,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($knit_charge,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_knit-$knit_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_knit>$knit_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_knit<$knit_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_knit==$knit_charge) 
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
	  	 $tot_pre_cost+=$pre_cost_knit;
	  	 $tot_knit_cost+=$knit_charge;
		 $total_balance_knit+=$tot_balance;
	   $i++;
					
	 }
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_knit_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_knit,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
    <? } ?>
         <br/>
		 <?
            echo signature_table(81, $cbo_company_name, "1113px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
         ?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
<?
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
                            <strong>Service Booking Sheet For Knitting</strong>
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
		$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.="'".$result_job[csf('po_number')]."'".",";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")";
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")", "po_break_down_id", "article_number"  );
		//print_r($article_number_arr);
		$booking_date=$nameArray[0][csf('booking_date')];
        foreach ($nameArray as $result)
        {
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
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="3">:&nbsp;
				<? 
				echo $txt_job_no=rtrim($job_no,',');
				?> 
                </td>
            </tr> 
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim(str_replace("'","",$po_no),','); ?> </td>
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
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no  and process=1 ");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and  process=1 "); //and sensitivity=1
		
       
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>Program No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons,program_no from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 group by po_break_down_id,fabric_color_id,description,rate,artwork_no,program_no ");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_item[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('program_no')]; ?> </td>
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
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=1"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and process=1 ");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."'");  
	
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=1"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and process=1  and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'");                          
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
		   //echo $currency_id.'aaaaa';
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
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,''),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
           <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
            <tr style="border:1px solid black;">
            <td>
				<?
                 echo get_spacial_instruction($txt_booking_no);
                ?>
            </td>
            </tr>
           </table>
        <table  width="90%" class="rpt_table" style="border:1px solid black; display:none"   border="0" cellpadding="0" cellspacing="0">
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
					$po_qty_arr=array();$knit_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date order by  b.id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS knit_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$knit_data_arr[$row[csf('job_no')]]['knit']=$row[csf('knit_cost')];	
					}
					
					if($db_type==0)
					{
						$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
					}
					
					
					
					$i=1; $total_balance_knit=0;$tot_knit_cost=0;$tot_pre_cost=0;
				
					$sql_knit=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_knit );
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
						$pre_cost_knit=($knit_data_arr[$selectResult[csf('job_no')]]['knit']/$costing_per_qty)*$po_qty;
						
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$knit_charge=$selectResult[csf("amount")]/$currency_rate;	
						}
						else
						{
							$knit_charge=$selectResult[csf("amount")];
						}
						//$knit_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
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
                     <? echo number_format($pre_cost_knit,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($knit_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_knit-$knit_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_knit>$knit_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_knit<$knit_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_knit==$knit_charge) 
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
	  	 $tot_pre_cost+=$pre_cost_knit;
	  	 $tot_knit_cost+=$knit_charge;
		 $total_balance_knit+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_knit_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_knit,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
        
         <br/>
        
		 <?
            echo signature_table(81, $cbo_company_name, "1150px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
         ?>
    </div>
<?
}
if($action=="show_trim_booking_report2" || $action=="show_trim_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.is_approved  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$is_approved=$result_job[csf('is_approved')];
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
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                             <td  width="50px">&nbsp; </td>
                             <td align="right">  &nbsp; &nbsp;<? if($is_approved==1) echo "<font style='color:red;font-size:25px;'><i>Approved</i></font>";else echo "";?></td>
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		
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
                <td width="100" style="font-size:12px"><b>Work Order No </b>   </td>
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
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $txt_job_no=rtrim($job_no,',');
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
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
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                <td style="border:1px solid black"><strong>Program No</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                		
                <td style="border:1px solid black" align="center"><strong>W/O Qty</strong></td>
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
            $nameArray_item_description=sql_select( "select distinct wo_qnty,program_no,description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                <td align="center" style="border:1px solid black" ><? echo $result_itemdescription[csf('program_no')]; ?></td>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                

                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qnty')],2); $total_wo+=$result_itemdescription[csf('wo_qnty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_wo_qnty = $result_itemdescription[csf('wo_qnty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_wo_qnty,4);
                $total_amount_as_per_wo_qnty+=$amount_as_per_wo_qnty;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong>Total</strong></td>
               
                <td style="border:1px solid black;  text-align:right"><? echo number_format($total_wo,2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_wo_qnty,4);
                $grand_total_as_per_wo_qnty+=$total_amount_as_per_wo_qnty; $booking_grand_total+=$grand_total_as_per_wo_qnty;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
           
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=1"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."'");  
	
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=1"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'");                          
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
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
            <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
            <tr>
            <td>
				<?
                 echo get_spacial_instruction($txt_booking_no);
                ?>
            </td>
            </tr>
           </table>
           <!--Not used-->
        <table  width="90%" class="rpt_table" style="border:1px solid black; display:none"   border="0" cellpadding="0" cellspacing="0">
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
    <? if ($show_comments!=1) { ?>
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
					$po_qty_arr=array();$knit_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date order by  b.id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS knit_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$knit_data_arr[$row[csf('job_no')]]['knit']=$row[csf('knit_cost')];	
					}
					$i=1; $total_balance_knit=0;$tot_knit_cost=0;$tot_pre_cost=0;
					$sql_knit=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_knit );
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_knit=($knit_data_arr[$selectResult[csf('job_no')]]['knit']/$costing_per_qty)*$po_qty;
						$knit_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
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
                     <? echo number_format($pre_cost_knit,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($knit_charge,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_knit-$knit_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_knit>$knit_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_knit<$knit_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_knit==$knit_charge) 
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
	  	 $tot_pre_cost+=$pre_cost_knit;
	  	 $tot_knit_cost+=$knit_charge;
		 $total_balance_knit+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_knit_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_knit,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
    <br/>
	<?
	}
    echo signature_table(81, $cbo_company_name, "1113px");
	echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
    ?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
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
	 $sql= "select booking_no,booking_date,company_id,buyer_id,is_approved,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,ready_to_approved from wo_booking_mst  where booking_no='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_process').value = '1';\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
        echo "$('#cbo_supplier_name').prop('disabled', true);";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_status').innerHTML = 'This booking is approved';\n";
			
		}else{
			echo "document.getElementById('app_status').innerHTML = '';\n";
			
		}
		
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		echo "load_drop_down( 'requires/service_booking_knitting_controller', '".$row[csf("job_no")]."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=2 and company_name=".$row[csf("company_id")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		
		//echo "load_drop_down( 'requires/service_booking_knitting_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";


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
                        <th width="150">Body Part</th>
                        <th width="200">Construction & Composition </th>
                        <th width="50">GSM</th>
                        <th width="150">Yarn Description </th>
                        <th width="50">UOM</th>
                        <th width="">rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?
						
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.body_part as BODY_PART,d.const_comp as CONST_COMP,d.gsm as GSM,d.yarn_description as YARN_DESCRIPTION,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=20 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=2 and d.comapny_id=$cbo_company_name and a.id=$cbo_supplier_name");
						
						$i=1;
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rate=$row['RATE']/($txt_exchange_rate*1);
							if($hidden_supplier_rate_id==$row['ID'])  $bgcolor="#FFFF00";
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25" onClick="js_set_value('<? echo $row['ID']; ?>','<? echo $rate; ?>','<? echo $row['CONST_COMP']; ?>')" style="cursor:pointer"> 
								<td><?php echo $i; ?></td>
								<td align="left"><? echo $body_part[$row['BODY_PART']]; ?>
								</td>
                                <td align="left"><? echo $row['CONST_COMP']; ?></td>
                                <td align="left"><? echo $row['GSM']; ?></td>
                                <td align="left"><? echo $row['YARN_DESCRIPTION']; ?></td>
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