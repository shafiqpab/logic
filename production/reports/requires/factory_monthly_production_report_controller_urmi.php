<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if (!function_exists("pre")) 
 {
	 function pre($array){
		 echo "<pre>";
		 print_r($array);
		 echo "</pre>";
	 } 	 
 }

$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
$color_Arr_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
//  $production_process=array(1=>'Cutting',2=>'Sewing Input',3=>'Sewing Output',4=>'Poly Output',5=>'Packing & Finishing',6=>'Ex-factory');
 $production_process=array(7=>'Cut & Lay',1=>'Cutting QC',11=>'Printing',12=>'Embroidery',2=>'Sewing Input',3=>'Sewing Output',8=>'Wash Send',9=>'Wash Receive',10=>'Finishing(Getup Pass)',4=>'Poly Output',5=>'Packing & Finishing',6=>'Ex-factory');

if($action=="print_button_variable_setting")
{
	// echo "YES-33";
	extract($_REQUEST); // 230
	$buttonIdArr = ['108#show_button', '149#show_button1', '150#show_button2'];
	$print_report_format_arr = get_report_button_array($data, 7, 82, $user_id, $buttonIdArr);
	exit();
}

 if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/floor_wise_sewing_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/floor_wise_sewing_wip_report_controller' );",0 );     	 
}
 if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 110, "select id, season_name from lib_buyer_season where buyer_id=$data and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
}


 

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}
if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	//$data=explode("_",$data);
	if($data[0]==1 || $data[0]==7) $floor_type=" and production_process=1";//Cutting QC
	else if($data[0]==2 || $data[0]==3 || $data[0]==4) $floor_type="and production_process=5";//sewing
	else if($data[0]==5) $floor_type="and production_process=11";//Gmts Finishing
	else $floor_type="";
	
	
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($data[1]) $floor_type group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();    	 
}


if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//txt_job_no
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'factory_monthly_production_report_controller_urmi', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$type_id=$data[5];
	$job=$data[6];
	
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	//if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	$search_value=$data[3];
	//$search_string="%".trim($data[3])."%";
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_by==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	 
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1)
	{
		$field_type="id,job_no_prefix_num";
	}
	else if($type_id==2)
	{
		$field_type="id,style_ref_no";
	}
	else if($type_id==3)
	{
		$field_type="id,po_number";
	}
	  $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and a.company_name=$company_id $search_con $buyer_id_cond $year_cond   order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No", "120,130,80,50,120","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
} // Job Search end


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_working_company_id=str_replace("'","",$cbo_working_company_id);
	if(str_replace("'","",$cbo_working_company_id)==0)
	{
		$working_company="";
		$working_company7="";
	}
	else
	{
		$working_company=" and c.serving_company in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company_subcon=" and a.company_id in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company7=" and c.working_company_id in(".str_replace("'","",$cbo_working_company_id).")";
		$working_company8=" and c.SENDING_COMPANY in(".str_replace("'","",$cbo_working_company_id).")";
	}
	 
	if(str_replace("'","",$txt_job_no)=="")
	{ 
		$job_cond=""; 
		$job_cond_subcon=""; 
	}
	else
	{
		$job_cond="and a.job_no_prefix_num=".trim($txt_job_no)."";
		$job_cond_subcon="and b.job_no_prefix_num=".trim($txt_job_no)."";
	} 
	if (str_replace("'","",$txt_style_ref)=="") 
	{
		$style_cond="";
	}
	else
	{
		$style_cond="and a.style_ref_no=".trim($txt_style_ref)."";
	} 
	if (str_replace("'","",$txt_po_no)=="") 
	{
		$order_cond="";
		$order_cond_subcon=""; 

	}
	else
	{
		$order_cond="and b.po_number=".trim($txt_po_no).""; 
		$order_cond_subcon=" and c.order_no=".trim($txt_po_no).""; 
	} 
	if(str_replace("'","",$cbo_floor)==0)
	{
		$floor_cond=""; 
		$floor_cond_subcon="";

	}
	else 
	{
		$floor_cond.=" and c.floor_id=$cbo_floor";
		$floor_cond_subcon.=" and a.floor_id=$cbo_floor";
	}
	$floor_group=trim(str_replace("'","",$txt_floor_group));
	if($floor_group)
	{
		$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$floor_group' and status_active=1 ");
		$floor_group_arr=array();
		$floor_group_arr[0]=0;
		foreach($floor_group_sql as $fl)
			$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
		$all_floor_by_group=implode(",",$floor_group_arr);
		$floor_cond.=" and c.floor_id in($all_floor_by_group)";
		$floor_cond_subcon.=" and a.floor_id in($all_floor_by_group)";
		

	}
	else 
	{
		$floor_cond.=""; 
		$floor_cond_subcon.="";
	}


	if(str_replace("'","",$cbo_location)==0 || str_replace("'","",$cbo_location)=="")
	{
		$location_cond=""; 
		$location_cond_subcon="";

	}
	else 
	{
		$cbo_location = str_replace("'","",$cbo_location);
		$location_cond_subcon = str_replace("'","",$cbo_location);

		$location_cond=" and c.location in($cbo_location)";
		$location_cond_subcon=" and a.location_id in($cbo_location)";
	}

	$process_rpt_type=str_replace("'","",$cbo_production_process);
	$cbo_year=str_replace("'","",$cbo_year);
	$report_type=str_replace("'","",$type);
	$working_company_id=str_replace("'","",$cbo_working_company_id);
	$year_cond="";
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	$season_id=str_replace("'","",trim($cbo_season_id));
	$season_cond = $season_id ? " and a.season_buyer_wise=$season_id " : "";	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		$buyer_id_cond_subcon=" and b.party_id=$cbo_buyer_name";
	}
		
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($end_date,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($txt_date_from,"","",1);
			$end_date=change_date_format($txt_date_to,"","",1);
		}
		$prod_date_cond=" and c.production_date between '$start_date' and '$end_date'";
		$prod_date_cond_subcon=" and a.production_date between '$start_date' and '$end_date'";
		$prod_date_cond2=" and d.ex_factory_date between '$start_date' and '$end_date'";
		$prod_date_cond2_subcon=" and a.delivery_date between '$start_date' and '$end_date'";
		$prod_date_cond7=" and c.entry_date between '$start_date' and '$end_date'";
	}
		//$month=date("m",strtotime($start_date))-1;
		//echo date('Y-m', strtotime('-1 month', time()));
		$start_date=date("d-m-Y",strtotime($start_date));
		$start_date_tmp=date("Y-m",strtotime($start_date));
		$mon_data=explode("-",$start_date);
		$mon_id=$mon_data[1];
		$year_id=$mon_data[2];
		//echo $mon_id.'='.$year_id;
		$num_days = cal_days_in_month(CAL_GREGORIAN, $mon_id, $year_id);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		} //echo $year_cond.'assss';

		$company_names="";
		foreach(explode(",",$working_company_id) as $key=>$vals)
		{
			if($company_names=="")
			{
				$company_names=$company_library[$vals];
			}
			else
			{
				$company_names .=' , '.$company_library[$vals];
			}
		}
	$client_array = array();
	$sql_client=sql_select("SELECT a.id, a.buyer_name
	FROM lib_buyer a, lib_buyer_tag_company b
		WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.buyer_id = a.id AND a.id IN 
		(SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (7)) 
		group by a.id, a.buyer_name
	ORDER BY buyer_name");

	foreach ($sql_client as $key => $value) {
		$client_array[$value[csf('id')]] = $value[csf('buyer_name')];
	}
	// echo "<pre>";
	// print_r($client_array);
	$lib_season = return_library_array( "select id, season_name from lib_buyer_season ","id","season_name");
	if($report_type==1) //Show Button
	{
		$emble_name=0;
		if($process_rpt_type==11 || $process_rpt_type==12) //Printing OR Embroidery
		{
			 
			$supplier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');  
 
			if ($process_rpt_type==11) //PRINTING
			{
				$th_array = array(0=>"ISSUE TO PRINT CHALLAN NO.",1=>"PRINT ISSUE DATE",2=>"ISSUE TO PRINT QTY",3=>"RECEIVE FROM PRINT CHALLAN NO.",4=>"PRINT RECEIVED DATE",5=>"RECEIVED FROM PRINT QTY",6=>"PRINT REJECT QTY",7=>"PRINT WIP");

				 $emble_name = 1;
				
			}
			if ($process_rpt_type==12) //EMBRODARY 
			{
				$th_array = array(0=>"ISSUE TO EMBROIDERY CHALLAN NO.",1=>"EMBROIDERY ISSUE DATE",2=>"ISSUE TO EMBROIDERY QTY",3=>"RECEIVE FROM EMBROIDERY CHALLAN NO.",4=>"EMBROIDERY RECEIVED DATE",5=>"RECEIVED FROM EMBROIDERY QTY",6=>"EMBROIDERY REJECT QTY",7=>"EMBROIDERY WIP");
				$emble_name = 2;
			}
			//=========================================================================================================
			//												MAIN QUERY
			// ==========================================================================================================
 
			$working_company_cond= $cbo_working_company_id ? " and f.working_company_id in($cbo_working_company_id)" : ""; 
			$emble_cond = $emble_name ? " and c.embel_name=$emble_name " : "";
			$sql_prod = "SELECT f.serving_company as party_name,f.working_company_id as wo_company,f.production_source as source,a.job_no_prefix_num as job_no,a.buyer_name,a.season_buyer_wise as season_buyer,a.style_ref_no as style,b.po_number,b.id as po_id,c.country_id,b.shipment_date,e.color_number_id as color,e.order_quantity as po_qty,d.production_qnty as prod_qty,c.production_type as prod_type,c.embel_name,c.delivery_mst_id,f.delivery_date,f.sys_number,f.issue_challan_id,d.reject_qty,d.color_size_break_down_id FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e,pro_gmts_delivery_mst f WHERE a.id=b.job_id and c.po_break_down_id=b.id and c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.delivery_mst_id=f.id and c.production_type in(2,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $working_company_cond $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond $emble_cond $season_cond";
			
			// echo $sql_prod; die;
			$sql_result=sql_select($sql_prod); 
			if (count($sql_result) == 0 ) 
			{
				echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
				die();
			}
			$prod_data_arr=array(); 
			$issue_challan_array=$del_challan_array=$po_id_array=$color_size_id_array=array();
			foreach($sql_result as $v)
			{
				$po_id_array[$v['PO_ID']] = $v['PO_ID'];
				if ($v['PROD_TYPE']==2) 
				{
					$del_challan_array[$v['DELIVERY_MST_ID']]	= $v['DELIVERY_MST_ID']; 

					$prod_data_arr[$v['PO_ID']][$v['COLOR']][0]	= $v['SYS_NUMBER'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][1]	= $v['DELIVERY_DATE'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][2]	+= $v['PROD_QTY'];
				}
				else
				{
					$issue_challan_array[$v['ISSUE_CHALLAN_ID']]= $v['ISSUE_CHALLAN_ID'];

					$prod_data_arr[$v['PO_ID']][$v['COLOR']][3]	= $v['SYS_NUMBER'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][4]	= $v['DELIVERY_DATE'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][5]	+= $v['PROD_QTY'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][6]	= $v['REJECT_QTY'];
				} 
				$party_name = $v['SOURCE']==1 ?  $company_library[$v['PARTY_NAME']] : $supplier_arr[$v['PARTY_NAME']];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['WO_COMPANY'] 				= $company_library[$v['WO_COMPANY']];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['PARTY_NAME'][$party_name] = $party_name;
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['JOB_NO'] 					= $v['JOB_NO'];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['BUYER_NAME'] 				= $buyer_short_library[$v['BUYER_NAME']];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['SEASON_BUYER'] 			= $lib_season[$v['SEASON_BUYER']];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['STYLE'] 					= $v['STYLE'];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['PO_NUMBER'] 				= $v['PO_NUMBER'];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['COUNTRY'] 				= $country_arr[$v['COUNTRY_ID']];
				$prod_data_arr[$v['PO_ID']][$v['COLOR']]['SHIPMENT_DATE'] 			= $v['SHIPMENT_DATE'];

				/* if (!$color_size_id_array[$v['COLOR_SIZE_BREAK_DOWN_ID']]) 
				{
					$prod_data_arr[$v['PO_ID']][$v['COLOR']]['PO_QTY'] 				+= $v['PO_QTY']; 
				}
				$color_size_id_array [$v['COLOR_SIZE_BREAK_DOWN_ID']] = $v['COLOR_SIZE_BREAK_DOWN_ID']; */
			}
			// pre($prod_data_arr); die;

			$missing_issue_challan_id 	= array_diff($issue_challan_array,$del_challan_array);
			$no_of_missing_challan 		= count($missing_issue_challan_id);
			//=========================================================================================================
			//												CLEAR TEMP ENGINE
			// ==========================================================================================================
			$con = connect();
			execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 175 and ref_from in(1,2)");
			oci_commit($con);   
			// =========================================================================================================
			//												INSERT DATA INTO TEMP ENGINE
			// =========================================================================================================
			fnc_tempengine("gbl_temp_engine", $user_id, 175, 1,$po_id_array, $empty_arr);
			unset($po_id_array);  

			// ===================================================================================================
			//												ORDER QTY QUERY
			// ====================================================================================================
			$po_qty_sql = "SELECT a.po_break_down_id as po_id,a.color_number_id as color,order_quantity From wo_po_color_size_breakdown a,gbl_temp_engine tmp WHERE a.po_break_down_id=tmp.ref_val and tmp.entry_form=175 and tmp.ref_from=1 and tmp.user_id=$user_id and a.is_deleted=0 and a.status_active=1";
			// echo $po_sql ; die;
			$po_qty_sql_res = sql_select($po_qty_sql);
			$order_qty_array = array();
			foreach ($po_qty_sql_res as $v) 
			{
				$order_qty_array[$v['PO_ID']][$v['COLOR']]	+= $v['ORDER_QUANTITY'];
			} 
			// pre($order_qty_array); die;
			// =========================================================================================================
			//												MISSING ISSUE CHALLAN QUERY
			// =========================================================================================================
			if ($no_of_missing_challan>0) 
			{
				
				fnc_tempengine("gbl_temp_engine", $user_id, 175, 2,$missing_issue_challan_id, $empty_arr); 
				unset($missing_issue_challan_id); 

				$missing_issue_sql = "SELECT a.po_break_down_id as po_id,c.color_number_id as color,b.production_qnty as prod_qty,a.production_type as prod_type,d.delivery_date,d.sys_number FROM pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c,pro_gmts_delivery_mst d,gbl_temp_engine tmp WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.delivery_mst_id=d.id and d.id=tmp.ref_val and tmp.entry_form=175 and tmp.ref_from=2 and tmp.user_id=$user_id and a.production_type=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
				// echo $missing_issue_sql ; die;
				$missing_issue_data = sql_select($missing_issue_sql);
				foreach ($missing_issue_data as $v) 
				{
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][0]	= $v['SYS_NUMBER'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][1]	= $v['DELIVERY_DATE'];
					$prod_data_arr[$v['PO_ID']][$v['COLOR']][2]	+= $v['PROD_QTY'];
				}

			}
			// =========================================================================================================
			//												CUTTING LAY DATA
			// ========================================================================================================= 
			$lay_sql= "SELECT a.cutting_no,c.order_id,b.order_cut_no,b.color_id,c.size_qty FROM  ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,gbl_temp_engine tmp WHERE a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.order_id=tmp.ref_val and tmp.entry_form=175 and tmp.ref_from=1 and tmp.user_id=$user_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
			// echo $lay_sql; die;
			$lay_result = sql_select($lay_sql);
			$lay_data_array = array();
			foreach ($lay_result as  $v) 
			{
				$lay_data_array[$v['ORDER_ID']][$v['COLOR_ID']]['CUTTING_NO'][$v['CUTTING_NO']] 	= $v['CUTTING_NO'];
				$lay_data_array[$v['ORDER_ID']][$v['COLOR_ID']]['ORDER_CUT_NO'][$v['ORDER_CUT_NO']] = $v['ORDER_CUT_NO'];
				$lay_data_array[$v['ORDER_ID']][$v['COLOR_ID']]['CUTTING_QTY'] 						+= $v['SIZE_QTY'];
			}
			// pre($lay_data_array); die;
			// =========================================================================================================
			//												SEWING INPUT DATA
			// =========================================================================================================
			$sew_sql = "SELECT a.po_break_down_id as po_id,c.color_number_id as color,b.production_qnty as prod_qty  FROM pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c,gbl_temp_engine tmp WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=175 and tmp.ref_from=1 and tmp.user_id=$user_id and a.production_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
			// echo $sew_sql; die;
			$sew_sql_result = sql_select($sew_sql);
			$sew_data_array = array();
			foreach ($sew_sql_result as  $v) 
			{
				$sew_data_array[$v['PO_ID']][$v['COLOR']]['PROD_QTY'] 	+= $v['PROD_QTY']; 
			}

			//=========================================================================================================
			//												CLEAR TEMP ENGINE
			// ========================================================================================================== 
			execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 175 and ref_from in(1,2)");
			oci_commit($con);  
			disconnect($con);

			$tbl_width = 1640 + (count($th_array)*120) ;

			?> 
			<style>
				.break-all th{
					text-transform: uppercase !important; 
				}
				.break-all{
					word-break: break-all;
				}
			</style>
			<div style="width:<? echo $tbl_width+20;?>px; ruby-align:center" id="main-div">
				<fieldset style="width:<? echo $tbl_width;?>px;">
					<table width="<? echo $tbl_width;?>"  cellspacing="0"   >
					
						<tr style="border:none;">
							<td colspan="10" align="center" style="border:none; font-size:16px; font-weight:bold">
							<? echo  $production_process[$process_rpt_type].' Send & Receive  ('.$company_names.')'; ?>                                
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
							</td>
						</tr>
					</table>
					<br />
					<div align="center" style="height:auto; width:<? echo $tbl_width+20;?>px; margin:0 auto; padding:0;"> 	
						<table border="1" cellpadding="2" cellspacing="0" class="rpt_table break-all" width="<? echo $tbl_width;?>" rules="all" id="rpt_table_header" align="left">
							<thead>
								<tr >
									<th width="30">SL</th>
									<th width="130">Process</th>
									<th width="120">Working Company</th>
									<th width="120">Party Name</th>
									<th width="80">Job No</th>
									<th width="120">Buyer</th>
									<th width="60">Season</th>
									<th width="100">STYLE NO</th>
									<th width="80">ORDER NO</th>
									<th width="80">COUNTRY</th>
									<th width="80">COUNTRY SHIP DATE</th>
									<th width="80">COLOR</th>
									<th width="80">ORDER QTY</th>
									<th width="80">System Cut No.</th>
									<th width="80">ORDER CUT NO</th>
									<th width="80">CUTTING QTY</th>
									<th width="80">CUTTING BALANCE</th>
									<? 
										foreach ($th_array as $index => $th_caption) 
										{
											?>
												<th  width="120"> <?= $th_caption ?> </th>
											<?
										}
									?>  
									<th  width="80">SEWING INPUT QTY</th>
									<th  width="80">INPUT BALANCE QTY</th>
								</tr>
							</thead>
						</table>
						<div style="width:<?= $tbl_width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
							<table class="rpt_table break-all" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
								<?
									$grand_country_qty=$grand_cutting_qty=$grand_total_prod=$grand_country_qty_pcs=0;	
									$tot_rows=count($sql);
									$i=1;	   

									foreach($prod_data_arr as $po_id=>$po_val)	
									{
										foreach($po_val as $color_id=>$v)	
										{
											$lay_data 		= $lay_data_array[$po_id][$color_id];
											$po_qty			= $order_qty_array[$po_id][$color_id];
											$cut_qty		= $lay_data['CUTTING_QTY'];
											$sew_in_qty		= $sew_data_array[$po_id][$color_id]['PROD_QTY'];
											$cut_balance	= $po_qty - $cut_qty;
											$input_balance	= $po_qty - $sew_in_qty;

											$cut_balance_title		= " po_qty($po_qty) - cut_qty($cut_qty)";
											$input_balance_title	= " po_qty($po_qty) - sew_in_qty($sew_in_qty)";
											?>
												<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_2nd<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_2nd<?= $i; ?>">
													<td width="30"><?= $i++;?></td>
													<td width="130"><?= $production_process[$process_rpt_type] ?></td>
													<td width="120"><?= $v['WO_COMPANY'] ?></td>
													<td width="120"><?= implode(',',$v['PARTY_NAME']) ?></td>
													<td width="80" align="center"><?= $v['JOB_NO'] ?></td>
													<td width="120"><?=$v['BUYER_NAME'] ?></td>
													<td width="60"><?= $v['SEASON_BUYER'] ?></td>
													<td width="100"><?=$v['STYLE'] ?></td>
													<td width="80"><?= $v['PO_NUMBER'] ?></td> 
													<td width="80"><?= $v['COUNTRY'] ?></td>
													<td width="80"><?= $v['SHIPMENT_DATE'] ?></td>
													<td width="80"><?= $color_Arr_library[$color_id] ?></td>  
													<td width="80" align="right"><?= number_format($po_qty)  ?></td>
													<td width="80"><?= implode(',',$lay_data['CUTTING_NO']) ?></td>
													<td width="80"><?= implode(',',$lay_data['ORDER_CUT_NO']) ?></td>
													<td width="80" align="right"><?= number_format($cut_qty) ?></td>
													<td width="80" align="right" title="<?=$cut_balance_title?>"><?= number_format($cut_balance) ?></td>
													<? 
														foreach ($th_array as $index => $th_caption) 
														{
															$is_qty = in_array($index,[2,5,6,7]);
															$align = $is_qty ? "align='right'" : "";
															$value = ($index==7) ? ( $v[2]-$v[5]-$v[6]) :  $v[$index] ;
															$data =  $is_qty ?number_format($value) : $value;
															?>
																<td <?= $align  ?> width="120"> <?= $data ?> </td>
															<?
														}
													?>  
													<td  width="80" align="right"><?= number_format($sew_in_qty) ?></td>
													<td  width="80" align="right" title="<?= $input_balance_title ?>"><?= number_format($input_balance) ?></td>
													
												</tr>    
											<? 
										}
									}
								?>
							</table> 
						</div>
						<div style="width:<?= $tbl_width+20;?>px;float:left;">
							<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table break-all" rules="all" width="<?= $tbl_width;?>">
								<tfoot> 
									<tr>
										<th width="30"></th>
										<th width="130"></th>
										<th width="120"></th>
										<th width="120"></th>
										<th width="80"></th>
										<th width="120"></th>
										<th width="60"></th>
										<th width="100"></th>
										<th width="80"></th> 
										<th width="80"></th>
										<th width="80"></th>
										<th width="80">GRAND TOTAL</th>  
										<th width="80" id="th_po_qty"> </th>
										<th width="80"></th>
										<th width="80"></th>
										<th width="80" id="th_cut_qty"> </th>
										<th width="80" id="th_cut_balance"> </th>
										<? 
											foreach ($th_array as $index => $th_caption) 
											{
												$align = ( in_array($index,[2,5,6,7])) ? "align='right'" : "";
												?>
													<th align="right"  width="120" id="th_data_id_<?= $index ?>">  </th>
												<?
											}
										?>  
										<th  width="80" align="right" id="th_sew_in_qty"> </th>
										<th  width="80" align="right" id="th_input_balance" > </th> 
									</tr> 
								</tfoot>
							</table>		
						</div>
					</div>	
				</fieldset>
			</div>
			<script>
				let tableFilters =
				{ 
					col_operation: {
						id: ["th_po_qty","th_cut_qty","th_cut_balance","th_data_id_2","th_data_id_5","th_data_id_6","th_data_id_7","th_sew_in_qty","th_input_balance"],
						col: [12,15,16,19,22,23,24,25,26],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				} 
                setFilterGrid("table_body",-1,tableFilters); 
			</script>
			<?

		} 
		else  //OTHER PROCESS
		{ 
			if($process_rpt_type==1) //Cutting
			{
				$prod_type=1;
				$subcon_prod_type=1;
				$th_caption="Cutting QC Qty";
			}
			else if($process_rpt_type==2) //sewing Input
			{
				$prod_type=4;
				$subcon_prod_type=7;
				$th_caption="Input Qty";
			}
			else if($process_rpt_type==3) //sewing Out
			{
				$prod_type=5;
				$subcon_prod_type=2;
				$th_caption="Sew Output";
				
			}
			else if($process_rpt_type==4) //Poly
			{
				$prod_type=11;
				$subcon_prod_type=5;
				$th_caption="Poly Output Qty";
			}
			else if($process_rpt_type==5) //Finished
			{
				$prod_type=8;
				$subcon_prod_type=4;
				$th_caption="Finishing Qty";
			}
			else if($process_rpt_type==6) //Ship Qty/ Ex-F
			{
				$prod_type=0;
				$th_caption="Ship Qty";
			}
			else if($process_rpt_type==7) //Cut and Lay
			{
				$prod_type=0;
				$th_caption="Cut and Lay Qty";
			} 
			else if($process_rpt_type==8) //Wash Send
			{
				$prod_type=2;
				$emble_name=3;
				$th_caption="Wash Send Qty";
			}
			else if($process_rpt_type==9) //Wash Receive
			{
				$prod_type=3;
				$emble_name=3;
				$th_caption="Wash Receive Qty";
			}
			else if($process_rpt_type==10) //Finishing(Getup Pass) = Iron Entry
			{
				$prod_type=7;
				$th_caption="Finishing(Getup Pass) Qty";
			}
	
			if($process_rpt_type==1 || $process_rpt_type==2 || $process_rpt_type==3 || $process_rpt_type==4 || $process_rpt_type==5) //Cutting
			{
				$emble_cond = $emble_name ? " and c.embel_name=$emble_name " : "";
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.production_qnty) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type=$prod_type   and d.production_type=$prod_type and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)   $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond $emble_cond $season_cond order by b.id,c.country_id ";

				$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.prod_qnty) as prod_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type='$subcon_prod_type' and d.production_type='$subcon_prod_type' and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";
			}
			else if($process_rpt_type==7) //Cut and Lay
			{
				$location_cond = str_replace("c.location", "c.location_id", $location_cond);
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.size_qty) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
				WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond7 $season_cond order by b.id,d.country_id ";
			}else if ($process_rpt_type==8) 
			{
				$emble_cond = $emble_name ? " and c.embel_name=$emble_name " : "";
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.production_qnty) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type=$prod_type   and d.production_type=$prod_type and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)   $working_company8 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond $emble_cond $season_cond order by b.id,c.country_id ";
				//  echo $sql_prod;die;

				$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.prod_qnty) as prod_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type=9 and d.production_type=9 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

				// echo $subcon_sql_prod;die;
			}
			else if ($process_rpt_type==9) 
			{
				$emble_cond = $emble_name ? " and c.embel_name=$emble_name " : "";
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.production_qnty) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type=$prod_type   and d.production_type=$prod_type and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)   $working_company8 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond $emble_cond $season_cond order by b.id,c.country_id ";
				// echo $sql_prod;die;

				$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.prod_qnty) as prod_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type=10 and d.production_type=10 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";
			}
			else if ($process_rpt_type==10) 
			{
				// $emble_cond = $emble_name ? " and c.embel_name=$emble_name " : "";
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(d.production_qnty) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  c.id=d.mst_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type=$prod_type   and d.production_type=$prod_type and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)   $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond $emble_cond $season_cond order by b.id,c.country_id ";
				// echo $sql_prod;die;

				$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.prod_qnty) as prod_qty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type=(3) and d.production_type=(3) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";
				// echo $subcon_sql_prod;die;
			}
			else //Ex-Factory 
			{
				$sql_prod = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.season_buyer_wise as season_buyer
				FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
				WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $prod_date_cond2 $season_cond order by b.id,d.country_id ";

				$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<> 4  and a.process_id=3      $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";



			}
			
		
			$sql_result=sql_select($sql_prod);
			$sql_result_subcon=sql_select($subcon_sql_prod);
			$prod_data_arr=array();
			$po_id='';
			foreach($sql_result as $row)
			{
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['job']=$row[csf('job_no')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['pre_job']=$row[csf('job_no_prefix_num')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['client_id']=$row[csf('client_id')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_no']=$row[csf('po_number')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['company']=$row[csf('company_name')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['country_id']=$row[csf('country_id')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['season_buyer']=$row[csf('season_buyer')];
				$tmp_arr[$row[csf('po_id')]][$row[csf('country_id')]][date("Y-m-d",strtotime($row[csf("prod_date")]))]['prod_qty']+=$row[csf('prod_qty')];
				if($po_id=='') $po_id=$row[csf('po_id')];else $po_id.=",".$row[csf('po_id')];
			}
			// echo "<pre>";
			// print_r($prod_data_arr);die();

			foreach($sql_result_subcon as $row)
			{
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['job']=$row[csf('job_no')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['pre_job']=$row[csf('job_no_prefix_num')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_no']=$row[csf('po_number')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['company']=$row[csf('company_name')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['country_id']=$row[csf('country_id')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_quantity')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
				$prod_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
				$tmp_arr[$row[csf('po_id')]][$row[csf('country_id')]][date("Y-m-d",strtotime($row[csf("prod_date")]))]['prod_qty']+=$row[csf('prod_qty')];
				if($po_id=='') $po_id=$row[csf('po_id')];else $po_id.=",".$row[csf('po_id')];
				$all_subcon_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			}
			$all_sub_po=implode(",", $all_subcon_po_id_arr);  


			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_id_cond = "";
			if ($po_id != "")
			{
				$po_id =$po_id; //substr($po_id, 0, -1);
				if ($db_type == 0)
					$po_id_cond = "and b.id in(" . $po_id . ")";
				else 
				{
					$po_ids = explode(",", $po_id);
					if (count($po_ids) > 1000) {
						$po_id_cond = "and (";
						$po_ids = array_chunk($po_ids, 1000);
						$z = 0;
						foreach ($po_ids as $id) {
							$id = implode(",", $id);
							if ($z == 0)
								$po_id_cond .= " b.id in(" . $id . ")";
							else
								$po_id_cond .= " or b.id in(" . $id . ")";
							$z++;
						}
						$po_id_cond .= ")";
					} else
					$po_id_cond = "and b.id in(" . $po_id . ")";
				}
			}
			$sql_color="SELECT b.id as po_id,c.order_quantity as po_qty,c.country_id from   wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) $po_id_cond $buyer_id_cond $year_cond $job_cond $style_cond $order_cond";
			$po_country_qty=array();
			$sql_result_color=sql_select($sql_color);
			foreach($sql_result_color as $row)
			{
				$po_country_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
			}
			$sql_sub_po="SELECT id,order_quantity from subcon_ord_dtls where status_active=1 and is_deleted=0 and id in($all_sub_po)";
			foreach(sql_select($sql_sub_po) as $row)
			{
				$po_country_qty_sub[$row[csf('id')]]['po_qty']+=$row[csf('order_quantity')];
			}
	

				$tot_width=1240+(50*$num_days);
				ob_start(); 
				
			?>
			<div>
			<div style="width:<? echo $tot_width+30;?>px; ruby-align:center">
			<fieldset style="width:<? echo $tot_width;?>px;">
				<table width="<? echo $tot_width;?>"  cellspacing="0"   >
				
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none; font-size:16px; font-weight:bold">
						<? echo  $production_process[$process_rpt_type].' ('.$company_names.')'; ?>                                
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
						</td>
					</tr>
				</table>
				<br />	
				<table class="rpt_table" width="<? echo $tot_width+20;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<tr >
							<th width="30">SL</th>
							<th width="130">PO Company</th>
							<th width="120">Buyer Name</th>
							<th width="120">Season</th>
							<th width="120">Buyer Client</th>
							<th width="120">Style Ref.</th>
							<th width="60">Job No</th>
							<th width="100">PO No.</th>
							<th width="100">Country</th>
							<th width="80">Country Qty</th>
							<th width="80">Country Qty Pcs</th>
							<th width="80"><p><? echo $th_caption;?></p></th>
							<?
							for($m=1;$m<=$num_days;$m++)
							{
								?>
								<th width="50"> <? echo  ($m<=9)? '0'.$m:$m; ?></th>
								<?
								}
								?>
							<th  width="">Total Qty.</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $tot_width+20;?>px; max-height:425px; overflow-y:scroll"   id="scroll_body">
					<table class="rpt_table" width="<? echo $tot_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$grand_country_qty=$grand_cutting_qty=$grand_total_prod=$grand_country_qty_pcs=0;	
						$tot_rows=count($sql);
						$i=1;	   

						foreach($prod_data_arr as $po_id=>$po_val)	
						{
							foreach($po_val as $country_id=>$val)	
							{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									$cutting_prod_qty=0;
									for($k=1;$k<=$num_days;$k++)
									{
										$dayss=($k<=9)? '0'.$k:$k;
										$prod_date_key=$start_date_tmp."-".$dayss;
										$cutting_prod_qty+=$tmp_arr[$po_id][$country_id][$prod_date_key]['prod_qty'];
									}//$process_rpt_type
									$po_button="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_prod_country_size_report','$prod_type')\"> ".number_format($cutting_prod_qty,0)." </a>";
									
									
									
									$set_ratio=$val['ratio']; 
									$po_qty=$po_country_qty[$po_id][$country_id]['po_qty']/$set_ratio;
									$po_qty_pcs=$po_country_qty[$po_id][$country_id]['po_qty'];
									
									if($val['country_id']=="00")
									{
										// $po_button_qty=0;
										$po_qty=$po_country_qty_sub[$po_id]['po_qty'];
										$po_qty_pcs=$po_qty;
										$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_qty,0)." </a>";
									}
									else
									{
										$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_qty,0)." </a>";

									}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
									<td width="30"><? echo $i;?></td>
									<td width="130"><p><? echo  $company_library[$val['company']];  ?></p></td>
									<td width="120"><div style="word-break:break-all"><? echo $buyer_short_library[$val['buyer_name']];?>&nbsp;</div></td>
									<td width="120"><div style="word-break:break-all"><? echo $lib_season[$val['season_buyer']];?></div></td>
									<td width="120"><div style="word-break:break-all"><? echo $client_array[$val['client_id']];?>&nbsp;</div></td>
									<td width="120"><div style="word-break:break-all"><? echo $val['style'];?></div></td>
									<td width="60"><p><? echo $val['pre_job'];?></p></td>
									<td width="100" align="center"><div style="word-break:break-all"><? echo $val['po_no'];echo $sub_self=($val['country_id']=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
									<td width="100"><div style="word-break:break-all"><? echo $country_arr[$country_id];?></div></td>
									<td width="80"  align="right"><p><? echo $po_button_qty;//number_format($po_qty,0);?></p></td>
									<td width="80"  align="right" title="<? echo $set_ratio;?>"><p><? echo number_format($po_qty_pcs,0);?></p></td>
									<td width="80"  align="right"><p><? echo $po_button;?></p></td>
									<?
									$tot_prod_qty=0;
								for($m=1;$m<=$num_days;$m++)
								{
									$days=($m<=9)? '0'.$m:$m;
									$prod_date_key=$start_date_tmp."-".$days;
									$prod_qty=$tmp_arr[$po_id][$country_id][$prod_date_key]['prod_qty'];
									$tot_prod_qty+=$prod_qty;
									$friday=date('D',strtotime($prod_date_key));
									$total_prod_qty_arr[$m]+=$prod_qty;
									if($friday=='Fri')
									{
									$td_color="red";	
									}
									else  $td_color="";	
											?>
									<td width="50" bgcolor="<? echo $td_color; ?>" title="<? echo $friday;?>"  align="right"> <? echo  number_format($prod_qty,0); ?></td>
								<?
									}
								?>
								<td  width="" align="right"><p><? echo  number_format($tot_prod_qty,0); ?></p></td>
								</tr>    
							<?
							$i++;
							$grand_total_prod+=$tot_prod_qty;
							$grand_country_qty+=$po_qty;
							$grand_country_qty_pcs+=$po_qty_pcs;
							$grand_cutting_qty+=$cutting_prod_qty;
							}
					}
					?>
				</table>
				<table style="width:<? echo $tot_width;?>px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
						<th width="30">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100"><strong>Grand Total:</strong></th>
						<th width="80"  align="right"><? echo number_format($grand_country_qty,0); ?></th>
						<th width="80"  align="right"><? echo number_format($grand_country_qty_pcs,0); ?></th>
						<th width="80"  align="right"><? echo number_format($grand_cutting_qty,0); ?></th>
						<?
							for($m=1;$m<=$num_days;$m++)
							{
								$day=($m<=9)? '0'.$m:$m;
										?>
								<th width="50" align="right"> <? echo number_format($total_prod_qty_arr[$m],0); ?></th>
							<?
								}
							?>
					<th width="" align="right"><? echo number_format($grand_total_prod,0); ?></th>
				</tfoot>
				</table>
				</div>
			</fieldset>
			</div>
			<?	
		}	
	}
	elseif ($report_type==3)  //Summary 2 Button
	{

		$current_month=date("m",strtotime($start_date));
		//$current_month=date('m');
		$current_year=date("Y",strtotime($start_date));
		$lastmonth=$current_month-1;
		if($current_month==1)
		{
			$lastmonth2=12;
			$current_year2=$current_year-1;
		}

		$firstdate= "01-".$lastmonth."-".$current_year ;
		// $lastdateofmonth=date('t',$lastmonth);//
		$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year);
		$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
		$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
		$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
		//echo $firstdate ."==".$lastdate ;

		$firstdate=change_date_format($firstdate,"","",1);
		$lastdate=change_date_format($lastdate,"","",1);
		$lastdate2=change_date_format($lastdate2,"","",1);

		$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
		$sum_width=1010;
		$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";

		//CUT AND LAY QTY.....................................
		$buyer_data_arr=array();
		$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
		$sql_cut_lay = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
		(d.size_qty) as prod_qty
		FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
		WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
		// echo $sql_cut_lay;die;

		$cut_lay_result_arr=sql_select($sql_cut_lay);
		foreach($cut_lay_result_arr as $rows)
		{
			$buyer_data_arr[$rows[csf('buyer_name')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['style']=$rows[csf('style_ref_no')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['buyer_name']=$rows[csf('buyer_name')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['po_number']=$rows[csf('po_number')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['ratio']=$rows[csf('ratio')];
			$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['job_no']=$rows[csf('job_no')];

			$tmp_count[$rows[csf('country_id')]]=$rows[csf('country_id')];
			$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];

		}
		// echo "<pre>";
		// print_r($buyer_data_arr);
		// echo "</pre>";
		//CUT AND LAY QTY.....................................


				$sql_prod_sum = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
				(CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
				(CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
				(CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
				(CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty,

				(CASE WHEN c.production_type=7 and d.production_type=7 THEN d.production_qnty ELSE 0 END)  as iron_qty
				FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
					WHERE  a.id=b.job_id and c.po_break_down_id=b.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,7,8) and d.production_type in(1,4,5,7,8)  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)$working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";
					//(CASE WHEN c.production_type=2 and d.production_type=2 and c.embel_name=3 THEN d.production_qnty ELSE 0 END)  as wash_send_qty,
					//(CASE WHEN c.production_type=3 and d.production_type=3 and c.embel_name=3 THEN d.production_qnty ELSE 0 END)  as wash_rcve_qty,

					// echo $sql_prod_sum; die;

					$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio,
						(CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty,

						(CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty,
						(CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty,
						(CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty,
						(CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty,
						(CASE WHEN a.production_type=10 and d.production_type=10   THEN d.prod_qnty ELSE 0 END)  as WASH_RCVE_QTY,
						(CASE WHEN a.production_type=9 and d.production_type=9   THEN d.prod_qnty ELSE 0 END)  as wash_send_qty,
						(CASE WHEN a.production_type=3 and d.production_type=3   THEN d.prod_qnty ELSE 0 END)  as iron_qty
						from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,3,4,5,7,9,10) and d.production_type in(1,2,4,3,5,7,9,10) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

			// echo $subcon_sql_prod; die;
		



		$sum_result=sql_select($sql_prod_sum);
		$sum_result_subcon=sql_select($subcon_sql_prod);

		//$po_id='';	$country_id='';$buyer_ids='';
		foreach($sum_result as $v)
		{
			$buyer_data_arr[$v['BUYER_NAME']]['cut_qty']+=$v['CUT_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['sew_in_qty']+=$v['SEW_IN_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['sew_out_qty']+=$v['SEW_OUT_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['poly_qty']+=$v['POLY_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['finish_qty']+=$v['FINISH_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['prod_qty']+=$v['PROD_QTY'];
			// $buyer_data_arr[$v['BUYER_NAME']]['wash_send']+=$v[$wash_qnty_arr['WASH_SEND_QTY'];
			// $buyer_data_arr[$v['BUYER_NAME']]['wash_rcve']+=$v['WASH_RCVE_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['iron_qty']+=$v['IRON_QTY'];

			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['cut_qty']+=$v['CUT_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['sew_in_qty']+=$v['SEW_IN_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['sew_out_qty']+=$v['SEW_OUT_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['poly_qty']+=$v['POLY_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['finish_qty']+=$v['FINISH_QTY'];
			// $prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['wash_send']+=$v['WASH_SEND_QTY'];
			// $prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['wash_rcve']+=$v['WASH_RCVE_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['iron_qty']+=$v['IRON_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['style']=$v['STYLE_REF_NO'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['buyer_name']=$v['BUYER_NAME'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['po_number']=$v['PO_NUMBER'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['ratio']=$v['RATIO'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['job_no']=$v['JOB_NO'];

			$tmp_po[$v['PO_ID']]=$v['PO_ID'];
			$tmp_count[$v['COUNTRY_ID']]=$v['COUNTRY_ID'];

		}
			$wash_sql="SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
			(CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
			(CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
			(CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
			(CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty,

			(CASE WHEN c.production_type=7 and d.production_type=7  THEN d.production_qnty ELSE 0 END)  as iron_qty,
			(CASE WHEN c.production_type=2 and d.production_type=2 and c.embel_name=3 THEN d.production_qnty ELSE 0 END)  as wash_send_qty,
			(CASE WHEN c.production_type=3 and d.production_type=3 and c.embel_name=3 THEN d.production_qnty ELSE 0 END)  as wash_rcve_qty

			FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
				WHERE  a.id=b.job_id and c.po_break_down_id=b.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,2,3,4,5,7,8) and d.production_type in(1,2,3,4,5,7,8)  and c.embel_name in(3) and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)  $working_company8 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";

		//    echo $wash_sql; die;
			$wash_qnty_sql=sql_select($wash_sql);
		
		foreach ($wash_qnty_sql as $val)
		{
			$buyer_data_arr[$val['BUYER_NAME']]['wash_send']+=$val['WASH_SEND_QTY'];
			$buyer_data_arr[$val['BUYER_NAME']]['wash_rcve']+=$val['WASH_RCVE_QTY'];

			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['cut_qty']+=$val['CUT_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['sew_in_qty']+=$val['SEW_IN_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['sew_out_qty']+=$val['SEW_OUT_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['poly_qty']+=$val['POLY_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['finish_qty']+=$val['FINISH_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['wash_send']+=$val['WASH_SEND_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['wash_rcve']+=$val['WASH_RCVE_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['iron_qty']+=$val['IRON_QTY'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['style']=$val['STYLE_REF_NO'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['buyer_name']=$val['BUYER_NAME'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['po_number']=$val['PO_NUMBER'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['ratio']=$val['RATIO'];
			$prod_dtls_data_arr[$val['PO_ID']][$val['COUNTRY_ID']]['job_no']=$val['JOB_NO'];
		}

		// echo '<pre>';
		// print_r($buyer_data_arr);die;

		foreach($sum_result_subcon as $v)
		{

			$buyer_data_arr[$v['BUYER_NAME']]['cut_qty']+=$v['CUT_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['sew_in_qty']+=$v['SEW_IN_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['sew_out_qty']+=$v['SEW_OUT_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['poly_qty']+=$v['POLY_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['finish_qty']+=$v['FINISH_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['wash_send']+=$v['WASH_SEND_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['wash_rcve']+=$v['WASH_RCVE_QTY'];
			$buyer_data_arr[$v['BUYER_NAME']]['iron_qty']+=$v['IRON_QTY'];

			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['cut_qty']+=$v['CUT_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['sew_in_qty']+=$v['SEW_IN_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['sew_out_qty']+=$v['SEW_OUT_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['poly_qty']+=$v['POLY_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['finish_qty']+=$v['FINISH_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['wash_send']+=$v['WASH_SEND_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['wash_rcve']+=$v['WASH_RCVE_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['iron_qty']+=$v['IRON_QTY'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['style']=$v['STYLE_REF_NO'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['buyer_name']=$v['BUYER_NAME'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['po_number']=$v['PO_NUMBER'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['ratio']=$v['RATIO'];
			$prod_dtls_data_arr[$v['PO_ID']][$v['COUNTRY_ID']]['job_no']=$v['JOB_NO'];

			$tmp_po[$v['PO_ID']]=$v['PO_ID'];
			$tmp_count[$v['COUNTRY_ID']]=$v['COUNTRY_ID'];
			$all_subcon_po_id_arrs[$v['PO_ID']]=$v['PO_ID'];

		}

		// 	echo '<pre>';
		// print_r($prod_dtls_data_arr);die;


		$location_cond_ex = str_replace("c.location", "c.location_id", $location_cond);
		$sql_prod_exf = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
			FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
		WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond  $prod_date_cond2 order by b.id,d.country_id ";

		$subcon_sql_ex="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0     and a.process_id=3   $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";


		$result_prod_exf=sql_select($sql_prod_exf);
		$result_prod_exf_subcon=sql_select($subcon_sql_ex);
		$po_no_id="";$buyer_ids2='';
		$po_country_id='';
		foreach($result_prod_exf as $row)
		{
			$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
			$po_ex_fact_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];

			$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ex_factory']+=$row[csf('prod_qty')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
			$prod_dtls_data_arr[$row['PO_ID']][$row['COUNTRY_ID']]['job_no']=$row['JOB_NO'];
			$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
			if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
			if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];
			$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
			//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];
		}
		// echo "<pre>";
		// print($po_ex_fact_sum_qty);
		// echo "</pre>";
		foreach($result_prod_exf_subcon as $row)
		{
			$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
			$po_ex_fact_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];

			$buyer_data_arr[$row[csf('buyer_name')]]['ex_factory']+=$row[csf('prod_qty')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ex_factory']+=$row[csf('prod_qty')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
			$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
			$prod_dtls_data_arr[$row['PO_ID']][$row['COUNTRY_ID']]['job_no']=$row['JOB_NO'];
			$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
			if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
			if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];
			$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
			//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];
		}


		$po_country_id=implode(",",array_unique(explode(",",$po_country_id)));
		$po_no_id=implode(",",array_unique(explode(",",$po_no_id)));
		//echo $po_cond_for_in;
		$poIds=implode(",",$tmp_po);
		$poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1="";
		$po_ids=count(array_unique(explode(",",$poIds)));
		if($po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$poIds)));
			$po_cond_for_in=" and b.id in($poIds)";
		}

		// Country
		$country_Id=implode(",",$tmp_count);
		$poIds_c=chop($country_Id,','); $po_cond_for_in2="";// $order_cond1="";
		$po_ids_c=count(array_unique(explode(",",$country_Id)));
		if($po_ids_c>1000)
		{
			$po_cond_for_in2=" and (";
			$poIdsArr_c=array_chunk(explode(",",$poIds_c),999);
			foreach($poIdsArr_c as $ids)
			{
				$ids_c=implode(",",$ids);
				$po_cond_for_in2.=" c.country_id in($ids_c) or";
			}
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
		}
		else
		{
			$poIds_c=implode(",",array_unique(explode(",",$poIds_c)));
			$po_cond_for_in2=" and c.country_id in($poIds_c)";
		}

		$sql_prod_exf_last = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,
		(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.client_id as buyer_client
		FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
		WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $sum_prod_date_cond2 order by b.id,d.country_id ";
		$result_prod_exf_last=sql_select($sql_prod_exf_last);
		foreach($result_prod_exf_last as $row)
		{
			$po_ex_fact_sum_qty_lastmon[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
		}


		$sql_color="SELECT a.buyer_name,c.order_quantity as po_qty,b.id as po_id,c.country_id,a.client_id as buyer_client from   wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3)  $po_cond_for_in $po_cond_for_in2  $buyer_id_cond order by b.id,c.country_id ";
		$po_country_sum_qty=array();$po_country_dtls_qty=array();
		$sql_result_color=sql_select($sql_color);
		foreach($sql_result_color as $row)
		{
			$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
			$po_country_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
			$summ+=$row[csf('po_qty')];
		}
		$sub_po_cond=implode(",",$all_subcon_po_id_arrs);
		$sql_color_subcon ="SELECT a.party_id as buyer_name ,c.qnty as po_qty,b.id as po_id,'00' as country_id,b.cust_buyer as buyer_client from   subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.subcon_job=b.job_no_mst and c.order_id=b.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_po_cond) order by b.id ";
		foreach(sql_select($sql_color_subcon) as $row)
		{
			$po_country_sum_qty[$row[csf('buyer_name')]]['po_qty']+=$row[csf('po_qty')];
			$po_country_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
			$summ+=$row[csf('po_qty')];
		}

		$sum_width=1210;
		ob_start();
		?>
		<!-- SUMMARY PART -->
		<div style="width:<? echo $sum_width+30;?>px;  ruby-align:center" >
			<fieldset style="width:<? echo $sum_width+30;?>px;">
                <table width="<? echo $sum_width;?>"  cellspacing="0">
                    <tr style="border:none;">
                        <td colspan="13" align="center" style="border:none; font-size:16px; font-weight:bold">
                        <? echo  'Buyer wise summary '.' ('.$company_names.')'; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
                        </td>
                    </tr>
                </table>
                <br />
                <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="120">Buyer</th>
                            <th width="100"><p>Country Order Qty (Pcs)</p></th>
                            <th width="80">Cut & Lay Qty</th>
                            <th width="80">Cut & Lay Qty %</th>
                            <th width="80">Cutting QC</th>
                            <th width="80">Cutting QC %</th>
                            <th width="80">Input.</th>
                            <th width="80">Sew Output</th>
                            <th width="80">Wash Send</th>
                            <th width="80">Wash Received</th>
                            <th width="80">Finishing (Getup Pass)</th>
                            <th width="80">Packing And Finishing</th>
                            <th width="80">Ship Out</th>
                            <th width="">Last Month's Ship Out</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo $sum_width+20;?>px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
                    <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    	<?
							$k=1;$grand_sum_cut_qty=$grand_sum_in_qty=$grand_sum_out_qty=$grand_sum_wash_send=$grand_sum_finished_qty=$grand_sum_exfact_qty=$grand_sum_country_qty=$grand_sum_last_month_qty=$grand_sum_wash_rcve=$grand_sum_iron_qty=0;
							// echo "<pre>";
							// print_r($buyer_data_arr);
							foreach($buyer_data_arr as $buyer_id=>$val)
							{

								if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$po_buyer_qty=$po_country_sum_qty[$buyer_id]['po_qty'];
								$ex_fact_qty=$buyer_data_arr[$buyer_id]['ex_factory'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];

								$last_cut_qty=$lastmon_buyer_data_arr[$buyer_id]['cut_qty'];
								$last_sew_in_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_in_qty'];
								$last_sew_out_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_out_qty'];
								$last_finish_qty=$lastmon_buyer_data_arr[$buyer_id]['finish_qty'];
								$last_ex_fact_sum_qty=$po_ex_fact_sum_qty_lastmon[$buyer_id]['prod_qty'];
								//$last_month_qty=$last_cut_qty+$last_sew_in_qty+$last_sew_out_qty+$last_poly_qty+$last_finish_qty+$last_ex_fact_sum_qty;
								$last_month_qty=$last_ex_fact_sum_qty;

								// GRAND TOTAL CALCULATION
								$grand_sum_exfact_qty+=$ex_fact_qty;
								$grand_sum_finished_qty+=$val['finish_qty'];
								$grand_sum_out_qty+=$val['sew_out_qty'];
								$grand_sum_in_qty+=$val['sew_in_qty'];
								$grand_sum_cut_lay_qty+=$val['cut_lay_qty'];
								$grand_sum_cut_qty+=$val['cut_qty'];
								$grand_sum_wash_send+=$val['wash_send'];
								$grand_sum_wash_rcve+=$val['wash_rcve'];
								$grand_sum_iron_qty+=$val['iron_qty'];
								$grand_sum_country_qty+=$po_buyer_qty;
								$grand_sum_last_month_qty+=$last_month_qty;
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
										<td width="30"><? echo $k;?></td>
										<td width="120"><div style="word-break:break-all"><? echo  $buyer_short_library[$buyer_id];  ?></div></td>
										<td width="100"  align="right"><p><? echo $po_buyer_qty;?></p></td>
										<td width="80"  align="right"><p><? echo $val['cut_lay_qty'];?></p></td>
										<td width="80"  align="right"><p><? $lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty; echo number_format($lay_percent,2); ?>%</p></td>
										<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? echo number_format($val['cut_qty'],0);?></p></td>
										<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? $cut_percent = ($val['cut_qty']*100)/$po_buyer_qty; echo number_format($cut_percent,2); ?>%</p></td>
										<td width="80" align="right" title="<? echo $last_sew_in_qty;?>"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
										<td width="80" align="right" title="<? echo $last_sew_out_qty;?>"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
										<td width="80" align="right" ><p><? echo number_format($val['wash_send'],0); ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($val['wash_rcve'],0); ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($val['iron_qty'],0); ?></p></td>
										<td width="80" align="right" title="<? echo $last_finish_qty;?>"><p><? echo number_format($val['finish_qty'],0);?></p></td>
										<td width="80"  align="right" title="<? echo $last_ex_fact_sum_qty;?>"><p><? echo number_format($ex_fact_qty,0);?></p></td>
										<td width=""  align="right"><p><? echo $last_month_qty;?></p></td>
									</tr>
								<?
									$k++;

							}
						?>
                    </table>
                    <table style="width:<? echo $sum_width;?>px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="120">Grand Total:</th>
							<th width="100" align="right"><? echo number_format($grand_sum_country_qty,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_cut_lay_qty,0); ?></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"><? echo number_format($grand_sum_cut_qty,0); ?></th>
							<th width="80" align="right"></th>
							<th width="80" align="right"><? echo number_format($grand_sum_in_qty,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_out_qty,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_wash_send,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_wash_rcve,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_iron_qty,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_finished_qty,0); ?></th>
							<th width="80" align="right"><? echo number_format($grand_sum_exfact_qty,0); ?></th>
							<th width="" align="right"><? echo number_format($grand_sum_last_month_qty,0); ?></th>
						</tfoot>
                	</table>
                </div>
            </fieldset>
        </div>

		<!-- DETAILS PART -->
		<?  $dtls_width=1650; ?>
			<br/>
			<div style="width:<? echo $dtls_width+25;?>px;">
				<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<caption><strong>Detail Report</strong></caption>
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Buyer</th>
							<th width="100">Job No</th>
							<th width="120">Style</th>
							<th width="100">PO No</th>
							<th width="100">Country</th>
							<th width="100"><p>Country Qty</p></th>
							<th width="100"><p>Country Qty Pcs</p></th>
							<th width="80">Cut & Lay Qty</th>
							<th width="80">Cut & Lay Qty %</th>
							<th width="80">Cutting QC</th>
							<th width="80">Cutting QC %</th>
							<th width="80">Input.</th>
							<th width="80">Sew Output</th>
							<th width="80">Wash Send</th>
							<th width="80">Wash Received</th>
							<th width="80">Finishing (Getup Pass)</th>
							<th width="80">Packing And Finishing</th>
							<th width="">Ship Out</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
							$j=1;$grand_cut_qty=$grand_in_qty=$grand_out_qty=$grand_finished_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=$grand_ttl_wash_send=$grand_ttl_wash_rcve=$grand_ttl_iron=0;
							/* echo "<pre>";
							print_r($prod_dtls_data_arr);
							echo "</pre>";*/

							foreach($prod_dtls_data_arr as $po_id=>$po_data)
							{
								foreach($po_data as $country_id=>$val)
								{
									// echo "<pre>";
									// print_r($val);
									if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$set_ratio=$val['ratio'];

									$po_country_qty=$po_country_dtls_qty[$po_id][$country_id]['po_qty']/$set_ratio;

									$po_country_qty_pcs=$po_country_dtls_qty[$po_id][$country_id]['po_qty'];
									$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id][$country_id]['prod_qty'];
									//$cut_lay_qty=$cut_lay_qty_data_arr[$po_id][$country_id]['cut_lay_qty'];
									// echo "<pre>";
									// print_r($wash_qnty_arr);


									// GRAND TOTAL CALCULATION
									$grand_exfact_qty+=$ex_fact_country_qty;
									$grand_finished_qty+=$val['finish_qty'];
									$grand_out_qty+=$val['sew_out_qty'];
									$grand_in_qty+=$val['sew_in_qty'];
									$grand_cut_lay_qty+=$val['cut_lay_qty'];
									$grand_cut_qty+=$val['cut_qty'];
									$grand_ttl_wash_send+=$val['wash_send'];
									$grand_ttl_wash_rcve+=$val['wash_rcve'];
									$grand_ttl_iron+=$val['iron_qty'];
									$grand_country_qty+=$po_country_qty;
									$grand_country_qty_pcs+=$po_country_qty_pcs;

									if($country_id=="00")
									{
										$po_country_qty=$po_country_dtls_qty[$po_id][$country_id]['po_qty'];
										$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
									}
									else
									{
										$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
									}
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
											<td width="30"><? echo $j;?></td>
											<td width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
											<td width="100"><div style="word-break:break-all"><? echo  $val['job_no'];  ?></div></td>
											<td width="120"><div style="word-break:break-all"><? echo $val['style'];?></div></td>
											<td width="100"><div style="word-break:break-all"><? echo $val['po_number'];echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
											<td width="100"><div style="word-break:break-all"><? echo $country_arr[$country_id];?></div></td>
											<td width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $po_button_qty;//$po_country_qty;?></p></td>
											<td width="100" align="right" title="<? echo 'Po-'.$po_id.'=C-'.$country_id;?>"><p><? echo $po_country_qty_pcs;?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['cut_lay_qty'],0);?></p></td>
											<td width="80" align="right"><p><? $cut_lay_p = ($val['cut_lay_qty']*100)/$po_country_qty_pcs; echo number_format($cut_lay_p,2);?>%</p></td>
											<td width="80" align="right"><p><? echo number_format($val['cut_qty'],0);?></p></td>
											<td width="80" align="right"><p><? $cut_qty_p = ($val['cut_qty']*100)/$po_country_qty_pcs; echo number_format($cut_qty_p,2);?>%</p></td>
											<td width="80" align="right"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['wash_send'],0); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['wash_rcve'],0); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['iron_qty'],0); ?></p></td>
											<td width="80" align="right"><p><? echo number_format($val['finish_qty'],0);?></p></td>
											<td width=""  align="right"><p><? echo number_format($ex_fact_country_qty,0);?></p></td>
										</tr>
									<?
									$j++;
								}
							}
						?>
					</table>
					<table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all">
						<tr>
							<td width="30">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">Grand Total:</td>
							<td width="100" align="right"><? echo number_format($grand_country_qty,0); ?></td>
							<td width="100" align="right"><? echo number_format($grand_country_qty_pcs,0); ?></td>
							<td width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
							<td width="80" align="right"></td>
							<td width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
							<td width="80" align="right"></td>
							<td width="80" align="right"><? echo number_format($grand_in_qty,0); ?></td>
							<td width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>
							<td width="80" align="right"> <? echo number_format($grand_ttl_wash_send,0); ?></td>
							<td width="80" align="right"> <? echo number_format($grand_ttl_wash_rcve,0); ?></td>
							<td width="80" align="right"> <? echo number_format($grand_ttl_iron,0); ?></td>
							<td width="80" align="right"><? echo number_format($grand_finished_qty,0); ?></td>
							<td width=""  align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
						</tr>
						<?
							if($grand_sum_country_qty!=$grand_country_qty_pcs)
							{
								?>
									<tr>
										<td width="30" colspan="14" align="center">
											<b style="color:#FF0000"> Country Qty of summary part and details part mismatch reason 1. After production country changed 2. Summary part data is correct as per PO entry. </b>
										</td>
									</tr>
								<?
							}
						?>
					</table>
				</div>
			</div>
		<?
	}
	else //Summary Button
	{
			
			$current_month=date("m",strtotime($start_date));
			//$current_month=date('m');
			$current_year=date("Y",strtotime($start_date));
			$lastmonth=$current_month-1;
			if($current_month==1)
			{
				$lastmonth2=12;
				$current_year2=$current_year-1;
			}

			$firstdate= "01-".$lastmonth."-".$current_year ;
			// $lastdateofmonth=date('t',$lastmonth);// 	
			$lastdateofmonth=cal_days_in_month(CAL_GREGORIAN, $lastmonth, $current_year); 
			$lastdateofmonth2=cal_days_in_month(CAL_GREGORIAN, $lastmonth2, $current_year2);
			$lastdate=$lastdateofmonth."-".$lastmonth."-".$current_year ;
			$lastdate2=$lastdateofmonth2."-".$lastmonth2."-".$current_year2 ;
			//echo $firstdate ."==".$lastdate ;
						
			if($db_type==0)
			{
				$firstdate=change_date_format($firstdate,"yyyy-mm-dd","");
				$lastdate=change_date_format($lastdate,"yyyy-mm-dd","");
				$lastdate2=change_date_format($lastdate2,"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$firstdate=change_date_format($firstdate,"","",1);
				$lastdate=change_date_format($lastdate,"","",1);
				$lastdate2=change_date_format($lastdate2,"","",1);
			}
			$sum_prod_date_cond=" and c.production_date between '$firstdate' and '$lastdate'";
			$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
			if($current_month==1) $sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate2'";
			$sum_width=1010;
			$sum_prod_date_cond2=" and d.ex_factory_date between '$firstdate' and '$lastdate'";
			$sum_width=1170;
			ob_start(); 
			?>
		<div style="width:<? echo $sum_width+30;?>px;  ruby-align:center" >
			<fieldset style="width:<? echo $sum_width+30;?>px;">
                <table width="<? echo $sum_width;?>"  cellspacing="0">
                    <tr style="border:none;">
                        <td colspan="13" align="center" style="border:none; font-size:16px; font-weight:bold">
                        <? echo  'Buyer wise summary '.' ('.$company_names.')'; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "Date: ". change_date_format($start_date).' To '. change_date_format($end_date);?>
                        </td>
                    </tr>
                </table>
                <br />	
                <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="120">Buyer</th>
                            <th width="120">Buyer Client</th>
                            <th width="100"><p>Country Order Qty (Pcs)</p></th>
                            <th width="80">Cut & Lay Qty</th>
                            <th width="80">Cut & Lay Qty %</th>
                            <th width="80">Cutting QC</th>
                            <th width="80">Cutting QC %</th>
                            <th width="80">Input.</th>
                            <th width="80">Sew Output</th>
                            <th width="80">Poly Output</th>
                            <th width="80">Finishing</th> 
                            <th width="80">Ship Out</th> 
                            <th width="">Last Month's Ship Out</th>
                        </tr>
                    </thead>
                </table>
                 <div style="width:<? echo $sum_width+20;?>px; max-height:245px; overflow-y:scroll; float:left;" id="scroll_body_summary">
                    <table class="rpt_table" width="<? echo $sum_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <?
					
				
					 
			//cut and lay qty.....................................
				$buyer_data_arr=array();
				$location_cond_cut_lay = str_replace("c.location", "c.location_id", $location_cond);
				$sql_cut_lay = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,c.entry_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
				  (d.size_qty) as prod_qty
				 FROM wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_bundle d
				WHERE  a.job_no=b.job_no_mst and a.job_no=c.job_no and d.order_id=b.id  and c.id=d.mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  $working_company7 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_cut_lay $floor_cond $prod_date_cond7 order by b.id,d.country_id ";
				//echo $sql_cut_lay;die;
				$cut_lay_result_arr=sql_select($sql_cut_lay);
				foreach($cut_lay_result_arr as $rows){
					$buyer_data_arr[$rows[csf('buyer_name')]][$rows[csf('buyer_client')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					
					
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['style']=$rows[csf('style_ref_no')];
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['buyer_name']=$rows[csf('buyer_name')];
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['buyer_client']=$rows[csf('buyer_client')];
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['po_number']=$rows[csf('po_number')];
					$prod_dtls_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['ratio']=$rows[csf('ratio')];
					
					
					$tmp_count[$rows[csf('country_id')]]=$rows[csf('country_id')];
					$tmp_po[$rows[csf('po_id')]]=$rows[csf('po_id')];
					//$cut_lay_qty_data_arr[$rows[csf('po_id')]][$rows[csf('country_id')]]['cut_lay_qty']+=$rows[csf('prod_qty')];

				}
				// echo "<pre>";
				// print_r($buyer_data_arr);
				// echo "</pre>";
				//cut and lay qty.....................................	
					  
					  				  
					$sql_prod_sum = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client,b.po_number,b.id as po_id,c.country_id,c.production_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,d.replace_qty,
							  (CASE WHEN c.production_type=1 and d.production_type=1 THEN d.production_qnty ELSE 0 END)  as cut_qty,
							  (CASE WHEN c.production_type=4 and d.production_type=4 THEN d.production_qnty ELSE 0 END)  as sew_in_qty,
							  (CASE WHEN c.production_type=5  and d.production_type=5 THEN d.production_qnty ELSE 0 END)  as sew_out_qty,
							  (CASE WHEN c.production_type=11 and d.production_type=11 THEN d.production_qnty ELSE 0 END)  as poly_qty,
							  (CASE WHEN c.production_type=8 and d.production_type=8  THEN d.production_qnty ELSE 0 END)  as finish_qty
							 FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
					WHERE  a.job_no=b.job_no_mst and a.job_no=e.job_no_mst and c.po_break_down_id=b.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and b.id=e.po_break_down_id and c.production_type in(1,4,5,11,8) and d.production_type in(1,4,5,11,8)  and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active in(1,2,3)  $working_company $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond $prod_date_cond order by b.id,c.country_id ";

					$subcon_sql_prod="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client, b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.production_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio,  
							  (CASE WHEN a.production_type=1 and d.production_type=1 THEN d.prod_qnty ELSE 0 END)  as cut_qty,

							  (CASE WHEN a.production_type=7  and d.production_type=7 THEN d.prod_qnty ELSE 0 END)  as sew_in_qty, 
							  (CASE WHEN a.production_type=2  and d.production_type=2 THEN d.prod_qnty ELSE 0 END)  as sew_out_qty,
							  (CASE WHEN a.production_type=5 and d.production_type=5 THEN d.prod_qnty ELSE 0 END)  as poly_qty,
							  (CASE WHEN a.production_type=4 and d.production_type=4  THEN d.prod_qnty ELSE 0 END)  as finish_qty
							   from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(1,2,4,5,7) and d.production_type in(1,2,4,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond_subcon ";

						
					$sum_result=sql_select($sql_prod_sum);
					$sum_result_subcon=sql_select($subcon_sql_prod);
 					
					//$po_id='';	$country_id='';$buyer_ids='';
					foreach($sum_result as $row)
					 {
							
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['cut_qty']+=$row[csf('cut_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['poly_qty']+=$row[csf('poly_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['finish_qty']+=$row[csf('finish_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['prod_qty']+=$row[csf('prod_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['buyer_client']=$row[csf('buyer_client')];
						
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['cut_qty']+=$row[csf('cut_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['poly_qty']+=$row[csf('poly_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['finish_qty']+=$row[csf('finish_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
						
						
						$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
						$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];	
						
					 } 

					 foreach($sum_result_subcon as $row)
					 {
							
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['cut_qty']+=$row[csf('cut_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['poly_qty']+=$row[csf('poly_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['finish_qty']+=$row[csf('finish_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['prod_qty']+=$row[csf('prod_qty')];
						$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['buyer_client']=$row[csf('buyer_client')];
						
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['cut_qty']+=$row[csf('cut_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['poly_qty']+=$row[csf('poly_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['finish_qty']+=$row[csf('finish_qty')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
						$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];
						
						
						$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];
						$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];	
						$all_subcon_po_id_arrs[$row[csf('po_id')]]=$row[csf('po_id')];
						
					 } 


					
				$location_cond_ex = str_replace("c.location", "c.location_id", $location_cond);
				$sql_prod_exf = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,a.client_id as buyer_client, b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,a.total_set_qnty as ratio,
			  	(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty
					 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
				WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond_ex $floor_cond  $prod_date_cond2 order by b.id,d.country_id ";

				$subcon_sql_ex="SELECT  b.subcon_job as job_no,b.job_no_prefix_num,b.party_id as buyer_name,c.cust_buyer as buyer_client,b.company_id as company_name,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id,'00' as country_id,a.delivery_date  as prod_date,c.order_quantity as po_quantity ,0 as ratio, (d.delivery_qty) as prod_qty from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0     and a.process_id=3   $working_company_subcon $buyer_id_cond_subcon $year_cond_subcon $job_cond_subcon  $order_cond_subcon $location_cond_subcon $floor_cond_subcon $prod_date_cond2_subcon ";


				$result_prod_exf=sql_select($sql_prod_exf);
				$result_prod_exf_subcon=sql_select($subcon_sql_ex);
				$po_no_id="";$buyer_ids2='';
				$po_country_id='';
				foreach($result_prod_exf as $row)
				{
					$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$po_ex_fact_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
					
					$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];	
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
				}
				// echo "<pre>";
				// print($po_ex_fact_sum_qty);
				// echo "</pre>";
				foreach($result_prod_exf_subcon as $row)
				{
					$po_ex_fact_sum_qty[$row[csf('buyer_name')]]['prod_qty']+=$row[csf('prod_qty')];
					$po_ex_fact_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
					
					$buyer_data_arr[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ex_factory']+=$row[csf('prod_qty')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['style']=$row[csf('style_ref_no')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_client']=$row[csf('buyer_client')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
					$prod_dtls_data_arr[$row[csf('po_id')]][$row[csf('country_id')]]['ratio']=$row[csf('ratio')];	
					$tmp_po[$row[csf('po_id')]]=$row[csf('po_id')];		
					if($po_no_id=='') $po_no_id=$row[csf('po_id')];else $po_no_id.=",".$row[csf('po_id')];
					if($po_country_id=='') $po_country_id=$row[csf('country_id')];else $po_country_id.=",".$row[csf('country_id')];	
					$tmp_count[$row[csf('country_id')]]=$row[csf('country_id')];
					//if($buyer_ids2=='') $buyer_ids2=$row[csf('country_id')];else $buyer_ids2.=",".$row[csf('country_id')];	
				}
				
				
				 $po_country_id=implode(",",array_unique(explode(",",$po_country_id)));
				 $po_no_id=implode(",",array_unique(explode(",",$po_no_id)));
				 
				
						//echo $po_cond_for_in;
						$poIds=implode(",",$tmp_po);
						$poIds=chop($poIds,','); $po_cond_for_in="";// $order_cond1=""; 
						$po_ids=count(array_unique(explode(",",$poIds)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$po_cond_for_in.=" b.id in($ids) or"; 
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
						}
						else
						{
							$poIds=implode(",",array_unique(explode(",",$poIds)));
							$po_cond_for_in=" and b.id in($poIds)";
						}
						
						// Country
						$country_Id=implode(",",$tmp_count);
						$poIds_c=chop($country_Id,','); $po_cond_for_in2="";// $order_cond1=""; 
						$po_ids_c=count(array_unique(explode(",",$country_Id)));
						if($db_type==2 && $po_ids_c>1000)
						{
							$po_cond_for_in2=" and (";
							$poIdsArr_c=array_chunk(explode(",",$poIds_c),999);
							foreach($poIdsArr_c as $ids)
							{
								$ids_c=implode(",",$ids);
								$po_cond_for_in2.=" c.country_id in($ids_c) or"; 
							}
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
						}
						else
						{
							$poIds_c=implode(",",array_unique(explode(",",$poIds_c)));
							$po_cond_for_in2=" and c.country_id in($poIds_c)";
						}
						
					$sql_prod_exf_last = "SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,a.company_name,a.style_ref_no,b.po_number,b.id as po_id,d.country_id,d.ex_factory_date as prod_date,b.po_quantity as po_quantity,
			  		(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as prod_qty,a.client_id as buyer_client
					 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
					WHERE c.delivery_company_id in($working_company_id) and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_cond $style_cond $order_cond $location_cond $floor_cond  $sum_prod_date_cond2 order by b.id,d.country_id ";
				$result_prod_exf_last=sql_select($sql_prod_exf_last);
				foreach($result_prod_exf_last as $row)
				{
					$po_ex_fact_sum_qty_lastmon[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['prod_qty']+=$row[csf('prod_qty')];
					//$po_ex_fact_sum_qty_lastmon[$row[csf('po_id')]][$row[csf('country_id')]]['prod_qty']+=$row[csf('prod_qty')];
				}
			 
				
				$sql_color="SELECT a.buyer_name,c.order_quantity as po_qty,b.id as po_id,c.country_id,a.client_id as buyer_client from   wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.is_deleted=0 and a.status_active=1 and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and  b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3)  $po_cond_for_in $po_cond_for_in2  $buyer_id_cond order by b.id,c.country_id ";
				$po_country_sum_qty=array();$po_country_dtls_qty=array();
				$sql_result_color=sql_select($sql_color);
				foreach($sql_result_color as $row)
				{
					$po_country_sum_qty[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['po_qty']+=$row[csf('po_qty')];
					$po_country_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
					$summ+=$row[csf('po_qty')];
					 //echo $row[csf('po_qty')]."=";
				}
				$sub_po_cond=implode(",",$all_subcon_po_id_arrs);
			    $sql_color_subcon ="SELECT a.party_id as buyer_name ,c.qnty as po_qty,b.id as po_id,'00' as country_id,b.cust_buyer as buyer_client from   subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where  a.subcon_job=b.job_no_mst and c.order_id=b.id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_po_cond) order by b.id ";
				foreach(sql_select($sql_color_subcon) as $row)
				{
					$po_country_sum_qty[$row[csf('buyer_name')]][$row[csf('buyer_client')]]['po_qty']+=$row[csf('po_qty')];
					$po_country_dtls_qty[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty']+=$row[csf('po_qty')];
					$summ+=$row[csf('po_qty')];
 				}

				
					 $k=1;$grand_sum_cut_qty=$grand_sum_in_qty=$grand_sum_out_qty=$grand_sum_poly_qty=$grand_sum_finished_qty=$grand_sum_exfact_qty=$grand_sum_country_qty=$grand_sum_last_month_qty=0;
					 // echo "<pre>";
					 // print_r($buyer_data_arr);
					foreach($buyer_data_arr as $buyer_id=>$client_val)
					{
						foreach($client_val as $client=>$val)
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
							$po_buyer_qty=$po_country_sum_qty[$buyer_id][$client]['po_qty'];
							$ex_fact_qty=$buyer_data_arr[$buyer_id][$client]['ex_factory'];//$po_ex_fact_sum_qty[$buyer_id]['prod_qty'];
							
							$last_cut_qty=$lastmon_buyer_data_arr[$buyer_id]['cut_qty'];
							$last_sew_in_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_in_qty'];
							$last_sew_out_qty=$lastmon_buyer_data_arr[$buyer_id]['sew_out_qty'];
							$last_poly_qty=$lastmon_buyer_data_arr[$buyer_id]['poly_qty'];
							$last_finish_qty=$lastmon_buyer_data_arr[$buyer_id]['finish_qty'];
							$last_ex_fact_sum_qty=$po_ex_fact_sum_qty_lastmon[$buyer_id][$client]['prod_qty'];
							//$last_month_qty=$last_cut_qty+$last_sew_in_qty+$last_sew_out_qty+$last_poly_qty+$last_finish_qty+$last_ex_fact_sum_qty;
							$last_month_qty=$last_ex_fact_sum_qty;
							?>

							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
								<td width="30"><? echo $k;?></td>
								<td width="120"><div style="word-break:break-all"><? echo  $buyer_short_library[$buyer_id];  ?></div></td>
								<td width="120"><div style="word-break:break-all"><? echo  $client_array[$client];  ?></div></td>
								<td width="100"  align="right"><p><? echo $po_buyer_qty;?></p></td>
								<td width="80"  align="right"><p><? echo $val['cut_lay_qty'];?></p></td>
								<td width="80"  align="right"><p><? $lay_percent = ($val['cut_lay_qty']*100)/$po_buyer_qty; echo number_format($lay_percent,2); ?>%</p></td>
								<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? echo number_format($val['cut_qty'],0);?></p></td>
								<td width="80" align="right" title="<? echo $last_cut_qty;?>"><p><? $cut_percent = ($val['cut_qty']*100)/$po_buyer_qty; echo number_format($cut_percent,2); ?>%</p></td>
								<td width="80" align="right" title="<? echo $last_sew_in_qty;?>"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
								<td width="80" align="right" title="<? echo $last_sew_out_qty;?>"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
								<td width="80" align="right" title="<? echo $last_poly_qty;?>"><p><? echo number_format($val['poly_qty'],0); ?></p></td>
								<td width="80" align="right" title="<? echo $last_finish_qty;?>"><p><? echo number_format($val['finish_qty'],0);?></p></td>
								<td width="80"  align="right" title="<? echo $last_ex_fact_sum_qty;?>"><p><? echo number_format($ex_fact_qty,0);?></p></td>
								<td width=""  align="right"><p><? echo $last_month_qty;?></p></td>
							</tr>
							<?
							$k++;
							$grand_sum_exfact_qty+=$ex_fact_qty;
							$grand_sum_finished_qty+=$val['finish_qty'];
							$grand_sum_poly_qty+=$val['poly_qty'];
							$grand_sum_out_qty+=$val['sew_out_qty'];
							$grand_sum_in_qty+=$val['sew_in_qty'];
							$grand_sum_cut_lay_qty+=$val['cut_lay_qty'];
							$grand_sum_cut_qty+=$val['cut_qty'];
							$grand_sum_country_qty+=$po_buyer_qty;
							$grand_sum_last_month_qty+=$last_month_qty;

						}
							
					}
						
						
					?>
                    
                    </table>
                     <table style="width:<? echo $sum_width;?>px" class="rpt_table" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <tfoot>
                        <th width="30">&nbsp;</th>
                        <th width="120"></th>
                        <th width="120">Grand Total:</th>
                        <th width="100" align="right"><? echo number_format($grand_sum_country_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_cut_lay_qty,0); ?></th>
                        <th width="80" align="right"></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_cut_qty,0); ?></th>
                        <th width="80" align="right"></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_in_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_out_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_poly_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_finished_qty,0); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_sum_exfact_qty,0); ?></th>
                     	<th width="" align="right"><? echo number_format($grand_sum_last_month_qty,0); ?></th>
                   </tfoot>
                </table>
                    </div>
                 </fieldset>
                 </div>
                 <?  $dtls_width=1490; ?>
                 <br/>
                 <!-- =========================================== DETAILS PART START ==================================== -->
                 <div style="width:<? echo $dtls_width+25;?>px;">
                 <table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                  <caption><strong>Detail Report</strong></caption>
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="100">Buyer</th>
                            <th width="100">Buyer Client</th>
                            <th width="120">Style</th>
                            <th width="100">PO No</th>
                            <th width="100">Country</th>
                            <th width="100"><p>Country Qty</p></th>
                            <th width="100"><p>Country Qty Pcs</p></th>
                            <th width="80">Cut & Lay Qty</th>
                            <th width="80">Cut & Lay Qty %</th>
                            <th width="80">Cutting QC</th>
                            <th width="80">Cutting QC %</th>
                            <th width="80">Input.</th>
                            <th width="80">Sew Output</th>
                            <th width="80">Poly Output</th>
                            <th width="80">Finishing</th> 
                            <th width="">Ship Out</th> 
                        </tr>
                    </thead>
                </table>
                 <div style="width:<? echo $dtls_width+20;?>px; max-height:245px; float:left; overflow-y:scroll" id="scroll_body">
                    <table class="rpt_table" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
                     $j=1;$grand_cut_qty=$grand_in_qty=$grand_out_qty=$grand_poly_qty=$grand_finished_qty=$grand_exfact_qty=$grand_country_qty=$grand_country_qty_pcs=0;
                    /* echo "<pre>";
                     print_r($prod_dtls_data_arr);
                     echo "</pre>";*/

					foreach($prod_dtls_data_arr as $po_id=>$po_data)
					{
						foreach($po_data as $country_id=>$val)
						{
							// echo "<pre>";
							// print_r($val);
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$set_ratio=$val['ratio'];
						
						$po_country_qty=$po_country_dtls_qty[$po_id][$country_id]['po_qty']/$set_ratio;
						 
						$po_country_qty_pcs=$po_country_dtls_qty[$po_id][$country_id]['po_qty'];
						$ex_fact_country_qty=$po_ex_fact_dtls_qty[$po_id][$country_id]['prod_qty'];	
						//$cut_lay_qty=$cut_lay_qty_data_arr[$po_id][$country_id]['cut_lay_qty'];
						
						if($country_id=="00")
						{
						 	$po_country_qty=$po_country_dtls_qty[$po_id][$country_id]['po_qty'];
						 	$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
						}
						else
						{
							$po_button_qty="<a href='##' onClick=\"generate_po_report_popup('".$po_id."','".$country_id."','".$working_company_id."','".$process_rpt_type."','".$txt_date_from."','".$txt_date_to."','show_po_country_size_report','$prod_type')\"> ".number_format($po_country_qty,0)." </a>";
						}
					?>
                     
                     <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>">
                    <td width="30"><? echo $j;?></td>
                    <td width="100"><div style="word-break:break-all"><? echo  $buyer_short_library[$val['buyer_name']];  ?></div></td>
                    <td width="100"><div style="word-break:break-all"><? echo  $client_array[$val['buyer_client']];  ?></div></td>
                    <td width="120"><div style="word-break:break-all"><? echo $val['style'];?></div></td>
                    <td width="100"><div style="word-break:break-all"><? echo $val['po_number'];echo $sub_self=($country_id=="00")?  "<br> (Sub-contract)" :   ""; ?></div></td>
                    <td width="100"><div style="word-break:break-all"><? echo $country_arr[$country_id];?></div></td>
                    <td width="100"  align="right" title="<? echo 'S '.$set_ratio;?>"><p><? echo $po_button_qty;//$po_country_qty;?></p></td>
                    <td width="100" align="right" title="<? echo 'Po-'.$po_id.'=C-'.$country_id;?>"><p><? echo $po_country_qty_pcs;?></p></td>
                    <td width="80" align="right"><p><? echo number_format($val['cut_lay_qty'],0);?></p></td>
                    <td width="80" align="right"><p><? $cut_lay_p = ($val['cut_lay_qty']*100)/$po_country_qty_pcs; echo number_format($cut_lay_p,2);?>%</p></td>
                    <td width="80" align="right"><p><? echo number_format($val['cut_qty'],0);?></p></td>
                    <td width="80" align="right"><p><? $cut_qty_p = ($val['cut_qty']*100)/$po_country_qty_pcs; echo number_format($cut_qty_p,2);?>%</p></td>
                    <td width="80" align="right"><p><? echo number_format($val['sew_in_qty'],0);?></p></td>
                    <td width="80" align="right"><p><? echo number_format($val['sew_out_qty'],0);?></p></td>
                    <td width="80" align="right"><p><? echo number_format($val['poly_qty'],0); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($val['finish_qty'],0);?></p></td>
                    <td width=""  align="right"><p><? echo number_format($ex_fact_country_qty,0);?></p></td>
                    </tr>
                    <?
                    	$j++;
						$grand_exfact_qty+=$ex_fact_country_qty;
						$grand_finished_qty+=$val['finish_qty'];
						$grand_poly_qty+=$val['poly_qty'];
						$grand_out_qty+=$val['sew_out_qty'];
						$grand_in_qty+=$val['sew_in_qty'];
						$grand_cut_lay_qty+=$val['cut_lay_qty'];
						$grand_cut_qty+=$val['cut_qty'];
						$grand_country_qty+=$po_country_qty;
						$grand_country_qty_pcs+=$po_country_qty_pcs;
						}
					}
					?>
                    </table>
                    <table style="width:<? echo $dtls_width;?>px" class="tbl_bottom" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all">
                       
                    	<tr>
                        <td width="30">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">Grand Total:</td>
                        <td width="100" align="right"><? echo number_format($grand_country_qty,0); ?></td>
                         <td width="100" align="right"><? echo number_format($grand_country_qty_pcs,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_cut_lay_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_cut_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_in_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_out_qty,0); ?></td>
                        <td width="80" align="right"> <? echo number_format($grand_poly_qty,0); ?></td>
                        <td width="80" align="right"><? echo number_format($grand_finished_qty,0); ?></td>
                        <td width=""  align="right"><? echo number_format($grand_exfact_qty,0); ?></td>
                    	 </tr>
                         <?
						 	if($grand_sum_country_qty!=$grand_country_qty_pcs)
							{
						 ?>
                         <tr>
                        	<td width="30" colspan="14" align="center">
                            	<b style="color:#FF0000"> Country Qty of summary part and details part mismatch reason 1. After production country changed 2. Summary part data is correct as per PO entry. </b>
                            </td>
                       
                    	 </tr>
                   		<? } ?>      
                   
                   </table>
                    </div>
                </div>
    		<?		
	}
		?>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$process_rpt_type"; 
	exit();	
}

if ($action=="show_prod_country_size_report")  // All Production Data popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	 $po_break_down_id=str_replace("'","",$po_id);
	 $country_id=str_replace("'","",$country_id);
	 $prod_popup_type=str_replace("'","",$type);
	 $company_name=str_replace("'","",$company_name);
	 if($prod_popup_type=="1"){$subcon_prod_type="1";}
	 else if($prod_popup_type=="4"){$subcon_prod_type="7";}
	 else if($prod_popup_type=="5"){$subcon_prod_type="2";}
	 else if($prod_popup_type=="11"){$subcon_prod_type="5";}
	 else if($prod_popup_type=="8"){$subcon_prod_type="4";}
 	 $process_rpt_type=str_replace("'","",$process_rpt_type);
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	if($country_id=="00")
	{  
		$sizearr_order=return_library_array("SELECT size_id as size_number_id,size_id as size_number_id from subcon_ord_breakdown where order_id=$po_break_down_id","size_number_id","size_number_id");
	}
 	$country_cond="";
	if($country_id>0) $country_cond=" and c.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and c.color_number_id=$color_id";
	
	$sql_order_size= sql_select("SELECT c.size_number_id, sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active in(1,2,3) and c.is_deleted=0 and c.po_break_down_id=$po_break_down_id  $country_cond  
		group by  c.size_number_id order by c.size_number_id");
	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]]=$row[csf('order_quantity')];
		$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}
	$country_cond="";
	if($country_id>0) $country_cond=" and a.country_id=$country_id";
	$color_size_sql=sql_select( "SELECT a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3)  and a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") $country_cond");

	$color_size_data=array(); $allcolor_id_arr=array();
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
	}
	if($country_id=="00")
	{
		$color_size_sql=sql_select( "SELECT id, color_id as color_number_id, size_id as size_number_id from subcon_ord_breakdown  where color_id>0 and size_id>0 and order_id in (".$po_break_down_id.") ");

		$color_size_data=array(); $allcolor_id_arr=array();
		foreach($color_size_sql as $row)
		{
			$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
			$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
		}

	}

		
		if($process_rpt_type==7)
		{
 			$country_cond="";
			if($country_id>0) $country_cond=" and c.country_id=$country_id";
			if($color_number_id>0) $color_number_id_cond=" and b.color_id='$color_number_id' ";
			/*$sql_colsize="SELECT b.color_id,b.size_id, sum(b.marker_qty) as production_qnty
			from ppl_cut_lay_mst a, ppl_cut_lay_size b c
			where a.id=b.mst_id and b.order_id=$po_break_down_id and a.entry_date between '$from_date' and '$to_date' $country_cond and a.working_company_id=$company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_id,b.size_id";*/
			 $sql_colsize="select b.color_id,c.size_id, sum(c.size_qty) as production_qnty from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and  c.order_id=$po_break_down_id and a.entry_date between '$from_date' and '$to_date' $country_cond and a.working_company_id in($company_name)    $color_number_id_cond   group by b.color_id,c.size_id";

		}else if($process_rpt_type==8)
		{
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			 $sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id and a.po_break_down_id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and  a.production_date between '$from_date' and '$to_date' $country_cond and a.SENDING_COMPANY in($company_name) and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			// echo $sql_colsize;die;
			if($country_id=="00")
			{
				  $sql_colsize="SELECT  b.ord_color_size_id as color_size_break_down_id, (b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where   a.order_id=$po_break_down_id and  a.production_type='$subcon_prod_type' and b.production_type='$subcon_prod_type' and a.company_id in($company_name) and  a.production_date between '$from_date' and '$to_date'  and a.id=b.dtls_id and a.order_id=c.order_id and a.status_active=1 and a.is_deleted=0   and c.id=b.ord_color_size_id";

			}

		}else if($process_rpt_type==10)
		{
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			 $sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id and a.po_break_down_id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and  a.production_date between '$from_date' and '$to_date' $country_cond and a.serving_company in($company_name) and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			// echo $sql_colsize;die;
			if($country_id=="00")
			{
				  $sql_colsize="SELECT  b.ord_color_size_id as color_size_break_down_id, (b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where   a.order_id=$po_break_down_id and  a.production_type=3 and b.production_type=3 and a.company_id in($company_name) and  a.production_date between '$from_date' and '$to_date'  and a.id=b.dtls_id and a.order_id=c.order_id and a.status_active=1 and a.is_deleted=0   and c.id=b.ord_color_size_id";

			}

		}else if($process_rpt_type==9)
		{
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			 $sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id and a.po_break_down_id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and  a.production_date between '$from_date' and '$to_date' $country_cond and a.SENDING_COMPANY in($company_name) and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			// echo $sql_colsize;die;
			if($country_id=="00")
			{
				  $sql_colsize="SELECT  b.ord_color_size_id as color_size_break_down_id, (b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where   a.order_id=$po_break_down_id and  a.production_type=3 and b.production_type=3 and a.company_id in($company_name) and  a.production_date between '$from_date' and '$to_date'  and a.id=b.dtls_id and a.order_id=c.order_id and a.status_active=1 and a.is_deleted=0   and c.id=b.ord_color_size_id";

			}

		}
		
		 

		else if($process_rpt_type!=6)
		{ 
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			 $sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id and a.po_break_down_id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and  a.production_date between '$from_date' and '$to_date' $country_cond and a.serving_company in($company_name) and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			if($country_id=="00")
			{
				  $sql_colsize="SELECT  b.ord_color_size_id as color_size_break_down_id, (b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where   a.order_id=$po_break_down_id and  a.production_type='$subcon_prod_type' and b.production_type='$subcon_prod_type' and a.company_id in($company_name) and  a.production_date between '$from_date' and '$to_date'  and a.id=b.dtls_id and a.order_id=c.order_id and a.status_active=1 and a.is_deleted=0   and c.id=b.ord_color_size_id";

			}
		}
		

		else //Ex-Factory
		{
			$country_cond="";
			if($country_id>0) $country_cond=" and a.country_id=$country_id";
			$sql_colsize="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
			from pro_ex_factory_mst a, pro_ex_factory_dtls b 
			where a.id=b.mst_id  and a.po_break_down_id=$po_break_down_id and  	a.ex_factory_date between '$from_date' and '$to_date'  $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id";
			if($country_id=="00")
			{
				$sql_colsize="SELECT c.id as color_size_break_down_id, sum(b.delivery_qty) as production_qnty from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_breakdown c where a.id=b.mst_id and b.order_id=c.order_id and a.status_active=1 and a.is_deleted=0  and a.process_id=3 and b.order_id=$po_break_down_id group by c.id";
			}
		}
		
		    //echo $sql_colsize;
		
		$sql_color_size=sql_select($sql_colsize);
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			if($process_rpt_type==7){
				$prod_color_size_data[$row[csf('color_id')]][$row[csf('size_id')]]["qty"]+=$row[csf('production_qnty')];
			}
			else
			{
				$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
			}
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption> <strong>Details<? echo '  '.$production_process[$process_rpt_type];?></strong></caption>
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				$k=1;
				foreach($allcolor_id_arr as $inc=>$color_id_val) 
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					 ?>
                
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
               
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["qty"]; ?></td> 
                        <?
						$tot_size_qty_arr[$size_id]+=$prod_color_size_data[$color_id_val][$size_id]["qty"];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_poly_qnty,0); ?></td>
                </tr>
                <? 
				$k++;
				} ?>
            </tbody>
            <tfoot>
            <th>Total</th>
            <?
			 $total_size_qty=0;
         	 foreach($sizearr_order as $size_id)
                    {
                        ?>
                     <th align="right"> <? echo number_format($tot_size_qty_arr[$size_id],0); ?></th>
                       <?
					   
					   $total_size_qty+=$tot_size_qty_arr[$size_id];
					}
					 ?>
            <th align="right"> <? echo number_format($total_size_qty,0);?></th>
            </tfoot>
		</table>
    	</div>
    	<?
	exit(); 
}


if ($action=="show_po_country_size_report")  
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');	
	$po_break_down_id=str_replace("'","",$po_id);
	$country_id=str_replace("'","",$country_id);
	$prod_popup_type=str_replace("'","",$type);
	$process_rpt_type=str_replace("'","",$process_rpt_type);
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	
	$total_set_qnty=return_field_value("total_set_qnty","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and b.id in($po_break_down_id)","total_set_qnty");
	//$po_job_Arr_library=return_library_array( "select id,total_set_qnty from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_break_down_id)", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	if($country_id=="00")
	{
		$sizearr_order=return_library_array("SELECT size_id as size_number_id,size_id as size_number_id from subcon_ord_breakdown where order_id=$po_break_down_id","size_number_id","size_number_id");
	}
	
	$country_cond="";
	if($country_id>0) $country_cond=" and c.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and c.color_number_id=$color_id";
	if($country_id!="00")
	{
		$sql_order_size= sql_select("SELECT c.size_number_id, c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active in(1,2,3) and c.is_deleted=0 and c.po_break_down_id=$po_break_down_id  $country_cond  
		group by  c.color_number_id,c.size_number_id order by c.size_number_id");
	}
	

	else 
	{
		$sql_order_size_sub= sql_select("SELECT size_id as  size_number_id, color_id as color_number_id,sum(qnty) as order_quantity, sum(plan_cut) as plan_cut_qnty
		from subcon_ord_breakdown  
		where order_id=$po_break_down_id   
		group by  size_id,color_id order by size_id");
	}

	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')]/$total_set_qnty;
		//$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}
	foreach($sql_order_size_sub as $row)
	{
		$order_size_qnty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')] ;
		//$plan_order_size_qnty[$row[csf('size_number_id')]]=$row[csf('plan_cut_qnty')];
	}

	$country_cond="";
	if($country_id>0) $country_cond=" and a.country_id=$country_id";
	$color_size_sql=sql_select( "SELECT a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3)  and a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") $country_cond");
	$color_size_data=array(); $allcolor_id_arr=array();
	if($country_id=="00")
	{
		$color_size_sql=sql_select( "SELECT a.id, a.color_id as color_number_id , a.size_id as size_number_id from subcon_ord_breakdown a where   a.color_id>0 and a.size_id>0 and a.order_id in (".$po_break_down_id.") ");
	}
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
	}
		$table_width=(200+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption> <strong>Color Size Details<? //echo '  '.$production_process[$process_rpt_type];?></strong></caption>
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				$k=1;
				foreach($allcolor_id_arr as $inc=>$color_id_val) 
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					 ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $k; ?>">
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_size_qnty_sum=0;
                    foreach($sizearr_order as $size_id)
                    {
						$color_size_qty=$order_size_qnty[$color_id_val][$size_id]['qty'];
                        ?>
                        <td align="right"><? echo number_format($color_size_qty,0); $total_size_qnty_sum+=$color_size_qty; ?></td> 
                        <?
						$tot_size_qty_arr[$size_id]+=$color_size_qty;
                    }
                    ?>
                    <td align="right"><? echo number_format($total_size_qnty_sum,0); ?></td>
                </tr>
                <? 
				$k++;
				} ?>
            </tbody>
            <tfoot>
            <th>Total</th>
            <?
			 $total_size_qty=0;
         	 foreach($sizearr_order as $size_id)
                    {
                        ?>
                     <th align="right"> <? echo number_format($tot_size_qty_arr[$size_id],0); ?></th>
                       <?
					   
					   $total_size_qty+=$tot_size_qty_arr[$size_id];
					}
					 ?>
            <th align="right"> <? echo number_format($total_size_qty,0);?></th>
            </tfoot>
		</table>
    	</div>
    	<?
	exit(); 
}
