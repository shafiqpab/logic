<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

	require_once('../../../includes/common.php');

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];
	$user_id = $_SESSION['logic_erp']['user_id'];
	if($db_type==0){$defalt_date_format="0000-00-00";}else{$defalt_date_format="";}



//--------------------------------------------------------------------------------------------------------------------



// print_r($party_library);die();

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );   
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
	//echo $cbo_search_by 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$search_by_arr=  array(1=>"Job",2=>"Style",3=>"PO",4=>"Cutting No"); 
	$captions= (($cbo_search_by==1)? "Job " : (($cbo_search_by==2)? "Style" : (($cbo_search_by==3)? " PO" : "Cutting No")));
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
                            <th width="100" ><? echo $captions; ?></th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_id">
                        <input type="hidden" id="selected_name"> 
                            <?
                            
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'order_wise_emb_report_controller_v2', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
	                        <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", $cbo_search_by,"",1 );
	                        ?>
                        	
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'create_job_list_view', 'search_div', 'order_wise_emb_report_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
		else if($data[2]==4)
		{

			$str_cond.= " and c.cut_num_prefix_no =$data[3]";

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
	
	if($data[2]==4)
	{
		$sql= "SELECT a.id,b.po_number,c.cutting_no as job_full ,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,c.cutting_no ,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name order by a.id desc";
		echo  create_list_view("list_view", "Company,Buyer Name,Cut No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "id,job_full", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_full,style_ref_no,po_number", "",'','0,0,0,0,0') ;

	}
	else
	{
		$js_set="";
		$js_set=(($data[2]==1) ? " id,job_full" : ( ($data[2]==2) ? " id,style_ref_no" :  ( ($data[2]==3) ? " id,po_number" : "" )));
		$sql= "SELECT a.id,b.po_number,a.job_no as job_full ,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no ,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name order by a.id desc";
		echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "$js_set", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_full,style_ref_no,po_number", "",'','0,0,0,0,0') ;
	}

	// echo $sql;die;
	
	exit();
} 


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);	
	
	echo create_drop_down( "cbo_floor_name", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($data[0]) and location_id in($data[1]) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select Floo --", $selected, "",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "", "");
	} 
	else if ($data[0] == 3) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", 0, "");
	} 
	else 
	{
		echo create_drop_down("cbo_party_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
}

if($action=="search_by_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_job = new Array; var selected_po = new Array;
		var selected_style = new Array; var selected_cutting = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_job ) == -1 ) {
				selected_job.push( str[1] );
				selected_po.push( str[2] );
				selected_style.push( str[3] );
				selected_cutting.push( str[4] );
				
			}
			else {
				for( var i = 0; i < selected_job.length; i++ ) {
					if( selected_job[i] == str[1] ) break;
				}
				selected_job.splice( i, 1 );
				selected_po.splice( i, 1 );
				selected_style.splice( i, 1 );
				selected_cutting.splice( i, 1 );
			}
			var job = ''; var po = ''; var style = ''; var cutting = '';
			// alert(selected_job);
			for( var i = 0; i < selected_job.length; i++ ) {
				job += selected_job[i] + ',';
				po += selected_po[i] + ',';
				style += selected_style[i] + ',';
				cutting += selected_cutting[i] + ',';
			}
			
			job = job.substr( 0, job.length - 1 );
			po = po.substr( 0, po.length - 1 );
			style = style.substr( 0, style.length - 1 );
			cutting = cutting.substr( 0, cutting.length - 1 );
			
			$('#hide_job_no').val( job );
			$('#hide_order_id').val( po );
			$('#hide_style_no').val( style );
			$('#hide_cutting_no').val( cutting );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:420px;">
	            <table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Company</th>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
	                    <input type="hidden" name="hide_cutting_no" id="hide_cutting_no" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
                               echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'order_wise_emb_report_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
	                        </td>      
	                        <td align="center" id="buyer_td">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>
	                        <td align="center">
	                        	<?
			                        $year_current=date("Y");
			                        echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "All",$year_current,'','');
			                    ?>
	                        </td>           
	                        	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_job_year').value, 'create_order_no_search_list_view', 'search_div', 'order_wise_emb_report_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];	
	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$job_year=$data[2];
	$year_cond = "";
	if($job_year>0)
	{
		if($db_type==0)
		{
			$year_cond =" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$year_cond =" and to_char(a.insert_date,'YYYY')='$job_year'";
		}	
	}

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number,c.cutting_no from wo_po_details_master a, wo_po_break_down b, ppl_cut_lay_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $buyer_id_cond $year_cond order by b.id, b.pub_shipment_date";


	echo create_list_view("tbl_list_search", "Job No,Style Ref. No, Po No, cutting_no", "120,120,120,120","500","220",0, $sql , "js_set_value", "job_no,id,style_ref_no,cutting_no","",1,"0,0,0,0",$arr,"job_no,style_ref_no,po_number,cutting_no","",'','0,0,0,0','',1) ;
		
	/*echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;*/
   exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// ========================================= GETTING SEARCH PARAMETER ========================================
	$working_company 	= str_replace("'", "", $cbo_working_company);
	$location_id 		= str_replace("'", "", $cbo_location);
	$buyer_name 		= str_replace("'", "", $cbo_buyer_name);
	$job_year 			= str_replace("'", "", $cbo_job_year);
	$emb_type 			= str_replace("'", "", $cbo_emb_type);
	$source 			= str_replace("'", "", $cbo_source);
	$party_id 			= str_replace("'", "", $cbo_party_name);
	$search_by 			= str_replace("'", "", $cbo_search_by);
	$search_type 		= str_replace("'", "", $txt_search_type);
	$date_from 			= str_replace("'", "", $txt_date_from);
	$date_to 			= str_replace("'", "", $txt_date_to);

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$color_library=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
	$party_library=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );

	// echo $reportType;die;

	if($reportType == 1) // FTML
	{
		// ====================================== MAIN QUERY =========================================================
		$sql = "SELECT a.floor_id,d.buyer_name,c.color_number_id,a.po_break_down_id,d.style_ref_no,e.po_number
				from 
				pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e
				where a.production_type=b.production_type and a.production_type in(2,3) and a.embel_name in(1,2,3,4) and c.id=b.color_size_break_down_id and a.id=b.mst_id  and a.po_break_down_id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.job_no=e.job_no_mst AND e.id=a.po_break_down_id AND e.id=c.po_break_down_id and d.status_active=1 and d.is_deleted=0";

		// =========================================== FOR EMBLISMENT QNTY ==================================
		$prod_sql= "SELECT c.po_break_down_id as po_id, a.color_number_id,a.item_number_id, a.country_id,e.style_ref_no,e.buyer_name,d.cut_no,
			NVL(sum(CASE WHEN d.production_type ='1'  and c.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				
			NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS print_issue,
			NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS print_recive,
				
			NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS emb_issue,
			NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS emb_recive,

			NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=3 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS wash_issue,
			NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=3 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS wash_recive,

			NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=4 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS spw_issue,
			NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=4 and c.production_source=$source THEN d.production_qnty ELSE 0 END),0) AS spw_recive,

			NVL(sum(CASE WHEN d.production_type in (2,3) and c.production_type in (2,3) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty			
				  
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id and a.job_no_mst=e.job_no  and a.id=d.color_size_break_down_id and c.po_break_down_id in($poId) and
				c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  
			group by c.po_break_down_id, a.color_number_id,a.item_number_id, a.country_id,e.style_ref_no,e.buyer_name,d.cut_no";

			$prod_sql_res = sql_select($prod_sql);
			$prod_qnty_array = array();
			foreach ($prod_sql_res as $row) 
			{
				$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['cutting_qnty'] = $row[csf('cutting_qnty')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['print_issue'] = $row[csf('print_issue')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['print_recive'] = $row[csf('print_recive')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['emb_issue'] = $row[csf('emb_issue')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['emb_recive'] = $row[csf('emb_recive')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['wash_issue'] = $row[csf('wash_issue')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['wash_recive'] = $row[csf('wash_recive')];

	        	$prod_qnty_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['reject_qty'] = $row[csf('reject_qty')];
			}
			// echo "<pre>";
			// print_r($prod_qnty_array);
			// echo "</pre>";		
			
		ob_start();
		?>
			<div style="width:3000px">
			<fieldset style="width:3000px;">	
				<table width="2980">
					<tr class="form_caption">
						<td colspan="20" align="center">Order Wise Embellishment Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="20" align="center"><? echo $company_library[$working_company]; ?></td>
					</tr>
				</table>
				<table class="rpt_table" width="2980" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th style="word-break: break-all;word-wrap: break-word;" width="30">SL</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="150">W. Company</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Floor</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Buyer</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Style</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Job No</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Order No</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Country</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Country Ship Date</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Gmts. Item</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Color</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Order Qnty</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Cutting Qnty</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Cutting QC</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">EMB Company</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Print Issue</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Print Receive</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">print Reject</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Print WIP</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Emb. Issue</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Emb. Receive</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Emb. Reject</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Emb. WIP</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Wash Issue</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Wash Receive</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Wash Reject</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Wash WIP</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">S.Work Issue</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">S.Work Receive</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">S.Work Reject</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">S.Work WIP</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Remarks</th>
					</thead>
				</table>
				<div style="width:3000px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2980" cellpadding="0" cellspacing="0" border="1" id="table_body">
						<tr>						
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="30">.</td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="150"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="130"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
							<td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td>
		                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80"></td> 
						</tr>										
					</table>
				</div>
				<div>
					<table class="rpt_table" width="2980" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30">.</th>
							<th width="150"></th>
							<th width="130"></th>
							<th width="130"></th>
		                    <th width="130"></th>
		                    <th width="130"></th>
							<th width="130"></th>
		                    <th width="130"></th>
							<th width="80"></th>
							<th width="130"></th>
		                    <th width="130"></th>
		                    <th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
		                    <th width="80"></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
		<?
	}
	else // 2ND REPORT FTML2
	{
		 
		$sql_cond = "";
		$sql_cond .= ($working_company != "" && $working_company != 0) ? " and h.working_company_id in ($working_company)" : "";
		$sql_cond .= ($location_id 		!= "" && $location_id != 0) ? " and h.working_location_id in ($location_id)" : "";
		$sql_cond .= ($buyer_name != 0) ? " and a.buyer_name in ($buyer_name)" : "";
		$sql_cond .= ($job_year) ? " and to_char(a.insert_date,'YYYY')=$job_year" : "";
		$sql_cond .= ($source != 0) ? " and f.production_source in ($source) and h.production_source in ($source)" : "";
		$sql_cond .= ($party_id != 0) ? " and f.serving_company in ($party_id)" : "";

		$sql_cond .= ($emb_type != 0) ? " and f.production_type in (2,3) and f.embel_name in ($emb_type)" : " and f.production_type in (2,3) and f.embel_name in (1,2,3,4)";
		$sql_cond .= ($date_from != "" && $date_to != "") ? " and f.production_date between $txt_date_from and $txt_date_to" : "";
		if($source==1){$sql_cond .= "";}else if($source==3){$sql_cond .= "";}else{$sql_cond .= "";}
		switch ($search_by) 
		{
			case 1:
				$sql_cond .= " and a.job_no like '%$search_type%'";
				break;
			case 2:
				$sql_cond .= " and a.style_ref_no like '%$search_type%'";
				break;
			case 3:
				$sql_cond .= " and b.po_number like '%$search_type%'";
				break;
			case 4:
				$sql_cond .= " and g.cut_no like '%$search_type%'";
				break;			
			
			default:
				$sql_cond .="";
				break;
		}		 

		$sql = "SELECT   a.job_no,a.style_ref_no,a.buyer_name,a.client_id as buyer_client,a.company_name, b.id as po_id,b.po_number,c.color_number_id,c.country_ship_date,c.country_id,c.item_number_id,f.location,f.embel_name,f.serving_company, h.working_company_id,f.production_source,h.working_location_id,g.cut_no as cutting_no,h.production_type,h.manual_challan_no,h.delivery_date
				from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst f, pro_garments_production_dtls g,pro_gmts_delivery_mst h				
				where a.id=b.job_id  and b.id=c.po_break_down_id and b.id=f.po_break_down_id and f.id=g.mst_id and h.id=g.delivery_mst_id and g.color_size_break_down_id=c.id and h.status_active=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and f.status_active=1  and g.status_active=1  $sql_cond and f.production_type in(2,3) ";		
		// echo $sql;die();		
		$sql_res = sql_select($sql);		
		if(count($sql_res) == 0){ echo "<div style='color:red;text-align:center;font-size:18px;'>Data not available! </div>";die();}
		$main_array = array();
		$po_array = array();
		$cut_no_chk=array();
		$ccc="";
		$c=1;
		foreach ($sql_res as $row) 
		{
			if(!in_array($row[csf('cutting_no')], $cut_no_chk))
			{
				$ccc.=" ,".$row[csf('cutting_no')]."<br>";
				$cut_no_chk[$row[csf('cutting_no')]]=$row[csf('cutting_no')];
			}
			$po_array[$row[csf('po_id')]] = $row[csf('po_id')];
			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['working_company_id'] = $row[csf('working_company_id')];

			 

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['job_no'] = $row[csf('job_no')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['po_number'] = $row[csf('po_number')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['country_ship_date'] = $row[csf('country_ship_date')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['entry_date'] = $row[csf('entry_date')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['order_cut_no'] = $row[csf('order_cut_no')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['location'] = $row[csf('working_location_id')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['embel_name'] = $row[csf('embel_name')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['buyer_client'] = $row[csf('buyer_client')];

			$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['production_source'] = $row[csf('production_source')];

		
				if($c==1 && $row[csf('manual_challan_no')] !=""){
					$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['manual_challan_no'] .=$row[csf('manual_challan_no')];
					$c=2;
				}else if($c==2 && $row[csf('manual_challan_no')] !=""){
					$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['manual_challan_no'] .=",".$row[csf('manual_challan_no')];
				}
			
			

			if($row[csf('production_type')]==3){
				$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['receive_date'] = $row[csf('delivery_date')];
			}else{
				$main_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]][$row[csf('serving_company')]][$row[csf('embel_name')]]['issue_date'] = $row[csf('delivery_date')];
			}
		}
		//echo "$ccc";die;
		$poId = implode(",", $po_array);
		// echo "<pre>";
		// print_r($main_array);
		// echo "</pre>";
		$rowSpan = array();
		foreach ($main_array as $buyer_id => $buyer_data) 
		{
			foreach ($buyer_data as $style_no => $style_data) 
			{
				foreach ($style_data as $po_id => $po_data) 
				{
					foreach ($po_data as $country_id => $country_data) 
					{
						foreach ($country_data as $item_id => $item_data) 
						{
							foreach ($item_data as $color_id => $color_data) 
							{
								foreach ($color_data as $cutting_no => $cutting_data) 
								{
									foreach ($cutting_data as $emb_company_id => $row) 
									{
										$rowSpan[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id][$cutting_no][$emb_company_id]++;
									}	
								}
							}
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($rowSpan);
		// echo "</pre>";
		

		// ============================= store data in gbl table ==============================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=157");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 157, 1, $po_array, $empty_arr);//Po ID
		disconnect($con);
		// =================================== FOR ORDER QNTY ============================
		$sql_order = "SELECT c.po_break_down_id as po_id,c.color_number_id,c.country_id,c.item_number_id,c.order_quantity as order_qnty
				from wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
				where  c.po_break_down_id=tmp.ref_val and tmp.entry_form=157  and tmp.user_id=$user_id and tmp.ref_from=1 and c.status_active=1";
		// echo $sql_order;die;		
		$sql_order_res = sql_select($sql_order);
		$order_qnty_array = array();
		foreach ($sql_order_res as $row) 
		{
			$order_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] += $row[csf('order_qnty')];
		}
		
		// =========================================== FOR EMBLISMENT QNTY ==================================
		$prod_date = ($date_from != "" && $date_to != "") ? " and c.production_date between $txt_date_from and $txt_date_to" : "";
		$source_cond = ($source !=0) ? " and c.production_source=$source" : "";
		$emb_type_cond = ($emb_type != 0) ? " and c.embel_name in ($emb_type)" : " and c.embel_name in (1,2,3,4)";

		$prod_sql= "SELECT c.po_break_down_id as po_id, a.color_number_id,a.item_number_id, a.country_id,d.cut_no,c.embel_name,
			(CASE WHEN d.production_type ='1'  and c.production_type ='1' $prod_date THEN d.production_qnty ELSE 0 END) AS cutting_qnty,
				
			(CASE WHEN d.production_type ='2' $emb_type_cond $source_cond $prod_date THEN d.production_qnty ELSE 0 END) AS issue_qnty,
			(CASE WHEN d.production_type ='3' $emb_type_cond $source_cond $prod_date THEN d.production_qnty ELSE 0 END) AS recive_qnty,	

			(CASE WHEN d.production_type in (2,3) and c.production_type in (2,3)  and d.is_rescan =0 $prod_date THEN d.reject_qty ELSE 0 END) AS reject_qnty,
			(CASE WHEN d.production_type in (2,3) and c.production_type in (2,3)  and d.is_rescan =1 $prod_date THEN d.production_qnty ELSE 0 END) AS resc_qcpass			
				  
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,GBL_TEMP_ENGINE tmp 
			where  c.id=d.mst_id and d.color_size_break_down_id=a.id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=157  and tmp.user_id=$user_id and tmp.ref_from=1 and c.production_type in(1,2,3) and	c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";
			// echo $prod_sql;die;
			$prod_sql_res = sql_select($prod_sql);
			$prod_qnty_array = array();
			foreach ($prod_sql_res as $row) 
			{
				$prod_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cut_no')]][$row[csf('embel_name')]]['cutting_qnty'] += $row[csf('cutting_qnty')];

	        	$prod_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cut_no')]][$row[csf('embel_name')]]['issue_qnty'] += $row[csf('issue_qnty')];

	        	$prod_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cut_no')]][$row[csf('embel_name')]]['recive_qnty'] += $row[csf('recive_qnty')];

	        	$prod_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cut_no')]][$row[csf('embel_name')]]['reject_qty'] += $row[csf('reject_qnty')];

	        	$prod_qnty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cut_no')]][$row[csf('embel_name')]]['resc_qcpass'] += $row[csf('resc_qcpass')];
			}
			// echo "<pre>";
			// print_r($prod_qnty_array);
			// echo "</pre>";
			

		// =========================================== FOR CUT AND LAY QTY ====================================
		$cutting_sql="SELECT e.country_id,d.color_id,d.gmt_item_id, e.order_id AS po_id,e.size_qty AS cutting_qnty, c.cutting_no,c.entry_date,d.order_cut_no
        from ppl_cut_lay_mst c, ppl_cut_lay_dtls d,ppl_cut_lay_bundle e,GBL_TEMP_ENGINE tmp 
        where c.id=d.mst_id and c.id=e.mst_id and d.id=e.dtls_id and e.order_id=tmp.ref_val and tmp.entry_form=157  and tmp.user_id=$user_id and tmp.ref_from=1 and c.status_active=1 and d.status_active=1 and e.status_active=1";
		// echo $cutting_sql;die;
        $cutting_sql_res = sql_select($cutting_sql);
        $cutting_array = array();
		$cutting_info_array = array();
        $cutting_info_array2 = array();
        foreach ($cutting_sql_res as $row) 
        {
        	$cutting_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]] += $row[csf('cutting_qnty')];

			if($cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no']=="")
        		$cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no'] = $row[csf('order_cut_no')];
        	else
        	{
        		$cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no'] .= ','.$row[csf('order_cut_no')];
        	}

        	$cutting_info_array[$row[csf('cutting_no')]]['cutting_date'] = $row[csf('entry_date')];
        }	

        // =========================================== FOR CUTTING INFO ====================================
		/* $cutting_info_sql="SELECT e.country_id,d.color_id,d.gmt_item_id, e.order_id AS po_id,c.cutting_no,c.entry_date,d.order_cut_no
        from ppl_cut_lay_mst c, ppl_cut_lay_dtls d,ppl_cut_lay_bundle e
        where c.id=d.mst_id and c.id=e.mst_id and d.id=e.dtls_id and e.order_id in($poId) and c.status_active=1 and d.status_active=1 and e.status_active=1   group by e.country_id, d.color_id, d.gmt_item_id, e.order_id , c.cutting_no, c.entry_date, d.order_cut_no"; // echo $cutting_info_sql;
        $cutting_info_sql_res = sql_select($cutting_info_sql);
        $cutting_info_array = array();
        $cutting_info_array2 = array();
        foreach ($cutting_info_sql_res as $row) 
        {
        	if($cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no']=="")
        		$cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no'] = $row[csf('order_cut_no')];
        	else
        	{
        		$cutting_info_array2[$row[csf('cutting_no')]][$row[csf('po_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]]['order_cut_no'] .= ','.$row[csf('order_cut_no')];
        	}

        	$cutting_info_array[$row[csf('cutting_no')]]['cutting_date'] = $row[csf('entry_date')];
        } */
        // print_r($cutting_info_array);
		// =========================================== FOR CUTTING QC ====================================
		/* $cutting_qc_sql="SELECT a.style_ref_no,a.buyer_name,b.country_id,b.color_number_id,b.item_number_id,d.order_id as po_id, sum(d.qc_pass_qty) as qc_pass_qty,c.cutting_no
        from wo_po_details_master a, wo_po_color_size_breakdown b,pro_gmts_cutting_qc_mst c, pro_gmts_cutting_qc_dtls d 
        where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id=d.color_size_id and c.id=d.mst_id and d.color_id=b.color_number_id and d.order_id in($poId) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1
        group by a.style_ref_no,a.buyer_name,b.country_id,b.color_number_id,b.item_number_id,d.order_id,c.cutting_no";
        $cutting_qc_sql_res = sql_select($cutting_qc_sql);
        $cutting_qc_array = array();
        foreach ($cutting_qc_sql_res as $row) 
        {
        	$cutting_qc_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('cutting_no')]] += $row[csf('qc_pass_qty')];
        } */
 	 	// echo "<pre>";
		// print_r($cutting_qc_array);
		// echo "</pre>";
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=157");
		oci_commit($con);
		disconnect($con);
		
		ob_start();
		?>
			<style type="text/css">	            
	            .gd-color
	            {
					background: #f0f9ff; /* Old browsers */
					background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color2
				{
					background: rgb(247,251,252); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
					font-weight: bold;
				}
				.gd-color3
				{
					background: rgb(254,255,255); /* Old browsers */
					background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
					background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
					background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
					border: 1px solid #dccdcd;
					font-weight: bold;
				}

	        </style>
			<div style="width:2720px">
			<fieldset style="width:2720px;">	
				<table width="2710">
					<tr class="form_caption">
						<td colspan="23" align="center">Order Wise Embellishment Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="24" align="center"><? echo $company_library[$working_company]; ?></td>
					</tr>
					<?
					if(str_replace("'","",$txt_date_from) && str_replace("'","",$txt_date_from))
					{
						?>
						<tr class="form_caption">
						<td colspan="23" align="right" style="color:crimson;font-size: 18px;">WIP can be shown negative value in case of report generate by date range</td>
						</tr>

						<?
					}
					?>
				</table>
				<table class="rpt_table" width="2710" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th style="word-break: break-all;word-wrap: break-word;" width="30">SL</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="150">W. Company</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Location</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Buyer</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Buyer Client</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Style</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Job No</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Order No</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="130">Country</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="75">Country Ship Date</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="135">Gmts. Item</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="100">Color</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Order Qnty</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Cutting Date</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="110">Sys. Cut. No.</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="100">Challan No</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Order Cut. No.</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Cutting Qnty</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Cutting QC</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="130">Party Name</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Process</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Send Date</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Send</th>
						<th style="word-break: break-all;word-wrap: break-word;" width="80">Receive Date</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Receive</th>

	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">Reject</th>
	                    <th style="word-break: break-all;word-wrap: break-word;" width="80">WIP</th>
					</thead>
				</table>
				<div style="width:2730px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2710" cellpadding="0" cellspacing="0" border="1" id="table_body">
						<?
						$sl=1;

						$gr_order_qnty 		= 0;
						$gr_cutting_qnty 	= 0;
						$gr_cutting_qc 		= 0;
						$gr_send_qnty 		= 0;
						$gr_receive_qnty 	= 0;
						$gr_reject_qnty 	= 0;
						$gr_wip_qnty 		= 0;

						foreach ($main_array as $buyer_id => $buyer_data) 
						{
							$buyer_order_qnty 	= 0;
							$buyer_cutting_qnty = 0;
							$buyer_cutting_qc 	= 0;
							$buyer_send_qnty 	= 0;
							$buyer_receive_qnty = 0;
							$buyer_reject_qnty 	= 0;
							$buyer_wip_qnty 	= 0;

							foreach ($buyer_data as $style_no => $style_data) 
							{
								$style_order_qnty 	= 0;
								$style_cutting_qnty = 0;
								$style_cutting_qc 	= 0;
								$style_send_qnty 	= 0;
								$style_receive_qnty = 0;
								$style_reject_qnty 	= 0;
								$style_wip_qnty 	= 0;

								foreach ($style_data as $po_id => $po_data) 
								{
									$po_order_qnty 		= 0;
									$po_cutting_qnty 	= 0;
									$po_cutting_qc 		= 0;
									$po_send_qnty 		= 0;
									$po_receive_qnty 	= 0;
									$po_reject_qnty 	= 0;
									$po_wip_qnty 		= 0;

									foreach ($po_data as $country_id => $country_data) 
									{
										foreach ($country_data as $item_id => $item_data) 
										{
											foreach ($item_data as $color_id => $color_data) 
											{ 
												foreach ($color_data as $cutting_no => $cutting_data) 
												{
													$r=0;
													foreach ($cutting_data as $emb_company_id => $serving_com_data) 
													{	
														foreach ($serving_com_data as $embel_name => $row) 
														{
															
														$bgcolor = ($sl%2==0) ? "#e8f6ff" : "#ffffff";
														$order_cut_no = implode(",",array_unique(explode(",",$cutting_info_array2[$cutting_no][$po_id][$item_id][$color_id]['order_cut_no'])));

														$orderQnty = $order_qnty_array[$po_id][$country_id][$item_id][$color_id];
														$tempOrderQnty = $orderQnty;
														if($chk_arr[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id]=="")
														{
															$chk_arr[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id]=420;
														}
														else
														{
															$tempOrderQnty =0;
														}
														// $cuttingQnty = $prod_qnty_array[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id][$cutting_no]['cutting_qnty'];
														$cuttingQnty = $cutting_array[$po_id][$country_id][$item_id][$color_id][$cutting_no];
														$temp_cuttingQnty=$cuttingQnty;

														if($chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no][$order_cut_no]["lay"]=="")
														{
															$chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no][$order_cut_no]["lay"]=420;

														}
														else 
														{
															//echo $sl."<br>";
															$temp_cuttingQnty =0;
														}

														
														// $cuttingQcQnty = $cutting_qc_array[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id][$cutting_no];
														$cuttingQcQnty = $prod_qnty_array[$po_id][$country_id][$item_id][$color_id][$cutting_no][0]['cutting_qnty'];

														$temp_cuttingQcQnty=$cuttingQcQnty;
														if($chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no][$order_cut_no]["qc"]=="")
														{
															$chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no][$order_cut_no]["qc"]=420;

														}
														else 
														{
															$temp_cuttingQcQnty =0;
														}



														$issueQnty = $prod_qnty_array[$po_id][$country_id][$item_id][$color_id][$cutting_no][$embel_name]['issue_qnty'];

														$temp_issueQnty=$issueQnty;
														if($chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no]["issue_qnty"]=="")
														{
															$chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no]["issue_qnty"]=420;

														}
														else 
														{
															$temp_issueQnty=0;
														}

														$receiveQnty = $prod_qnty_array[$po_id][$country_id][$item_id][$color_id][$cutting_no][$embel_name]['recive_qnty'];

														$temp_receiveQnty=$receiveQnty;
														if($chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no]["receive_qnty"]=="")
														{
															$chk_arr2[$po_id][$country_id][$item_id][$color_id][$cutting_no]["receive_qnty"]=420;

														}
														else 
														{
															$temp_receiveQnty=0;
														}

														
														$reScQcpass = $prod_qnty_array[$po_id][$country_id][$item_id][$color_id][$cutting_no][$embel_name]['resc_qcpass'];

														$rejectQnty = $prod_qnty_array[$po_id][$country_id][$item_id][$color_id][$cutting_no][$embel_name]['reject_qty'];
														$rejectQnty = $rejectQnty - $reScQcpass;

														$cutting_date = $cutting_info_array[$cutting_no]['cutting_date'];
														
														 
														// check production. 
														if($issueQnty !=0 || $receiveQnty !=0){
														$gr_order_qnty 		+= $tempOrderQnty;
														$gr_cutting_qnty 	+= $temp_cuttingQnty;
														$gr_cutting_qc 		+= $temp_cuttingQcQnty;
														$gr_send_qnty 		+= $temp_issueQnty;
														$gr_receive_qnty 	+= $temp_receiveQnty;
														$gr_reject_qnty 	+= $rejectQnty;

														$buyer_order_qnty 	+= $tempOrderQnty;
														$buyer_cutting_qnty += $temp_cuttingQnty;
														$buyer_cutting_qc 	+= $temp_cuttingQcQnty;
														$buyer_send_qnty 	+= $temp_issueQnty;
														$buyer_receive_qnty += $temp_receiveQnty;
														$buyer_reject_qnty 	+= $rejectQnty;

														$style_order_qnty 	+= $tempOrderQnty;
														$style_cutting_qnty += $temp_cuttingQnty;
														$style_cutting_qc 	+= $temp_cuttingQcQnty;
														$style_send_qnty 	+= $temp_issueQnty;
														$style_receive_qnty += $temp_receiveQnty;
														$style_reject_qnty 	+= $rejectQnty;

														$po_order_qnty 		+= $tempOrderQnty;
														$po_cutting_qnty 	+= $temp_cuttingQnty;
														$po_cutting_qc 		+= $temp_cuttingQcQnty;
														$po_send_qnty 		+= $temp_issueQnty;
														$po_receive_qnty 	+= $temp_receiveQnty;
														$po_reject_qnty 	+= $rejectQnty;

														// $embel_name = $row['embel_name'];
														$rowspan="";
														$rowspan = $rowSpan[$buyer_id][$style_no][$po_id][$country_id][$item_id][$color_id][$cutting_no][$emb_company_id];
														$rowspan=1;
														switch ($embel_name) {
															case 1:
																$emb_txt = "Print";
																break;
															case 2:
																$emb_txt = "Embroydary";
																break;
															case 3:
																$emb_txt = "Wash";
																break;
															case 4:
																$emb_txt = "Special Work";
																break;		
															default:
																$emb_txt = "";
																break;
														}
														
														?>
														<tr  id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" bgcolor="<? echo $bgcolor; ?>">	
															<? //if($r==0){?>
															<td style="word-break: break-all;word-wrap: break-word;" align="left" width="30"><? echo $sl;?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="150"><? echo $company_library[$row['working_company_id']]; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $location_library[$row['location']];?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $buyer_library[$buyer_id]; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $buyer_library[$row['buyer_client']]; ?></td>
										                    <td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $style_no; ?></td>
										                    <td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $row['job_no']; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $row['po_number']; ?></td>
										                    <td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="130"><? echo $country_library[$country_id];?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="center" width="75"><? echo change_date_format($row['country_ship_date']); ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="135"><? echo $garments_item[$item_id];?></td>
										                    <td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="left" width="100"><? echo $color_library[$color_id];?></td>
										                    <td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="right" width="80"><? echo number_format($orderQnty,0); ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="center" width="80"><? echo change_date_format($cutting_date); ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="center" width="110"><? echo $cutting_no; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="center" width="110"><? echo $row['manual_challan_no']; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="center" width="80"><? echo $order_cut_no; ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="right" width="80"><? echo number_format($cuttingQnty,0); ?></td>
															<td rowspan="<? echo $rowspan;?>" style="word-break: break-all;word-wrap: break-word;" align="right" width="80"><? echo number_format($cuttingQcQnty,0); ?></td>

															<? // } $r++;?>

										                    <td style="word-break: break-all;word-wrap: break-word;" align="left" width="130">
										                    	<? 
										                    	if($row['production_source']==1)
										                    	{
										                    		echo $company_library[$emb_company_id];
										                    	}
										                    	else
										                    	{
										                    		echo $party_library[$emb_company_id];
										                    		// echo $company_library[$row['embel_com']];
										                    	}
										                    	$sources=$row['production_source'];
										                    	?>	
																									                    		
										                    </td>
										                    <td style="word-break: break-all;word-wrap: break-word;" align="left" width="80"><? echo $emb_txt; ?></td>
															<td style="word-break: break-all;word-wrap: break-word;" align="left" width="80"><? echo $row['issue_date']; ?></td>
										                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80">
										                    	<a href="javascript:void(0)" onClick="open_emb_popup('<? echo $po_id."_".$country_id."_".$item_id."_".$color_id; ?>_<? echo $cutting_no;?>_<? echo $source;?>_<? echo $date_from;?>_<? echo $date_to;?>_2_<? echo $embel_name.'_'.$sources;?>');">
										                    		<? echo number_format($issueQnty,0); ?>
										                    		</a>
										                    	</td>
															<td style="word-break: break-all;word-wrap: break-word;" align="left" width="80"><? echo $row['receive_date']; ?></td>
										                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80">
										                    	<a href="javascript:void(0)" onClick="open_emb_popup('<? echo $po_id."_".$country_id."_".$item_id."_".$color_id; ?>_<? echo $cutting_no;?>_<? echo $source;?>_<? echo $date_from;?>_<? echo $date_to;?>_3_<? echo $embel_name.'_'.$sources;?>');">
										                    		<? echo number_format($receiveQnty,0); ?>
										                    	</a>
										                    </td>
										                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80">
										                    	<? echo number_format($rejectQnty,0); ?>                  		
										                    	</td>
										                    <td style="word-break: break-all;word-wrap: break-word;" align="right" width="80">
										                    	<? 
										                    	echo number_format(($issueQnty-($receiveQnty+$rejectQnty)),0); 
										                    	?>										                    		
										                    	</td>
														</tr>
														<?
														$sl++;
														}
													}
													}
												}
											}
										}
									}
									?>
									<tr class="gd-color">
										<td width="30"></td>
										<td width="150"></td>
										<td width="130"></td>
										<td width="130"></td>
										<td width="130"></td>
					                    <td width="130"></td>
					                    <td width="130"></td>
										<td width="130"></td>
					                    <td width="130"></td>
										<td width="75"></td>
										<td width="135"></td>
					                    <td width="100" align="right">PO Wise Total :</td>
					                    <td width="80" align="right"><? echo number_format($po_order_qnty,0); ?></td>
										<td width="80"></td>
										<td width="110"></td>
										<td width="110"></td>
										<td width="80"></td>
										<td width="80" align="right"><? echo number_format($po_cutting_qnty,0); ?></td>
										<td width="80" align="right"><? echo number_format($po_cutting_qc,0); ?></td>
					                    <td width="130"></td>
					                    <td width="80"></td>
										<td width="80"></td>
					                    <td width="80" align="right"><? echo number_format($po_send_qnty,0); ?></td>
										<td width="80"></td>
					                    <td width="80" align="right"><? echo number_format($po_receive_qnty,0); ?></td>
					                    <td width="80" align="right"><? echo number_format($po_reject_qnty,0); ?></td>
					                    <td width="80" align="right"><? //echo number_format($po_wip_qnty,0); ?></td>
				                	</tr>
									<?
								}
								?>
								<tr class="gd-color2">
									<td width="30"></td>
									<td width="150"></td>
									<td width="130"></td>
									<td width="130"></td>
									<td width="130"></td>
				                    <td width="130"></td>
				                    <td width="130"></td>
									<td width="130"></td>
				                    <td width="130"></td>
									<td width="75"></td>
									<td width="135"></td>
				                    <td width="100" align="right">Style Wise Total :</td>
				                    <td width="80" align="right"><? echo number_format($style_order_qnty,0); ?></td>
									<td width="80"></td>
									<td width="110"></td>
									<td width="110"></td>
									<td width="80"></td>
									<td width="80" align="right"><? echo number_format($style_cutting_qnty,0); ?></td>
									<td width="80" align="right"><? echo number_format($style_cutting_qc,0); ?></td>
				                    <td width="130"></td>
				                    <td width="80"></td>
									<td width="80"></td>
				                    <td width="80" align="right"><? echo number_format($style_send_qnty,0); ?></td>
									<td width="80"></td>
				                    <td width="80" align="right"><? echo number_format($style_receive_qnty,0); ?></td>
				                    <td width="80" align="right"><? echo number_format($style_reject_qnty,0); ?></td>
				                    <td width="80" align="right"><? //echo number_format($style_wip_qnty,0); ?></td>
			                	</tr>
								<?
							}
							?>
							<tr class="gd-color3">
								<td width="30"></td>
								<td width="150"></td>
								<td width="130"></td>
								<td width="130"></td>
								<td width="130"></td>
			                    <td width="130"></td>
			                    <td width="130"></td>
								<td width="130"></td>
			                    <td width="130"></td>
								<td width="75"></td>
								<td width="135"></td>
			                    <td width="100" align="right">Buyer Wise Total :</td>
			                    <td width="80" align="right"><? echo number_format($buyer_order_qnty,0); ?></td>
								<td width="80"></td>
								<td width="110"></td>
								<td width="110"></td>
								<td width="80"></td>
								<td width="80" align="right"><? echo number_format($buyer_cutting_qnty,0); ?></td>
								<td width="80" align="right"><? echo number_format($buyer_cutting_qc,0); ?></td>
			                    <td width="130"></td>
			                    <td width="80"></td>
								<td width="80"></td>
			                    <td width="80" align="right"><? echo number_format($buyer_send_qnty,0); ?></td>
								<td width="80"></td>
			                    <td width="80" align="right"><? echo number_format($buyer_receive_qnty,0); ?></td>
			                    <td width="80" align="right"><? echo number_format($buyer_reject_qnty,0); ?></td>
			                    <td width="80" align="right"><? //echo number_format($buyer_wip_qnty,0); ?></td>
		                	</tr>
							<?
						}
						?>									
					</table>
				</div>
				<div style="width:2710px;">
					<table class="rpt_table" width="2710" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30">.</th>
							<th width="150"></th>
							<th width="130"></th>
							<th width="130"></th>
		                    <th width="130"></th>
		                    <th width="130"></th>
		                    <th width="130"></th>
							<th width="130"></th>
		                    <th width="130"></th>
							<th width="75"></th>
							<th width="135"></th>
		                    <th width="100">Grand Total :</th>
		                    <th width="80" align="right"><? echo number_format($gr_order_qnty,0);?></th>
							<th width="80"></th>
							<th width="110"></th>
							<th width="110"></th>
							<th width="80"></th>
							<th width="80" align="right"><? echo number_format($gr_cutting_qnty,0);?></th>
							<th width="80" align="right"><? echo number_format($gr_cutting_qc,0);?></th>
		                    <th width="130"></th>
		                    <th width="80"></th>
							<th width="80"></th>
		                    <th width="80" align="right"><? echo number_format($gr_send_qnty,0);?></th>
							<th width="80"></th>
		                    <th width="80" align="right"><? echo number_format($gr_receive_qnty,0);?></th>
		                    <th width="80" align="right"><? echo number_format($gr_reject_qnty,0);?></th>
		                    <th width="80" align="right"><? //echo number_format($gr_wip_qnty,0);?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
		<?
	}	
	
	foreach (glob("*.xls") as $filename) 
	{
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();	
}
disconnect($con);

if($action=="open_emb_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	// po,country,item,color,cutting,source,date from, date to , production type
	// print_r($data);
	$ex_data = explode("_", $data);
	$po = $ex_data[0];
	$country = $ex_data[1];
	$item = $ex_data[2];
	$color = $ex_data[3];
	$cut_no = $ex_data[4];
	$source = $ex_data[5];
	$date_from = $ex_data[6];
	$date_to = $ex_data[7];
	$prod_type = $ex_data[8];
	$embel_name = $ex_data[9];
	$production_source = $ex_data[10];

	$size_Arr_library=return_library_array( "SELECT id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("SELECT size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po order by size_order","size_number_id","size_number_id");
	// ============================================ getting size wise order qnty ==================================================
	$sql_order_size= "SELECT c.size_number_id, c.order_quantity as order_quantity
		from wo_po_color_size_breakdown c 
		where c.status_active=1 and c.is_deleted=0 and c.po_break_down_id=$po and c.item_number_id=$item and c.country_id=$country and c.color_number_id=$color  order by c.size_order";
	$sql_order_size_res = sql_select($sql_order_size);	 
	foreach($sql_order_size_res as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('order_quantity')];
	}
	// ======================================== getting cutting wise size qnty ========================================
	$prod_cond = "";
	if($source) $prod_cond="and a.production_source=$source";else $prod_cond="";
	if($date_from !="" && $date_to !="") $prod_cond="and a.production_date between '$date_from' and '$date_to' ";else $prod_cond="";

	$color_size_sql="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty,c.sys_number,d.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_color_size_breakdown d
	where a.id=b.mst_id and c.id=b.delivery_mst_id and b.color_size_break_down_id=d.id and a.production_type=$prod_type and a.embel_name=$embel_name and a.production_source='$production_source' $prod_cond and a.po_break_down_id=$po and a.item_number_id=$item and b.cut_no='$cut_no' and d.country_id=$country and d.color_number_id=$color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by b.color_size_break_down_id,c.sys_number,d.size_number_id order by c.sys_number";
	$color_size_sql_res = sql_select($color_size_sql);
	$color_size_data=array(); 
	$allcolor_id_arr=array();
	$prod_color_size_data=array();
	foreach($color_size_sql_res as $row)
	{
		$allcolor_id_arr[$row[csf("sys_number")]]=$row[csf("sys_number")];
		$color_size_data[$row[csf("sys_number")]]=$row[csf("size_number_id")];
		$prod_color_size_data[$row[csf("sys_number")]][$row[csf("size_number_id")]] += $row[csf('production_qnty')];
	}

	$table_width=(200+(count($sizearr_order)*60));
	?>
	<div style="width:<? echo $table_width; ?>px;margin: 10px auto;" align="center" id="details_reports">
		<? $txt = ($prod_type==2) ? "Print Issue": "Print Receive";?>
		<div style="font-size: 18px;font-weight: bold;"><? echo $txt;?></div>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="140">Size</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th align="center" width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th align="center" width="60" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <?
                    	$sl=1;
                    	$g_total = 0;
	                    $v_total_arr = array();
                     	foreach ($color_size_data as $key => $value) 
	                    {
	                    	?>
	                    	<tr bgcolor="<? echo ($sl%2==0) ? "#c2dcff" : "#f9f9f9";?>">
	                    		<td><? echo $key;?></td>
	                    	<?
	                    	$h_total = 0;
	                    	foreach ($sizearr_order as $size_id) 
	                    	{
	                    		?>
	                    			<td align="right">
	                    				<? 
	                    				$h_total+=$prod_color_size_data[$key][$size_id];
	                    				$v_total_arr[$size_id] += $prod_color_size_data[$key][$size_id];
	                    				echo $prod_color_size_data[$key][$size_id];
	                    				$g_total +=$prod_color_size_data[$key][$size_id];
	                    				?>
	                    			</td>
	                    		<?
	                    	}
	                    	?>
	                    	<td align="right"><? echo $h_total; ?></td>
	                    	</tr>
	                    	<? 
	                    } 
                    ?>
                    <tr>
                    	<th>Total</th>
                    	<?
                    	foreach ($sizearr_order as $size_id) 
                    	{
                    		?>
                    			<td align="right"><? echo $v_total_arr[$size_id]; ?></td>
                    		<?                    		
                    	}
                    	?>
                    	<td align="right"><? echo $g_total; ?></td>
                    </tr>
                    	
                    
                    
                </tbody>
            </table>
    	</div>
    	<?
}
?>