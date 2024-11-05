<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$defalt_date_format="0000-00-00";
}
else
{
	$defalt_date_format="";
}



//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$season_arr = return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );

$party_library=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
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
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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

		function showReport(){
			if((form_validation('cbo_company_mst','Company Name')==false) || (form_validation('txt_style_ref','Style')==false && form_validation('txt_order_no','Order')==false && form_validation('txt_file_no','File')==false && (form_validation('txt_date_from','From Date')==false && form_validation('txt_date_to','To Date')==false)))
			{			
				return;
			}
			else{
			show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_job_list_view', 'search_div', 'order_wise_emb_report_controller', 'setFilterGrid(\'list_view\',-1)');
			}
		}
		
		
    </script>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="780" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                	 
                            <th width="100" class="must_entry_caption">Company Name</th>
                            <th width="100" class="">Buyer Name</th>
                            <th width="100">Style</th>
                            <th width="100">PO</th>
                            <th width="100" >File</th>
                            <th width="180">Pub. Ship Date</th>
                            <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_id">
                        <input type="hidden" id="selected_name"> 
                            <?
                             echo create_drop_down( "cbo_company_mst", 100, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_name,"load_drop_down( 'order_wise_emb_report_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );
                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?>
                        </td>
                        <td>
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_style_ref" id="txt_style_ref" />
                        </td>
                        <td>
                        	<input type="text" style="width:80px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
                        </td>
                        <td>
                        	<input type="text" style="width:80px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />
                        </td>
                        
                        <td>
                        	 <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >&nbsp; To
                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date" >

                        </td>
                        
                                                
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="showReport()" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                    
                </table>
            </form>
        </div>
        <div id="search_div"></div>
        <script type="text/javascript">
        //document.getElementById('cbo_company_mst').value='<? echo $company_name;?>';
        </script>
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
	$str_cond.=($data[2])? " and a.style_ref_no like'%$data[2]%' " : "";
	$str_cond.=($data[3])? " and b.po_number like'%$data[3]%' " : "";
	$str_cond.=($data[4])? " and b.file_no like'%$data[4]%' " : "";
	
	$start_date=str_replace("'","",$data[5]);
	$end_date=str_replace("'","",$data[6]);
	
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'dd-mm-yyyy','-',1);
		$end_date=change_date_format($end_date,'dd-mm-yyyy','-',1);
	}	 
	$str_cond.=($start_date && $end_date)? " and b.pub_shipment_date between '$start_date' and '$end_date' " : "";

	 
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	
		$sql= "SELECT a.id,b.po_number,b.file_no,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name,b.JOB_NO_MST from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number ,a.job_no_prefix_num,a.style_ref_no,b.file_no,a.company_name,a.buyer_name,b.JOB_NO_MST order by a.id desc";
		  //echo $sql;
		echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No,File No", "120,100,100,100,140,100","700","290",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,JOB_NO_MST,style_ref_no,po_number,file_no", "",'','0,0,0,0,0') ;

	
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
                               echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'order_wise_emb_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_job_year').value, 'create_order_no_search_list_view', 'search_div', 'order_wise_emb_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$company_name 		= str_replace("'", "", $cbo_company_name);
	$buyer_name 		= str_replace("'", "", $cbo_buyer_name);
	$emb_type 			= str_replace("'", "", $cbo_emb_type);
	$source 			= str_replace("'", "", $cbo_source);
	$party_id 			= str_replace("'", "", $cbo_party_name);
	$job_no 			= str_replace("'", "", $txt_job_no);
	$date_from 			= str_replace("'", "", $txt_date_from);
	$date_to 			= str_replace("'", "", $txt_date_to);
	$internal_ref 			= str_replace("'", "", $txt_inter_ref);

		 
	$sql_cond = "";
	$sql_cond .= ($company_name != 0) ? " and a.company_name =$company_name" : "";
	$sql_cond .= ($buyer_name != 0) ? " and a.buyer_name in ($buyer_name)" : "";
	$sql_cond .= ($source != 0) ? " and c.production_source=$source" : "";
	$sql_cond .= ($party_id != 0) ? " and c.serving_company in ($party_id)" : "";
	$sql_cond .= ($job_no != "") ? " and a.job_no_prefix_num =$job_no" : "";
	$sql_cond .= ($internal_ref != "") ? " and b.grouping ='$internal_ref'" : "";

	$sql_cond .= ($emb_type != 0) ? " and c.embel_name in ($emb_type)" : "";
	$sql_cond .= ($date_from != "" && $date_to != "") ? " and c.production_date between $txt_date_from and $txt_date_to" : "";
	if($source!=0){$sql_cond .= " and c.production_source=$source";}

	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id =31 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	//print_r($format_ids);
	$row_id=$format_ids[0];

	//print_r($format_ids);
	
	/* ==================================================================================== /
	/ 										main query										/
	/ ===================================================================================  */
	$sql = "SELECT a.job_no, a.style_ref_no, a.buyer_name, a.client_id as buyer_client, a.company_name, b.pub_shipment_date, b.pub_shipment_date, b.id as po_id, b.po_number, b.po_quantity, b.grouping, c.embel_name, c.serving_company, c.production_type, c.production_quantity, c.production_source
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c
			where a.id=b.job_id  and  b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $sql_cond and c.production_type in(2,3)";				
	// echo $sql;die();		
	$sql_res = sql_select($sql);		
	if(count($sql_res) == 0){ echo "<div style='color:red;text-align:center;font-size:18px;'>Data not available! </div>";die();}
	$data_array = array();
	$po_array = array();
	$po_qty_array = array();
	$po_chk = array();
	foreach ($sql_res as $row) 
	{
		$po_array[$row[csf('po_id')]] = $row[csf('po_id')]; 

		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['job_no'] = $row[csf('job_no')];
		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['buyer_name'] = $row[csf('buyer_name')];

		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['po_number'] = $row[csf('po_number')];
		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['inter_ref'] = $row[csf('grouping')];

		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];

		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];

		$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['production_source'] = $row[csf('production_source')];

		if($row[csf('production_type')]==2)
		{
			$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['issue_qnty'] += $row[csf('production_quantity')];
		}
		else
		{
			$data_array[$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('po_id')]][$row[csf('serving_company')]]['recive_qnty'] += $row[csf('production_quantity')];
		}

		if(!in_array($row[csf('po_id')], $po_chk))
		{
			$po_qty_array[$row[csf('po_id')]] += $row[csf('po_quantity')];
			$po_chk[$row[csf('po_id')]] = $row[csf('po_id')];
		}

		
	}
	//echo "$ccc";die;
	$poIds = implode(",", $po_array);
	//echo "<pre>"; print_r($data_array); echo "</pre>";

	/* ==================================================================================== /
	/ 										costing data									/
	/ ===================================================================================  */
	$po_id_cond_in=where_con_using_array($po_array,0,'b.po_break_down_id');
	
	$sql = "SELECT b.po_break_down_id as PO_ID,b.RATE, b.AMOUNT from WO_PRE_COST_EMBE_COST_DTLS a, WO_PRE_COS_EMB_CO_AVG_CON_DTLS b where a.id=b.pre_cost_emb_cost_dtls_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_id_cond_in";
	// echo $sql;die();
	$res = sql_select($sql);
	$costing_array = array();
	foreach ($res as $val) 
	{
		$costing_array[$val['PO_ID']]['rate'] += $val['RATE']; 
		$costing_array[$val['PO_ID']]['amount'] += $val['AMOUNT']; 
	}

	/* ==================================================================================== /
	/ 										EB WO Data										/
	/ ===================================================================================  */
	$sql = "SELECT a.ID,a.BOOKING_NO,a.COMPANY_ID,a.BUYER_ID,c.SEASON_BUYER_WISE,a.JOB_NO,a.SUPPLIER_ID, a.BOOKING_DATE, a.DELIVERY_DATE, a.CURRENCY_ID, a.PAY_MODE, a.SOURCE, a.EXCHANGE_RATE,b.ID as DTLS_ID,b.PO_BREAK_DOWN_ID as PO_ID,a.BOOKING_NO_PREFIX_NUM, b.AMOUNT from WO_BOOKING_MST a, WO_BOOKING_DTLS b,wo_po_details_master c where a.booking_no=b.booking_no  AND a.JOB_NO = c.JOB_NO and a.status_active=1 and b.status_active=1 and A.JOB_NO is not null and a.is_deleted=0  and b.is_deleted=0  and b.booking_type=6 and b.is_short=2 $po_id_cond_in";
	//echo $sql;die();
	$res = sql_select($sql);
	$eb_wo_data_array = array();
	$eb_wo_id_array = array();
	$eb_wo_dtl_id_array = array();
	foreach ($res as $val) 
	{
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['wo_no'] = $val['BOOKING_NO_PREFIX_NUM']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['supplier_id'] = $supplier_library[$val['SUPPLIER_ID']]; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['supplier_id'] =$val['SUPPLIER_ID']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['booking_no'] = $val['BOOKING_NO']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['season_buyer_wise'] = $season_arr[$val['SEASON_BUYER_WISE']]; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['company_id'] = $val['COMPANY_ID']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['buyer_id'] = $val['BUYER_ID']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['booking_date'] = $val['BOOKING_DATE']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['delivery_date'] = $val['DELIVERY_DATE']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['currency_id'] = $val['CURRENCY_ID']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['pay_mode'] = $val['PAY_MODE']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['job_no'] = $val['JOB_NO']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['source'] = $val['SOURCE']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['exchange_rate'] = $val['EXCHANGE_RATE']; 
		$eb_wo_data_array[$val['PO_ID']][$val['SUPPLIER_ID']]['amount'] += $val['AMOUNT']; 
		$eb_wo_id_array[$val['ID']] = $val['ID'];
		$eb_wo_dtl_id_array[$val['DTLS_ID']] = $val['PO_ID'];
	}
// 	echo "<pre>";
// print_r($eb_wo_data_array); 
//   echo "</pre>";die();
	$eb_wo_id = implode(",", $eb_wo_id_array);

	/* ==================================================================================== /
	/ 										PI Data 										/
	/ ===================================================================================  */
	$wo_id_cond_in=where_con_using_array($eb_wo_id_array,0,'b.work_order_id');
	$sql = "SELECT a.SUPPLIER_ID,a.id as PI_ID,a.PI_NUMBER, b.AMOUNT,b.WORK_ORDER_DTLS_ID from COM_PI_MASTER_DETAILS a, COM_PI_ITEM_DETAILS b where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0   $wo_id_cond_in";
	// echo $sql;die();
	$res = sql_select($sql);
	$pi_data_array = array();
	$pi_id_array = array();
	$pi_wise_po_array = array();
	foreach ($res as $val) 
	{
		$pi_data_array[$eb_wo_dtl_id_array[$val['WORK_ORDER_DTLS_ID']]][$val['SUPPLIER_ID']]['pi_number'] = $val['PI_NUMBER']; 
		$pi_data_array[$eb_wo_dtl_id_array[$val['WORK_ORDER_DTLS_ID']]][$val['SUPPLIER_ID']]['pi_id'] = $val['PI_ID']; 
		$pi_data_array[$eb_wo_dtl_id_array[$val['WORK_ORDER_DTLS_ID']]][$val['SUPPLIER_ID']]['amount'] += $val['AMOUNT']; 
		$pi_id_array[$val['PI_ID']] = $val['PI_ID'];
		$pi_wise_po_array[$val['PI_ID']] = $eb_wo_dtl_id_array[$val['WORK_ORDER_DTLS_ID']];
	}
	// echo "<pre>";print_r($pi_wise_po_array);
	$pi_ids = implode(",", $pi_id_array);
	/* ==================================================================================== /
	/ 										BTB/LC Data 									/
	/ ===================================================================================  */
	$po_id_cond_in=where_con_using_array($pi_id_array,0,'c.pi_id');
	$sql = "SELECT a.SUPPLIER_ID,a.LC_NUMBER, b.CURRENT_DISTRIBUTION,c.PI_ID from com_btb_lc_master_details a, com_btb_export_lc_attachment b,com_btb_lc_pi c where a.id=b.import_mst_id and a.id=c.com_btb_lc_master_details_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_id_cond_in";
	// echo $sql;die();
	$res = sql_select($sql);
	$btb_data_array = array();
	foreach ($res as $val) 
	{
		$btb_data_array[$pi_wise_po_array[$val['PI_ID']]][$val['SUPPLIER_ID']]['lc_number'] = $val['LC_NUMBER']; 
		$btb_data_array[$pi_wise_po_array[$val['PI_ID']]][$val['SUPPLIER_ID']]['amount'] += $val['CURRENT_DISTRIBUTION']; 
	}
	// echo "<pre>";print_r($btb_data_array);
 
	$rowspan_arr = array();
	foreach ($pi_data_array as $po_id => $po_data) 
	{
		foreach ($po_data as $wo_com_id => $row) 
		{
			$rowspan_arr[$row['pi_id']]++;
		}
	}
	// echo "<pre>";print_r($rowspan_arr);
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
		<div style="width:1730px">
		<fieldset style="width:1730px;">	
			<table width="1710">
				<tr class="form_caption">
					<td colspan="18" align="center" style="font-size:20px">Order Wise Embellishment Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="18" align="center" style="font-size:18px"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="1800" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="100">Style</th>
					<th width="100">Order No</th>
                    <th width="100">Internal Ref.</th>
                    <th width="80">Order Qnty</th>
					<th width="80">Max Ship Date</th>
                    <th width="100">Service Company</th>
                    <th width="80">Issue Qty</th>
                    <th width="80">Receive Qty</th>
					<th width="80">Balance Qty</th>
					<th width="80">Costing Rate</th>
					<th width="80">Costing Value</th>
					<th width="100">WO Number</th>
					<th width="100">WO Value</th>
					<th width="100">PI No</th>
                    <th width="100">PI Value</th>
                    <th width="100">BTB LC No</th>
                    <th width="100">BTB LC Value</th>
				</thead>
			</table>
			<div style="width:1820px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1800" cellpadding="0" cellspacing="0" border="1" id="table_body">
					<?
					$sl=1;
					$gr_order_qnty 	= 0;
					$gr_issue_qty 	= 0;
					$gr_receive_qnty= 0;
					$gr_costing_val = 0;
					$gr_wo_val 		= 0;
					$gr_pi_value 	= 0;
					$gr_lc_value 	= 0;

					foreach ($data_array as $buyer_id => $buyer_data) 
					{   
						foreach ($buyer_data as $style_no => $style_data) 
						{
							foreach ($style_data as $po_id => $po_data) 
							{
								foreach ($po_data as $wo_com_id => $row) 
								{														
									$bgcolor = ($sl%2==0) ? "#e8f6ff" : "#ffffff";
									$source = $row['production_source'];

									$eb_rate = $eb_wo_data_array[$po_id][$wo_com_id]['wo_no'];
									$supplier_id = $eb_wo_data_array[$po_id][$wo_com_id]['supplier_id'];
									$hidden_supplier_id = $eb_wo_data_array[$po_id][$wo_com_id]['supplier_id'];
									//$supplier_id = $eb_wo_data_array[$po_id][$wo_com_id]['supplier_id'];
									//echo $supplier_id;die;
									$booking_no = $eb_wo_data_array[$po_id][$wo_com_id]['booking_no'];
									$booking_date = $eb_wo_data_array[$po_id][$wo_com_id]['booking_date'];
									$delivery_date = $eb_wo_data_array[$po_id][$wo_com_id]['delivery_date'];
									$currency_id = $eb_wo_data_array[$po_id][$wo_com_id]['currency_id'];
									$pay_mode = $eb_wo_data_array[$po_id][$wo_com_id]['pay_mode'];
									$seasson = $eb_wo_data_array[$po_id][$wo_com_id]['season_buyer_wise'];
									$source = $eb_wo_data_array[$po_id][$wo_com_id]['source'];
									$exchange_rate = $eb_wo_data_array[$po_id][$wo_com_id]['exchange_rate'];
									$eb_amount = $eb_wo_data_array[$po_id][$wo_com_id]['amount'];
									$company_id = $eb_wo_data_array[$po_id][$wo_com_id]['company_id
									'];
									
									$job_no = $eb_wo_data_array[$po_id][$wo_com_id]['job_no'];
									$company_id = $eb_wo_data_array[$po_id][$wo_com_id]['company_id'];
									//echo $company_id;
									$buyer_id = $eb_wo_data_array[$po_id][$wo_com_id]['buyer_id'];

									$pi_number = $pi_data_array[$po_id][$wo_com_id]['pi_number'];
									$pi_amount = $pi_data_array[$po_id][$wo_com_id]['amount'];

									$lc_number = $btb_data_array[$po_id][$wo_com_id]['lc_number'];
									$btb_amount = $btb_data_array[$po_id][$wo_com_id]['amount'];
									$blance_qty = $row['issue_qnty'] - $row['recive_qnty'];

									if($row_id==86) $action='show_trim_booking_report';
								   else if($row_id==87)$action='show_trim_booking_report1';
								   else if($row_id==88)$action='show_trim_booking_report2';
								   else if($row_id==89)$action='show_trim_booking_report_urmi';

								   $function = "generate_worder_report('".$action."','".$booking_no."','".$job_no."',".$company_id.",".$buyer_id.",'".$booking_date."','".$delivery_date."',".$currency_id.", '".$supplier_id."', '".$hidden_supplier_id."', ".$pay_mode.", ".$exchange_rate.", ".$source.", '".$seasson."');";

							
									if($source==1)
									{
										$supplier =  $company_library[$wo_com_id];
									}
									else
									{
										$supplier =  $supplier_library[$wo_com_id];
									}

									// echo $po_id."**".$wo_com_id;
									?>
									<tr  id="tr_<? echo $sl; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" bgcolor="<? echo $bgcolor; ?>">	
										<? //if($r==0){?>
										<td style="word-break: break-all;word-wrap: break-word;" align="left" width="30"><? echo $sl;?></td>
										<td align="left" width="100"><? echo $buyer_library[$buyer_id]; ?></td>
					                    <td align="left" width="100"><? echo $row['job_no']; ?></td>
					                    <td align="left" width="100"><? echo wordwrap($style_no,14,"<br>", true); ?></td>
                                        <td align="left" width="100"><p><? echo $row['po_number']; ?></p></td>
										<td align="left" width="100"><p><? echo $row['inter_ref']; ?></p></td>
					                    <td align="right" width="80"><? echo number_format($po_qty_array[$po_id],0); ?></td>
										<td align="center" width="80"><? echo change_date_format($row['pub_shipment_date']); ?></td>
										<td align="left" width="100"><? echo $supplier; ?></td>
										<td align="right" width="80">											
											<a href="javascript:void(0)" onClick="open_emb_popup('<? echo $company_name."__".$po_id."__".$wo_com_id."__".$source."__".$date_from."__".$date_to."__2"?>')">
												<? echo number_format($row['issue_qnty'],0);?>
											</a>
										</td>
										<td align="right" width="80">
											<a href="javascript:void(0)" onClick="open_emb_popup('<? echo $company_name."__".$po_id."__".$wo_com_id."__".$source."__".$date_from."__".$date_to."__3"?>')">
												<? echo number_format($row['recive_qnty'],0); ?>
											</a>
										</td>
										<td align="right" width="80">
											<a href="javascript:void(0)" onClick="open_emb_balance_popup('<? echo $company_name."__".$po_id."__".$wo_com_id."__".$source."__".$date_from."__".$date_to."__"?>')">
												<? echo $blance_qty ; ?>
											</a>
										</td>
					                    <td align="right" width="80"><? echo number_format($costing_array[$po_id]['rate'],2);?></td>
										<td align="right" width="80"><? echo number_format($costing_array[$po_id]['amount'],2);?></td>
					                    
										<td align="right" width="100"><a href='##' onClick="<?=$function; ?>"><?=$eb_rate; ?></a></td>
										<td align="right" width="100"><? echo number_format($eb_amount,2); ?></td>
										<td align="left" width="100"><? echo $pi_number; ?></td>
										<td align="right" width="100"><? echo number_format($pi_amount,2); ?></td>
										<td align="center" width="100"><? echo $lc_number; ?></td>
										<td align="right" width="100"><? echo number_format($btb_amount,2); ?></td>
									</tr>
									<?
									$sl++;									
									$gr_order_qnty 	+= $po_qty_array[$po_id];
									$gr_issue_qty 	+= $row['issue_qnty'];
									$gr_receive_qnty+= $row['recive_qnty'];
									$gr_blance_qnty+= $row['issue_qnty'] - $row['recive_qnty'];
									$gr_costing_val += 0;
									$gr_pi_value 	+= 0;
									$gr_lc_value 	+= 0;
								}
							}
						}
					}
					?>									
				</table>
			</div>
			<div style="width:1820px;">
				<table class="rpt_table" width="1800" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="100"></th>
	                    <th width="100">Grand Total :</th>
	                    <th width="80" align="right"><? echo number_format($gr_order_qnty,0);?></th>
	                    <th width="80"></th>
						<th width="100"></th>
	                    <th width="80" align="right"><? echo number_format($gr_issue_qty,0);?></th>
						<th width="80" align="right"><? echo number_format($gr_receive_qnty,0);?></th>
						<th width="80" align="right"><? echo number_format($gr_blance_qnty,0);?></th>
						<th width="80"></th>
	                    <th width="80" align="right"><? //echo number_format($gr_costing_val,2);?></th>
	                    <th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100" align="right"><? //echo number_format($gr_pi_value,2);?></th>
						<th width="100"></th>
						<th width="100" align="right"><? //echo number_format($gr_lc_value,2);?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<?
		
	
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
	disconnect($con);
	exit();	
}

if($action=="open_emb_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	// po,country,item,color,cutting,source,date from, date to , production type
	// print_r($data);
	$ex_data = explode("__", $data);
	$company_name = $ex_data[0];
	$po = $ex_data[1];
	$working_company = $ex_data[2];
	$source = $ex_data[3];
	$date_from = $ex_data[4];
	$date_to = $ex_data[5];
	$prod_type = $ex_data[6];
	$print_report_format = 0;
	if($prod_type==2)
	{		
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =$company_name and module_id=7 and report_id=21 and is_deleted=0 and status_active=1");
	}
	else
	{
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =$company_name and module_id=7 and report_id=123 and is_deleted=0 and status_active=1");
	}
	$format_ids=explode(",",$print_report_format);
	// print_r($format_ids);die();
	$size_Arr_library=return_library_array( "SELECT id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("SELECT size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po order by size_order","size_number_id","size_number_id");
	// ============================================ getting size wise order qnty ==================================================
	$sql_order_size= "SELECT c.size_number_id, c.order_quantity as order_quantity
		from wo_po_color_size_breakdown c 
		where c.status_active=1 and c.is_deleted=0 and c.po_break_down_id=$po order by c.size_order";
	$sql_order_size_res = sql_select($sql_order_size);	 
	foreach($sql_order_size_res as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('order_quantity')];
	}
	// ======================================== getting cutting wise size qnty ========================================
	$prod_cond = "";
	if($source) $prod_cond .=" and a.production_source=$source";else $prod_cond=" ";
	if($date_from !="" && $date_to !="") $prod_cond .=" and a.production_date between '$date_from' and '$date_to' ";else $prod_cond="";

	$sql="SELECT a.id as CHALLAN_NO,a.SERVING_COMPANY,a.PRODUCTION_SOURCE,a.EMBEL_NAME,a.EMBEL_TYPE,sum(production_quantity) as PROD_QTY,a.REMARKS from pro_garments_production_mst a where a.po_break_down_id=$po  and a.serving_company=$working_company and a.production_type=$prod_type $prod_cond and a.status_active=1 and a.is_deleted=0 group by a.id,a.serving_company,a.production_source,a.embel_name,a.embel_type,a.remarks";
	// echo $sql;
	$sql_res = sql_select($sql);

	$table_width=710;
	?>
	<div style="width:<? echo $table_width; ?>px;margin: 10px auto;" align="center" id="details_reports">
		<? $txt = ($prod_type==2) ? "Embellishment Issue Details": "Embellishment Receive Details";?>
		<div style="font-size: 18px;font-weight: bold;"><? echo $txt;?></div>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="30">Sl</th>
                    	<th width="100">System ID</th>
                    	<th width="100">Emb. Name</th>
                    	<th width="100">Emb. Type</th>
                    	<th width="100">Source</th>
                    	<th width="100">Emb. Company</th>
                    	<th width="80">Qty</th>
                    	<th width="100">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    	$sl=1;
                    	$j = 0;
                    	$g_total = 0;
                    	$action = "";
                     	foreach ($sql_res as $key => $val) 
	                    {
	                    	if($prod_type==2)
	                    	{
		                    	if($format_ids[0]==47) 
	                            {
	                                $type=1;
	                                $action = "emblishment_issue_print";
	                            }
	                           	elseif($format_ids[0]==48) 
	                            {
	                                $type=2;
	                                $action = "emblishment_issue_print2";
	                            }
	                            elseif($format_ids[0]==66) 
	                            {	
	                            	$type=3;
	                                $action = "emblishment_issue_print3";
	                            }
								
	                        }
	                        else
	                        { 
	                        	if($format_ids[0]==66) //Print 2
	                            {
	                                $type=1;
	                                $action = "emblishment_issue_print2";
	                            }
	                        	if($format_ids[0]==86) //Print
	                            {
	                                $type=4;
	                                $action = "emblishment_receive_print";
	                            }
	                        	if($format_ids[0]==111) //Print 3
	                            {
	                                $type=5;
	                                $action = "emblishment_receive_print_2";
	                            }
	                        	if($format_ids[0]==129) //Print 5
	                            {
	                                $type=6;
	                                $action = "emblishment_receive_print5";
	                            }
	                        }
	                    	?>
	                    	<tr bgcolor="<? echo ($sl%2==0) ? "#c2dcff" : "#f9f9f9";?>">
	                    		<td><? echo $sl;?></td>
	                    		<td align="center">
	                    			<a href="javascript:void(0)" onClick="print_report('<? echo $company_name;?>','<? echo $val['CHALLAN_NO'];?>','<? echo $prod_type;?>','<? echo $action;?>')">
	                    			<? echo $val['CHALLAN_NO']; ?>
	                    		</a>
	                    		</td>
	                    		<td align="left"><? echo $emblishment_name_array[$val['EMBEL_NAME']]; ?></td>
	                    		<td align="left">
	                    			<? 
	                    			if($val['EMBEL_NAME']==1){echo $emblishment_print_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==2){echo $emblishment_embroy_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==3){echo $emblishment_wash_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==4){echo $emblishment_spwork_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==5){echo $emblishment_gmts_type[$val['EMBEL_TYPE']]; }	                    			
	                    			?>
	                    		</td>
	                    		<td align="left"><? echo ($val['PRODUCTION_SOURCE']==1) ? "Inhouse" : "Outbound"; ?></td>
	                    		<td align="left"><? echo ($val['PRODUCTION_SOURCE']==1) ? $company_library[$val['SERVING_COMPANY']] : $supplier_library[$val['SERVING_COMPANY']]; ?></td>
	                    		<td align="right"><? echo $val['PROD_QTY']; ?></td>
	                    		<td align="left"><? echo $val['REMARKS']; ?></td>
	                    	</tr>
	                    	<? 
	                    	$sl++;
	                    	$g_total += $val['PROD_QTY'];
	                    } 
                    ?>
                </tbody>
                <tfoot>                	
                    <tr>
                    	<th colspan="6">Total</th>
                    	<th align="right"><? echo $g_total; ?></th>
                    	<th align="right"></th>
                    </tr>
                </tfoot>
            </table>
    	</div>
    	<script type="text/javascript">  
			function print_report(company_name,id,prod_type,action)
			{
				var report_title='';

				var data=company_name+'*'+id+'*'+report_title+'*'+3;

				//freeze_window(5);
				if(prod_type==2)
				{
					http.open("POST","../../../production/requires/print_embro_issue_controller.php",true);					
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function()
					{
						if(http.readyState == 4) 
					    {
					    	//alert(action+"**"+action_type);
							window.open("../../../production/requires/print_embro_issue_controller.php?action="+action+'&data='+data, "_blank");
							//release_freezing();
					   }	
					}
				}
				else
				{
					http.open("POST","../../../production/requires/print_embro_receive_controller.php",true);					
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function()
					{
						if(http.readyState == 4) 
					    {
					    	//alert(action+"**"+action_type);
							window.open("../../../production/requires/print_embro_receive_controller.php?action="+action+'&data='+data, "_blank");
							//release_freezing();
					   }	
					}
				}
			}
    	</script>
    	<?
}
if($action=="open_emb_balance_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	// po,country,item,color,cutting,source,date from, date to , production type
	// print_r($data);
	$ex_data = explode("__", $data);
	$company_name = $ex_data[0];
	$po = $ex_data[1];
	$working_company = $ex_data[2];
	$source = $ex_data[3];
	$date_from = $ex_data[4];
	$date_to = $ex_data[5];

	// print_r($format_ids);die();
	$size_Arr_library=return_library_array( "SELECT id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("SELECT size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po order by size_order","size_number_id","size_number_id");
	// ============================================ getting size wise order qnty ==================================================
	$sql_order_size= "SELECT c.size_number_id, c.order_quantity as order_quantity
		from wo_po_color_size_breakdown c 
		where c.status_active=1 and c.is_deleted=0 and c.po_break_down_id=$po order by c.size_order";
	$sql_order_size_res = sql_select($sql_order_size);	 
	foreach($sql_order_size_res as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('order_quantity')];
	}
	// ======================================== getting cutting wise size qnty ========================================
	$prod_cond = "";
	if($source) $prod_cond="and a.production_source=$source";else $prod_cond="";
	if($date_from !="" && $date_to !="") $prod_cond="and a.production_date between '$date_from' and '$date_to' ";else $prod_cond="";

	$sql="SELECT  a.SERVING_COMPANY,a.PRODUCTION_SOURCE,a.EMBEL_NAME,a.EMBEL_TYPE,sum (case when a.production_type=2 then  production_quantity else 0 end) as issue_qty,sum (case when a.production_type=3 then  production_quantity else 0 end) as receive_qty,a.REMARKS from pro_garments_production_mst a where a.po_break_down_id=$po and a.production_type in(2,3) $prod_cond and a.status_active=1 and a.is_deleted=0 group by a.serving_company,a.production_source,a.embel_name,a.embel_type,a.remarks";
	// echo $sql;
	$sql_res = sql_select($sql);

	$table_width=710;
	?>
	<div style="width:<? echo $table_width; ?>px;margin: 10px auto;" align="center" id="details_reports">
		<? $txt = ($prod_type==2) ? "Embellishment Issue Details": "Embellishment Receive Details";?>
		<div style="font-size: 18px;font-weight: bold;"><? echo $txt;?></div>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="30">Sl</th>
                    
                    	<th width="100">Emb. Name</th>
                    	<th width="100">Emb. Type</th>
                    	<th width="100">Source</th>
                    	<th width="100">Emb. Company</th>
                    	<th width="80">Qty</th>
                    	<th width="100">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    	$sl=1;
                    	$j = 0;
                    	$g_total = 0;
                    	$action = "";
                     	foreach ($sql_res as $key => $val) 
						
	                    {
						
	                    	?>
	                    	<tr bgcolor="<? echo ($sl%2==0) ? "#c2dcff" : "#f9f9f9";?>">
	                    		<td><? echo $sl;?></td>
	                    		<!-- <td align="center">
	                    		//	<? echo $val['CHALLAN_NO']; ?>
	                    		</td> -->
	                    		<td align="left"><? echo $emblishment_name_array[$val['EMBEL_NAME']]; ?></td>
	                    		<td align="left">
	                    			<? 
	                    			if($val['EMBEL_NAME']==1){echo $emblishment_print_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==2){echo $emblishment_embroy_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==3){echo $emblishment_wash_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==4){echo $emblishment_spwork_type[$val['EMBEL_TYPE']]; }
	                    			elseif($val['EMBEL_NAME']==5){echo $emblishment_gmts_type[$val['EMBEL_TYPE']]; }	                    			
	                    			?>
	                    		</td>
	                    		<td align="left"><? echo ($val['PRODUCTION_SOURCE']==1) ? "Inhouse" : "Outbound"; ?></td>
	                    		<td align="left"><? echo ($val['PRODUCTION_SOURCE']==1) ? $company_library[$val['SERVING_COMPANY']] : $supplier_library[$val['SERVING_COMPANY']]; ?></td>
	                    		<td align="right"><? echo $val['ISSUE_QTY'] - $val['RECEIVE_QTY'];  ?></td>
	                    		<td align="left"><? echo $val['REMARKS']; ?></td>
	                    	</tr>
	                    	<? 
	                    	$sl++;
	                    	$g_total += $val['ISSUE_QTY'] - $val['RECEIVE_QTY'];
	                    } 
                    ?>
                </tbody>
                <tfoot>                	
                    <tr>
                    	<th colspan="5">Total</th>
                    	<th align="right"><? echo $g_total; ?></th>
                    	<th align="right"></th>
                    </tr>
                </tfoot>
            </table>
    	</div>
    	<script type="text/javascript">  
			function print_report(company_name,id,prod_type,action)
			{
				var report_title='';

				var data=company_name+'*'+id+'*'+report_title+'*'+3;

				//freeze_window(5);
				if(prod_type==2)
				{
					http.open("POST","../../../production/requires/print_embro_issue_controller.php",true);					
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function()
					{
						if(http.readyState == 4) 
					    {
					    	//alert(action+"**"+action_type);
							window.open("../../../production/requires/print_embro_issue_controller.php?action="+action+'&data='+data, "_blank");
							//release_freezing();
					   }	
					}
				}
				else
				{
					http.open("POST","../../../production/requires/print_embro_receive_controller.php",true);					
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function()
					{
						if(http.readyState == 4) 
					    {
					    	//alert(action+"**"+action_type);
							window.open("../../../production/requires/print_embro_receive_controller.php?action="+action+'&data='+data, "_blank");
							//release_freezing();
					   }	
					}
				}
			}
    	</script>
    	<?
}
?>