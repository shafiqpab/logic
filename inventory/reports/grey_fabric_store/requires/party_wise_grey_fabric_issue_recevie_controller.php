<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "txt_dyeing_com_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",0, "--Select Party--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{	
		//select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2
		echo create_drop_down( "txt_dyeing_com_id", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$company_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name",0, "-- Select --", 1, "" );
	}
	else
	{
		echo create_drop_down( "txt_dyeing_com_id", 140, $blank_array,"",1, "--Select Party--", 1, "" );
	}
	
	exit();
}*/

/*if ($action=="eval_multi_select")
{
 	echo "set_multiselect('txt_dyeing_com_id','0','0','','0');\n";
	$data = explode("_",$data);
	
	if($data[0]==1)
	{
		echo "set_multiselect('txt_dyeing_com_id','0','1','".$data[1]."','0');\n";
	}
	exit();
}*/

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_year;
	
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
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
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $cbo_report_basis; ?>', 'create_job_no_search_list_view', 'search_div', 'party_wise_grey_fabric_issue_recevie_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	 $month_id=$data[5];
	 $report_basis=$data[6];

	//echo $year_id.'DD';
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
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";
	
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
		
	 $sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
	if( $report_basis==3)
	{	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	}
	else
	{
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	}
	
   exit(); 
} 

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, '','','','');
	extract($_REQUEST);
	
	?>
     
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
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
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
    <fieldset style="width:390px;">
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_dyeing_source==3)
	{
		 $sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(21,24) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name";
	}
	else if($cbo_dyeing_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where id=$companyID and status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "330","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	?>
    </fieldset>
    </div>
    </body>
    </html>
    <?php
   exit(); 
}
 
if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, '', $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];
	
	?> 
	<script>
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		//alert(booking_no);
		parent.emailwindow.hide();
	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;">
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<fieldset style="width:750px;">
        <table width="750" cellspacing="0" cellpadding="0" border="0" align="center" rules="all">
	    	<tr>
	        	<td align="center" width="100%">
	            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
	                    <thead>                	 
	                       <th width="150">Buyer Name</th>
                           <th width="200">Date Range</th>
                           <th></th>           
	                    </thead>
	        			<tr>
	                    <input type="hidden" id="selected_booking">
	                   	<td>
	                     <? 
						echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
								?>
	                    </td>
	                    <td>
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						 </td> 
	            		 <td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>','create_booking_search_list_view', 'search_div', 'party_wise_grey_fabric_issue_recevie_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	        <!--<tr>
	            <td align="center"valign="top" id="search_div"> 
	            </td>
	        </tr>-->
	    </table>
        </fieldset>    
	    </form>
        <fieldset>
        	<div id="search_div"></div>
        </fieldset>
	   </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[3];
	//if ($data[3]!=0) $company="  company_id='$data[3]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $buyer=" and buyer_id='$data[0]'"; else { echo "Please Select Buyer First."; die; }
	//if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
		}
	$po_array=array();
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst  where company_id='$company' $buyer $booking_date and booking_type=1 and is_short=2 and   status_active=1  and is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		//print_r( $po_id);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].","; 
		}

		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	} //echo $po_array[$row[csf("po_break_down_id")]];
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	$sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where company_id=$company $buyer $booking_date and booking_type=1 and is_short in(1,2) and  status_active=1  and 	is_deleted=0 order by booking_no"; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');
	
	exit(); 
}// Booking Search End


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$booking_no_job_arr=return_library_array( "select booking_no, job_no from wo_booking_mst", "booking_no", "job_no");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$batch_color_id_arr=return_library_array( "select id, color_id from pro_batch_create_mst", "id", "color_id");
	
	$po_quantity_arr=return_library_array( "select id, po_quantity from wo_po_break_down",'id','po_quantity');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$item_desc_arr=return_library_array( "select id, product_name_details from  product_details_master", "id", "product_name_details");	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_determin_id_arr=return_library_array( "select id, detarmination_id from product_details_master", "id", "detarmination_id");
	$date_cond='';$date_cond_style='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
	 if($db_type==0)
		{
			$from_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$to_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$from_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$to_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
 	$date_cond=" and a.issue_date between '$from_date' and '$to_date'";
	$date_cond_style=" and a.receive_date between '$from_date' and '$to_date'";
	$date_cond_style_date2=" and e.pub_shipment_date between '$from_date' and '$to_date'";
	
	}
	else
	{
	$date_cond="";
	$date_cond_style="";$date_cond_style_date2="";
	}
	$dyeing_company=str_replace("'","",$txt_dyeing_com_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$report_basis=str_replace("'","",$cbo_report_basis);
	$job_no=str_replace("'","",$txt_job_no);
	$txt_style_id=str_replace("'","",$txt_style_hidden);
	$txt_style=str_replace("'","",$txt_style);
	$type=str_replace("'","",$type);
	$cbo_year= str_replace("'","",$cbo_year);
	$txt_book_no=str_replace("'","",$txt_book_no);
	/*if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond=""; else $dyeing_source_cond=" and a.knit_dye_source=$cbo_dyeing_source";
	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_rec_cond=""; else $dyeing_source_rec_cond=" and a.knitting_source=$cbo_dyeing_source";
	if ($dyeing_company=='') $dyeing_company_cond=""; else  $dyeing_company_cond="  and a.knit_dye_company in ($dyeing_company)";*/
	
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	$style_arr='';$s=1;
	//echo $txt_style;
	$style_data=explode(",",$txt_style);
	foreach($style_data as $row)
	{
		if($s==1) $style_arr="'".$row."'";else $style_arr.=","."'". $row."'";
		//$style_arr.="'". $row."'".',';	
	$s++;
	}
	if($report_basis==3) //Style Wise
	{
		if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond=""; else $dyeing_source_cond=" and a.knitting_source=$cbo_dyeing_source";
	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond2=""; else $dyeing_source_cond2=" and a.knit_dye_source=$cbo_dyeing_source";
	if ($dyeing_company=='') $dyeing_company_cond=""; else  $dyeing_company_cond="  and a.knit_dye_company in ($dyeing_company)";
	if ($txt_style=="") $style_cond=""; else $style_cond=" and a.style_ref_no in($style_arr)";
	if ($txt_style=="") $style_cond2=""; else $style_cond2=" and f.style_ref_no in($style_arr)";
	if ($txt_style_id=="") $style_id_cond2=""; else $style_id_cond2=" and f.id in ($txt_style_id) ";

	//if ($txt_style_id=="") $style_id_cond=""; else $style_id_cond=" and e.id in ($txt_style_id) ";
	//if($cbo_buyer_name==0)  $buyer_cond=""; else $buyer_cond="and a.buyer_id=".$cbo_buyer_name."";
			if($cbo_buyer_name==0) 
			{
			 $wo_buyer_cond="";
			 $wo_buyer_cond2="";
			}
			else 
			{
				$wo_buyer_cond="and a.buyer_name=".$cbo_buyer_name."";
				$wo_buyer_cond2="and f.buyer_name=".$cbo_buyer_name."";
			}
			$date_cond_style_date="";
			if($from_date!='' && $to_date!='')
			{
				$date_cond_style_date=" and b.pub_shipment_date between '$from_date' and '$to_date'";
			}
		
	}
	else
	{
		$date_cond_style_date="";//$date_cond_style_date2="";
	//	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_cond=""; else $dyeing_source_cond=" and a.knitting_company=$cbo_dyeing_source";
	if (str_replace("'","",$cbo_dyeing_source)==0) $dyeing_source_rec_cond=""; else $dyeing_source_rec_cond=" and a.knitting_company=$cbo_dyeing_source";
	//if ($dyeing_company=='') $dyeing_company_cond=""; else  $dyeing_company_cond="  and a.knit_dye_company in ($dyeing_company)";	
	if (str_replace("'","",$cbo_dyeing_source)!=0)
	{
		if ($dyeing_company!='' || $dyeing_company!=0) 
		{
			if (str_replace("'","",$cbo_dyeing_source)==1) $dyeing_company_cond=" and a.knit_dye_company in($dyeing_company)";
			else if (str_replace("'","",$cbo_dyeing_source)==3) $dyeing_company_cond=" and a.knit_dye_company in($dyeing_company)";
			else $dyeing_company_cond="";
			//$dyeing_company_cond=""; else  $dyeing_company_cond="  and a.knit_dye_company in ($dyeing_company)";
		}
	}
	if ($txt_style=="") $style_cond=""; else $style_cond=" and a.style_ref in($style_arr)";
	if ($txt_style_id=="") $style_id_cond=""; else $style_id_cond=" and c.id in ($txt_style_id) ";
	
	
	if($cbo_buyer_name==0)  $buyer_cond=""; else $buyer_cond="and a.buyer_id=".$cbo_buyer_name."";
	if($cbo_buyer_name==0)  $wo_buyer_cond=""; else $wo_buyer_cond="and a.buyer_name=".$cbo_buyer_name."";
	
	}
	//a.knitting_source,a.knitting_company
	//echo $job_no_cond;
	
	//echo $style_arr;
	
	if ($txt_book_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no like '%$txt_book_no%'";
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) 
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
			$year_cond2=" and YEAR(f.insert_date)=$cbo_year";
		}
		else if($db_type==2)
		 { 
			 $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			 $year_cond2=" and to_char(f.insert_date,'YYYY')=$cbo_year";
		 }
		else { $year_cond="";$year_cond2="";}
	}
	else { $year_cond="";$year_cond2=""; }
	//echo $year_cond;
	if($txt_book_no!="")
	{
		 $sql_book=sql_select("select max(a.booking_no) as booking_no from wo_booking_mst a,wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$cbo_company_name $buyer_cond $booking_no_cond and a.booking_type=1 and a.is_short=2 and   a.status_active=1  and a.is_deleted=0 order by a.booking_no");
	  	$booking_no=$sql_book[0][csf('booking_no')];
	 	if($booking_no!="")  $bookin_cond=" and a.booking_no='$booking_no'"; else $bookin_cond=""; 
	}
		$booking_qty_arr=array();
	$sql_result=sql_select("select a.booking_no as booking_no,b.po_break_down_id,b.fabric_color_id,sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$cbo_company_name $buyer_cond $booking_no_cond and a.booking_type in(1,4) and a.is_short=2 and a.item_category=2 and  a.status_active=1  and a.is_deleted=0 group by a.booking_no,b.po_break_down_id,b.fabric_color_id order by a.booking_no");
		foreach($sql_result as $row)
		{
			$booking_qty_arr[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['grey_qty']=$row[csf('grey_fab_qnty')];
			$booking_qty_arr[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]['fin_qty']+=$row[csf('fin_fab_qnty')];
		}	
		//$job_no_mst_arr=return_library_array( "select id, job_no_mst from wo_po_break_down",'id','job_no_mst');
		$po_array=array(); $po_style_ref="";$all_po_id="";$job_no_mst_arr=array();
		 $sql_job=sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number,b.pub_shipment_date,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $job_no_cond $wo_buyer_cond $year_cond $date_cond_style_date $style_cond");
		foreach($sql_job as $row)
		{
			//$job_no_mst_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			//$job_no_mst_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
			$job_no_mst_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
			//$job_no_mst_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			
			$po_style_ref.="'".$row[csf('style_ref_no')]."'".",";
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}
		//print_r($job_no_mst_arr);
		//echo $all_po_id;
		$po_style_ref_all=rtrim($po_style_ref,',');
		if($job_no!='' || $txt_style!="")
		{
			if($po_style_ref_all!='') $po_style_ref_cond=" $style_cond";
	
		}
		else
		{
			 //$po_style_ref_cond="and a.style_ref in($po_style_ref_all)";
		}
		//echo $po_style_ref_cond;
		$po_id=rtrim($all_po_id,",");
		$po_id_cond="";
		if($po_id!="") 
		{
			//echo $po_id=substr($po_id,0,-1);po_break_down_id
			if($db_type==0) $po_id_cond="and c.po_breakdown_id in(".$po_id.")";
			else
			{
				$po_ids=explode(",",$po_id);
				if(count($po_ids)>990)
				{
					$po_id_cond="and (";
					$po_ids=array_chunk($po_ids,990);
					$z=0;
					foreach($po_ids as $id)
					{
						$id=implode(",",$id);
						if($z==0) $po_id_cond.=" c.po_breakdown_id in(".$id.")";
						else $po_id_cond.=" or c.po_breakdown_id in(".$id.")";
						$z++;
					}
					$po_id_cond.=")";
				}
				else $po_id_cond="and c.po_breakdown_id in(".$po_id.")";
			}
		}
		//echo $po_id_cond;
		
	if($report_basis==1)
	{
		ob_start();
	?>
        <fieldset style="width:1660px">
           <div style="width:1653px; max-height:350px;">  
            <table width="1650" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="18" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="18" style="font-size:16px"><strong><? echo $report_title; ?> </strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="18" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
                 <tr>
                 <td align="left"  colspan="18">
                 <strong> Color Wise </strong>
                  </td>
                </tr>
            </table>
            <br />
            <table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th> 
                    <th width="120">Booking No </th> 
                    <th width="100">Buyer </th> 
                    <th width="120">Job No</th>  
                    <th width="100">Style</th> 
                    <th width="100">Order No</th> 
                    <th width="80">Order Qty</th>
                    <th width="150">Fabric Des. </th> 
                    <th width="100">Fabric Color</th> 
                    <th width="100">Grey Issued</th>
                    <th width="80">Grey Issued Rtn.</th>
                    <th width="80">Fin. Fabric Recv.</th>
                    <th width="80">Fin. Recv. Rtn.</th>
                    <th width="80">Balance </th>
                    <th width="60">Balance %</th>
                    <th width="80">Process Loss as per Budget</th> 
                    <th width="80">Reject Fabric Recv. </th> 
                    <th>Claimable</th>
                </thead>
            </table>
            <div style="width:1670px; overflow-y: scroll; max-height:380px;" id="scroll_body">
             <table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<? 
				
						$issue_ret_data_qty=array();$finnish_fab_recv_qty=array();$recv_ret_data_qty=array();
					 	$sql_issue=("select a.issue_id as issue_id, a.challan_no,
						sum(case when a.entry_form in(51) and a.item_category=13  then b.cons_quantity end) as issue_return
						
						 from inv_receive_master a,  inv_transaction b where a.id=b.mst_id   and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_id,a.challan_no");
						$result_data=sql_select($sql_issue);
						foreach($result_data as $row)
						{
							 $issue_ret_data_qty[$row[csf('issue_id')]]['ret_qty']=$row[csf('issue_return')];
						}
						$sql_fab_recv=("select c.po_breakdown_id,c.color_id,a.knitting_company,
						sum(case when c.entry_form in(37) and a.item_category=2  then c.quantity end) as finish_recv,
						sum(case when c.entry_form in(37) and a.item_category=2  then c.returnable_qnty end) as reject_qty
						 from inv_receive_master a,  inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id  and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.knitting_company,c.po_breakdown_id,c.color_id");
						$result_data_recv=sql_select($sql_fab_recv);
						foreach($result_data_recv as $row)
						{
							$finnish_fab_recv_qty[$row[csf('knitting_company')]][$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['finish_recv']=$row[csf('finish_recv')];
							$finnish_fab_recv_qty[$row[csf('knitting_company')]][$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['reject_qty']=$row[csf('reject_qty')];	
						}
						
						$sql_fin_recv_ret=("select a.challan_no,c.po_breakdown_id,c.color_id,a.received_mrr_no,
						sum(case when a.entry_form in(46) and a.item_category=2  then c.quantity end) as recv_return
						 from inv_issue_master a,  inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and c.trans_id=b.id  and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.color_id,a.challan_no,c.po_breakdown_id,a.received_mrr_no");
						$result_data=sql_select($sql_fin_recv_ret);
						foreach($result_data as $row)
						{
							 $recv_ret_data_qty[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['recv_return']=$row[csf('recv_return')];	
						}
						$process_loss_arr=array();
				 		$sql_booking_sam=("select a.id,
						sum(case when b.process_id not in(1,2,4,30,101,120,121,122,123,124,125,130,131,134)  then b.process_loss end) as process_loss 
						from  lib_yarn_count_determina_mst a, conversion_process_loss b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id");
						$booking_data_sam=sql_select($sql_booking_sam);
						foreach($booking_data_sam as $row)
						{
							 $process_loss_arr[$row[csf('id')]]['process_loss']=$row[csf('process_loss')];	
						}
						 $sql_data=("select   a.id as issue_id,a.buyer_id,a.booking_no,a.issue_number,a.issue_number_prefix_num,b.prod_id,b.color_id,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.order_id,
						sum(case when a.entry_form in (16,61) and a.item_category=13 then b.issue_qnty end) as grey_issue_qnty,
						sum(case when a.entry_form=45 and a.item_category=13  then b.issue_qnty end) as recv_return_qnty
						from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id  and a.company_id=$cbo_company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis in(1,3,2) and a.entry_form in (16,61)  $dyeing_company_cond $buyer_cond $booking_no_cond $po_style_ref_cond $style_cond group by   a.id,b.color_id,a.order_id,b.prod_id,a.issue_number_prefix_num,a.buyer_id,a.booking_no,a.issue_number,a.issue_basis,a.knit_dye_source,a.knit_dye_company order by a.knit_dye_company");
					$dataArray=sql_select($sql_data);
					$i=1; $k=1; $party_check_arr=array();$total_issue_qty=0;$total_po_qty=0;$total_grey_issue_return_qty=0;$total_fin_recv_qty=0;$total_fin_recv_ret_qty=0;$total_balance=0;$total_process_loss_budget=0;$total_calimable_qty=0;$total_fin_recv_reject_qty=0;
                    
                    foreach($dataArray as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('knit_dye_source')]==1)
							$knitting_party=$company_arr[$row[csf('knit_dye_company')]];
						else if($row[csf('knit_dye_source')]==3)
							$knitting_party=$supplier_arr[$row[csf('knit_dye_company')]];
						else
							$knitting_party="&nbsp;";
							$issue_basis=$row[csf('issue_basis')];
							$issue_number=$row[csf('issue_number')];
							$batch_no_color=$batch_color_id_arr[$row[csf('batch_no')]];
							$job_no_mst='';$job_style=''; $wo_po_id='';
							$order_id=explode(",",$row[csf('order_id')]);
							$po_qnty=0;$issue_return=0;$fin_recv_ret_qty=0;$fin_recv_qty=0;
							//echo $knitting_party;
							$yarn_determin_id=$yarn_determin_id_arr[$row[csf('prod_id')]];
							$process_loss=$process_loss_arr[$yarn_determin_id]['process_loss'];
							foreach($order_id as $po_id)
							{
								//echo $po_id;
								$po_qnty+=$po_quantity_arr[$po_id];
								if($wo_po_id=='') $wo_po_id=$po_number_arr[$po_id];else $wo_po_id .=','.$po_number_arr[$po_id]; //$job_no_mst_arr[$row[csf('id')]]['style']
								if($job_no_mst=='') $job_no_mst=$job_no_mst_arr[$po_id]['job'];else $job_no_mst .=','.array_unique($job_no_mst_arr[$po_id]['job']);
								if($job_style=='') $job_style=$job_no_mst_arr[$po_id]['style'];else $job_style .=','.array_unique($job_no_mst_arr[$po_id]['style']);
								//$issue_return=$issue_ret_data_qty[$po_id][$row[csf('issue_id')]]['ret_qty'];
								$fin_recv_qty=$finnish_fab_recv_qty[$row[csf('knit_dye_company')]][$po_id][$row[csf('color_id')]]['finish_recv'];
								$fin_recv_reject_qty=$finnish_fab_recv_qty[$row[csf('knit_dye_company')]][$po_id][$row[csf('color_id')]]['reject_qty'];
								$fin_recv_ret_qty=$recv_ret_data_qty[$po_id][$row[csf('color_id')]]['recv_return'];
							}
							$issue_return=$issue_ret_data_qty[$row[csf('issue_id')]]['ret_qty'];
							$booking_no_job=$booking_no_job_arr[$row[csf('booking_no')]];	
							$booking_qty=$booking_data_qty[$row[csf('booking_no')]][$batch_no_color]['req_qty'];
							$tot_balance=($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')]+$fin_recv_ret_qty)-($fin_recv_qty+$issue_return);
							$tot_balance_percent=($tot_balance/($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')]+$fin_recv_ret_qty))*100;
							$process_loss_as_budget=(($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')])-$issue_return)*($process_loss/100);
							$calimable_qty=($tot_balance-$process_loss_as_budget)+$fin_recv_reject_qty;
							
							if (!in_array($row[csf('knit_dye_company')],$party_check_arr) )
							{ 
									?>
								<tr bgcolor="#EFEFEF"><td colspan="18"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
									 <?
								 $party_check_arr[]=$row[csf('knit_dye_company')]; 
							} ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                             <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('booking_no')]//; ?></p></td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]];//$row[csf('style_ref')]; ?></p></td>  
                            <td width="120">
                            <div style="word-wrap:break-word; width:120px;">
                            <?
							 	echo rtrim($job_no_mst,',');
							 ?>
                             </div>
                             </td>
                             <td width="100">
                            <div style="word-wrap:break-word; width:100px;">
                            <?
							 	echo rtrim($job_style,',');
							 ?>
                             </div>
                             </td>
                            <td width="100">
                         	<div style="word-wrap:break-word; width:100px;">
                            <?  
								echo  rtrim($wo_po_id,',');
							?>
                            </div>
                            </td>
                            <td width="80" align="right"><p><? echo  $po_qnty; ?></p></td>
                            <td width="150"><div style="word-wrap:break-word; width:150px;"><? echo $item_desc_arr[$row[csf('prod_id')]];//$knitting_party; ?></div></td>
                            <td width="100" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100" align="right" title="Grey Issue + Grey Recv Return"><p><? echo number_format($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')],2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_return,2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><?  echo number_format($fin_recv_qty,2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($fin_recv_ret_qty,2,'.',''); ?></p></td>
                            <td width="80" align="right" title="Tot Balance=(Tot Issue=(Grey Issue Qnty+Grey Recv Return)+Fin Recv Rtn Qty)-(Fin Recv Qty+Issue Return)"><p><? echo number_format($tot_balance,2,'.',''); ?></p></td>
                            <td width="60" align="right" title="Tot Balance Percent=(Tot Balance/(Tot Issue=(Grey Issue Qnty+Grey Recv Return Qnty)+Fin Recv Rtn Qty))*100;"><p><? echo number_format($tot_balance_percent,2,'.',''); ?></p></td>
                            <td width="80" align="right" title="Process Loss As Budget=(Tot Issue=(Grey Issue Qnty+Recv Return Qnty))-Issue Return)*(Process Loss/100);"><p><? echo number_format($process_loss_as_budget,2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($fin_recv_reject_qty,2,'.',''); ?></p></td>
                            <td align="right" title="Calimable Qty=(Tot Balance-Process Loss As Budget)+Fin Recv Reject Qty;"><p><? echo number_format($calimable_qty,2,'.',''); ?></p></td>
                        </tr>
						<?  $total_po_qty+=$po_qnty;
                            $total_grey_issue_return_qty+=$issue_return;
                            $total_fin_recv_qty+=$fin_recv_qty;
                            $total_fin_recv_ret_qty+=$fin_recv_ret_qty;
                            $total_issue_qty+=$row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')];
                            $total_process_loss_budget+=$process_loss_as_budget;
                            $total_balance+=$tot_balance;
                            $total_fin_recv_reject_qty+=$fin_recv_reject_qty;
                            $total_calimable_qty+=$calimable_qty;
                            $i++;
						} 
						?>
                     </table>
                      <table width="1650" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                       <tr>
                            <td width="40">&nbsp;</th>
                            <td width="120">&nbsp;</th>  
                            <td width="100">&nbsp;</th>
                            <td width="120">&nbsp;</th>
                            <td width="100">&nbsp;</th>
                            <td width="100">Total</th>
                            <td width="80" id="value_tot_po"><? echo number_format($total_po_qty,2); ?></th>
                            <td width="150" align="right"><? //echo number_format($tot_opening,2);  ?></td>
                            <td width="100" align="right"><? //echo number_format($total_issue_qty,2);  ?></td>
                            <td width="100" align="right" id="value_tot_issue"><? echo number_format($total_issue_qty,2);  ?>
                            </td>
                            <td width="80" align="right" id="value_tot_grey_return"><? echo number_format($total_grey_issue_return_qty,2);  ?></td>
                            <td width="80" align="right" id="value_total_receive"><? echo number_format($total_fin_recv_qty,2);  ?></td>                 <td width="80" align="right" id="value_tot_recv_ret"><? echo number_format($total_fin_recv_ret_qty,2);  ?></td>
                            <td width="80" align="right" id="value_tot_balance"><? echo number_format($total_balance,2);  ?></td>              
                            <td width="60" align="right"><? //echo number_format($tot_transfer_out,2);  ?></td>  
                            <td width="80" align="right" id="value_tot_process_budget"><? echo number_format($total_process_loss_budget,2);  ?></td> 
                            <td width="80" align="right" id="value_tot_recv_reject"><? echo number_format($total_fin_recv_reject_qty,2);  ?></td>         
                            <td align="right" id="value_tot_claimable"><? echo number_format($total_calimable_qty,2);  ?></td>                      </tr>
                  </table>  
            </div>
            </div>
        </fieldset> 
	<?
	} //Color Wise End
	if($report_basis==2)
	{
	ob_start();
	?>
        <fieldset style="width:1850px">
           <div style="width:1843px; max-height:350px;">  
            <table width="1840" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?> </strong></td>    </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>    </tr>
                <tr>
                 <td align="left"  colspan="19">
                 <strong> Transaction Ref. Wise </strong>
                  </td>
                </tr>
            </table>
            <br />
            <table width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th> 
                    <th width="80">Date</th>   
                    <th width="110">Transaction Ref.</th>
                    <th width="110">Recv Challan No.</th>  
                    <th width="110">Issue Challan No.</th>
                    <th width="120">Booking No </th> 
                    <th width="100">Buyer </th> 
                    <th width="120">Job No</th> 
                    <th width="100">Style </th>   
                    <th width="100">Order  No</th> 
                    <th width="80">Order Qty</th>
                    <th width="150">Fabric Des. </th> 
                    <th width="100">Fabric Color</th> 
                    <th width="100">Grey Issued</th>
                    <th width="80">Grey Returned</th>
                    <th width="80">Fin. Fabric Recv.</th>
                    <th width="80">Fin. Recv. Returned</th>
                    <th width="80">Balance </th>
                    <th width="">Balance  %</th>
                </thead>
            </table>
            <div style="width:1860px; overflow-y: scroll; max-height:380px;" id="scroll_body">
             <table width="1840" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<?	$issue_ret_data_qty=array();$finnish_fab_recv_qty=array();$recv_ret_data_qty=array();$recv_challan_no=array();				
					$sql_issue=("select a.issue_id as issue_id, a.challan_no,
						sum(case when a.entry_form in(51) and a.item_category=13  then b.cons_quantity end) as issue_return
						 from inv_receive_master a,  inv_transaction b where a.id=b.mst_id   and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_id,a.challan_no");
						 $result_data=sql_select($sql_issue);
						foreach($result_data as $row)
						{
							 $issue_ret_data_qty[$row[csf('issue_id')]]['ret_qty']=$row[csf('issue_return')];
						}
						$sql_recv_trans=("select a.knitting_company,a.challan_no,c.po_breakdown_id,c.color_id,
						sum(case when a.entry_form in(37) and a.item_category=2  then c.quantity end) as finish_recv,
						sum(case when a.entry_form in(37) and a.item_category=2  then c.returnable_qnty end) as reject_qty
						 from inv_receive_master a,  inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and c.trans_id=b.id   and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.knitting_company,c.po_breakdown_id,c.color_id,a.challan_no");
						$result_trans=sql_select($sql_recv_trans);
						foreach($result_trans as $row)
						{
							 $finnish_fab_recv_qty[$row[csf('knitting_company')]][$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['finish_recv']=$row[csf('finish_recv')];
							 $finnish_fab_recv_qty[$row[csf('knitting_company')]][$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['reject_qty']=$row[csf('reject_qty')];
							 $recv_challan_no[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['challan_no']=$row[csf('challan_no')];			}
						$sql_fin_recv_ret=("select a.challan_no,c.po_breakdown_id,c.color_id,a.received_mrr_no,
						sum(case when a.entry_form in(46) and a.item_category=2  then c.quantity end) as recv_return
						 from inv_issue_master a,  inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and c.trans_id=b.id  and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.color_id,a.challan_no,c.po_breakdown_id,a.received_mrr_no");
						$result_data=sql_select($sql_fin_recv_ret);
						foreach($result_data as $row)
						{
							 $recv_ret_data_qty[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['recv_return']=$row[csf('recv_return')];		}
						$process_loss_arr=array();
				 		$sql_booking_sam=("select a.id,
						sum(case when b.process_id not in(1,2,4,30,101,120,121,122,123,124,125,130,131,134)  then b.process_loss end) as process_loss 
						from  lib_yarn_count_determina_mst a, conversion_process_loss b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id");
						$booking_data_sam=sql_select($sql_booking_sam);
						foreach($booking_data_sam as $row)
						{
							 $process_loss_arr[$row[csf('id')]]['process_loss']=$row[csf('process_loss')];	
						}
					 $sql_data=("select  a.id as issue_id,a.buyer_id,a.booking_no,a.challan_no,a.buyer_job_no,a.received_mrr_no,a.issue_date,a.issue_number,a.issue_number_prefix_num,b.prod_id,b.color_id,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.order_id,
						sum(case when a.entry_form in (16) and a.item_category=13 then b.issue_qnty end) as grey_issue_qnty,
						sum(case when a.entry_form=45 and a.item_category=13  then b.issue_qnty end) as recv_return_qnty
						from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis in(1,3,2) $dyeing_company_cond $buyer_cond $date_cond $booking_no_cond $po_style_ref_cond $style_cond group by b.color_id,a.order_id,a.id,b.prod_id,a.issue_number_prefix_num,a.challan_no,a.issue_date,a.buyer_id,a.booking_no,a.buyer_job_no,a.issue_number,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.received_mrr_no order by a.knit_dye_company");
					$dataArray=sql_select($sql_data);
					$i=1; $k=1; $party_check_arr=array();$total_issue_qty=0;$total_po_qty=0;$total_grey_issue_return_qty=0;$total_fin_recv_qty=0;$total_fin_recv_ret_qty=0;$total_balance=0;$total_process_loss_budget=0;$total_calimable_qty=0;$total_fin_recv_reject_qty=0;
                   
                    foreach($dataArray as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('knit_dye_source')]==1)
							$knitting_party=$company_arr[$row[csf('knit_dye_company')]];
						else if($row[csf('knit_dye_source')]==3)
							$knitting_party=$supplier_arr[$row[csf('knit_dye_company')]];
						else
							$knitting_party="&nbsp;";
							$issue_basis=$row[csf('issue_basis')];
							$issue_number=$row[csf('issue_number')];
							$batch_no_color=$batch_color_id_arr[$row[csf('batch_no')]];
							$job_no_mst='';$job_style=''; $wo_po_id='';$po_recv_challan_no='';
							$order_id=explode(",",$row[csf('order_id')]);
							$po_qnty=0;$issue_return=0;$fin_recv_ret_qty=0;$fin_recv_qty=0;
							//echo $knitting_party;
							$yarn_determin_id=$yarn_determin_id_arr[$row[csf('prod_id')]];
							$process_loss=$process_loss_arr[$yarn_determin_id]['process_loss'];
							foreach($order_id as $po_id)
							{
								$po_qnty+=$po_quantity_arr[$po_id];
								if($wo_po_id=='') $wo_po_id=$po_number_arr[$po_id];else $wo_po_id .=','.$po_number_arr[$po_id];								if($job_no_mst=='') $job_no_mst=$job_no_mst_arr[$po_id]['job'];else $job_no_mst .=','.array_unique($job_no_mst_arr[$po_id]['job']);		
								if($job_style=='') $job_style=$job_no_mst_arr[$po_id]['style'];else $job_style .=','.array_unique($job_no_mst_arr[$po_id]['style']);
								if($po_recv_challan_no=='') $po_recv_challan_no=$recv_challan_no[$po_id][$row[csf('color_id')]]['challan_no'];else $po_recv_challan_no .=','.$recv_challan_no[$po_id][$row[csf('color_id')]]['challan_no'];
								// $recv_challan_no=$recv_challan_no[$po_id][$row[csf('color_id')]]['challan_no'];
								$fin_recv_qty=$finnish_fab_recv_qty[$row[csf('knit_dye_company')]][$po_id][$row[csf('color_id')]]['finish_recv'];
								$fin_recv_reject_qty=$finnish_fab_recv_qty[$row[csf('knit_dye_company')]][$po_id][$row[csf('color_id')]]['reject_qty'];
								$fin_recv_ret_qty=$recv_ret_data_qty[$po_id][$row[csf('color_id')]]['recv_return'];
							}
							$issue_return=$issue_ret_data_qty[$row[csf('issue_id')]]['ret_qty'];
							
							$booking_no_job=$booking_no_job_arr[$row[csf('booking_no')]];	
							$booking_qty=$booking_data_qty[$row[csf('booking_no')]][$batch_no_color]['req_qty'];
							$tot_balance=($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')]+$fin_recv_ret_qty)-($fin_recv_qty+$issue_return);
							$tot_balance_percent=($tot_balance/($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')]+$fin_recv_ret_qty))*100;
							$process_loss_as_budget=(($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')])-$issue_return)*($process_loss/100);
							$calimable_qty=($tot_balance-$process_loss_as_budget)+$fin_recv_reject_qty;
							
							if (!in_array($row[csf('knit_dye_company')],$party_check_arr) )
							{ 
									?>
										<tr bgcolor="#EFEFEF"><td colspan="19"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
									 <?
								 $party_check_arr[]=$row[csf('knit_dye_company')]; 
							} ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="80"><p><? echo change_date_format($row[csf('issue_date')]);//; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]//; ?></p></td>
                            <td width="110"><p><? echo $po_recv_challan_no; ?></p></td>
                            <td width="110"><p><? echo $row[csf('challan_no')]//; ?></p></td>
                            <td width="120"><p><? echo $row[csf('booking_no')]//; ?></p></td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]];//$row[csf('style_ref')]; ?></p></td>                            <td width="120">
                            <div style="word-wrap:break-word; width:120px;">
                            <?
								 echo rtrim($job_no_mst,',');
							 ?>
                             </div>
                             </td>
                              <td width="100">
                            <div style="word-wrap:break-word; width:100px;">
                            <?
								 echo rtrim($job_style,',');
							 ?>
                             </div>
                             </td>
                            <td width="100">
                         	<div style="word-wrap:break-word; width:100px;">
                            <?  
								echo  rtrim($wo_po_id,',');
							?>
                            </div>
                            </td>
                            <td width="80" align="right"><p><? echo  $po_qnty; ?></p></td>
                            <td width="150" title="<? echo $row[csf('prod_id')];?>"><div style="word-wrap:break-word; width:150px;"><? echo $item_desc_arr[$row[csf('prod_id')]];//$knitting_party; ?></div></td>
                            <td width="100" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100" align="right" title="Grey Issue+Grey Recv Return"><p><? echo number_format($row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')],2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_return,2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><?  echo number_format($fin_recv_qty,2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($fin_recv_ret_qty,2,'.',''); ?></p></td>
                            <td width="80" align="right" title="Tot Balance=(Grey Issue Qty+ Grey Recv Rtn Qnty+Fin Recv Rtn Qty)-(Fin Recv Qty+Issue Return)"><p><? echo number_format($tot_balance,2,'.',''); ?></p></td>
                            <td width="" align="right" title="Tot Balance Percent=(Tot Balance/(Grey Issue Qnty+Recv Return Qnty+Fin Recv Rtn Qty))*100"><p><? echo number_format($tot_balance_percent,2,'.',''); ?></p></td>
                        </tr>
                    <?
                      	$total_po_qty+=$po_qnty;
						$total_grey_issue_return_qty+=$issue_return;
						$total_fin_recv_qty+=$fin_recv_qty;
						$total_fin_recv_ret_qty+=$fin_recv_ret_qty;
					    $total_issue_qty+=$row[csf('grey_issue_qnty')]+$row[csf('recv_return_qnty')];
						$total_process_loss_budget+=$process_loss_as_budget;
						$total_balance+=$tot_balance;
						$total_fin_recv_reject_qty+=$fin_recv_reject_qty;
						$total_calimable_qty+=$calimable_qty;
						$i++;
						} 
						?>
                     </table>
                      <table width="1840" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                       <tr>
                            <td width="40">&nbsp;</th>
                            <td width="80">&nbsp;</th>
                            <td width="110">&nbsp;</th>
                            <td width="110">&nbsp;</th>
                            <td width="110">&nbsp;</th>
                            <td width="120">&nbsp;</th>  
                            <td width="100">&nbsp;</th>
                            <td width="120">&nbsp;</th>
                            <td width="100">&nbsp;</th>
                            <td width="100">Total</th>
                            <td width="80" id="value_tot_po"><? echo number_format($total_po_qty,2); ?></th>
                            <td width="150" align="right"><? //echo number_format($tot_opening,2);  ?></td>
                            <td width="100" align="right"><? //echo number_format($total_issue_qty,2);  ?></td>
                            <td width="100" align="right" id="value_tot_issue"><? echo number_format($total_issue_qty,2);  ?>
                            </td>
                            <td width="80" align="right" id="value_tot_grey_return"><? echo number_format($total_grey_issue_return_qty,2);  ?></td>
                            <td width="80" align="right" id="value_total_receive"><? echo number_format($total_fin_recv_qty,2);  ?></td>
                            <td width="80" align="right" id="value_tot_recv_ret"><? echo number_format($total_fin_recv_ret_qty,2);  ?></td>
                            <td width="80" align="right" id="value_tot_balance"><? echo number_format($total_balance,2);  ?></td>                      <td width="" align="right"><? //echo number_format($tot_transfer_out,2);  ?></td>  
                      </tr>
                  </table>  
            </div>
            </div>
        </fieldset> 
          
	<?
	} //Transfer Ref End
	if($report_basis==3)
	{
		ob_start();
	?>
    <style type="text/css">
           
            hr {
                border: 0; 
                background-color: #000;
                height: 1px;
            }  
    </style> 
        
        <fieldset style="width:2200px">
           <div style="width:2200px; max-height:350px;">  
            <table width="2200" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="25" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="25" style="font-size:16px"><strong><? echo $report_title; ?> </strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="25" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
                 <tr>
                 <td align="left"  colspan="25">
                	 <strong> Style Wise </strong>
                  </td>
                </tr>
            </table>
            <br />
            <table width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th> 
                    <th width="120">Booking No </th> 
                    <th width="100">Buyer </th> 
                    <th width="120">Job No</th> 
                    <th width="100">Style</th> 
                    <th width="100">Order No</th> 
                    <th width="80">Order Qty</th>
                    <th width="150">Fabric Des. </th> 
                 	<th width="80">Grey Req. Qty</th>
                    <th width="100">Grey Issued</th>
                    <th width="80">Grey Issued Rtn.</th>
                    <th width="80">Total Grey Issued</th>
                 
                    <th width="100">Fabric Color</th> 
                    <th width="80">Fin. Req.</th>
                    <th width="80">Fin. Fabric Recv.</th>
                    
                    <th width="80">Fin. Recv. Rtn.</th>
                    <th width="80">Total Fin. Recv.</th>
                    <th width="80">Fin. Balance</th>
                    <th width="80">Balance% From Fin. Req</th>
                    <th width="80">Grey Used</th>
                    
                    <th width="80">Process Adj.</th>
                    <th width="80">Reject Fabric Recv. </th> 
                    
                    <th width="80">Claimable </th>
                    <th width="60">Process Loss %</th>
                    <th width="">Process Loss as per Budget</th> 
                    
                </thead>
            </table>
            <div style="width:2200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
             <table width="2180" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<? 
						//echo $all_po_id;die;
						$poid=rtrim($all_po_id,",");
						$poid_cond="";
						if($poid!="") 
						{
							//echo $po_id=substr($po_id,0,-1);po_break_down_id
							if($db_type==0) $poid_cond="and b.po_break_down_id in(".$poid.")";
							else
							{
								$poids=explode(",",$poid);
								if(count($poids)>990)
								{
									$poid_cond="and (";
									$poids=array_chunk($poids,990);
									$z=0;
									foreach($poids as $id)
									{
										$id=implode(",",$id);
										if($z==0) $poid_cond.=" b.po_break_down_id in(".$id.")";
										else $poid_cond.=" or b.po_break_down_id in(".$id.")";
										$z++;
									}
									$poid_cond.=")";
								}
								else $poid_cond="and b.po_break_down_id in(".$po_id.")";
							}
						}
						//echo $poid_cond;die;
						$issue_ret_data_qty=array();$finnish_fab_recv_qty=array();$recv_ret_data_qty=array();
					 	$sql_issue=("select a.knitting_company,d.dia_width,d.gsm,d.detarmination_id as deter_id,
						sum(case when c.entry_form in(51,84) and a.item_category=13  then c.quantity  end) as issue_return
						
						 from inv_receive_master a,inv_transaction b,product_details_master d,order_wise_pro_details c where a.id=b.mst_id and b.prod_id=d.id  and b.id=c.trans_id and c.prod_id=d.id and a.company_id=$cbo_company_name  and a.entry_form in(51,84) and d.entry_form in(51,84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_cond  group by a.knitting_company,d.detarmination_id,d.dia_width,d.gsm");
						$result_data=sql_select($sql_issue);
						foreach($result_data as $row)
						{
							 $issue_ret_data_qty[$row[csf('knitting_company')]][$row[csf('deter_id')]]['ret_qty']=$row[csf('issue_return')];
							 //$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('deter_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]]['fin_recv_qty']
						}//grey_used_qty
						// wo_pre_cost_fabric_cost_dtls is_short
						$booking_no_arr=array();$booking_no_data_arr=array();$booking_desc_arr=array();
						  $sql_book=("select a.id as booking_id,a.is_short,a.job_no,a.booking_no,a.booking_type,b.po_break_down_id as po_id,b.pre_cost_fabric_cost_dtls_id,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.fabric_color_id,b.wo_qnty,b.fin_fab_qnty,b.grey_fab_qnty,f.style_ref_no from wo_booking_mst a,wo_booking_dtls b ,wo_po_details_master f where a.booking_no=b.booking_no and  b.job_no=f.job_no and a.company_id=$cbo_company_name $buyer_cond $booking_no_cond  $poid_cond and  a.status_active=1  and a.is_deleted=0   order by a.booking_no");//[$row[csf('style_ref_no')]]
						$result_data_book=sql_select($sql_book);
						$grey_booking_qty=0;
							foreach($result_data_book as $row)
							{
								$fabric_desc=$row[csf('construction')].','.$row[csf('copmposition')].','.$row[csf('gsm_weight')].','.$row[csf('dia_width')];
								 $booking_no_arr[$row[csf('booking_id')]]=$row[csf('booking_no')];
								 if($row[csf('booking_type')]==1 && $row[csf('is_short')]==2)
								 {
								 $po_booking_no_arr[$row[csf('po_id')]]=$row[csf('booking_no')];
								 }
								if($row[csf('booking_type')]==3)
								{
									 $grey_booking_qty=$row[csf('wo_qnty')];
									$fin_booking_qty=$row[csf('wo_qnty')];
								}
								else
								{
									$grey_booking_qty=$row[csf('grey_fab_qnty')];
									$fin_booking_qty=$row[csf('fin_fab_qnty')];
								}
									$po_booking_nos_arr[$row[csf('po_id')]].=$row[csf('booking_no')].',';
								 
								 $booking_no_data_arr[$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('fabric_color_id')]]['grey_req']+=$grey_booking_qty;
								 $booking_no_data_arr[$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('fabric_color_id')]]['fin_req']+=$fin_booking_qty;
								  $booking_desc_arr[$row[csf('booking_no')]][$row[csf('style_ref_no')]]['fab_desc'].=$fabric_desc.'**';
								
							}	
							//print_r($booking_no_data_arr);
						/* $result_data_rec_ret=sql_select($sql_data_grey_ret);
						 $recv_ret_data_qty=array();
						foreach($result_data_rec_ret as $row)
						{
							 $recv_ret_data_qty[$row[csf('received_id')]]['ret_qty']=$row[csf('recv_return')];
						}*/
						$sql_data_grey_ret=("select  c.color_id,f.style_ref_no as style_ref,d.detarmination_id as deter_id,
						sum(case when c.entry_form in (46) then c.quantity end) as recv_return
						
						from inv_issue_master a, inv_transaction b,order_wise_pro_details c,product_details_master d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id  and c.trans_id=b.id and d.id=c.prod_id  and d.id=b.prod_id  and e.job_no_mst=f.job_no and c.po_breakdown_id=e.id  and a.company_id=$cbo_company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and a.item_category=2 and a.entry_form in(46) and c.entry_form in(46)    $wo_buyer_cond2  $po_id_cond  group by  c.color_id,f.style_ref_no,d.detarmination_id ");
						 $result_data_rec_ret=sql_select($sql_data_grey_ret);
						 $recv_ret_data_qty=array();
						foreach($result_data_rec_ret as $row)
						{
							 $recv_ret_data_qty[$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]]['ret_qty']+=$row[csf('recv_return')];
						}
						// print_r($recv_ret_data_qty);
						  $sql_fab_recv=("select a.id as recv_id,b.id as dtls_id,e.id as po_id,c.color_id,a.booking_no,a.knitting_company,b.fabric_description_id as deter_id,f.style_ref_no as style_ref,f.job_no,
						sum(case when c.entry_form in(37,7) and a.item_category in(2)  then b.grey_used_qty end) as grey_used_qty,
						sum(case when c.entry_form in(37,7) and a.item_category=2  then c.returnable_qnty end) as reject_qty,
						sum(case when c.entry_form in(37,7)   then c.quantity  end) as finish_recv
						 from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, product_details_master d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id and b.id=c.dtls_id  and d.id=c.prod_id  and d.id=b.prod_id and  e.job_no_mst=f.job_no and c.po_breakdown_id=e.id and a.company_id=$cbo_company_name  and c.entry_form in(37,7) and  a.item_category in(2,13)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $wo_buyer_cond2   $date_cond_style_date2 $year_cond2  $style_id_cond2 $style_cond2 and b.trans_id>0  group by a.id, e.id,f.style_ref_no,f.job_no,a.booking_no,b.id,a.knitting_company,c.color_id,b.fabric_description_id order by a.knitting_company,f.style_ref_no");
						$result_data_recv=sql_select($sql_fab_recv);
						$grey_used_dtls_arr=array();$fin_recv_qty_arr=array();$fin_recv_color_arr=array();$fin_recv_color_data=array();
						foreach($result_data_recv as $row)
						{
							//$fin_recv_qty_arr_ret_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]]['recv_id'].=$row[csf('recv_id')].',';
							$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]]['fin_recv_qty']+=$row[csf('finish_recv')];
							$grey_used_dtls_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]]['dtls_id'].=$row[csf('dtls_id')].',';	
							$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]]['reject_qty']+=$row[csf('reject_qty')];	
							$fin_recv_color_data[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]]['color'].=$row[csf('color_id')].',';
						
						}
						//print_r($fin_recv_qty_arr);
						
						$process_loss_arr=array();
				 		$sql_booking_sam=("select a.id,
						sum(case when b.process_id not in(1,2,4,30,101,120,121,122,123,124,125,130,131,134)  then b.process_loss end) as process_loss 
						from  lib_yarn_count_determina_mst a, conversion_process_loss b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id");
						$booking_data_sam=sql_select($sql_booking_sam);
						foreach($booking_data_sam as $row)
						{
							 $process_loss_arr[$row[csf('id')]]['process_loss']+=$row[csf('process_loss')];	
						}
						
						if(trim($cbo_year)!=0) 
						{
							if($db_type==0) $year_conds=" and YEAR(e.insert_date)=$cbo_year";
							else if($db_type==2) $year_conds=" and to_char(e.insert_date,'YYYY')=$cbo_year";
							else $year_conds="";
						}	//$batch_booking_no_arr=return_library_array( "select a.id, a.booking_no from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id=b.mst_id", "id", "booking_no");

						$material_data=sql_select("select dtls_id, used_qty from pro_material_used_dtls where  entry_form=37 ");$grey_used_arr=array();
						foreach($material_data as $value)
						{
							$grey_used_arr[$value[csf('dtls_id')]]['gused']+=$value[csf('used_qty')];
						}
						
						if($db_type==2)
						{
						 $group_con="LISTAGG(e.id, ',') WITHIN GROUP (ORDER BY e.id) as po_id,LISTAGG(CAST(e.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY e.po_number) as po_number,LISTAGG(CAST(a.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY a.id) as issue_number"; 
						 $group_by="group by  a.received_id,b.color_id,b.program_no,a.booking_id,d.detarmination_id,f.job_no,f.style_ref_no,b.prod_id,
						 f.buyer_name,a.booking_no,a.entry_form,a.knit_dye_source,a.knit_dye_company ";
						}
						
						else
						{
							 $group_con="group_concat(distinct e.id) as po_id,group_concat(distinct e.po_number) as po_number,group_concat(distinct a.id) as issue_number";
							 $group_by="group by d.detarmination_id,f.job_no,f.style_ref_no,a.knit_dye_source,a.knit_dye_company";
						}
						  
						 $sql_data_grey=("select a.received_id,a.booking_no,a.booking_id,a.entry_form,a.knit_dye_source as knitting_source,a.knit_dye_company as knitting_company,f.job_no,f.buyer_name,f.style_ref_no as style_ref,max(b.program_no) as program_no,b.prod_id,b.color_id,d.detarmination_id as deter_id,$group_con,
						sum(c.quantity) as grey_issue
						from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id  and c.dtls_id=b.id and d.id=b.prod_id and e.job_no_mst=f.job_no and c.po_breakdown_id=e.id and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and a.entry_form in(16,61) and c.entry_form in(16,61) $dyeing_source_cond2 $dyeing_company_cond $wo_buyer_cond2   $date_cond_style_date2 $year_cond2  $style_id_cond2 $style_cond2 $group_by order by a.knit_dye_company,f.style_ref_no,b.prod_id");
						 
						$dataArray=sql_select($sql_data_grey);
						$programNos="";
						foreach($dataArray as $row)
                    	{
                    		if($row[csf('entry_form')]!=16)
							{
								$programNos.=$row[csf('program_no')].",";
							}
                    	}
                    	$programNos = chop($programNos,",");
                    	if($programNos!="")
                    	{
                    		$sql_booking_by_program=sql_select("select dtls_id as program,booking_no from ppl_planning_entry_plan_dtls where dtls_id in($programNos)");
							//echo "select dtls_id as program,booking_no from ppl_planning_entry_plan_dtls where dtls_id in($programNos)";
                    		$bookingNo="";
                    		foreach ($sql_booking_by_program as $row) {
                    			$booking_no_by_prog_arr[$row[csf('program')]]=$row[csf('booking_no')];
								$bookingNo.="'".$row[csf('booking_no')]."'".",";
                    		}
                    		$bookingNo=chop($bookingNo,",");
	                    	/*if($bookingNo!="")
	                    	{
	                    		$bookingID="";
	                    		$sql_booking_id=sql_select("select id,booking_no from wo_booking_mst where booking_no in($bookingNo)");	
	                    		foreach ($sql_booking_id as $row) {
	                    			$bookingID.=$row[csf('id')].",";
	                    		}
	                    		$bookingID = chop($bookingID,",");
	                    	}*/
                    		
                    	}
						
						
						$i=1; $k=1; $party_check_arr=array();$total_issue_qty=0;$total_po_qty=0;$total_grey_issue_return_qty=0;$total_fin_recv_qty=0;$total_fin_recv_ret_qty=0;$total_balance=0;$total_process_loss_budget=0;$total_calimable_qty=0;$total_fin_recv_reject_qty=0;$total_grey_req_qty=0;$total_fin_req_qty=0;$total_fin_req_qty=0;$total_process_adj=0;$total_grey_used=0;$total_grey_issue_bal=0;$total_fin_req=0;$total_fin_recv_qty=0;$total_fin_recv_balance_qty=0;
                    foreach($dataArray as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('knitting_source')]==1)
							$knitting_party=$company_arr[$row[csf('knitting_company')]];
						else if($row[csf('knitting_source')]==3)
							$knitting_party=$supplier_arr[$row[csf('knitting_company')]];
						else
							$knitting_party="&nbsp;";
							
							$issue_number=$row[csf('issue_number')];
							$entry_form=$row[csf('entry_form')];
							$batch_no_color=$batch_color_id_arr[$row[csf('batch_no')]];
							$job_no_mst=''; $wo_po_id='';
							$item_des=array_unique(explode(",",$item_desc_arr[$row[csf('prod_id')]]));
							$gsm=$item_des[2];
							$dia=$item_des[3];
							
							$po_id=array_unique(explode(",",$row[csf('po_id')]));
							$po_qnty=0;
							$recv_dtls_id='';$booking_noS='';
							$fin_recv_reject=0;
							foreach($po_id as $pid)
							{
								
								$booking_noS=rtrim($po_booking_no_arr[$pid],',');
							
								$booking_NOs=rtrim($po_booking_nos_arr[$pid],',');
								//echo $booking_noS.'d';
								$po_qnty=$po_quantity_arr[$pid];
								$fin_color=$fin_recv_color_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$pid]['color'];
								
								$fin_recv_reject+=$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$row[csf('color_id')]][$pid]['reject_qty'];
							}
							//print_r($recv_color);
							 if($entry_form==16)
							{
							$color_id=rtrim($fin_recv_color_data[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]]['color'],',');
							}
							else  if($entry_form==61)
							{
							$color_id=$row[csf('color_id')];
							}
							$color_ids=array_unique(explode(",",$color_id));
							$booking_no=implode(",",array_unique(explode(",",$booking_noS)));
							//$booking_no=implode(",",array_unique(explode(",",$booking_noS)));
							$booking_NOs=implode(",",array_unique(explode(",",$booking_NOs)));
							//echo $recv_color;
							if($entry_form==16)
							{
								//$booking_no=$booking_no_arr[$row[csf('booking_id')]];
								
							}
							else //$row[csf('job_no')]
							{
								//echo $row[csf('program_no')];
								// $booking_no=$booking_no_by_prog_arr[$row[csf('program_no')]];
								//$grey_req_qty=$booking_no_data_arr[$row[csf('program_no')]][$row[csf('style_ref')]]['grey_req'];
								//$fin_req_qty=$booking_no_data_arr[$row[csf('program_no')]][$row[csf('style_ref')]]['fin_req'];
							}
							//echo $booking_no;
							 $grey_req_qty=0;$grey_used_id='';$fin_recv_qty2=0;$fin_recv_ret_qty2=0;
							 foreach($color_ids as $cid)
							{
								
								 if($entry_form==16)
									{
										$grey_req_qty+=$booking_no_data_arr[$booking_no][$row[csf('style_ref')]][$cid]['grey_req'];
									}
									else
									{
										
										//$grey_req_qty+=$booking_no_data_arr[$booking_no][$row[csf('style_ref')]][$cid]['grey_req'];
										$grey_req_qty+=$booking_no_data_arr[$booking_no][$row[csf('style_ref')]][$cid]['grey_req'];
										//echo $booking_no.'='.$row[csf('style_ref')].'='.$cid;
									}
									$dtls_id=rtrim($grey_used_dtls_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['dtls_id'],',');
									if($grey_used_id=='') $grey_used_id=$dtls_id; else $grey_used_id.=$dtls_id.',';
									
									$fin_recv_qty2=$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['fin_recv_qty'];
									$fin_recv_ret_qty2=$recv_ret_data_qty[$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['ret_qty'];
								 
							}
							
							
							$grey_issue_qty=$row[csf('grey_issue')];
							$grey_issue_qty_ret=$grey_issue_arr[$row[csf('deter_id')]][$row[csf('style_ref')]][$row[csf('knitting_company')]]['ret'];
							
							$process_loss_as_budget=$process_loss_arr[$row[csf('deter_id')]]['process_loss'];
							 $issue_return=$issue_ret_data_qty[$row[csf('knitting_company')]][$row[csf('deter_id')]]['ret_qty'];
							$booking_no_job=$booking_no_job_arr[$booking_no];	
							$booking_qty=$booking_data_qty[$booking_no][$batch_no_color]['req_qty'];
							//$colorid=array_unique(explode(",",$row[csf('color_id')]));
							
							
						
							
							$grey_used_id=rtrim($grey_used_id,',');
							$recv_dtls_ids=array_unique(explode(",",$grey_used_id));
							//$recv_dtls_ids=explode(",",$recv_dtls_id);
							//grey_used_dtls_arr 
							$grey_used_qty=0;
							foreach($recv_dtls_ids as $dtl_id)
							{
								//echo $dtl_id;
								$grey_used_qty+=$grey_used_arr[$dtl_id]['gused'];
							}
							$fab_desc=rtrim($booking_desc_arr[$booking_no][$row[csf('style_ref')]]['fab_desc'],"**");
							$fabric_desc=implode("**",array_unique(explode("**",$fab_desc)));
							
							$fab_desc_row=count(array_unique(explode("**",$fab_desc)));
							if($fab_desc_row>1)
							{
								 $view_button="<a href='##' value='View' onClick=\"setdata_po('".$fabric_desc."','Fabric  Description')\"> View<a/>";
							}
							else
							{
								$view_button=$item_desc_arr[$row[csf('prod_id')]];
							}
				
							//print_r($fabric_desc);
							$process_adj=$grey_used_qty-$fin_recv_qty2;
							$tot_balance=($grey_issue_qty+$grey_issue_qty_ret+$fin_recv_ret_qty2)-($row[csf('finish_recv')]+$issue_return);
							$tot_balance_percent=($tot_balance/($grey_issue_qty+$fin_recv_ret_qty2))*100;
							//$process_loss_as_budget=(($grey_issue_qty+$fin_recv_ret_qty)-$issue_return)*($process_loss/100);
							
							$calimable_qty=(($grey_issue_qty+$fin_recv_ret_qty2)-($fin_recv_qty2+$issue_return+$process_adj+$fin_recv_reject));
							$process_percent=$process_adj/($grey_issue_qty-$grey_issue_qty_ret-$calimable_qty)*100;
							if (!in_array($row[csf('knitting_company')],$party_check_arr) )
							{  
									?>
										<tr bgcolor="#EFEFEF"><td colspan="25"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
									 <?
								 $party_check_arr[]=$row[csf('knitting_company')]; 
							} ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120" title="<? echo $row[csf('issue_number')].',All Booking='.$booking_NOs; ?>"><p><? echo $booking_no; ?></p></td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]];//$row[csf('style_ref')]; ?></p></td>
                            <td width="120">
                            <div style="word-wrap:break-word; width:120px;">
                            <?
							 	echo $row[csf('job_no')];
							 ?>
                             </div>
                             </td>
                             <td width="100" title="<? echo $row[csf('deter_id')];?>">
                         	<div style="word-wrap:break-word; width:100px;">
                            <?  
								echo  $row[csf('style_ref')];
							?>
                            </div>
                            <td width="100">
                         	<div style="word-wrap:break-word; width:100px;">
                            <?  
								echo  implode(",",array_unique(explode(",",$row[csf('po_number')])));
							?>
                            </div>
                            </td>
                            <td width="80" align="right"><p><? echo  $po_qnty; ?></p></td>
                            <td width="150" title="<? echo $row[csf('prod_id')];?>">
                             <? echo $view_button; ?>
                          
                            </td>
                            <td width="80" align="right"><p><? echo  number_format($grey_req_qty,2); ?></p></td>
                           


                            <td width="100" align="right" title="Grey Issue"><p>
                            <a href='#report_details' onClick="openmypage_grey_issue('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('knitting_source')]; ?>','<? echo $row[csf('style_ref')]; ?>','<? echo $row[csf('color_id')]; ?>','<? echo $row[csf('prod_id')]; ?>','<? echo $row[csf('issue_number')]; ?>','<? echo $row[csf('deter_id')]; ?>','<? echo $row[csf('knitting_company')]; ?>','style_grey_issue_popup');"><?  echo number_format($grey_issue_qty,2,'.',''); ?></a>
                            
                            <? //echo number_format($row[csf('grey_issue_qnty')]+$row[csf('finish_recv')],2,'.','');
							//$total_issue_qty=($grey_issue_qty+$grey_issue_qty_ret)-$issue_return;
							//$grey_balance_qty=$total_issue_qty-$grey_used_qty;
							 ?></p></td>



							 
                            <td width="80" align="right"><p><? echo number_format($issue_return,2,'.',''); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($grey_issue_qty-$issue_return,2,'.',''); ?></p></td>
                          
                           <td width="100" align="center">
                           <div style="word-break:break-all;font-size:small">
                          <?
                          foreach($color_ids as $cid)
							{ 
								if($cid!=0) echo $color_arr[$cid].'<hr><br/>';
							}
                          ?>
                            </div>
                           </td>
                           
                             <td width="80" align="right">
                              <div style="word-break:break-all;font-size:small">
							  <?
                                $tot_fin_req=0;
                              foreach($color_ids as $cid)
                                { 
                                    $fin_req_qty=$booking_no_data_arr[$booking_no][$row[csf('style_ref')]][$cid]['fin_req'];
                                   if($cid!=0) echo number_format($fin_req_qty,2).'<hr><br/>';
                                    $tot_fin_req+=$fin_req_qty;
									 $tot_fin_reg_qty_bal[$cid]=$fin_req_qty;
                                }
                              ?>
                            </div>
                             </td>
                            <td width="80" align="right">
							  <div style="word-break:break-all;font-size:small">
                          <?
						   
						 	$fin_recv_qty=0;$tot_fin_recv=0;
                          	foreach($color_ids as $cid)
							{ 
								$fin_recv_qty=$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['fin_recv_qty'];
								$view_button_const="<a href='##' value='View' onClick=\"openmypage_grey_fab_recv('".$row[csf('po_id')]."','".$row[csf('knitting_source')]."','".$row[csf('style_ref')]."','".$cid."','".$booking_no."','".$row[csf('deter_id')]."','".$row[csf('knitting_company')]."','fin_fab_recv_popup_style')\">".number_format($fin_recv_qty,2)."<a/>";
								 //$recv_ret_id=$fin_recv_qty_arr[$row[csf('knitting_company')]][$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['recv_id'];
								if($cid!=0) echo $view_button_const.'<hr><br/>';
								$tot_fin_recv+=$fin_recv_qty;
								 $bal_fin_recv_qty[$cid]=$fin_recv_qty;
							}
                          ?>
                            </div></td>
                            
                           
                            
                             <td width="80" align="right">
                              <div style="word-break:break-all;font-size:small">
							  <?
                                $tot_fin_recv_ret_qty=0;
                              foreach($color_ids as $cid)
                                { 
                                    //$fin_recv_ret_qty=$booking_no_data_arr[$row[csf('booking_id')]][$row[csf('style_ref')]][$cid]['fin_req'];
									$fin_recv_ret_qty=$recv_ret_data_qty[$row[csf('style_ref')]][$row[csf('deter_id')]][$cid]['ret_qty'];
									//echo $row[csf('style_ref')].'='.$cid;
                                    if($cid!=0) echo number_format($fin_recv_ret_qty,2).'<hr><br/>';
                                    $tot_fin_recv_ret_qty+=$fin_recv_ret_qty;
									 $bal_fin_recv_ret_qty[$cid]=$fin_recv_ret_qty;
                                }
                              ?>
                            </div>
                             </td>
                             <td width="80" align="right" title="Total Fin Recv">
                              <div style="word-break:break-all;font-size:small">
							  <?
                                $tot_fin_balance_qty=0;
                             	 foreach($color_ids as $cid)
                                { 
                                   
									$tot_fin_recv_bal_qty=$bal_fin_recv_qty[$cid]-$bal_fin_recv_ret_qty[$cid];
                                    if($cid!=0) echo number_format($tot_fin_recv_bal_qty,2).'<hr><br/>';
                                    $tot_fin_balance_qty+=$tot_fin_recv_bal_qty;
									 $tot_fin_balance_qty_bal[$cid]=$tot_fin_recv_bal_qty;
                                }
                              ?>
                            </div>
                             </td>
                             <td width="80" align="right" title="Fin. Req-Total Fin. Recv">
                               <div style="word-break:break-all;font-size:small">
							  <?
                                $tot_fin_recv_bal_qty=0;
                             	foreach($color_ids as $cid)
                                { 
                                    
									$total_fin_req_qty_up=$tot_fin_reg_qty_bal[$cid];
									$totalfin_recv_balance=$tot_fin_balance_qty_bal[$cid];
                                    if($cid!=0) echo number_format($total_fin_req_qty_up-$totalfin_recv_balance,2).'<hr><br/>';
                                   $tot_fin_recv_bal_qty+=$total_fin_req_qty_up-$totalfin_recv_balance;
								    $tot_fin_recv_bal_qty_arr[$cid]=$total_fin_req_qty_up-$totalfin_recv_balance;
                                }
                              ?>
                            </div>
                             </td>
                             <td width="80" align="right" title="(Fin Recv. Balance*100)/Fin. Req.">
                              <div style="word-break:break-all;font-size:small">
							  <?
                                $tot_fin_balance_qty_per=0;
                             	foreach($color_ids as $cid)
                                { 
                                   
									$fin_recv_bal=$tot_fin_recv_bal_qty_arr[$cid];
									$fin_req=$tot_fin_reg_qty_bal[$cid];
                                    if($cid!=0) echo number_format((($fin_recv_bal*100)/$fin_req),2).'<hr><br/>';
                                   $tot_fin_balance_qty_per+=(($fin_recv_bal*100)/$fin_req);
                                }
                              ?>
                            </div>
                             </td>
                             <td width="80" align="right"><p><? echo number_format($grey_used_qty,2,'.',''); ?> </p></td>
                           
                            <td width="80" align="right" title="Grey Used Qty-Fin Recv Qty"><p><? echo number_format($process_adj,2,'.',''); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($fin_recv_reject,2,'.',''); ?></p></td>
                            
                            <td width="80" align="right" title="Claimable=(Gray_Issue+Fin Rec Ret)-(Fin Recv+Issue Ret+Proc Adj+Fin recv Reject);"><p><? echo number_format($calimable_qty,2,'.',''); ?></p></td>
                           <td width="60" align="right"><p><? echo number_format($process_percent,2,'.',''); ?></p></td>
                             
                            <td width="" align="right" title="Process Loss As Budget"><p><? echo number_format($process_loss_as_budget,2,'.',''); ?></p></td>
                            
                           
                        </tr>
                    <?
							$total_po_qty+=$po_qnty;
							$total_grey_issue_return_qty+=$issue_return;
							$total_fin_recv_qty+=$tot_fin_recv;
							$total_fin_recv_ret_qty+=$tot_fin_recv_ret_qty;
							$total_issue_qty+=$grey_issue_qty+$grey_issue_qty_ret;
							$total_process_loss_budget+=$process_loss_as_budget;
							$total_balance+=$tot_balance;
							$total_fin_recv_reject_qty+=$fin_recv_reject;
							$total_calimable_qty+=$calimable_qty;
							$total_grey_req_qty+=$grey_req_qty;
							$total_fin_req_qty+=$fin_req_qty;
							$total_process_adj+=$process_adj;
							$total_grey_used+=$grey_used_qty;
							$total_fin_recv_qty+=$tot_fin_recv_qty;
							$total_fin_recv_balance_qty+=$tot_fin_recv_bal_qty;
							$total_grey_issue_bal+=$grey_issue_qty-$issue_return;
							$i++;
						} 
						?>
                     </table>
                      <table width="2180" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                       <tr>
                            <td width="40">&nbsp;</th>
                            <td width="120">&nbsp;</th>  
                            <td width="100">&nbsp;</th>
                            <td width="120">&nbsp;</th>
                            <td width="100">&nbsp;</th>
                            <td width="100">Total</th>
                            <td width="80" id="value_tot_po"><? echo number_format($total_po_qty,2); ?></th>
                            <td width="150" align="right"><? //echo number_format($tot_opening,2);  ?></td>
                             <td width="80" id="value_tot_grey_req"><? //echo number_format($total_po_qty,2); ?></th>
                           
                            <td width="100" align="right" id="value_tot_issue"><? echo number_format($total_issue_qty,2);  ?>
                            </td>
                            <td width="80" align="right" id="value_tot_grey_return"><? echo number_format($total_grey_issue_return_qty,2);  ?></td>
                            <td width="80" align="right" id="value_tot_grey_total"><? echo number_format($total_grey_issue_bal,2);  ?></td>
                          
                             <td width="100" align="right" id=""><? //echo number_format($total_grey_used,2);  ?></td>
                              <td width="80" align="right" id="value_tot_grey_req_total"><? echo number_format($total_fin_req_qty,2);  ?></td>
                             <td width="80" align="right" id="value_tot_grey_total"><? echo number_format($total_fin_recv_qty,2);  ?></td>
                             
                           
                            <td width="80" align="right" id="value_total_receive"><? echo number_format($total_fin_recv_ret_qty,2);  ?></td>
                             <td width="80" align="right" id="" title="total Fin Recv Bal"><? echo number_format($total_fin_recv_qty,2);  ?></td>
                             <td width="80" align="right" id=""><? echo number_format($total_fin_recv_balance_qty,2);  ?></td>
                             <td width="80" align="right" id=""><? //echo number_format($total_fin_recv_ret_qty,2);  ?></td>
                             <td width="80" align="right" id=""><? echo number_format($total_grey_used,2);  ?></td>
                            
                            <td width="80" align="right" id="value_tot_recv_ret"><? echo number_format($total_process_adj,2);  ?></td>
                            <td width="80" align="right" id="value_tot_recv_ret"><? echo number_format($total_fin_recv_reject_qty,2);  ?></td>
                            <td width="80" align="right" id="value_tot_balance"><? echo number_format($total_calimable_qty,2);  ?></td>              
                           
                            <td width="60" align="right" id="value_tot_process_budget"><? //echo number_format($total_process_loss_budget,2);  ?></td> 
                                   
                            <td align="right" id="value_tot_claimable"><? echo number_format($total_process_loss_budget,2);  ?></td>                      </tr>
                  </table>  
            </div>
            </div>
        </fieldset> 
	<?
	} //Style Wise End
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	
	exit();
}


if($action=="grey_issue_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:650px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="7"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="100">Booking No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qty</th>
                        <th>Recv.Return Qty</th>
                    </tr>
				</thead>
             </table>
             <div style="width:660px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $issue_to='';
					//$colors_id=explode(",",$colors);
					if($issue_number!='') $issue_number_cond="and a.issue_number ='$issue_number'";
					if($db_type==2)
					{
					if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					}
					else
					{
						if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id='' ";
					}
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null";
                   $sql="select a.id,a.issue_number, a.issue_date, a.issue_purpose,a.booking_no,
				  (case when a.entry_form in (16) and a.item_category=13 then b.issue_qnty end) as issue_qnty,
				  (case when a.entry_form=45 and a.item_category=13  then b.issue_qnty end) as recv_ret_qnty
				  from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form in(45,16) and a.style_ref='$style' and a.knit_dye_source=$knit_source and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $color_con $issue_number_cond";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    { 
						$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                        echo number_format($row[csf('issue_qnty')],2);
                                        $total_issue_qnty+=$row[csf('issue_qnty')];
                                   
                                ?>
                            </td>
                            <td align="right">
                                <?
                                        echo number_format($row[csf('recv_ret_qnty')],2);
                                        $total_issue_qnty_ret+=$row[csf('recv_ret_qnty')];
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_ret,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_ret,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
  
    <br>
    
    
    <?
	exit();
}
if($action=="style_grey_issue_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:650px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="7"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="100">Booking No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qty</th>
                        <th>Recv.Return Qty</th>
                    </tr>
				</thead>
             </table>
             <div style="width:660px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                    <?
					$sql_data_grey_ret=("select   a.received_id,
						sum(case when c.entry_form in (46) then c.quantity end) as recv_return
						
						from inv_issue_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id  and c.trans_id=b.id and a.knit_dye_company=$knit_source  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and c.po_breakdown_id in($po_id) and a.item_category=2 and a.entry_form in(46) and c.entry_form in(46)  and a.knit_dye_source=$knit_source  
						 group by   a.received_id ");
						 $result_data_rec_ret=sql_select($sql_data_grey_ret);
						 $recv_ret_data_qty=array();
						foreach($result_data_rec_ret as $row)
						{
							 $recv_ret_data_qty[$row[csf('received_id')]]['ret_qty']=$row[csf('recv_return')];
						}
						
                    $i=1; $issue_to='';
					//$colors_id=explode(",",$colors);
					$knitting_company=str_replace("'","",$knitting_company);
					if($knitting_company!=0)
					{
						$kint_com="and a.knit_dye_company=$knitting_company";
					}
					else
					{
						$kint_com="";
					}
					
					if($issue_number!='') $issue_number_cond="and a.issue_number ='$issue_number'";
					if($db_type==2)
					{
					if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					}
					else
					{
						if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id='' ";
					}
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null"; issue_number style
                $sql="select a.id,a.issue_number,a.received_id, a.issue_date, a.issue_purpose,a.booking_no,
				  sum(d.quantity) as issue_qnty
				 
				  from inv_issue_master a, inv_grey_fabric_issue_dtls b,product_details_master c,order_wise_pro_details d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id and b.prod_id=c.id and d.dtls_id=b.id and f.job_no=e.job_no_mst and d.po_breakdown_id=e.id and a.entry_form in(16,61) and d.entry_form in(16,61)  and a.knit_dye_source=$knit_source and c.detarmination_id=$deter_id and a.id in($issue_number) and f.style_ref_no='$style' and b.prod_id=$prod_id and d.po_breakdown_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $kint_com group by a.id,a.issue_number,a.received_id, a.issue_date, a.issue_purpose,a.booking_no";
                    $result=sql_select($sql); // 	
        			foreach($result as $row)
                    { 
						$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
							$ret_qty=$recv_ret_data_qty[$row[csf('received_id')]]['ret_qty'];
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                        echo number_format($row[csf('issue_qnty')],2);
                                        $total_issue_qnty+=$row[csf('issue_qnty')];
                                   
                                ?>
                            </td>
                            <td align="right">
                                <?
                                        echo number_format($ret_qty,2);
                                        $total_issue_qnty_ret+=$ret_qty;
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_ret,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_ret,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
  
    <br>
    
    
    <?
	exit();
}
if($action=="fin_fab_recv_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:550px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="6"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="120">Booking No</th>
                    <th width="80">Rec. Date</th>
                    <th width="100">Rec. Basis</th>
                    <th>Receive Qnty</th>
                    
				</thead>
             </table>
             <div style="width:560px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0">
                    <?
					//if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null";
					
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company=''; $recv_data_arr=array();
                   $sql="select a.booking_no, a.recv_number, a.receive_date, a.receive_basis,  b.prod_id, c.quantity as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in($po_id) and c.color_id='$colors' and b.fabric_description_id='$deter_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                             <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    <?
	exit();
}


//style start...
if($action=="fin_fab_recv_popup_style")
{
	echo load_html_head_contents("Finish Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$batch_booking_no_arr=return_library_array( "select a.id, a.booking_no from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id=b.mst_id", "id", "booking_no");
	$color_name_arr=return_library_array( "select a.id, a.color_name from lib_color a ", "id", "color_name");
	?>
<fieldset style="width:650px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="120">Booking No</th>
                    <th width="80">Rec. Date</th>
                    <th width="100">Rec. Basis</th>
                    <th width="100">Color</th>
                    <th>Receive Qnty</th>
                    
				</thead>
             </table>
             <div style="width:660px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                    <?
					//if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null";
					
                    $knitting_company=str_replace("'","",$knitting_company);
					if($knitting_company!=0)
					{
						$kint_com="and a.knitting_company=$knitting_company";
					}
					else
					{
						$kint_com="";
					}
					
					//b.pi_wo_batch_no as batch_no
					//style,colors,prod_id,deter_id
					//
					 /*$sql_fab_recv=("select b.id as dtls_id ,b.gsm,b.width,c.po_breakdown_id,c.color_id,a.knitting_company,d.detarmination_id as deter_id,
						sum(case when c.entry_form in(7,37) and a.item_category in(2)  then b.grey_used_qty end) as grey_used_qty,
						sum(case when c.entry_form in(7,37) and a.item_category=2  then c.returnable_qnty end) as reject_qty,
						sum(case when c.entry_form in(7,37) and a.item_category in(2,13)  then c.quantity  end) as finish_recv
						 from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id  and d.id=c.prod_id  and d.id=b.prod_id and a.company_id=$cbo_company_name  and c.entry_form in(7,37) and  a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond  group by b.id,a.knitting_company,c.po_breakdown_id,b.gsm,b.width,c.color_id,d.detarmination_id");*///and e.gsm=$gsm and e.width='$dia'
					$colors=str_replace("'","",$colors);
					//echo $style;
					$booking_no=str_replace("'","",$prod_id);
					$diagsm=explode("_",$gsm_dia);
					$gsm=$diagsm[0];
					$dia=$diagsm[1];
					if($colors!=0) $colors_con=" and c.color_id in($colors)";else $colors_con="and c.color_id in(0)";
					$i=1;
                    $total_fabric_recv_qnty=0; $dye_company=''; $recv_data_arr=array();
					    $sql=("select a.booking_no, a.recv_number, a.receive_date, a.receive_basis,c.prod_id,c.color_id,
						sum(case when c.entry_form in(37,7)   then c.quantity  end) as finish_recv
						 from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, product_details_master d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id  and d.id=c.prod_id  and d.id=b.prod_id and  e.job_no_mst=f.job_no and c.po_breakdown_id=e.id  and c.entry_form in(37,7)  and a.entry_form in(37,7) and  a.item_category in(2,13) and  d.item_category_id in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and b.fabric_description_id='$deter_id' and f.style_ref_no='$style' $colors_con $kint_com and b.trans_id>0 group by a.booking_no, a.recv_number, a.receive_date, a.receive_basis,c.prod_id,c.color_id");
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                        $total_fabric_recv_qnty+=$row[csf('finish_recv')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                             <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="100"><? echo $color_name_arr[$row[csf('color_id')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('finish_recv')],2); ?></td>
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    <?
	exit();
}

?>