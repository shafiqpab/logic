<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');


$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$color_library = return_library_array("select id,color_name from  lib_color", "id", "color_name");
$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");


if ($db_type == 2) $select_date = " to_char(a.insert_date,'YYYY')";
else if ($db_type == 0) $select_date = " year(a.insert_date)";

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
if ($action=="load_drop_down_customer_buyer")
{
	$data=explode("_",$data);

	////if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	//else $load_function="";
	
	if($data[1]==1)
	{
		//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
		
		echo create_drop_down( "cbo_buyer_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if($action=="job_no_popup")
{
	//echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	 
      <script>
	   function js_set_value(str) {
		   //alert(str);
		   str=str.split("_");
            $("#hide_job_id").val(str[0]);
			 $("#hide_job_no").val(str[1]);
            parent.emailwindow.hide();
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
							$party_name_cond="";
							//echo $cbo_party_name.'DD';
							 if($cbo_within_group==1)
							 {
								 if($cbo_party_name>0 ) $party_name_cond="and comp.id=$cbo_party_name";
								  
							 
							echo create_drop_down( "cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond  $party_name_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $buyer_name, "");
							 
							 }
							 else
							 {
								 if($cbo_party_name>0 ) $party_name_cond="and buy.id=$cbo_party_name";
								 	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond $party_name_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							 }
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		if($type==1)
							{
								$kk=2;
							}
							else
							{
								$kk=1;
							}
							$search_by_arr=array(1=>"Wash Job No",2=>"Buyer Style",3=>"W/O");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";	
													
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $kk,$dd,0 );
							
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $cbo_within_group; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $buyer_customer; ?>', 'create_list_style_search', 'search_div', 'style_wise_daily_chemical_cost_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	$type=$data[6];
	$buyer_customer=$data[7];
 
	$buyer_customer=str_replace("'","",$buyer_customer);
	$buyer_customerArr=explode("_",$buyer_customer);
	$buyer_customer_name=$buyer_customerArr[1];
	
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
	 if($type==1)
	 {
		 $type_cond="id,style_ref_no";
	 }
	 else
	 {
		  $type_cond="id,job_no_prefix_num";
	 }
	 if($buyer_customerArr[0]>0)
			{
			if($buyer_customer_name) $buyer_cust_cond=" and b.party_buyer_name='$buyer_customer_name'"; else $buyer_cust_cond=" ";
			}
			
	$arr=array (2=>$party_arr,7=>$buyer_arr);
	if($within_group==1)
	{
		
	    $sql= "select a.id,b.id as po_id, a.party_id,a.subcon_job as job_no, a.job_no_prefix_num, a.company_id as company_name, a.party_id as buyer_name,b.party_buyer_name, b.buyer_style_ref as style_ref_no,b.order_no as wo_no,c.booking_no_prefix_num as wo_prefix, $year_field from subcon_ord_mst a,subcon_ord_dtls b,wo_booking_mst c where  a.subcon_job=b.job_no_mst and b.order_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.entry_form=295 and c.booking_type=6 and a.within_group=$within_group  and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id   $buyer_id_cond $search_cond $year_cond $buyer_cust_cond group by a.id,b.id,a.insert_date, a.subcon_job, a.job_no_prefix_num, a.company_id, b.order_no,b.party_buyer_name,a.party_id, b.buyer_style_ref,a.insert_date,c.booking_no_prefix_num order by a.id desc";
	}
	else
	{
		  $sql= "select a.id, a.party_id,a.subcon_job as job_no, a.job_no_prefix_num, a.company_id as company_name, a.party_id as buyer_name,b.party_buyer_name, b.buyer_style_ref as style_ref_no,b.order_no as wo_no, $year_field from subcon_ord_mst a,subcon_ord_dtls b where  a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.entry_form=295 and a.company_id=$company_id  and a.within_group=$within_group  $buyer_id_cond $buyer_cust_cond $search_cond $year_cond  group by a.id,a.insert_date, a.subcon_job, a.job_no_prefix_num, a.company_id, b.order_no,b.party_buyer_name,a.party_id, b.buyer_style_ref,a.insert_date order by a.id desc";
	}
	//echo $sql;die;
	echo create_list_view("list_view", "Wash Job no,Job Year,Customer,Customer Buyer,Buyer Style,WO No,WO Suffix", "120,50,100,80,100,100,60","700","240",0, $sql , "js_set_value", "$type_cond", "", 1, "0,0,party_id,0,0", $arr , "job_no_prefix_num,year,party_id,party_buyer_name,style_ref_no,wo_no,wo_prefix",  "","setFilterGrid('list_view',-1)","0","",0) ;
	
	echo "<input type='hidden' id='hide_job_no' />";
	echo "<input type='hidden' id='hide_job_id' />";
	//echo "<input type='hidden' id='hide_job_sl' />";
	?>
   
    <?
	exit(); 
} // Job Search end

 

if($action=="report_generate")
{
			$process = array( &$_POST );
			extract(check_magic_quote_gpc( $process ));
			//$season_name_arr=return_library_array( "select id, season_name from  lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
			//$brand_name_arr=return_library_array( "select id, brand_name from  lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
			$reporttype=str_replace("'","",$reporttype);
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			//$cbo_season_year=str_replace("'","",$cbo_season_year);
			$txt_style=str_replace("'","",$txt_style);
			$txt_style_id=str_replace("'","",$txt_style_id);
			$txt_job=str_replace("'","",$txt_job);
			$txt_job_id=str_replace("'","",$txt_job_id);
			$cbo_within_group=str_replace("'","",$cbo_within_group);
			$cbo_party_name=str_replace("'","",$cbo_party_name);
			//$file_no=rtrim($cbo_brand_id,',');buyer_customer
			$buyer_customer=str_replace("'","",$buyer_customer);
			$buyer_customerArr=explode("_",$buyer_customer);
			$buyer_customer_name=$buyer_customerArr[1];
			//echo $buyer_customer_name.'DD';die;
			 
			
			if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_id=$cbo_company_name ";
			if($cbo_party_name==0) $party_cond=""; else $party_cond=" and a.party_id='$cbo_party_name' ";
			if($cbo_within_group==0) $within_cond=""; else $within_cond=" and a.within_group='$cbo_within_group' ";
			if($buyer_customerArr[0]>0)
			{
			if($buyer_customer_name) $buyer_cust_cond=" and b.party_buyer_name='$buyer_customer_name'"; else $buyer_cust_cond=" ";
			}
			//echo $buyer_cust_cond.'DD';;

			 if($cbo_within_group==1)
			{
				//$within_con="and d.within_group=$cbo_within_group";
				$com_party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
				//print_r($com_party_arr);
			}
			else
			{
				$com_party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
			}

			$job_style_cond="";
			if(trim(str_replace("'","",$txt_style))!="")
			{
				if(str_replace("'","",$txt_style_id)!="")
				{
					$job_style_cond=" and a.id in(".str_replace("'","",$txt_style_id).")";
				}
				else
				{
					$job_style_cond=" and b.buyer_style_ref = '".trim(str_replace("'","",$txt_style))."'";
				}
			}

			$job_cond="";
			if(trim(str_replace("'","",$txt_job))!="")
			{
				if(str_replace("'","",$txt_job_id)!="")
				{
					$job_cond=" and a.id in(".str_replace("'","",$txt_job_id).")";
				}
				else
				{
					$job_cond=" and a.subcon_job = '".trim(str_replace("'","",$txt_job))."'";
				}
			}
					 

			ob_start();

			//$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			////$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
			//$supplier_library_fabric=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"); //b.party_type in(1,9) and

	if($reporttype==1) //Budget Button
	{
		      $sql_wash="select  a.id, a.party_id,a.subcon_job as job_no, a.job_no_prefix_num, a.company_id as company_name, a.party_id as buyer_name,b.party_buyer_name, b.buyer_style_ref as style_ref_no,b.order_no as wo_no,b.id as po_id,b.gmts_color_id from subcon_ord_mst a, subcon_ord_dtls b  where a.subcon_job=b.job_no_mst   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond  $job_cond $job_style_cond  $buyer_id_cond  $buyer_cust_cond $party_cond $within_cond order  by b.id";  
			//echo $sql; die;
				$sql_po_result=sql_select($sql_wash);
				$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
				$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
				//echo $buyer_name;die;
				foreach($sql_po_result as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					$po_qty_by_job[$row[csf("job_no")]]=$row[csf('po_quantity')]*$row[csf('ratio')];
					$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$style_wise_arr[$row[csf("po_id")]]['party_id']=$row[csf("party_id")];
					$style_wise_arr[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
					$style_wise_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
					
					$style_color_wise_arr[$row[csf("po_id")]]['gmts_color_id']=$row[csf("gmts_color_id")];
					$wash_po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
				}
				
					//print_r($wash_batch_arr);
					//echo $txt_ex_rate;
				    $sql_wash_issue="select a.id,a.style_ref,a.issue_date,a.buyer_job_no as job_no,a.sub_order_id,b.prod_id,b.cons_quantity, b.cons_rate, b.cons_amount,c.item_description,e.sub_process as color_id,e.recipe_id from inv_issue_master a,inv_transaction b,product_details_master c,dyes_chem_issue_dtls e where  a.id=b.mst_id and c.id=b.prod_id and b.id=e.trans_id and a.id=e.mst_id and  a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0    and a.entry_form=298 and a.sub_order_id>0  ".where_con_using_array($wash_po_id_arr,0,'a.sub_order_id')."  order  by a.id";
				    $sql_wash_issue_result=sql_select($sql_wash_issue);
					 
					foreach($sql_wash_issue_result as $row)
					{
						$party_id=$style_wise_arr[$row[csf("sub_order_id")]]['party_id'];
					 //	echo $party_id.'ff';
						$style_ref_no=$style_wise_arr[$row[csf("sub_order_id")]]['style_ref_no'];
						$job_no=$style_wise_arr[$row[csf("sub_order_id")]]['job_no'];
						//$gmts_color_id=$style_color_wise_arr[$row[csf("sub_order_id")]]['gmts_color_id']; 
						$gmts_color_id=$row[csf("color_id")];
						$party_data=$party_id.'_'.$style_ref_no.'_'.$gmts_color_id;
						$issue_date=$row[csf("issue_date")];
						
						$party_style_color_wise_arr[$party_data][$issue_date].=$issue_date.',';
						$party_style_job_no_arr[$party_data].=$job_no.',';
						
						$prod_style_wise_arr[$party_data][$row[csf("prod_id")]]['issue_date']=$issue_date;
						$prod_style_wise_arr[$party_data][$row[csf("prod_id")]]['decs']=$row[csf("item_description")];
						//$prod_style_wise_qty_arr[$party_data][$row[csf("prod_id")]][$issue_date]['qty']+=$row[csf("cons_quantity")];
						$prod_style_wise_qty_arr[$party_data][$row[csf("prod_id")]][$issue_date]['qty']+=$row[csf("cons_quantity")];
						$prod_style_wise_qty_arr[$party_data][$row[csf("prod_id")]][$issue_date]['amt']+=$row[csf("cons_amount")];
						
						$wash_recipeId_arr[$row[csf("recipe_id")]]=$row[csf("recipe_id")];
					}
					unset($sql_wash_issue_result);
				//	print_r($party_style_color_wise_arr);die;
					//echo $sew_smv;
					
					//print_r($po_qty_by_job);
				 $sql_wash_batch="select d.id as recipe_id,a.id,a.extention_no,a.batch_no,a.color_id,b.roll_no as batch_qnty,b.po_id,c.issue_date,c.req_no,b.buyer_style_ref from pro_batch_create_mst a,pro_batch_create_dtls b,inv_issue_master c,pro_recipe_entry_mst d where  a.id=b.mst_id and c.sub_order_id=b.po_id and d.batch_id=a.id and b.mst_id=d.batch_id and d.entry_form=300 and to_char(d.id)=c.lap_dip_no  and a.entry_form=316  and c.entry_form=298 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($wash_recipeId_arr,0,'d.id')."   order  by a.id";
				    $sql_wash_batch_result=sql_select($sql_wash_batch);
					 
					foreach($sql_wash_batch_result as $row)
					{
						 $party_id=$style_wise_arr[$row[csf("po_id")]]['party_id'];
						 $party_data=$party_id.'_'.$row[csf("buyer_style_ref")].'_'.$row[csf("color_id")];
						// echo $row[csf("batch_qnty")].'='.$row[csf("batch_no")].'<br>';
						$ext_no="";
						if($row[csf("extention_no")])
						{
							$ext_no="-".$row[csf("extention_no")];
						}
						$recipe_id=$row[csf("recipe_id")];
						//$wash_batch_arr[$party_data][$row[csf("issue_date")]]+=$row[csf("batch_qnty")];
						$issue_date_arr[$party_data][$recipe_id]=$row[csf("issue_date")];
						$wash_batch_no_arr[$party_data][$row[csf("issue_date")]].=$row[csf("batch_no")].$ext_no.',';
					}
					unset($sql_wash_result);
					
					$sql_wash_batch2="select d.id as recipe_id,a.id,a.extention_no,a.batch_no,a.color_id,b.roll_no as batch_qnty,b.po_id,b.buyer_style_ref from pro_batch_create_mst a,pro_batch_create_dtls b,pro_recipe_entry_mst d where  a.id=b.mst_id  and d.batch_id=a.id and b.mst_id=d.batch_id and d.entry_form=300  and a.entry_form=316  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($wash_recipeId_arr,0,'d.id')."   order  by a.id";
				    $sql_wash_batch_result2=sql_select($sql_wash_batch2);
					 
					foreach($sql_wash_batch_result2 as $row)
					{
						 $party_id=$style_wise_arr[$row[csf("po_id")]]['party_id'];
						 $party_data=$party_id.'_'.$row[csf("buyer_style_ref")].'_'.$row[csf("color_id")];
						 $issue_date=$issue_date_arr[$party_data][$row[csf("recipe_id")]];
						// echo $row[csf("batch_qnty")].'='.$row[csf("recipe_id")].'<br>';
						
						$wash_batch_arr[$party_data][$issue_date]+=$row[csf("batch_qnty")];
						 
					}
					unset($sql_wash_batch_result2);
					
					
				 
			
				$style1="#E9F3FF";
				$style="#FFFFFF";
			//print_r($style_wise_arr);
			//echo count($style_wise_arr);
			/*if(count($party_style_color_wise_arr)==1)
			{
				$width_td=570+80*count($party_style_color_wise_arr);
			}
			else
			{
			$width_td=490*count($party_style_color_wise_arr);
			}*/
			$width_td=570;
			/*foreach($party_style_color_wise_arr as $party_data=>$party_arr)
			{
				//echo count($party_arr).'m';
				//$width_td+=240*count($party_arr);
			}*/
			//echo count($party_style_color_wise_arr).'d';
			//echo $width_td;
			//$width_td=$width_td/count($party_style_color_wise_arr);
		?>
        <div style="width:100%">
        <style>
		@media print {
			  #page_break_div {
				page-break-before: always;
			  }

				.footer_signature {
				position:fixed;
				height:auto;
				bottom:0;
				width:100%;
				}
			}
		</style>
 			<div style="width:<? echo $width_td;?>px; margin:2 auto; margin-left:5px;">
          <table cellpadding="0" cellspacing="0" width="<? echo $width_td;?>">
         		
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? 
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
						foreach ($nameArray as $result)
                        {
							$email=$result[csf('email')];
							$plot_no=$result[csf('plot_no')];
							$level_no=$result[csf('level_no')];
							$road_no=$result[csf('road_no')];
							$block_no=$result[csf('block_no')];	
							
							//$location_name_arr[$location];
						}
						echo $company_library[$cbo_company_name];
						echo "<br>";
						echo $plot_no.','.$level_no.','.$road_no.','.$block_no;
						
						 ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="6" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
                </tr>
                
            </table>
            <?
			//$garnd_total_order_qty=$garnd_total_order_amount=$garnd_total_recv_qty=$garnd_total_delivery_qty=$garnd_total_revenue=$garnd_total_access_shortage_per=$garnd_total_access_shortage_val=0;
			$p=1;
			$summary_issue_date_data=array();$summary_issue_data=array();
           foreach($party_style_color_wise_arr as $party_id=>$partyArr)
			{
				asort($partyArr);
				$company_party_arr=explode('_',$party_id);
				
				$width_td=500+(240*count($partyArr));
				$job_no=rtrim($party_style_job_no_arr[$party_id],",");
				$job_nos=implode(",",array_unique(explode(",",$job_no)));
			?>
            <div style="width:<? echo $width_td;?>px;">
            <table width="<? echo $width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
             
            <tr>
            <td colspan="7"><b style="float:left" title=""> &nbsp;Buyer: <?   echo $com_party_arr[$company_party_arr[0]];?> ,&nbsp;<b title="<? echo "Wash Job:".$job_nos;?>">Style: <?  echo $company_party_arr[1];?></b>,  &nbsp;Color: <?  echo $color_library[$company_party_arr[2]];?></b> </td>  
            
            </tr>
            </table>
            </div>
            <table width="<? echo $width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
            	 
                <thead>
                         <tr>
                         	<th width="30">&nbsp;</th>
                              <th width="230">&nbsp;</th>
							<?
                                $tot_days=$tot_prod_qty=0;
								foreach($partyArr as $issue_date=>$dateArr)
								{
									$tot_days++;
									$prod_qty=$wash_batch_arr[$party_id][$issue_date];
									$batch_no=rtrim($wash_batch_no_arr[$party_id][$issue_date],',');
									$batch_nos=implode(",",array_unique(explode(",",$batch_no)));
									//Summapary
									$summary_issue_date_data[$issue_date]+=$prod_qty;
							  ?>
                                <th  width="240" colspan="3" >
                                
                                <b style="float:left" >
                                Date: <? echo $issue_date;?><br>
                                <b title="Batch No:<?=$batch_nos;?>">Prod Qty: <? echo number_format($prod_qty,0);?> Pcs </b>
                                </b>
                                </th>
                                <?
								$tot_prod_qty+=$prod_qty;
								}
								?>
                                <th  width="240" colspan="3">
                                <b style="float:left">
                                Total Days: <? echo $tot_days;?><br>
                                Prod Qty: <? echo number_format($tot_prod_qty,0);?> Pcs
                                </b>
                                </th>
                         </tr>
                         <tr>
                          	   <th width="30">#SL</th>
                                <th width="230">Name of chemicals</th>
                                <?
                                foreach($partyArr as $issue_date=>$dateArr)
								{
							  ?>
                                <th  width="80">Qty.</th>
                                <th  width="80">Rate</th>
                                <th  width="80">Amount</th>
                                <?
								}
								?>
                             	<th  width="80">Qty.</th>
                                <th  width="80">Rate</th>
                                <th  width="80">Amount</th>
                             </tr>
                </thead>
            </table>
            <?
           // die;
			?>
             <div style="max-height:300px; overflow-y:scroll; width:<? echo $width_td+20;?>px" id="scroll_body">
             <table width="<? echo $width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
				<?  
					$i=1;
					$tot_issue_amt_arr=array();$tot_issue_qty_arr=array();
					$total_issue_qty=$total_issue_amt=$total_recv_qty=$total_delivery_qty=$total_revenue=$total_access_shortage_per=$total_access_shortage_val=0;
					foreach($prod_style_wise_arr[$party_id] as $prod_id=>$row)
					{
						 
						$decs=$row['decs'];
						
						//$party_jobAll=;
						
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$p; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$p; ?>">
                          
                            <td  width="30" id="wrd_brk"><? echo $i; ?></td>
                            <td width="230" ><p style="word-break:break-all"><? echo $decs; ?></p></td>
                               <?
							   $tot_issue_amt=0; $tot_issue_qty=0;
                                foreach($partyArr as $issue_date=>$dateArr)
								{
									$issue_qty=$prod_style_wise_qty_arr[$party_id][$prod_id][$issue_date]['qty'];
									$issue_amt=$prod_style_wise_qty_arr[$party_id][$prod_id][$issue_date]['amt'];
							  ?>
                                <td  width="80" align="right" title="<? echo $issue_qty;?>"><? echo number_format($issue_qty,2); ?></td>
                                <td  width="80" align="right"><? echo number_format($issue_amt/$issue_qty,2); ?></td>
                                <td  width="80" align="right" title="<? echo $issue_amt;?>"><? echo number_format($issue_amt,2); ?></td>
                                <?
								$tot_issue_amt+=$issue_amt;
								$tot_issue_qty+=$issue_qty;
								
								$tot_issue_amt_arr[$party_id][$issue_date]+=$issue_amt;
								$tot_issue_qty_arr[$party_id][$issue_date]+=$issue_qty;
								
								$summary_issue_qty_arr[$issue_date][$decs]['qty']+=$prod_style_wise_qty_arr[$party_id][$prod_id][$issue_date]['qty'];
								$summary_issue_qty_arr[$issue_date][$decs]['amt']+=$prod_style_wise_qty_arr[$party_id][$prod_id][$issue_date]['amt'];
								$summary_issue_dtls_arr[$decs]+=$issue_qty;
								}
								?>
                             	<td  width="80" align="right" title="<? echo $tot_issue_qty;?>"><? echo number_format($tot_issue_qty,2); ?></td>
                                <td  width="80" align="right"><? echo number_format($tot_issue_amt/$tot_issue_qty,2); ?></td>
                                <td  width="80" align="right" title="<? echo $tot_issue_amt;?>"><? echo number_format($tot_issue_amt,2); ?></td>
                             
						  </tr>
						<?	
						$total_issue_qty+=$issue_qty;	
						$total_issue_amt+=$issue_amt;
						 
					$i++;
					}
                  ?>
                </table>
         	</div>
            <?
            
			?>
         	<table width="<? echo $width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="30" >&nbsp;</td>
                <td width="230" >&nbsp;</td>
				<?
				$total_issue_qty=$total_issue_amt=0;
                foreach($partyArr as $issue_date=>$dateArr)
                {
					$tot_issue_qty=$tot_issue_qty_arr[$party_id][$issue_date];
					$tot_issue_amt=$tot_issue_amt_arr[$party_id][$issue_date];
                ?>
                <td  width="80" align="right"><? echo number_format($tot_issue_qty,2); ?></td>
                <td  width="80" align="right"><? //echo number_format($tot_issue_amt/$tot_issue_qty),2; ?></td>
                <td  width="80" align="right"><? echo number_format($tot_issue_amt,2); ?></td>
                <?
				$total_issue_qty+=$tot_issue_qty;
				$total_issue_amt+=$tot_issue_amt;
                }
                ?>
                <td  width="80" align="right"><? echo number_format($total_issue_qty,2); ?></td>
                <td  width="80" align="right"><? //echo number_format($total_issue_amt/$tot_issue_qty,2); ?></td>
                <td  width="80" align="right"><? echo number_format($total_issue_amt,2); ?></td>
                
			</tr>
		</table> 
        <?
						$garnd_total_order_qty+=$total_order_qty;
						$garnd_total_order_amount+=$total_order_amount;
						$garnd_total_recv_qty+=$total_recv_qty;
						 
				$p++;
			}
			
			$summ_width_td=500+(240*count($summary_issue_date_data));
		?>
        <br>
          <table width="<? echo $summ_width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table">
          <caption> <b style="float:left"> Total Style and Color Cost :</b> </caption>
            	 
                <thead>
                         <tr>
                         	<th width="30">&nbsp;</th>
                              <th width="230">&nbsp;</th>
							<?
                                $summ_tot_days=$tot_prod_qty=0;
								foreach($summary_issue_date_data as $issue_date=>$prod_qty)
								{
									$summ_tot_days++;
									
							  ?>
                                <th  width="240" colspan="3">
                                <b style="float:left">
                                Date: <? echo $issue_date;?><br>
                                Prod Qty: <? echo number_format($prod_qty,0);?> Pcs
                                </b>
                                </th>
                                <?
								$tot_prod_qty+=$prod_qty;
								}
								?>
                                <th  width="240" colspan="3">
                                <b style="float:left">
                                Total Days: <? echo $summ_tot_days;?><br>
                                Prod Qty: <? echo number_format($tot_prod_qty,0);?> Pcs
                                </b>
                                </th>
                         </tr>
                         <tr>
                          	   <th width="20">#SL</th>
                                <th width="230">Name of chemicals</th>
                                <?
                                foreach($summary_issue_date_data as $issue_date=>$dateArr)
								{
							  ?>
                                <th  width="80">Qty.</th>
                                <th  width="80">Rate</th>
                                <th  width="80">Amount</th>
                                <?
								}
								?>
                             	<th  width="80">Qty.</th>
                                <th  width="80">Rate</th>
                                <th  width="80">Amount</th>
                             </tr>
                </thead>
            </table>
            <?
           // die;
			?>
             <div style="max-height:300px; overflow-y:scroll; width:<? echo $summ_width_td+20;?>px" id="scroll_body">
             <table width="<? echo $summ_width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body<? //echo $p;?>">
				<?  
					$i=1;
					$tot_issue_amt_arr=array();$tot_issue_qty_arr=array();
					$total_issue_qty=$total_issue_amt=$total_recv_qty=$total_delivery_qty=$total_revenue=$total_access_shortage_per=$total_access_shortage_val=0;
					foreach($summary_issue_dtls_arr as $prod_id=>$row)
					{
						 
						$decs=$row['decs'];
						
						//$party_jobAll=;
						
						?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$p; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$p; ?>">
                          
                            <td  width="30" id="wrd_brk"><? echo $i; ?></td>
                            <td width="230" ><p style="word-break:break-all"><? echo $prod_id; ?></p></td>
                               <?
							   $tot_issue_amt=0; $tot_issue_qty=0;
                                foreach($summary_issue_date_data as $issue_date=>$dateArr)
								{
									$issue_qty=$summary_issue_qty_arr[$issue_date][$prod_id]['qty'];
									$issue_amt=$summary_issue_qty_arr[$issue_date][$prod_id]['amt'];
							  ?>
                                <td  width="80" align="right"  title="<? echo $issue_qty;?>"><? echo number_format($issue_qty,2); ?></td>
                                <td  width="80" align="right"><? echo number_format($issue_amt/$issue_qty,2); ?></td>
                                <td  width="80" align="right"  title="<? echo $issue_amt;?>"><? echo number_format($issue_amt,2); ?></td>
                                <?
								$tot_issue_amt+=$issue_amt;
								$tot_issue_qty+=$issue_qty;
								
								$tot_issue_amt_arr[$issue_date]+=$issue_amt;
								$tot_issue_qty_arr[$issue_date]+=$issue_qty;
								}
								?>
                             	<td  width="80" align="right" title="<? echo $tot_issue_qty;?>"><? echo number_format($tot_issue_qty,2); ?></td>
                                <td  width="80" align="right"><? echo number_format($tot_issue_amt/$tot_issue_qty,2); ?></td>
                                <td  width="80" align="right" itle="<? echo $tot_issue_amt;?>"><? echo number_format($tot_issue_amt,2); ?></td>
                             
						  </tr>
						<?	
						$total_issue_qty+=$issue_qty;	
						$total_issue_amt+=$issue_amt;
						 
					$i++;
					}
                  ?>
                </table>
         	</div>
            <?
            
			?>
         	<table width="<? echo $summ_width_td;?>" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="30" >&nbsp;</td>
                <td width="230" >&nbsp;</td>
				<?
				$summ_tot_issue_qty=$summ_tot_issue_amt=0;
                foreach($summary_issue_date_data as $issue_date=>$dateArr)
                {
					$tot_issue_qty=$tot_issue_qty_arr[$issue_date];
					$tot_issue_amt=$tot_issue_amt_arr[$issue_date];
                ?>
                <td  width="80" align="right"><? echo number_format($tot_issue_qty,2); ?></td>
                <td  width="80" align="right"><? //echo number_format($tot_issue_amt/$tot_issue_qty),2; ?></td>
                <td  width="80" align="right"><? echo number_format($tot_issue_amt,2); ?></td>
                <?
				$summ_tot_issue_qty+=$tot_issue_qty;
				$summ_tot_issue_amt+=$tot_issue_amt;
                }
                ?>
                <td  width="80" align="right"><? echo number_format($summ_tot_issue_qty,2); ?></td>
                <td  width="80" align="right"><? //echo number_format($total_issue_amt/$tot_issue_qty,2); ?></td>
                <td  width="80" align="right"><? echo number_format($summ_tot_issue_amt,2); ?></td>
                
			</tr>
		</table> 
        <?
						//$garnd_total_order_qty+=$total_order_qty;
						//$garnd_total_order_amount+=$total_order_amount;
						//$garnd_total_recv_qty+=$total_recv_qty;
						 
				//$p++;
			 
		?>
        
     </div>

     		<?
        		// echo signature_table(109, $cbo_company_name, "850px");
   			 ?>
        </div>
		<?
	}

		$html = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename";
	
    exit();
}

if ($action == 'trims_popup') {
    echo load_html_head_contents("Trims Details info", "../../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);
    //echo $po_break_down_id."*".$tot_po_qnty;die;

    //echo $ratio;die;

    ?>
    <script>

        function window_close() {
            parent.emailwindow.hide();
        }

    </script>
    <fieldset style="width:650px;">
        <legend>Accessories Status pop up</legend>
        <div style="100%" id="report_container">
            <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
                </thead>
                <?
                $item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
                $trims_array = array();
                $trimsDataArr = sql_select("select b.item_group_id,
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
                foreach ($trimsDataArr as $row) {
                    $trims_array[$row[csf('item_group_id')]]['recv'] = $row[csf('recv_qnty')];
                    $trims_array[$row[csf('item_group_id')]]['iss'] = $row[csf('issue_qnty')];
                }


                //$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
                $trimsDataArr = sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
                $i = 1;
                $tot_accss_req_qnty = 0;
                $tot_recv_qnty = 0;
                $tot_iss_qnty = 0;
                $tot_recv_bl_qnty = 0;
                $tot_trims_left_over_qnty = 0;
                foreach ($trimsDataArr as $row) {
                    if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                    $dzn_qnty = '';
                    if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
                    else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
                    else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
                    else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
                    else $dzn_qnty = 1;

                    $dzn_qnty = $dzn_qnty * $ratio;
                    $accss_req_qnty = ($row[csf('cons_dzn_gmts')] / $dzn_qnty) * $tot_po_qnty;

                    $trims_recv = $trims_array[$row[csf('trim_group')]]['recv'];
                    $trims_issue = $trims_array[$row[csf('trim_group')]]['iss'];
                    $recv_bl = $accss_req_qnty - $trims_recv;
                    $trims_left_over = $trims_recv - $trims_issue;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($accss_req_qnty, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_recv, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($recv_bl, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_issue, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_left_over, 2, '.', ''); ?>&nbsp;</td>
                    </tr>
                    <?
                    $tot_accss_req_qnty += $accss_req_qnty;
                    $tot_recv_qnty += $trims_recv;
                    $tot_recv_bl_qnty += $recv_bl;
                    $tot_iss_qnty += $trims_issue;
                    $tot_trims_left_over_qnty += $trims_left_over;
                    $i++;
                }
                $tot_trims_left_over_qnty_perc = ($tot_trims_left_over_qnty / $tot_recv_qnty) * 100;
                ?>
                <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty, 0, '.', ''); ?>&nbsp;</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?

    exit();
}
//Ex-Factory Delv. and Return
if ($action == "ex_factory_popup") {
    echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);
    //echo $id;//$job_no;
    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:500px">
            <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div>
            <br/>

            <div style="width:100%">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                    </tr>
                    </thead>
                </table>
            </div>
            <div style="width:100%; max-height:400px;">
                <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <?
                    $i = 1;

                    $exfac_sql = ("select b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                    $sql_dtls = sql_select($exfac_sql);

                    foreach ($sql_dtls as $row_real) {
                        if ($i % 2 == 0) $bgcolor = "#EFEFEF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                            <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                            <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                            <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        </tr>
                        <?
                        $rec_qnty += $row_real[csf("ex_factory_qnty")];
                        $rec_return_qnty += $row_real[csf("ex_factory_return_qnty")];
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th><? echo number_format($rec_qnty, 2); ?></th>
                        <th><? echo number_format($rec_return_qnty, 2); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3">Total Balance</th>
                        <th colspan="2" align="right"><? echo number_format($rec_qnty - $rec_return_qnty, 2); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <?
    exit();
}
//disconnect($con);
?>
