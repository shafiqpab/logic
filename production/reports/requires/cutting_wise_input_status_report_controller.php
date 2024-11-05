<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$lib_prod_floor=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
  

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(40) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in('$data') order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_wise_input_status_report_controller', this.value+'_'+$data, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_", $data);
 	echo create_drop_down( "cbo_floor", 110, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($data[0])  and company_id in ($data[1]) and production_process = 1 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
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
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'cutting_wise_input_status_report_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );

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
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'create_job_list_view', 'search_div', 'cutting_wise_input_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	 $sql= "SELECT a.id,b.po_number,a.job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no,a.style_ref_no,a.company_name,a.buyer_name";
	 // echo $sql;die;
	echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,po_number", "",'','0,0,0,0,0') ;
	exit();
} 




 



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	//cbo_company_name*cbo_working_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*cbo_search_by*txt_job_po_style_no
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_company_name=str_replace("'","",$cbo_working_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_cut_year=str_replace("'","",$cbo_job_year);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_job_po_style_no=str_replace("'","",$txt_job_po_style_no);  
	$str_conds="";
	$str_conds.=($cbo_company_name)? " and a.company_id= '$cbo_company_name'" : "";
	$str_conds.=($cbo_working_company_name)? " and a.working_company_id= '$cbo_working_company_name'" : "";
	$str_conds.=($cbo_location)? " and a.location_id= '$cbo_location'" : "";
	$str_conds.=($cbo_floor)? " and a.floor_id= '$cbo_floor'" : "";
	$str_conds.=($cbo_buyer_name)? " and b.buyer_name= '$cbo_buyer_name'" : "";
	if($cbo_company_name)
	{
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name='$cbo_company_name' and variable_list=23 and is_deleted=0 and status_active=1");
	}
	else
	{
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name='$cbo_working_company_name' and variable_list=23 and is_deleted=0 and status_active=1");
	}

	$line_lib = return_library_array("SELECT id,line_name from lib_sewing_line","id","line_name");
	if($prod_reso_allo==1)
	{  

		$line_libr ="SELECT id,line_number from prod_resource_mst where  is_deleted=0 ";
		foreach(sql_select($line_libr) as $row)
		{             
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_lib[$val]; else $line.=",".$line_lib[$val];
			}
			$line_lib_resource[$row[csf('id')]]=$line; 
		}       

	} 


	if($cbo_search_by)
	{
		if($cbo_search_by==1)
		{
			$str_conds.=" and b.job_no_prefix_num='$txt_job_po_style_no'";
		}
		else if($cbo_search_by==2)
		{
			$str_conds.=" and   b.style_ref_no like '%$txt_job_po_style_no%'";
		}

		else if($cbo_search_by==3)
		{
			$str_conds.="  and  c.po_number like '%$txt_job_po_style_no%'";
		}
		else if($cbo_search_by==4)
		{
			$str_conds.=" and  a.cut_num_prefix_no='$txt_job_po_style_no'";
		}
		else if($cbo_search_by==5)
		{
			$str_conds.=" and  c.grouping like '%$txt_job_po_style_no%'";
		}
	}
	if($cbo_cut_year>0)
	{
		if($db_type==0)
		{
			$str_conds .=" and year(a.insert_date)='$cbo_cut_year'";
		}
		else
		{
			$str_conds .=" and to_char(a.insert_date,'YYYY')='$cbo_cut_year'";
		}	
	}

	  $cut_sql="SELECT a.id,a.company_id,a.working_company_id, a.location_id ,a.floor_id,b.buyer_name,b.job_no,b.style_ref_no,b.style_description,a.cutting_no,d.gmt_item_id,d.color_id ,d.order_cut_no from ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d,pro_garments_production_mst e  where  a.job_no=b.job_no and b.job_no=c.job_no_mst and  a.id=d.mst_id and a.cutting_no=e.cut_no and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active in(1,2,3)  and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 and e.production_type=4   $str_conds group by a.id, a.company_id,a.working_company_id, a.location_id ,a.floor_id,b.buyer_name,b.job_no,b.style_ref_no,b.style_description,a.cutting_no,d.gmt_item_id,d.color_id ,d.order_cut_no ";
	  //echo "$cut_sql";die;

	  $cut_lay_array=array();
	  $cut_mst_id_arr=array();
	  $cut_no_array=array();
	 
	  foreach( sql_select($cut_sql) as $keys=>$vals)
	  {
	  	//if($vals[csf("sewing_line")])
	  	//{
	  		 
	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["job_no"]=$vals[csf("job_no")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["company_id"]=$vals[csf("company_id")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["working_company_id"]=$vals[csf("working_company_id")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["location_id"]=$vals[csf("location_id")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["floor_id"]=$vals[csf("floor_id")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["buyer_name"]=$vals[csf("buyer_name")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["style_ref_no"]=$vals[csf("style_ref_no")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["style_description"]=$vals[csf("style_description")];

	  			$cut_lay_array[$vals[csf("cutting_no")]][$vals[csf("gmt_item_id")]][$vals[csf("color_id")]]["order_cut_no"]=$vals[csf("order_cut_no")];
	  			$cut_mst_id_arr[$vals[csf("id")]]=$vals[csf("id")];
	  			$cut_no_array[$vals[csf("cutting_no")]]=$vals[csf("cutting_no")];

	  		 
	  		

	  	//}

	  	


	  }
	 // echo "<pre>";print_r($cut_lay_array);die;
	  $mst_id_from_cutlay=implode(",", $cut_mst_id_arr);
	  if(!$mst_id_from_cutlay)
	  {
	  	$mst_id_from_cutlay=0;
	  }
	  $size_id_array=array();
	  $cutting_wise_cut_lay=array();
	  $cutting_wise_all_po_array=array();

	  $size_sqls="SELECT a.cutting_no, b.size_id,sum(b.size_qty) as qty,b.order_id from ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and   b.status_active=1 and b.is_deleted=0 and b.mst_id in ($mst_id_from_cutlay) group by  a.cutting_no, b.size_id,b.order_id";
	  foreach(sql_select( $size_sqls) as $size_id=>$size_keys)
	  {
	  	//$size_id_array[$size_keys[csf("size_id")]]=$size_keys[csf("size_id")];
	  	$size_wise_cut_lay[$size_keys[csf("size_id")]]+=$size_keys[csf("qty")];
	  	$cutting_wise_cut_lay[$size_keys[csf("cutting_no")]]+=$size_keys[csf("qty")];
	  	$cutting_wise_all_po_array[$size_keys[csf("order_id")]]=$size_keys[csf("order_id")];
	  }

	 //$all_size_ids=implode(",",$size_id_array);
	 $cutting_wise_all_po_ids=implode(",",$cutting_wise_all_po_array);
	 if(!$cutting_wise_all_po_ids)
	 {
	 	$cutting_wise_all_po_ids=0;
	 }
	 //$size_count=count($size_id_array);

	 $size_wise_po_sqls="SELECT size_number_id,order_quantity,size_order from wo_po_color_size_breakdown where status_active in(1,2,3)  and is_deleted=0 and po_break_down_id in ($cutting_wise_all_po_ids) order by size_order asc";
	 $size_wise_po_qty_array=array();
	 foreach(sql_select($size_wise_po_sqls) as $po_size=>$po_val)
	 {
	 	$size_id_array[$po_val[csf("size_number_id")]]=$po_val[csf("size_number_id")];
	 	$size_wise_po_qty_array[$po_val[csf("size_number_id")]]+=$po_val[csf("order_quantity")];
	 }
	 $all_size_ids=implode(",",$size_id_array);
	 $size_count=count($size_id_array);
	//print_r($size_wise_po_qty_array);die;
	
	  
	
	 
	 
	if($type==0)
	{
		$all_cut_no_cond = '';
		if (count($cut_no_array)>999) 
		{
			$chunk_arr = array_chunk($cut_no_array,999);
			foreach($chunk_arr as $vals)
			{
				$chunk_cut_no="'".implode("','", $vals)."'";
				if($all_cut_no_cond=="")
				{
					$all_cut_no_cond.=" and (b.cut_no in ($chunk_cut_no)";
				}
				else
				{
					$all_cut_no_cond.=" or  b.cut_no in ($chunk_cut_no)";
				}
			}
			$all_cut_no_cond.= " )";
		}
		else
		{
			$all_cut_no="'".implode("','", $cut_no_array)."'";
			$all_cut_no_cond.=" and b.cut_no in ($all_cut_no)";
		}
		// echo $all_cut_no_cond; die;
		 
	    $production_sql="SELECT a.item_number_id,c.color_number_id,a.sewing_line,c.size_number_id,b.cut_no,sum( case when  a.production_type=4 and b.production_type=4 then b.production_qnty else 0 end ) as sewing_qnty 
	 	,sum( case when  a.production_type=1 and b.production_type=1 then b.production_qnty else 0 end ) as cut_qc_qnty
	 	from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  $all_cut_no_cond
		group by a.item_number_id,c.color_number_id,a.sewing_line,c.size_number_id,b.cut_no  ";
		$production_array=array();
		$cutting_wise_qc_qnty=array();
		$cutting_size_wise_sewing_qnty=array();
		$cut_item_color_wise_line=array();
		foreach (sql_select($production_sql) as $key => $rows)
		{
			$production_array[$rows[csf("cut_no")]][$rows[csf("sewing_line")]][$rows[csf("item_number_id")]][$rows[csf("color_number_id")]][$rows[csf("size_number_id")]]+=$rows[csf("sewing_qnty")];
			$cutting_wise_qc_qnty[$rows[csf("cut_no")]]+=$rows[csf("cut_qc_qnty")];
			$cutting_wise_sewing_qnty[$rows[csf("cut_no")]]+=$rows[csf("sewing_qnty")];
			$cutting_size_wise_sewing_qnty[$rows[csf("cut_no")]][$rows[csf("size_number_id")]]+=$rows[csf("sewing_qnty")];
			if($rows[csf("sewing_line")])
			{
				if($cut_item_color_wise_line[$rows[csf("cut_no")]][$rows[csf("item_number_id")]][$rows[csf("color_number_id")]]["line"]=="")
				{
					$cut_item_color_wise_line[$rows[csf("cut_no")]][$rows[csf("item_number_id")]][$rows[csf("color_number_id")]]["line"]=$rows[csf("sewing_line")];
				}
				else
				{
					$cut_item_color_wise_line[$rows[csf("cut_no")]][$rows[csf("item_number_id")]][$rows[csf("color_number_id")]]["line"].=','.$rows[csf("sewing_line")];
				}
				

			}
		}
		 
		ob_start();	

		$width=1460+($size_count*45);
		$width2=1480+($size_count*45);

		/*$cut_span_array=array();
		foreach($cut_lay_array as $cutting_id=>$item_data)
		{
			$cut_span=0;
			foreach($item_data as $item_id=>$color_data)
			{
				//foreach($color_data as $color_id=>$line_data)
				//{
					foreach($color_data as $color_id=>$rows)
					{
						$cut_span++;
					}
				//}
			}
			$cut_span_array[$cutting_id]=$cut_span;
		}*/
		//print_r($cut_span_array);
		//$cut_item_color_wise_line[$rows[csf("cut_no")]][$rows[csf("item_number_id")]][$rows[csf("color_number_id")]]["line"]
		$cut_span_array=array();
		$item_color_span_array=array();
		foreach($cut_item_color_wise_line as $cutting_id=>$item_data)
		{
			$cut_span=0;
			foreach($item_data as $item_id=>$color_data)
			{
				
				 
					foreach($color_data as $color_id=>$rows)
					{
						$item_color_span=0;
						$vals=$cut_item_color_wise_line[$cutting_id][$item_id][$color_id]["line"];
						foreach(array_unique(explode(",", trim($vals))) as $values)
						{

							$cut_span++;
							$item_color_span++;
						}

						$item_color_span_array[$cutting_id][$item_id][$color_id]=$item_color_span;
					}
				 
			}
			$cut_span_array[$cutting_id]=$cut_span;
		}
		/*echo "<pre>";
		print_r($cut_span_array);die;*/
		
		?>
        <div>
        	<table width="<? echo $width;?>" cellspacing="0" >

        		<tr class="form_caption" style="border:none;">
        			<td colspan="<? echo 18+$size_count;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				<? echo $company_library[$cbo_company_name];?>
        			</strong></td>
        		</tr>

        		<tr class="form_caption" style="border:none;">
        			<td colspan="<? echo 18+$size_count;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
        				Cutting wise input status report
        			</strong></td>
        		</tr>
        		 
        	 
        	</table>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:<?echo $width2;?>px">Details Part</div>
            
			<div style="float:left; width:1930px">
				<table width="<? echo $width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
						<tr style="background-color:#ACC9F0;">
							<td style="word-wrap: break-word;word-break: break-all;" colspan="16" width="1410" align="right"><strong><p>Size</p></strong></td>
							<?
							foreach($size_id_array as $vals)
							{
								?>
								<td style="word-wrap: break-word;word-break: break-all;" width="45" align="center"><strong><? echo $size_lib[$vals]; ?></strong></td>

								<?

							}
							?>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="45"><strong> &nbsp; &nbsp;Total    &nbsp;(Pcs)</strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="50"></td>

						</tr>

						<tr style="background-color:#ACC9F0;">
							<td  style="word-wrap: break-word;word-break: break-all;" colspan="16" width="1410" align="right"><strong><p>Order Qty.</p></strong></td>
							<?
							$totals=0;
							foreach($size_id_array as $vals)
							{
								?>  
								<td style="word-wrap: break-word;word-break: break-all;" align="center" width="45"><strong><? echo  $size_wise_po_qty_array[$vals]; ?></strong></td>

								<?
								$totals+=$size_wise_po_qty_array[$vals];

							}
							?>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="45"><strong><? echo $totals;?></strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="50"></td>

						</tr>

						<tr>
							<th style="word-wrap: break-word;word-break: break-all;"  width="50" ><p>SL No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="120" ><p>Company Name</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="120" ><p>Working Company</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="100" ><p>Location</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="100" ><p>Floor</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="100" ><p>Buyer</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="80" ><p>Job No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="100" ><p>Style</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="100" ><p>Style Description</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="110" ><p>Gmts Item</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="80" ><p>Color</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="90" ><p>System Cut No.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="70" ><p>Order Cut No.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="60" ><p>Lay Qty.</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="70" ><p>Cutting QC</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="60" ><p>Line No</p></th>
							<th style="word-wrap: break-word;word-break: break-all;" width="<? echo $size_count*45;?>"  colspan="<? echo $size_count+1;?>"     ><p>Input Qty</p></th>
							<th style="word-wrap: break-word;word-break: break-all;"   width="50" ><p>Balance</p></th>


						</tr>
					 
						
						  
					   
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:<?echo $width2;?>px" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="<? echo $width;?>" rules="all" id="" >
						<?
						/*echo "<pre>";
						print_r($cut_lay_array);die;*/
						$k=1;
						$p=1;
						$mm=0;
 						foreach($cut_lay_array as $cutting_id=>$item_data)
						{
							
							$ll=0;
							

							foreach($item_data as $item_id=>$color_data)
							{
								foreach($color_data as $color_id=>$rows)
								{
									$item_col_sp=0;
									//foreach($line_data as $line_id=>$rows)
									//{
									 	 $all_line=$cut_item_color_wise_line[$cutting_id][$item_id][$color_id]["line"];
									 	 $line_arrs=array_unique(explode(",",trim($all_line)));
									 	// print_r($line_arrs);
									 	 foreach($line_arrs as $line_key=>$line_val)
									 	 {
									 	 	//echo "adc ". $line_val ." bc ";
									 	 	if($line_val)
									 	 	{




									 	 		$p++;
									 	 		if ($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									 	 		?>
									 	 		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $p; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $p; ?>">


									 	 			<?
									 	 			if($ll==0)
									 	 			{

									 	 				?>
									 	 				<td align="center" valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>" style="word-wrap: break-word;word-break: break-all;"  width="50" ><p><? echo $k;$k++;?></p></td>
									 	 				<td  valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="120" ><p><? echo $company_library[$rows["company_id"]] ;?> </p></td>
									 	 				<td  valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="120" ><p><? echo $company_library[$rows["working_company_id"]] ;?> </p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="100" ><p><? echo $location_library[$rows["location_id"]] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="100" ><p><? echo $lib_prod_floor[$rows["floor_id"]] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="100" ><p><? echo $buyer_library[$rows["buyer_name"]] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="80" ><p><? echo $rows["job_no"] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="100" ><p><? echo $rows["style_ref_no"] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"   align="center"  style="word-wrap: break-word;word-break: break-all;"   width="100" ><p><? echo $rows["style_description"] ;?> </p></td>
									 	 				<?
									 	 			}
									 	 			if($item_col_sp==0)
									 	 			{


									 	 				?>

									 	 				<td align="center"  valign="middle" rowspan="<? echo $item_color_span_array[$cutting_id][$item_id][$color_id]; ?>"  style="word-wrap: break-word;word-break: break-all;"   width="110" ><p><? echo $garments_item[$item_id] ;?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $item_color_span_array[$cutting_id][$item_id][$color_id]; ?>" align="center"  style="word-wrap: break-word;word-break: break-all;"   width="80" ><p><? echo $color_Arr_library[$color_id] ;?></p></td>
									 	 				<?
									 	 			}
									 	 			if($ll==0)
									 	 			{


									 	 				?>
									 	 				<td align="center" valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"  style="word-wrap: break-word;word-break: break-all;"   width="90" ><p><? echo $cutting_id;?></p></td>
									 	 				<?
									 	 			}

									 	 			if($item_col_sp==0)
									 	 			{



									 	 				?>
									 	 				<td  valign="middle" rowspan="<? echo $item_color_span_array[$cutting_id][$item_id][$color_id]; ?>"  align="center"  style="word-wrap: break-word;word-break: break-all;"   width="70" ><p><? echo $rows["order_cut_no"] ;?></p></td>
									 	 				<?
									 	 			}
									 	 			if($ll==0)
									 	 			{
									 	 				?>
									 	 				<td  valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>" align="center"  style="word-wrap: break-word;word-break: break-all;"   width="60" ><p><? echo $cutting_wise_cut_lay[$cutting_id];?></p></td>
									 	 				<td valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]; ?>"  align="center"  style="word-wrap: break-word;word-break: break-all;"   width="70" ><p><? echo $cutting_wise_qc_qnty[$cutting_id];?></p></td>
									 	 				<? 
									 	 				$balance=$cutting_wise_qc_qnty[$cutting_id]-$cutting_wise_sewing_qnty[$cutting_id];
									 	 			}
									 	 			?>
									 	 			<td align="center"  style="word-wrap: break-word;word-break: break-all;"   width="60" ><p>
									 	 				<? 

									 	 				$line=array_unique(explode(",", $line_val));

									 	 				$line_name="";
									 	 				foreach($line as $v)
									 	 				{
									 	 					if($prod_reso_allo==1)
									 	 					{ 
									 	 						$line_name.= $line_lib_resource[$v].",";
									 	 					}
									 	 					else
									 	 					{
									 	 						$line_name.=$line_lib[$v].",";
									 	 					}



									 	 				}
									 	 				echo trim($line_name,","); ?>


									 	 			</p></td>
									 	 			<?
									 	 			$total_vals=0;
									 	 			foreach($size_id_array as $vals)
									 	 			{
									 	 				?>
									 	 				<td style="word-wrap: break-word;word-break: break-all;" align="center"   width="45"><? echo $size_val=$production_array[$cutting_id][$line_val][$item_id][$color_id][$vals]; ?></td>

									 	 				<?
									 	 				$total_vals+=$size_val;

									 	 			}

									 	 			?>
									 	 			<td align="center"  style="word-wrap: break-word;word-break: break-all;" width="45"><p><? echo $total_vals;?></p></td>
									 	 			<?
									 	 			if($ll==0)
									 	 			{

									 	 				$cut_number="'".$cutting_id."'";
									 	 				?>
									 	 				<td align="center" valign="middle" rowspan="<? echo $cut_span_array[$cutting_id]+1; ?>"  style="word-wrap: break-word;word-break: break-all;"   width="50" ><a href="##" onclick="openmypage_remarks(<? echo $cut_number;?>,'remarks_popup')";><p><? echo $balance; ?></p></a></td>
									 	 				<?


									 	 			}
									 	 			?>


									 	 		</tr>
									 	 		<?
									 	 		$item_col_sp++;
									 	 		$ll++;
									 	 	}
									 	 }
									

									//}
									  
									 
									//$k++;

								}

							}
							 
							if ($mm%2==0) $bgcolor="#E4E4E4"; else $bgcolor="#E4E4E4";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_21nd<? echo $mm; ?>','<? echo $bgcolor; ?>')" id="tr_21nd<? echo $mm; ?>">

							 
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"  width="50" ><p> </p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="120" ><p> </p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="120" ><p> </p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="100" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="100" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="100" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="80" ><p> </p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="100" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="100" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="110" ><p> </p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="80" ><p></p></td>
								<td style="word-wrap: break-word;word-break: break-all;border-right: 1px solid #E4E4E4;"   width="90" ><p>  </p></td>
								<td  align="right" style="word-wrap: break-word;word-break: break-all;"   width="70" ><p>  Subtotal</p></td>

								<td align="center" style="word-wrap: break-word;word-break: break-all;" width="60"><p><? echo $cutting_wise_cut_lay[$cutting_id];?></p></td> 
								<td align="center" style="word-wrap: break-word;word-break: break-all;" width="70"><p><? echo $cutting_wise_qc_qnty[$cutting_id];?></p></td> 
								<td align="center" style="word-wrap: break-word;word-break: break-all;" width="60"><p>&nbsp;</p></td> 
								
								<?
								$total_vals_subtotal=0;
								foreach($size_id_array as $vals)
								{
									?>
									<td style="word-wrap: break-word;word-break: break-all;" align="center"   width="45"><? echo $size_val_sub=$cutting_size_wise_sewing_qnty[$cutting_id][$vals]; ?></td>

									<?
									$total_vals_subtotal+=$size_val_sub;

								}

								?>
								<td align="center"  style="word-wrap: break-word;word-break: break-all;" width="45"><p><? echo $total_vals_subtotal;?></p></td>


								 


							</tr>


							<?
							$mm++;
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
    echo "$html**$filename";
    exit();  
	 
}



if($action=="remarks_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	$production_sql="SELECT c.po_break_down_id, c.color_number_id,c.size_number_id,a.production_type,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 and d.status_active in(1,2,3)  and d.is_deleted=0 and b.cut_no='$cut_no' group by c.po_break_down_id, c.color_number_id,c.size_number_id,a.production_type";
	 $cutting_qc_wise_arr=array();
	 $input_wise_arr=array();
	 $size_all_arr=array();
 
	 $po_ids_array=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	if($vals[csf("production_type")]==1)
	 	{
	 		$cutting_qc_wise_arr[$vals[csf("size_number_id")]]+=$vals[csf("qntys")];
	 	}

	 	if($vals[csf("production_type")]==4)
	 	{
	 		$input_wise_arr[$vals[csf("size_number_id")]]+=$vals[csf("qntys")];
	 	}

	  

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 	$po_ids_array[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
	 }
	  $all_po=implode(",", $po_ids_array);
	 $po_size_sql=sql_select( "SELECT size_number_id,order_quantity from  wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in ($all_po) ");
	 $po_size_library=array();
	 foreach($po_size_sql as $keys=>$values)
	 {
	 	$po_size_library[$values[csf("size_number_id")]]+=$values[csf("order_quantity")];
	 }


	?>
	<script type="text/javascript">
		function new_window2()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$("#table_body tr:first").hide();
			$("#report_container3").css("margin","0px auto");

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container3').innerHTML+'</body</html>');
			d.close(); 
			//$(htmlSearchValue).prependTo("table#table_body tbody");
			//document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="300px";
			$("#table_body tr:first").show();
		}
	</script>
     

    </head>
    <body>
        <div align="center" style="width:100%;" id="" >
            
            <?
            $size_counts=count($size_all_arr);
            $width=($size_counts*45)+100+90;
             
             	 
             	?>
             	<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>
            <div id="report_container3">
             		
             	
             	
             	<table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="" rules="all">
             		 
             		<thead>
             		 

             			<tr>                	 
             				 
             				<th width="100">Size</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th  align="center" width="90">Total (Pcs)</th>
             				 
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;" id="scroll_body">
             	<table id="table_body" width="<? echo $width;?>" border="1" rules="all" class="rpt_table">
             		 

             	 	 
             	 		<tr>                	 

             	 			<td align="center"  width="90">Order Qty.</td>
             	 			<?
             	 			$total_vals=0;
             	 			foreach($size_all_arr as $key=>$vals)
             	 			{
             	 				$total_vals+=$po_size_library[$vals];
             	 				?>
             	 				<td align="center"  width="45"><? echo $po_size_library[$vals];?></td>

             	 				<?
             	 			}

             	 			?>
             	 			<td align="center"  width="90"><? echo $total_vals; ?></td>


             	 		</tr>

             	 		 
             	 		<tr>                	 

             	 			<td align="center"  width="90">Cutting QC Qty</td>
             	 			<?
             	 			$total_vals=0;
             	 			foreach($size_all_arr as $key=>$vals)
             	 			{
             	 				$total_vals+=$cutting_qc_wise_arr[$vals];
             	 				?>
             	 				<td align="center"  width="45"><? echo $cutting_qc_wise_arr[$vals];?></td>

             	 				<?
             	 			}

             	 			?>
             	 			<td align="center"  width="90"><? echo $total_vals; ?></td>


             	 		</tr>

             	 		 
             	 		<tr>                	 

             	 			<td align="center"  width="90">Input Qty</td>
             	 			<?
             	 			$total_vals=0;
             	 			foreach($size_all_arr as $key=>$vals)
             	 			{
             	 				$total_vals+=$input_wise_arr[$vals];
             	 				?>
             	 				<td align="center"  width="45"><? echo $input_wise_arr[$vals];?></td>

             	 				<?
             	 			}

             	 			?>
             	 			<td align="center"  width="90"><? echo $total_vals; ?></td>


             	 		</tr>

             	 		<tr>                	 

             	 			<td align="center"  width="90">Balance</td>
             	 			<?
             	 			$total_vals=0;
             	 			foreach($size_all_arr as $key=>$vals)
             	 			{
             	 				$total_vals+=$cutting_qc_wise_arr[$vals]-$input_wise_arr[$vals];
             	 				?>
             	 				<td align="center"  width="45"><? echo $cutting_qc_wise_arr[$vals]-$input_wise_arr[$vals];?></td>

             	 				<?
             	 			}

             	 			?>
             	 			<td align="center"  width="90"><? echo $total_vals; ?></td>


             	 		</tr>

             	 		 

             	 
              
             		 
             		</table>
             		</div>
            </div>
             		
             	<?
              

            ?>

                 <script> 
                  
                 	setFilterGrid("table_body",-1);
                  
                 
                  </script>
          
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
 



 


?>