<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
  
if($action=="print_button_variable_setting")
    {
    	 
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=80 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(40) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_buyer_popup")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}
 
if($action=="job_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;	
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( job_id )
		{
			var arrs=job_id.split("_");
 			document.getElementById('selected_id').value=arrs[0];
 			document.getElementById('selected_name').value=arrs[1]; 
			parent.emailwindow.hide();
		}

		function dynamic_ttl_change(data)
		{
			var titles="";
			if(data==1)
			{
				titles="Job No";
			}
			else if(data==2)
			{
				titles="Style Ref."
			}
			else if(data==3)
			{
				titles="Po No.";
			}
			else
			{
				titles="Job No";
			}
			$("#dynamic_ttl").html(titles);
		}
		
		
		
    </script>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                	 
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="130" class="">Buyer Name</th>
                            <th width="100">Search By</th>
                            <th width="100" id="dynamic_ttl">Job No</th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_id">
                        <input type="hidden" id="selected_name"> 
                            <?
                            $search_by_arr=[1=>"Job No",2=>"Style Ref.",3=>"Po No"];
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
	                        <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", '',"dynamic_ttl_change(this.value);" );
	                        ?>
                        	
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'create_job_list_view', 'search_div', 'daily_rmg_production_status_format2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                    </tr>
                    
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="create_job_list_view")
{
	$data=explode('_',$data);
	if(!$data[0])
	{
		echo "Select Company Name !!";die;
	}
	$str_cond="";
	$str_cond.=($data[0])? " and a.company_name='$data[0]' " : "";
	$str_cond.=($data[1])? " and a.buyer_name='$data[1]' " : "";
	if($data[3])
	{
		if($data[2]==1)
		{
			$str_cond.= " and a.job_no_prefix_num='$data[3]'";

		}
		else if($data[2]==2)
		{
			$str_cond.= " and a.style_ref_no like '%$data[3]%'";

		}
		else if($data[2]==3)
		{
			$str_cond.= " and b.po_number like '%$data[3]%'";

		}
	}
	if($data[4])
	{
	   if($db_type==2)
	   {
	   	 $str_cond.=" and to_char(a.insert_date,'YYYY')='$data[4]'";
	   }
	   else
	   {
	   		$str_cond.=" and year(a.insert_date)='$data[4]'";
	   }
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	 $sql= "SELECT a.id,b.po_number,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name";
	 // echo $sql;die;
	echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,po_number", "",'','0,0,0,0,0') ;
	exit();
} 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);  
	$txt_production_date=str_replace("'","",$txt_production_date);
	  
	
	if($txt_production_date!="")
	{
		if($db_type==0)
		{
			$txt_production_date=change_date_format($txt_production_date,'yyyy-mm-dd');
 		}
		else if($db_type==2)
		{
			$txt_production_date=change_date_format($txt_production_date,'','',-1);
 		}
	}
	 
	 
	if($type==0)
	{
			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";

		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		//if($txt_production_date!="")  $str_po_cond .=" and d.production_date= '$txt_production_date'";  

	    $order_sql="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id, sum(c.order_quantity) as order_quantity,max(b.shiping_status) as shiping_status,

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input ,


		sum(case when d.production_type=5 and e.production_type=5 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_output ,
		sum(case when d.production_type=5 and e.production_type=5   then e.production_qnty else 0 end ) as total_sewing_output ,


		sum(case when d.production_type=11 and e.production_type=11 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_poly ,
		sum(case when d.production_type=11 and e.production_type=11   then e.production_qnty else 0 end ) as total_poly ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_packing ,
		sum(case when d.production_type=8 and e.production_type=8   then e.production_qnty else 0 end ) as total_packing ,


		sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=0 and d.production_date='$txt_production_date' then e.reject_qty else 0 end )- sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_reject_qty ,
		sum(case when d.production_type in( 5) and e.production_type in( 5) and e.is_rescan=0  then e.reject_qty else 0 end )  -sum(case when d.production_type in(5) and e.production_type in( 5) and e.is_rescan=1   then e.production_qnty else 0 end ) as total_sewing_reject_qty 




		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond 
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.client_id, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id";
		
		//echo $order_sql."<br>";
		
		
		$sql_po_result=sql_select($order_sql);
		$production_main_array=array();
		$po_wise_color_production=array();
		foreach($sql_po_result as $row)
		{
			//if($row[csf("today_cutting")] || $row[csf("today_sewing_input")] || $row[csf("today_sewing_output")] ||  $row[csf("today_poly")] ||    $row[csf("today_packing")])
			//{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["client_id"]=$row[csf("client_id")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_cutting"]+=$row[csf("today_cutting")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_cutting"]+=$row[csf("total_cutting")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_input"]+=$row[csf("today_sewing_input")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_input"]+=$row[csf("total_sewing_input")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_output"]+=$row[csf("today_sewing_output")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_output"]+=$row[csf("total_sewing_output")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_poly"]+=$row[csf("today_poly")];


				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_poly"]+=$row[csf("total_poly")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_packing"]+=$row[csf("today_packing")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_packing"]+=$row[csf("total_packing")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];




				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_sewing_reject_qty"]+=$row[csf("total_sewing_reject_qty")];


				$all_po_id.=$row[csf("po_id")].",";

			//}

				if($row[csf("total_cutting")] || $row[csf("total_sewing_input")] || $row[csf("total_sewing_output")] ||  $row[csf("total_poly")] ||    $row[csf("total_packing")])
				{
					$po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("color_number_id")];

				}

			  

			
		}
		$all_po_id=implode(',',array_unique(explode(",",chop($all_po_id,","))));
		 
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst where po_number_id in ('".$all_po_id."') group by po_number_id, template_id","po_number_id","template_id");
		
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

		$po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and a.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(explode(",",$all_po_id),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (a.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or a.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
				
			}
		}

		$po_product_cond2=str_replace("a.po_break_down_id in", "b.id not in", $po_product_cond);

		$order_sql_lay="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id,c.size_number_id, c.order_quantity ,max(b.shiping_status) as shiping_status ,
		sum(case when d.entry_date='$txt_production_date' then f.size_qty else 0 end ) as today_lay,sum(f.size_qty) as total_lay
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id and c.size_number_id=f.size_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay  
		group by a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  , c.country_id, c.country_ship_date, c.color_number_id ,c.size_number_id,c.order_quantity ";
		$lay_new_po=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
			if($po_wise_color_production[$row[csf("po_id")]][$row[csf("color_number_id")]]=="")
			{
				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["shiping_status"]=$row[csf("shiping_status")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				
				if($po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

					$po_col_size_qnty_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];

				}
				

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["today_lay"]+=$row[csf("today_lay")];

				$production_main_array[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("country_ship_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]["total_lay"]+=$row[csf("total_lay")];
				//$lay_new_po[$row[csf("po_id")]]=$row[csf("po_id")];
			}
		}
		/*$lay_new_po_ids=implode(",", $lay_new_po);
		if($lay_new_po_ids)
		{
			$new_cond= " or c.order_id in($lay_new_po_ids) ";
		}
		*/


		$po_product_cond_cut_lay=str_replace("a.po_break_down_id", "c.order_id", $po_product_cond);
		$cut_lay_sql="SELECT a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id  ,sum(c.size_qty) as qntys from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0   $po_product_cond_cut_lay   group by a.entry_date, c.order_id, b.gmt_item_id, b.color_id ,c.country_id"; 
		$cut_lay_array=array();
		foreach(sql_select($cut_lay_sql) as $keys=>$vals)
		{
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]][change_date_format($vals[csf("entry_date")])]["today_lay"]+=$vals[csf("qntys")];
			$cut_lay_array[$vals[csf("order_id")]][$vals[csf("gmt_item_id")]][$vals[csf("country_id")]][$vals[csf("color_id")]]["total_lay"]+=$vals[csf("qntys")];

			 
		}



		$order_qnty_pcs_arr=array();
		$order_qnty_pcs_sql="SELECT a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date,   sum(a.order_quantity) as qntys from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.is_deleted=0 $po_product_cond  group by  a.po_break_down_id,a.job_no_mst,a.color_number_id,a.item_number_id  , a.country_id, a.country_ship_date";
		foreach(sql_select($order_qnty_pcs_sql) as $pcs_key=>$pcs_val)
		{
			$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date")])][$pcs_val[csf("color_number_id")]]+=$pcs_val[csf("qntys")];
		}
		
		
		if($po_product_cond=="")
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		}
		
		
		$color_id_arr=return_library_array( "select a.id,a.color_number_id from wo_po_color_size_breakdown a where a.status_active in(1,2,3) and a.color_number_id>0 $po_product_cond", "id", "color_number_id");
		
		
		 
		 
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id ,sum(CASE WHEN a.entry_form <> 85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END)-
		sum(CASE WHEN a.entry_form=85 and a.ex_factory_date='$txt_production_date' THEN b.production_qnty ELSE 0 END) as today_ex_factory_qnty,
		sum(CASE WHEN a.entry_form <> 85   THEN b.production_qnty ELSE 0 END) 
		-
		sum(CASE WHEN a.entry_form=85   THEN b.production_qnty ELSE 0 END) as total_ex_factory_qnty ,a.total_carton_qnty
		 from pro_ex_factory_mst a, pro_ex_factory_dtls b 
		 where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $po_product_cond 
		 group by a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,a.total_carton_qnty");
		
		foreach($ex_factory_data as $exRow)
		{
			 
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['today_ex_factory_qnty']+=$exRow[csf('today_ex_factory_qnty')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$color_id_arr[$exRow[csf('color_size_break_down_id')]]]['total_ex_factory_qnty']+=$exRow[csf('total_ex_factory_qnty')];		 

		}
		
		ob_start();	
		
		?>
		 
        <div>
        	<table width="2810" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold" >
        				<?
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:2810px">Details Part</div>
            
			<div style="float:left; width:1930px">
				<table width="2810" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;" rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Client</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Style Ref.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Job No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Order No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Country</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Country Shipdate</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="120"><p>Garment Item</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Color</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order Qty.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2"   width="160"><p>Cut & Lay</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Cutting Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Input</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Sewing Output</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Today Sewing Reject</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="100"><p>Total Sewing Reject</p></th>
						 
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Sewing WIP</p></th> 
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Poly Entry</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Poly WIP</p></th> 
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p>Packing &Fin.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Pac &Fin. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="2" width="160"><p> Ex-Factory</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Ex-Fac. WIP</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Remarks</p></th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>

					</tr>
						
						  
					   
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:2830px" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="2810" rules="all" id="table_body" >
					<?
					$po_wise_ship_span_arr=array();
					// echo "<pre>";
					// print_r($production_main_array);die();
					foreach($production_main_array as $buyer_id=>$job_data)
					{
						foreach($job_data as $job_id=>$po_data)
						{
							foreach($po_data as $po_id=>$country_data)
							{
								$po_wise_ship_span=0;
								foreach($country_data as $country_id=>$shipdate_data)
								{
									foreach($shipdate_data as $shipdate_id=>$item_data)
									{
										foreach($item_data as $item_id=>$color_data)
										{
											foreach($color_data as $color_id=>$rows)
											{
												$po_wise_ship_span++;
											}
										}
									}
								}
								$po_wise_ship_span_arr[$po_id]=$po_wise_ship_span;

							}

							
						}

					}
					 
						$k=1;
						$gr_wise_order_qnty=0;
						$gr_wise_lay_qnty_today=0;
						$gr_wise_lay_qnty_total=0;
						$gr_wise_today_cutting=0;
						$gr_wise_total_cutting=0;
						$gr_wise_today_sewing_input=0;
						$gr_wise_total_sewing_input=0;
						$gr_wise_today_sewing_output=0;
						$gr_wise_total_sewing_output=0;
						$gr_wise_today_sewing_rej=0;
						$gr_wise_total_sewing_rej=0;
						$gr_wise_today_poly=0;
						$gr_wise_total_poly=0;
						$gr_wise_today_pac=0;
						$gr_wise_total_pac=0;
						$gr_wise_today_ex_fac=0;
						$gr_wise_total_ex_fac=0;
						$gr_wise_sewing_wip=0;
						$gr_wise_poly_wip=0;
						$gr_wise_pack_wip=0;
						$gr_wise_exfac_wip=0;
						 
						foreach($production_main_array as $buyer_id=>$job_data)
						{
							$buyer_wise_order_qnty=0;
							$buyer_wise_lay_qnty_today=0;
							$buyer_wise_lay_qnty_total=0;
							$buyer_wise_today_cutting=0;
							$buyer_wise_total_cutting=0;
							$buyer_wise_today_sewing_input=0;
							$buyer_wise_total_sewing_input=0;
							$buyer_wise_today_sewing_output=0;
							$buyer_wise_total_sewing_output=0;
							$buyer_wise_today_sewing_rej=0;
							$buyer_wise_total_sewing_rej=0;
							$buyer_wise_today_poly=0;
							$buyer_wise_total_poly=0;
							$buyer_wise_today_pac=0;
							$buyer_wise_total_pac=0;
							$buyer_wise_today_ex_fac=0;
							$buyer_wise_total_ex_fac=0;
							$buyer_wise_sewing_wip=0;
							$buyer_wise_poly_wip=0;
							$buyer_wise_pack_wip=0;
							$buyer_wise_exfac_wip=0;

							foreach($job_data as $job_id=>$po_data)
							{
								$job_wise_order_qnty=0;
								$job_wise_lay_qnty_today=0;
								$job_wise_lay_qnty_total=0;
								$job_wise_today_cutting=0;
								$job_wise_total_cutting=0;
								$job_wise_today_sewing_input=0;
								$job_wise_total_sewing_input=0;
								$job_wise_today_sewing_output=0;
								$job_wise_total_sewing_output=0;
								$job_wise_today_sewing_rej=0;
								$job_wise_total_sewing_rej=0;
								$job_wise_today_poly=0;
								$job_wise_total_poly=0;
								$job_wise_today_pac=0;
								$job_wise_total_pac=0;
								$job_wise_today_ex_fac=0;
								$job_wise_total_ex_fac=0;
								$job_wise_sewing_wip=0;
								$job_wise_poly_wip=0;
								$job_wise_pack_wip=0;
								$job_wise_exfac_wip=0;

								foreach($po_data as $po_id=>$country_data)
								{
									$kk=0;
									$po_wise_order_qnty=0;
									$po_wise_lay_qnty_today=0;
									$po_wise_lay_qnty_total=0;
									$po_wise_today_cutting=0;
									$po_wise_total_cutting=0;
									$po_wise_today_sewing_input=0;
									$po_wise_total_sewing_input=0;
									$po_wise_today_sewing_output=0;
									$po_wise_total_sewing_output=0;
									$po_wise_today_sewing_rej=0;
									$po_wise_total_sewing_rej=0;
 									$po_wise_today_poly=0;
									$po_wise_total_poly=0;
									$po_wise_today_pac=0;
									$po_wise_total_pac=0;
									$po_wise_today_ex_fac=0;
									$po_wise_total_ex_fac=0;
									$po_wise_sewing_wip=0;
									$po_wise_poly_wip=0;
									$po_wise_pack_wip=0;
									$po_wise_exfac_wip=0;
									 
									foreach($country_data as $country_id=>$shipdate_data)
									{
										foreach($shipdate_data as $shipdate_id=>$item_data)
										{
											foreach($item_data as $item_id=>$color_data)
											{
												foreach($color_data as $color_id=>$rows)
												{
													// echo "<pre>";
													// print_r($rows);
													$today_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['today_ex_factory_qnty'];
													$total_ex_fac=$ex_factory_arr[$po_id][$item_id][$country_id][$color_id]['total_ex_factory_qnty'];
													//$order_qnty_pcs_arr[$pcs_val[csf("job_no_mst")]][$pcs_val[csf("po_break_down_id")]][$pcs_val[csf("item_number_id")]][$pcs_val[csf("country_id")]][change_date_format($pcs_val[csf("country_ship_date"))]][$pcs_val[csf("color_number_id")]]
													$order_qntys=$order_qnty_pcs_arr[$job_id][$po_id][$item_id][$country_id][change_date_format($shipdate_id)][$color_id];

													$cut_lays_qnty_today=$cut_lay_array[$po_id][$item_id][$country_id][$color_id][change_date_format(str_replace("'", "", $txt_production_date))]["today_lay"];
													

													$cut_lays_qnty_total=$cut_lay_array[$po_id][$item_id][$country_id][$color_id]["total_lay"];

													if(!$cut_lays_qnty_today)
													{
														$cut_lays_qnty_today=$rows["today_lay"];
 													}
													if(!$cut_lays_qnty_total)
													{
														$cut_lays_qnty_total=$rows["total_lay"];
													}
													if(!$order_qntys)
													{
														$order_qntys=$rows["order_quantity"];
													}
													$sewing_wip= ($rows["total_sewing_output"]+$rows["total_sewing_reject_qty"])-$rows["total_sewing_input"];

													$po_wise_order_qnty+=$order_qntys;
													$po_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$po_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$po_wise_today_cutting+=$rows["today_cutting"];
													$po_wise_total_cutting+=$rows["total_cutting"];
													$po_wise_today_sewing_input+=$rows["today_sewing_input"];
													$po_wise_total_sewing_input+=$rows["total_sewing_input"];
													$po_wise_today_sewing_output+=$rows["today_sewing_output"];
													$po_wise_total_sewing_output+=$rows["total_sewing_output"];
													$po_wise_today_poly+=$rows["today_poly"];
													$po_wise_total_poly+=$rows["total_poly"];
													$po_wise_today_pac+=$rows["today_packing"];
													$po_wise_total_pac+=$rows["total_packing"];
													$po_wise_today_ex_fac+=$today_ex_fac;
													$po_wise_total_ex_fac+=$total_ex_fac;
													$po_wise_sewing_wip+=$sewing_wip;
													$po_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$po_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$po_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$po_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$po_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];


													$job_wise_order_qnty+=$order_qntys;
													$job_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$job_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$job_wise_today_cutting+=$rows["today_cutting"];
													$job_wise_total_cutting+=$rows["total_cutting"];
													$job_wise_today_sewing_input+=$rows["today_sewing_input"];
													$job_wise_total_sewing_input+=$rows["total_sewing_input"];
													$job_wise_today_sewing_output+=$rows["today_sewing_output"];
													$job_wise_total_sewing_output+=$rows["total_sewing_output"];
													$job_wise_today_poly+=$rows["today_poly"];
													$job_wise_total_poly+=$rows["total_poly"];
													$job_wise_today_pac+=$rows["today_packing"];
													$job_wise_total_pac+=$rows["total_packing"];
													$job_wise_today_ex_fac+=$today_ex_fac;
													$job_wise_total_ex_fac+=$total_ex_fac;
													$job_wise_sewing_wip+=$sewing_wip;
													$job_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$job_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$job_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$job_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$job_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];



													$buyer_wise_order_qnty+=$order_qntys; 
													$buyer_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$buyer_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$buyer_wise_today_cutting+=$rows["today_cutting"];
													$buyer_wise_total_cutting+=$rows["total_cutting"];
													$buyer_wise_today_sewing_input+=$rows["today_sewing_input"];
													$buyer_wise_total_sewing_input+=$rows["total_sewing_input"];
													$buyer_wise_today_sewing_output+=$rows["today_sewing_output"];
													$buyer_wise_total_sewing_output+=$rows["total_sewing_output"];
													$buyer_wise_today_poly+=$rows["today_poly"];
													$buyer_wise_total_poly+=$rows["total_poly"];
													$buyer_wise_today_pac+=$rows["today_packing"];
													$buyer_wise_total_pac+=$rows["total_packing"];
													$buyer_wise_today_ex_fac+=$today_ex_fac;
													$buyer_wise_total_ex_fac+=$total_ex_fac;
													$buyer_wise_sewing_wip+=$sewing_wip;
													$buyer_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$buyer_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$buyer_wise_exfac_wip+=$order_qntys-$total_ex_fac;
													$buyer_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$buyer_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];



													$gr_wise_order_qnty+=$order_qntys; 
													$gr_wise_lay_qnty_today+=$cut_lays_qnty_today;
													$gr_wise_lay_qnty_total+=$cut_lays_qnty_total;
													$gr_wise_today_cutting+=$rows["today_cutting"];
													$gr_wise_total_cutting+=$rows["total_cutting"];
													$gr_wise_today_sewing_input+=$rows["today_sewing_input"];
													$gr_wise_total_sewing_input+=$rows["total_sewing_input"];
													$gr_wise_today_sewing_output+=$rows["today_sewing_output"];
													$gr_wise_total_sewing_output+=$rows["total_sewing_output"];
													$gr_wise_today_poly+=$rows["today_poly"];
													$gr_wise_total_poly+=$rows["total_poly"];
													$gr_wise_today_pac+=$rows["today_packing"];
													$gr_wise_total_pac+=$rows["total_packing"];
													$gr_wise_today_ex_fac+=$today_ex_fac;
													$gr_wise_total_ex_fac+=$total_ex_fac;
													$gr_wise_sewing_wip+=$sewing_wip;
													$gr_wise_poly_wip+=$rows["total_poly"]-$rows["total_sewing_output"];
													$gr_wise_pack_wip+=$rows["total_packing"]-$rows["total_poly"];
													$gr_wise_exfac_wip+=$total_ex_fac-$order_qntys;
													$gr_wise_today_sewing_rej+=$rows["today_sewing_reject_qty"];
													$gr_wise_total_sewing_rej+=$rows["total_sewing_reject_qty"];





													if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $k; ?>">
													 
														<td style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $client_array[$rows["client_id"]]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $rows["style_ref_no"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $rows["job_no_prefix_num"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $rows["po_number"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80"><p><? echo $country_library[$country_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo change_date_format($shipdate_id); ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="120"><p><? echo $garments_item[$item_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id]; ?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $order_qntys;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cut_lays_qnty_today;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cut_lays_qnty_total;?></p></td>


														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["today_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["total_cutting"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["today_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["total_sewing_input"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["today_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["total_sewing_output"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p><? echo $rows["today_sewing_reject_qty"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="100"><p><? echo $rows["total_sewing_reject_qty"];?></p></td>
 														<td style="word-wrap: break-word;word-break: break-all;"    align="center"  width="80"><p><? echo $sewing_wip ;?></p></td> 
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["today_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"    align="center"  width="80"><p><? echo $poly_wip=$rows["total_poly"]- $rows["total_sewing_output"];?></p></td> 
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $rows["today_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="center"   width="80"><p><? echo $rows["total_packing"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo  $packing_wip=  $rows["total_packing"]- $rows["total_poly"];?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="center"   width="80"><p><? echo $today_ex_fac;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"  width="80"><p><? echo $total_ex_fac ;?></p></td>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $ex_fac_wip= $total_ex_fac-$order_qntys;?></p></td>
														 
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo  $shipment_status[$rows['shiping_status']];?></p></td>


															 
														
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p>
														 
															
															<a href="##" onclick="openmypage_remarks(<? echo $po_id;?>,<? echo $item_id;?>,<? echo $country_id;?>,<? echo $color_id;?>, 'remarks_popup');" >Remarks</a>

														</p></td>

								 
													</tr>	


													<?
													$kk++;

													$k++;

												}
											}
										}
									}

									

									?>
									<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Po Total</strong></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $po_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $po_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
									</tr> -->


									<?
								}

								?>
								<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Job Total</strong> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $job_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $job_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
								</tr> -->


								<?

							}


							?>
									<!-- <tr bgcolor="#E4E4E4">
										<td style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="center"><p><strong>Buyer Total</strong></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_order_qnty;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_lay_qnty_today;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p> <? echo $buyer_wise_lay_qnty_total;?></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_cutting; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_input; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_output; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_sewing_rej; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_sewing_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_poly; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_poly_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_pac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_pack_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_today_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_total_ex_fac; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p><? echo $buyer_wise_exfac_wip; ?></p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
									</tr> -->


									<?

							

							
							
							 		
								 
							 
						} 
						?>
						 

						
						
						 
					 
					</table>
					<table border="1" class="tbl_bottom" width="2810" rules="all" id="report_table_footer_1" >
						<tr >
							 
							<td style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80">&nbsp;</td>
							<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="120"><strong>Grand Total</strong></td>
							<td align="center" style="word-wrap: break-word;word-break: break-all;border-left: none;"   align="center"   width="80">&nbsp;</p></td>

							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  align="center" id="grand_total_order"><p> </p> <? //echo $gr_wise_order_qnty;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_lay"  align="center"><p> </p> <? //echo $gr_wise_lay_qnty_today;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_lay"  align="center"><p> </p> <? //echo $gr_wise_lay_qnty_total;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_today_cut" align="center"><p><? //echo $gr_wise_today_cutting; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_total_cut" align="center"><p><? //echo $gr_wise_total_cutting; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_today_sewin" align="center"><p><? //echo $gr_wise_today_sewing_input; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;"  id="grand_total_sewin" align="center"><p><? //echo $gr_wise_total_sewing_input; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_sewout" align="center"><p><? //echo $gr_wise_today_sewing_output; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_sewout" align="center"><p><? //echo $gr_wise_total_sewing_output; ?></p></td>
							<td  width="100" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_sew_rej" align="center"><p><? //echo $gr_wise_today_sewing_rej; ?></p></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_sewrej" align="center"><p><? echo $gr_wise_total_sewing_rej; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_sewingwip" align="center"><p><? //echo $gr_wise_sewing_wip; ?></p></td>


							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_poly" align="center"><p><? //echo $gr_wise_today_poly; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_poly" align="center"><p><? //echo $gr_wise_total_poly; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_poly_wip" align="center"><p><? //echo $gr_wise_poly_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_pack" align="center"><p><? //echo $gr_wise_today_pac; ?></p></td>
							<td  width="80" style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_pack" align="center"><p><? //echo $gr_wise_total_pac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_pack_wip" align="center"><p><?// echo $gr_wise_pack_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_today_exfac" align="center"><p><?// echo $gr_wise_today_ex_fac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_total_exfac" align="center"><p><?// echo $gr_wise_total_ex_fac; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;text-align: center;" id="grand_exfac_wip" align="center"><p><? //echo $gr_wise_exfac_wip; ?></p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"  align="center"><p> </p></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"  align="center"><p></p></td>
						</tr>
					</table>
					
					  
				</div>
			</div>
			 
			 
			 
			 
		 </div> 
        <?
	}
	else
	{
			
		$str_po_cond="";
		if($cbo_company_name!=0) $str_po_cond.=" and d.serving_company in($cbo_company_name)";

		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_location>0) $str_po_cond.=" and d.location=$cbo_location";
		if($cbo_floor>0) $str_po_cond.=" and d.floor_id=$cbo_floor";
		// if($txt_production_date != "") $str_po_cond.=" and d.production_date=$txt_production_date";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($hidden_job_id!="")
		{
			$str_po_cond.=" and a.id in($hidden_job_id)";
		}
		$str_po_cond_lay=str_replace("location", "location_id", $str_po_cond);
		$str_po_cond_lay=str_replace("serving_company", "working_company_id", $str_po_cond_lay);

		


		echo $order_sql="SELECT d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id,e.cut_no  ,   c.color_number_id, sum(c.order_quantity) as order_quantity, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_cutting ,
		sum(case when d.production_type=1 and e.production_type=1  then e.production_qnty else 0 end ) as total_cutting ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date='$txt_production_date' then e.production_qnty else 0 end ) as today_sewing_input ,
		sum(case when d.production_type=4 and e.production_type=4   then e.production_qnty else 0 end ) as total_sewing_input  



		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and b.shiping_status <> 3 and e.is_deleted=0  $str_po_cond 
		group by d.serving_company,d.location,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id , b.po_number,  c.item_number_id  ,  c.color_number_id,e.cut_no ";

		 

		foreach(sql_select($order_sql) as $vals)
		{
			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_cutting"]+=$vals[csf("today_cutting")];

			$today_total += $cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_cutting"];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_cutting"]+=$vals[csf("total_cutting")];

			// $total_cutting += $cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_cutting"];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["today_sewing_input"]+=$vals[csf("today_sewing_input")];

			$cutting_sewing_data[$vals[csf("serving_company")]][$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("po_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]]["total_sewing_input"]+=$vals[csf("total_sewing_input")];

		}


		 
		$order_sql_lay="SELECT d.working_company_id,d.location_id,d.cutting_no,e.order_cut_no,a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number,  c.item_number_id , c.color_number_id, c.order_quantity, e.batch_id
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e,ppl_cut_lay_bundle f
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=d.job_no and d.id=e.mst_id and d.id=f.mst_id and b.id=f.order_id and c.color_number_id=e.color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1   and e.is_deleted=0 and f.status_active=1  and f.is_deleted=0 $str_po_cond_lay  ";
		$production_main_array=array();
		$all_po_lay_id_array=array();
		$po_col_size_qnty_array=array();
		foreach(sql_select($order_sql_lay) as $row )
		{
				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["po_number"]=$row[csf("po_number")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["working_company_id"]=$row[csf("working_company_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["location_id"]=$row[csf("location_id")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_cut_no"]=$row[csf("order_cut_no")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["buyer_name"]=$row[csf("buyer_name")];

				$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["batch_id"]=$row[csf("batch_id")];

				 

				if($production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_quantity"]=="")
				{
					$production_main_array[$row[csf("style_ref_no")]][$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("cutting_no")]]["order_quantity"]+=$row[csf("order_quantity")];

				}	

				$all_po_lay_id_array[$row[csf("po_id")]]=	$row[csf("po_id")];					
			
		}
		$all_po_lay_id=implode(",", $all_po_lay_id_array);
		if( count($all_po_lay_id)>0 )
		{
			$po_conds=" and a.po_break_down_id in($all_po_lay_id)";
		}

		$booking_no_fin_qnty_array=array();
		$booking_sql="SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0   $po_conds";
		foreach(sql_select($booking_sql) as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["booking_no"]=$vals[csf("booking_no")];
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("color_number_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

		}
		$batch_sql=sql_select("SELECT id,booking_no,batch_no,color_id from pro_batch_create_mst where status_active=1 and is_deleted=0 ");
		foreach($batch_sql as $rows)
		{
			$batch_mst_arr[$rows[csf("booking_no")]][$rows[csf("color_id")]]=$rows[csf("batch_no")];
			$batch_mst_id_arr[$rows[csf("booking_no")]]=$rows[csf("id")];
		}
		/*echo "<pre>";
		 print_r($batch_mst_id_arr);die;*/
		 
		 $issue_sql=sql_select("SELECT batch_id,issue_qnty from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 ");
		 foreach($issue_sql as $values)
		 {
		 	$issue_qnty_arr[$values[csf("batch_id")]]+=$values[csf("issue_qnty")];
		 }



		foreach($production_main_array as $style_id=>$job_data)
		{
			foreach($job_data as $job_id=>$po_data)
			{
				foreach($po_data as $po_id=>$item_data)
				{
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$cutting_data)
						{
							$color_span=0;
							$cut=0;
							$sew=0;
							foreach($cutting_data as $cutting_id=>$row)
							{
								$cut+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_cutting"];;
 								$sew+=$cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_sewing_input"];
								$color_span++;
							}
							$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
							$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"]=$cut;
							$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"]=$sew;
 
						}

					}
				}

			}

		}

		$result_consumtion=array();
		$sql_consumtiont_qty=sql_select(" SELECT a.job_no, a.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs from wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id=b.pre_cost_fabric_cost_dtls_id   GROUP BY a.job_no, a.body_part_id");

		foreach($sql_consumtiont_qty as $row_consum)
		{

			$result_consumtion[$row_consum[csf('job_no')]]+=$row_consum[csf("requirment")]/$row_consum[csf("pcs")];
		}
		unset($sql_consumtiont_qty); 


		 
		
		if(count($production_main_array)==0)
		{
			echo '<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:1930px">No Data Found.</div>'; die;
		} 
		ob_start();	
		
		?>
		 
        <div>
        	<table width="2010" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Daily RMG Production Report
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="24" align="center" style="border:none;font-size:12px; font-weight:bold" >
        				<?
        				if(str_replace("'","",trim($txt_production_date))!="")
        				{
        					echo "Date ".change_date_format($txt_production_date)  ;
        				}
        				?>
        			</td>
        		</tr>
        	</table>
			<div style="float:left;">
				<table width="2010" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="30" ><p>SL</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Working Company</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Location</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Buyer Name</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="115"><p>Job No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Style Reff</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Order Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>F.Booking No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  rowspan="2" width="80"><p>Batch No</p></th>	

						<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Fabric Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="5" width="400"><p>Cutting Status</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  colspan="4" width="320"><p>Input Status</p></th>
					</tr>
					<tr>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Color Name</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Fab. Req.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Fin. Fab. Issued</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>F.Issued Balance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Possible Cut Qty</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>System Cut No.</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order Cut No</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Cut. Blance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Today</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Total</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Order - Input Blance</p></th>
						<th style="word-wrap: break-word;word-break: break-all;"  width="80"><p>Inhand Qty</p></th>

					</tr>
						
						  
					   
					</thead>
				</table>
				<div style="max-height:500px; overflow-y:scroll; width:2030px" id="scroll_body">
					<table cellspacing="0" cellpadding="2" border="1" class="rpt_table"  width="2010" rules="all" id="table_body" >
					<?
					$k=1;
					$jj=1;		
					
					foreach($production_main_array as $style_id=>$job_data)
					{				
						$style_wise_order_qty = 0;		
						$style_wise_fab_req = 0;		
						$style_wise_fab_issued = 0;		
						$style_wise_fab_issued_balance = 0;	

						$style_wise_fab_posible_cut_qty = 0;		
						$style_wise_cut_today = 0;		
						$style_wise_cut_total = 0;		
						$style_wise_cut_balance = 0;

						$style_wise_input_today = 0;		
						$style_wise_input_total = 0;		
						$style_wise_input_balance = 0;		
						$style_wise_inhand_qty = 0;	

						foreach($job_data as $job_id=>$po_data)
						{
							
							foreach($po_data as $po_id=>$item_data)
							{
								$order_wise_subtotal = 0;
								$order_wise_today_cutting=0;
								$order_wise_total_cutting=0;
								$order_wise_cut_balance=0;

								$order_wise_today_input=0;
								$order_wise_total_input=0;
								$order_wise_input_balance=0;
								$order_wise_inhand_qty=0;
								// fabric status sum
								$order_wise_fab_req = 0;
								$order_wise_fin_fab_req = 0;
								$order_wise_fab_issued_balance = 0;
								$order_wise_fab_possible_qty = 0;
								//

								foreach($item_data as $item_id=>$color_data)
								{
									foreach($color_data as $color_id=>$cutting_data)
									{
										$color_wise_today_cutting=0;
										$color_wise_total_cutting=0;
										$color_wise_today_sewing_input =0;
										$color_wise_total_sewing_input=0;
										$pp=0;
										$fin_req = 0;
										foreach($cutting_data as $cutting_id=>$row)
										{
											$fin_req=$booking_no_fin_qnty_array[$po_id][$color_id]["qnty"];
											$issue_qty=$issue_qnty_arr[$batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]]];
											$req_issue_bal=$fin_req-$issue_qty;
											$possible_cut_pcs=$issue_qty/$result_consumtion[$job_id];

											$today_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_cutting"];
											$total_cutting_qnty= $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_cutting"];
											$color_wise_today_cutting+=$today_cutting_qnty;
											$color_wise_total_cutting+=$total_cutting_qnty;

											$today_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
											$total_sewing_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["total_sewing_input"];
											$color_wise_today_sewing_input += $today_sewing_input;
											$color_wise_total_sewing_input += $total_sewing_input;

											// order wise

											$order_wise_today_cutting+=$today_cutting_qnty;
											$order_wise_total_cutting+=$total_cutting_qnty;
											$order_wise_cut_balance += $cut_balance;

											$order_wise_today_input += $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];
											$order_wise_total_input += $total_sewing_input;
											$order_wise_input_balance += $input_balance;
											$order_wise_inhand_qty += $total_cutting_qnty-$total_sewing_input;

											// style wise
											

											$style_wise_cut_today += $today_cutting_qnty;		
											$style_wise_cut_total += $total_cutting_qnty;		
											$style_wise_cut_balance += $cut_balance;

											$style_wise_input_today += $today_input;		
											$style_wise_input_total += $total_sewing_input;		
											$style_wise_input_balance += $input_balance;		
											$style_wise_inhand_qty += $order_wise_inhand_qty;
											
										 
											if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $jj; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $jj; ?>">
													<?
													$jj++;
													if($pp==0)
													{
														$order_wise_subtotal += $row["order_quantity"];
														$style_wise_order_qty += $row["order_quantity"];

														?>
													 
														<td valign="middle" rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"  width="30"><p><? echo $k;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $company_library[$row["working_company_id"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $location_library[$row["location_id"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $buyer_library[$row["buyer_name"]]; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="115"><p><? echo $job_id; ?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $style_id;?></p></td>
														 
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $po_id;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  align="center"    width="80"><p><? echo $row["order_quantity"]; ?></p></td> 
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"] ;?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $batch_mst_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]][$color_id];?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $color_Arr_library[$color_id];?></p></td>


														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $fin_req;?></p></td>

														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_fab_issue(<? echo $batch_mst_id_arr[$booking_no_fin_qnty_array[$po_id][$color_id]["booking_no"]];?>, 'fab_issue_popup');" > <p><? echo $issue_qty;?></p></a></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $req_issue_bal;?></p></td>
														<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]; ?>" style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo number_format($possible_cut_pcs,4);?></p></td>
													<?
													$order_wise_fab_req += $fin_req;
													$order_wise_fin_fab_req += $issue_qty;
													$order_wise_fab_issued_balance += $req_issue_bal;
													$order_wise_fab_possible_qty += $possible_cut_pcs;
													}
													//$pp++;
													?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cutting_id;?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $row["order_cut_no"];?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_cutting_qnty;?></p></td>
 
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'1', 'cutting_sewing_action');" > <p><? echo $total_cutting_qnty;?></p></a></td>
														<?
														if($pp==0)
														{
															?>
															<td valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $cut_balance = $row["order_quantity"]-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["cut"];?></p></td>
															<?
														}
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $today_input = $cutting_sewing_data[$row["working_company_id"]][$job_id][$style_id][$po_id][$item_id][$color_id][$cutting_id]["today_sewing_input"];?></p></td>

														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><a href="##" onclick="openmypage_cutting_sewing_total(<? echo $po_id;?>,<? echo $item_id;?>,'<? echo  $cutting_id;?>',<? echo $color_id;?>,'4', 'cutting_sewing_action');" ><p><? echo $total_sewing_input;?></a></p></td>
														<?
														if($pp==0)
														{


															?>
															<td  valign="middle"  rowspan="<? echo $style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo 	$input_balance = $row["order_quantity"]-$order_wise_cutting_total[$style_id][$job_id][$po_id][$item_id][$color_id]["sew"];?></p></td>
														<?
														}
														$pp++;
														?>
														<td style="word-wrap: break-word;word-break: break-all;"   align="center"   width="80"><p><? echo $total_cutting_qnty-$total_sewing_input;?></p></td>								 
													</tr>
											<?											
											
											
										}
										
										//$style_color_wise_span[$style_id][$job_id][$po_id][$item_id][$color_id]=$color_span;
										?>
										<tr bgcolor="#E4E4E4">
											<td colspan="16" align="right"><b>Color Wise Sub Total</b></td>
											<td></td>
											<td align="right"><b><?php echo $color_wise_today_cutting;?></b></td>
											<td align="right"><b><?php echo $color_wise_total_cutting;?></b></td>
											
											<td align="right"><b><?php echo $color_wise_today_sewing_input;?></b></td>
											<td align="right"><b><?php echo $color_wise_total_sewing_input;?></b></td>
											<td></td>
										
										</tr>
										<?
										$style_wise_fab_req += $fin_req;		
										$style_wise_fab_issued += $issue_qty;		
										$style_wise_fab_issued_balance += $req_issue_bal;		
										$style_wise_fab_posible_cut_qty += $possible_cut_pcs;
									}

								}
								?>
								<tr bgcolor="#E4E4E4">
									<td colspan="7" align="center"><b>Order Wise Sub Total</b></td>
									<td align="right"><b><? echo $order_wise_subtotal;?></b></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><b><? echo $order_wise_fab_req;?></b></td>
									<td align="right"><b><? echo $order_wise_fin_fab_req;?></b></td>
									<td align="right"><b><? echo $order_wise_fab_issued_balance;?></b></td>
									<td align="right"><b><? echo $order_wise_fab_possible_qty;?></b></td>
									<td></td>
									<td></td>
									<td align="right"><b><? echo $order_wise_today_cutting;?></b></td>
									<td align="right"><b><? echo $order_wise_total_cutting;?></b></td>
									<td align="right"><b><? echo $order_wise_cut_balance; ?></b></td>
									<td align="right"><b><? echo $order_wise_today_input;?></b></td>
									<td align="right"><b><? echo $order_wise_total_input;?></b></td>
									<td align="right"><b><? echo $order_wise_input_balance;?></b></td>
									<td align="right"><b><? echo $order_wise_inhand_qty;?></b></td>
								</tr>
								<?
							}
						
						}
						$k++;
						?>
						<tr bgcolor="#E4E4E4">
						<td colspan="7" align="right"><b>Style Wise Sub Total</b></td>
						<td align="right"><b><? echo $style_wise_order_qty;?></b></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><b><? echo $style_wise_fab_req;?></b></td>
						<td align="right"><b><? echo $style_wise_fab_issued;?></b></td>
						<td align="right"><b><? echo $style_wise_fab_issued_balance;?></b></td>
						<td align="right"><b><? echo $style_wise_fab_posible_cut_qty;?></b></td>
						<td></td>
						<td></td>
						<td align="right"><b><? echo $style_wise_cut_today;?></b></td>
						<td align="right"><b><? echo $style_wise_cut_total;?></b></td>
						<td align="right"><b><? echo $style_wise_cut_balance;?></b></td>
						<td align="right"><b><? echo $style_wise_input_today;?></b></td>
						<td align="right"><b><? echo $style_wise_input_total;?></b></td>
						<td align="right"><b><? echo $style_wise_input_balance;?></b></td>
						<td align="right"><b><? echo $style_wise_inhand_qty;?></b></td>
					</tr>	
						<?
					}

					?>	
										
					</table>					  
				</div>
			</div>
		 </div> 
        <?
	}

	foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $html = ob_get_contents();
    ob_clean();
    echo "$html**$filename**$type";
    exit();  
	 
}

if($action=="remarks_popup")
{
	extract($_REQUEST);
	list($po,$item,$country,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	 $production_sql="SELECT a.production_date,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and a.country_id='$country' and c.color_number_id='$color' group by a.production_date,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo";
	 $type_line_wise_arr=array();
	 $size_all_arr=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["prod_reso_allo"]=$vals[csf("prod_reso_allo")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["remarks"]=$vals[csf("remarks")];

	 	$type_line_wise_arr_sizewise[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 }
	 $cut_lay_sql="SELECT a.entry_date as production_date,b.color_id as color_number_id ,c.size_id as size_number_id ,sum(c.size_qty) as qntys,0 as floor_id,0 as  sewing_line  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id='$po' and b.color_id='$color' and b.gmt_item_id ='$item' and c.country_id='$country'  group by  a.entry_date,b.color_id,c.size_id";
	 foreach(sql_select($cut_lay_sql) as $keys=>$vals)
	 {
	 	$type_line_wise_arr[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr_sizewise[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 }
	 
	 $size_all_ids=implode(',', $size_all_arr);
	 $type_name=[1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",8=>"Finishing & Packing",11=>"Poly",0=>"Cut and Lay"];
	 

	?>
     

    </head>
    <body>
	    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
	    </div>
	    <?
	    ob_start();
		?>
        <div id="details_reports" align="center" style="width:100%;" >
            
            <?
            ksort($type_line_wise_arr);
            $total_type=0;
             foreach($type_line_wise_arr as $type_id=>$date_data)
             {
             	$total_type++;
             	$p=0;
             	?>
             	<table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             		<caption> <strong><? echo $type_name[$type_id];?></strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				<? if($type_id!=1 && $type_id !=0)
             				{
             					?>
             					<th width="100">Floor</th>
             					<th width="80">Line</th>

             					<?

             				}
             				?>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table id="table_body<? echo $total_type;?>" width="620" border="1" rules="all" class="rpt_table" align="center">
             		 

             	<?
             	foreach($date_data as $date_id=>$floor_data)
             	{
             		foreach($floor_data as $floor_id=>$line_data)
             		{
             			foreach($line_data as $line_id=>$color_data)
             			{
             				foreach($color_data as $color_id=>$rows)
             				{
             					$p++;
             					?>


             					<tr>                	 
             						<td align="center" width="30" ><? echo $p;?></td>
             						<td align="center"  width="90"><? echo change_date_format($date_id);?></td>
             						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
             						<?
             						foreach($size_all_arr as $key=>$val)
             						{
             							?>
             							<td align="center"  width="45"><? echo   $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"] ;?></td>

             							<?
             						}

             						?>

             						<td align="center"  width="80"><? echo $rows["qntys"];?></td>
             						<td align="center"  width="80"><? echo $rows["remarks"];?></td>
             						<? if($type_id!=1 && $type_id !=0)
             						{
             							?>
             							<td align="center"  width="100"><? echo $floor_library[$floor_id]; ?></td>
             							<td align="center"  width="80">
             								<?
             								$sewing_line='';

             								if($rows['prod_reso_allo']==1)
             								{
             									$line_number=explode(",",$prod_reso_arr[$line_id]);
             									foreach($line_number as $line_val)
             									{
             										if($sewing_line=='') $sewing_line=$sewing_line_library[$line_val]; else $sewing_line.=",".$sewing_line_library[$line_val];
             									}
             								}
             								else 
             								{
             									$sewing_line=$sewing_line_library[$line_id];
             								}
             								echo $sewing_line;

             								?>
             							 	
             							 </td>

             							<?

             						}
             						?>
             					</tr>           



             					<?

             				}
             				

             			}

             		}

             	}
             	?>
             		 
             		</table>
             		</div>
             		
             	<?
             }

            ?>

                 <script> 
                 var total_type='<? echo $total_type;?>';
                 for(i=1;i<=total_type;i++)
                 {
                 	setFilterGrid("table_body"+i,-1);
                 }
                 
                  </script>
          
        </div>
      	<?
		$html=ob_get_contents();
		ob_flush();
		
		foreach (glob(""."*.xls") as $filename) 
		{
		   @unlink($filename);
		}
		
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);	
		?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
 
 if($action=="cutting_sewing_action")
{
	extract($_REQUEST);
	list($po,$item,$cutting,$type,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	$production_sql="SELECT a.serving_company, c.color_number_id,c.size_number_id,sum(b.production_qnty) as qntys,c.order_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and b.cut_no='$cutting' and c.color_number_id='$color' and a.production_type='$type' group by  a.serving_company, c.color_number_id,c.size_number_id,c.order_quantity";
	 $color_size_wise_qnty=array();
	 $size_all_arr=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	if($po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=="")
	 	{
	 		$color_size_wise_qnty[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
	 		$po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=1;
	 	}
	 	
	 	$working_comp_color_size_wise_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["qntys"]+=$vals[csf("qntys")];
	 	$details_part_array[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("serving_company")];
	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 	$color_all_arr[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];
	 }
	  
	 $size_count=count($size_all_arr)*45;
	 $tbl_width=200+$size_count;
	?>
     

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="<? echo $tbl_width ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Size</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>
             					<?
             				}
             				?>
             				<th width="80">Total</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
              	$gr_size_total=array();
              	$size_total=0;
             	foreach($color_all_arr as  $keys=> $rows)            		 
             	{
             		$total_sizeqnty=0;
             		
             		?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$keys] ;?></td>
         						<?
         						
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo  $value= $color_size_wise_qnty[$keys][$size_key]["order_quantity"] ;?></td>

         							<?
         							$gr_size_total[$size_key]+=$value;
         							$total_sizeqnty+=$value;
         							 
         							
         						}
         					 
         						?>

         						<td align="right"  width="80"><b><? echo $total_sizeqnty;?></b></td>
         						 
             						 
             			</tr>  
				<?
				}
				?>   
						<tr>
							<td colspan="2" align="right"><b>Total</b></td>
							<?
							$gr_all_size=0;
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<td width="45" align="right"><b><? echo $vals=$gr_size_total[$key];?></b></td>

             					<?
             					$gr_all_size+=$vals;
             				}

             				?>
             				<td align="right"><b><? echo $gr_all_size?></b></td>
						</tr>            		 
             		</table>
             		</div>

             		<table width="<? echo $tbl_width+120 ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Details</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="120">Working Company</th>             				 
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45" align="right"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Total</th>
             				 
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width+120 ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
             	$detail_grand_total = 0;
             	$dtls_gr_size=array();
             	foreach($details_part_array as  $company_id=> $color_data)            		 
             	{             		
             		foreach($color_data as  $color_id=> $rows)
             		{
             			?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="120"><? echo $company_library[$company_id] ;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
         						<?
         						$size_total=0;
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo $size_qn=  $working_comp_color_size_wise_qnty[$company_id][$color_id][$size_key]["qntys"] ;?></td>

         							<?
         							$size_total+=$size_qn;
         							$dtls_gr_size[$size_key]+=$size_qn;
         							
         						}
         						$detail_grand_total += $size_total;
         						?>
         						<td align="right"  width="80"><b><? echo $size_total;?></b></td>         						 
             						 
             			</tr>  
					<?
					}
				}
				?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<td width="45" align="right"><b><? echo $dtls_gr_size[$key];?></b></td>

         					<?
         				}

         				?>
         				<td align="right"><b><?php echo $detail_grand_total;?></b></td>
					</tr>               		 
             	</table>
             </div>
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

 if($action=="fab_issue_popup")
{
	extract($_REQUEST);	 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	 ?>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="660" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Issue To Cutting Info</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Issue No</th>             				 
             				<th width="90">Challan No</th>             				 
             				<th width="90">Issue Date</th>             				 
             				<th width="90">Batch No</th>           				 
             				<th width="90">Issue Qnty</th>
             				<th width="160">Fabric Description</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="">
             	<table  width="660" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1; 
             	$sqls=sql_select("SELECT a.issue_number,a.issue_date,a.challan_no,b.batch_id,sum(b.issue_qnty) as qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.batch_id='$batch_id' group by a.issue_number,a.issue_date,a.challan_no,b.batch_id");

             	$batch_sql="SELECT a.id, a.batch_no,b.item_description from pro_batch_create_mst a,PRO_BATCH_CREATE_DTLS b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$batch_id'";
             	foreach(sql_select($batch_sql) as $vals)
             	{
             		$batch_array[$vals[csf("id")]]["batch_no"]=$vals[csf("batch_no")];
             		$batch_array[$vals[csf("id")]]["item_description"]=$vals[csf("item_description")];
             	}
             	$total=0;
             	foreach($sqls as  $keys=> $rows)            		 
             	{
             		
             		?>

             		<tr>                	 
         				<td align="center" width="30" ><? echo $p++;?></td>
         				<td align="center"  width="110"><? echo $rows[csf("issue_number")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("challan_no")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("issue_date")];?></td>             				 
         				<td align="center"  width="90"><? echo $batch_array[$rows[csf("batch_id")]]["batch_no"];?></td>           				 
         				<td align="center"  width="90"><? echo $rows[csf("qnty")];?></td>
         				<td align="center"  width="160"><? echo $batch_array[$rows[csf("batch_id")]]["item_description"];?></td>
             			</tr>   
             			 
					<?
					$total+=$rows[csf("qnty")];
				}
				?>   
						<tr bgcolor="#E4E4E4">                	 
             				<td colspan="5" align="right">Total</td>       				 
             				<td  align="center"  width="90"><? echo $total;?></td>
             				<td  align="center"  width="160">&nbsp;</td>
             			</tr>              		 
             		</table>
             		</div>

             		
          
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
?>