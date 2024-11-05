<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_party_type")
{
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if ($data == 1) //Yes
	{
		echo create_drop_down("cbo_buyer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active = 1 and comp.is_deleted = 0 ".$company_cond." order by comp.company_name", "id,company_name", 1, "-- Select Party--", "", "", 0, 0);
	}	
	else if ($data == 2) //No
	{
		echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$selected_company and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Party --", $selected, "");
	}
	//all
	else
	{
		echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- Select Party--", $selected, "", 1);
	}
	exit();
}

if($action=="fsoNo_popup")
{
  	echo load_html_head_contents("Job Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
		
			function js_set_value(job_id,job_no,booking_no)
			{	
				document.getElementById('hidden_fso_id').value=job_id;
				document.getElementById('hidden_fso_no').value=job_no;
				document.getElementById('hidden_booking_no').value=booking_no;
				parent.emailwindow.hide();
			}
		
	    </script>
	</head>
	<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Within Group</th>
	                    <th>Search By</th>
	                    <th>Search</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_fso_id" id="hidden_fso_id" value="">
	                         <input type="hidden" name="hidden_fso_no" id="hidden_fso_no" value="">
	                          <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td align="center">	
	                        <?
	                            echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", $cbo_within_group,$dd,0 );
	                        ?>
	                    </td>   
	                    <td align="center">	
	                        <?
	                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
	                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>                 
	                    <td align="center">				
	                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td> 						
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_fso_search_list_view', 'search_div', 'bill_processing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
	            <div id="search_div" style="margin-top:10px"></div>   
	        </form>
	    </fieldset>
	</div>    
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_fso_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1)
		{
			$search_field_cond=" and job_no like '%".$search_string."'";
		}
		else if($search_by==2)
		{
			$search_field_cond=" and sales_booking_no like '%".$search_string."'";
		}
		else
		{
			$search_field_cond=" and style_ref_no like '".$search_string."%'";
		}
	}
		
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="90">Sales Order No</th>
            <th width="60">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>               
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 
                if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('sales_booking_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
	<?	
	exit();	
}

if($action=="booking_popup")
{
	echo load_html_head_contents("Booking", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_booking_id').val( id );
			$('#hide_booking_no').val( ddd );
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
		
		function fn_generate_list(){
			if((form_validation('txt_booking','Booking')==false) && (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');			}
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
							<th>Booking</th>
							<th>Booking Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_booking" id="txt_booking" placeholder="Booking No" />
								</td>
								<td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" value="" readonly/>
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" value="" readonly/>
								</td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="fn_generate_list()" style="width:70px;" />
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

if($action=="create_booking_no_search_list_view")
{
	list($company_id,$buyer_name,$booking_no,$strt_sate,$end_date,$year)=explode('**',$data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_name";
	}

	if($booking_no!='') $where_cond .=" and a.booking_no like('%".trim($booking_no)."')";
	

	if($strt_sate!='' and $end_date!=''){
		if($db_type==0)
		{
			$strt_sate=change_date_format($strt_sate,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$strt_sate=change_date_format($strt_sate,'','',1);
			$end_date=change_date_format($end_date,'','',1);
		}
		$where_cond .=" and a.booking_date between '$strt_sate' and '$end_date'";		
	}


	$arr=array (0=>$buyer_arr);

	$sql = "select a.id,a.buyer_id,a.booking_no,a.job_no, a.booking_date  from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $buyer_id_cond $where_cond";	
	
	echo create_list_view("tbl_list_search", "Buyer Name,Booking No,Job No,Booking Date", "100,100,200,60","610","270",0, $sql , "js_set_value", "id,booking_no", "", 1, "buyer_id,0,0,0", $arr , "buyer_id,booking_no,job_no,booking_date", "",'','0,0,0,0','',1) ;
	exit();
}

if($action=="batch_popup")
{
	echo load_html_head_contents("Batch", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_batch_id').val( id );
			$('#hide_batch_no').val( ddd );
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
		
		
		function fn_generate_list(){
			if((form_validation('txt_batch','Company Name')==false) && (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_batch').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>', 'create_batch_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			}
		}
		
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Batch</th>
							<th>Batch Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:130px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_batch_id" id="hide_batch_id" value="" />
							<input type="hidden" name="hide_batch_no" id="hide_batch_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_batch" id="txt_batch" placeholder="Batch No" />
								</td>
								<td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" value="" readonly/>
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px;" value="" readonly/>
								</td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="fn_generate_list()" style="width:70px;" />
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

if($action=="create_batch_search_list_view")
{
	list($company_id,$batch_no,$strt_sate,$end_date,$year)=explode('**',$data);
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	if($batch_no!='') $where_cond .=" and a.batch_no like('%".trim($batch_no)."%')";

	if($strt_sate!='' and $end_date!=''){
		if($db_type==0)
		{
			$strt_sate=change_date_format($strt_sate,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$strt_sate=change_date_format($strt_sate,'','',1);
			$end_date=change_date_format($end_date,'','',1);
		}
		$where_cond .=" and a.batch_date between '$strt_sate' and '$end_date'";		
	}


	$arr=array (1=>$color_arr);

	$sql = "select a.id,a.batch_no,a.batch_date,a.color_id from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $where_cond";	
	
	echo create_list_view("tbl_list_search", "Batch No,Color,Batch Date", "200,100,100","610","270",0, $sql , "js_set_value", "id,batch_no", "", 1, "0,color_id,0", $arr , "batch_no,color_id,batch_date", "",'','0,0,3','',1) ;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$company_short_name_arr = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$cbo_buyer_name= str_replace("'","",$cbo_buyer_name);
	$txt_booking_no_show= str_replace("'","",$txt_booking_no_show);
	$txt_booking_no= str_replace("'","",$txt_booking_no);
	$txt_batch_no= str_replace("'","",$txt_batch_no);
	$txt_batch_id= str_replace("'","",$txt_batch_id);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);
	$txt_fso_no= str_replace("'","",$txt_fso_no);
	$hdn_fso_id= str_replace("'","",$hdn_fso_id);
	$cbo_year= str_replace("'","",$cbo_year);
	$txt_challan_no= str_replace("'","",$txt_challan_no);

	if($company_name==0) $company_cond=""; else $company_cond="and a.company_id='$company_name'";

	if($within_group>0)
	{
		$wg_cond    = "and c.within_group=$within_group";
	}
	$buyer_cond = ($cbo_buyer_name != 0) ? " and c.buyer_id=$cbo_buyer_name" : "";

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$delv_date_cond = " and a.issue_date between '$txt_date_from' and '$txt_date_to' ";
	}
	if($hdn_fso_id!="")
	{
		$fso_id_cond= "and c.id in('$hdn_fso_id')";
	}else {
		$fso_id_cond = "";
	}

	if($txt_fso_no!="")
	{
		$fso_no_cond= " and c.job_no like '%$txt_fso_no%'";

	}else{
		$fso_no_cond="";
	}

	if($txt_booking_no_show!="")
	{
		$booking_no_show_cond2= "and c.sales_booking_no like'%$txt_booking_no_show%'";
	}else{
		$booking_no_show_cond2="";
	}
	if($txt_booking_no!="")
	{
		$booking_id_cond2= "and c.booking_id in('$txt_booking_no')";
		$booking_no_show_cond2="";
	}else {
		$booking_id_cond = "";
		$booking_id_cond2 = "";
	}

	if($txt_batch_no!="")
	{
		$batch_no_cond= "and e.batch_no like'%$txt_batch_no%'";
	}else{
		$batch_no_cond="";
	}
	if($txt_batch_id!="")
	{
		$batch_id_cond= "and e.id in('$txt_batch_id')";
		$batch_no_cond="";
	}else {
		$batch_id_cond = "";
	}

	if($txt_challan_no!="")
	{
		$challan_no_cond= "and x.sys_number like'%$txt_challan_no%'";
	}else {
		$challan_no_cond = "";
	}

	if($db_type==0)
	{
		if($cbo_year==0) $year_cond=""; else $year_cond="and YEAR(a.insert_date)=$cbo_year";
	}
	else if($db_type==2)
	{
		if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}

	$con = connect();
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (291222,291223)");
    oci_commit($con);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
	}
	unset($deter_array);

	$sql_data="SELECT x.sys_number as multi_challan_no, a.issue_date, a.issue_number, a.company_id, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.within_group, 
	c.style_ref_no, b.batch_id,e.batch_no, d.color as color_id, d.detarmination_id, b.prod_id, d.gsm, d.dia_width, b.remarks, b.issue_qnty, x.delivery_to 
	from pro_fin_deli_multy_challan_mst x, pro_fin_deli_multy_challa_dtls y, inv_issue_master a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c, product_details_master d, pro_batch_create_mst e 
	where x.id=y.mst_id and y.delivery_id=a.id and y.delivery_dtls_id=b.id and x.entry_form=231 
	and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and a.id=b.mst_id and a.entry_form=224 and a.fso_id=c.id and b.prod_id=d.id and b.batch_id=e.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $delv_date_cond $batch_no_cond $fso_no_cond $fso_id_cond $buyer_cond $wg_cond $booking_no_show_cond2 $booking_id_cond2 $challan_no_cond $year_cond";
   	//echo $sql_data;die;
	$sql_data_result=sql_select($sql_data);
	$barcode_NOs="";
	$data_array=array();
	foreach($sql_data_result as $row)
	{
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["issue_qnty"]+=$row[csf("issue_qnty")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["gsm"]=$row[csf("gsm")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["dia_width"]=$row[csf("dia_width")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] .=$row[csf("remarks")].",";
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] =$row[csf("batch_no")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] =$row[csf("color_id")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["sales_booking_no"] =$row[csf("sales_booking_no")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["company_id"] =$row[csf("company_id")];
		if($row[csf("within_group")] ==1)
		{ 
			$booking_buyer= $row[csf("po_buyer")];
		}
		else
		{
			$booking_buyer= $row[csf("buyer_id")];
		}
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_buyer"] =$booking_buyer;
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] =$row[csf("style_ref_no")];
		$data_array[$row[csf("multi_challan_no")]][$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["delivery_to"] =$row[csf("delivery_to")];

		$all_batch_id_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$all_fso_id_arr[$row[csf("fso_id")]]=$row[csf("fso_id")];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 291222, 1,$all_batch_id_arr, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 291223, 2,$all_fso_id_arr, $empty_arr);
    oci_commit($con);
	//print_r($all_batch_id_arr);die;

	if(!empty($all_batch_id_arr))
	{
		$production_sql_data=sql_select("SELECT b.prod_id, b.batch_id, b.no_of_roll, c.grey_used_qty as grey_qnty, c.quantity as finish_qnty, c.id
		from GBL_TEMP_ENGINE t, inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
		where t.ref_val=b.batch_id and t.ref_from=1 and t.entry_form=291222 and t.user_id=$user_id and a.id=b.mst_id and a.entry_form=7 and b.id=c.dtls_id and c.entry_form=7 and b.status_active=1 and b.is_deleted=0 and c.is_sales=1");

		$production_data_arr=array();
		foreach($production_sql_data as $value)
		{
			if($dupli_qnty_chk[$value[csf("id")]]=="")
			{
				$dupli_qnty_chk[$value[csf("id")]]=$value[csf("id")];
				$production_data_arr[$value[csf("batch_id")]][$value[csf("prod_id")]]['grey_qty']+=$value[csf("grey_qnty")];	
				$production_data_arr[$value[csf("batch_id")]][$value[csf("prod_id")]]['fin_qty']+=$value[csf("finish_qnty")];	
				$production_data_arr[$value[csf("batch_id")]][$value[csf("prod_id")]]['no_of_roll']+=$value[csf("no_of_roll")];
			}
		}

		$shade_sql_data = return_library_array("SELECT a.batch_id, sum(b.ratio) as ratio from GBL_TEMP_ENGINE t, pro_recipe_entry_mst a, pro_recipe_entry_dtls b, product_details_master c where t.ref_val=a.batch_id and t.ref_from=1 and t.entry_form=291222 and t.user_id=$user_id and a.id =b.mst_id and a.entry_form=59 and b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and b.sub_process_id=117 and c.item_category_id=6  group by a.batch_id", "batch_id", "ratio");
	}
	
	if(!empty($all_fso_id_arr))
	{
		$fso_data_sql=sql_select("SELECT a.job_no_mst, a.color_id, a.determination_id, a.color_type_id from GBL_TEMP_ENGINE t, fabric_sales_order_dtls a where t.ref_val=a.mst_id and t.ref_from=2 and t.entry_form=291223 and t.user_id=$user_id and a.status_active=1 and a.is_deleted=0");
		foreach ($fso_data_sql as $row) 
		{
			$fso_data[$row[csf("job_no_mst")]][$row[csf("color_id")]][$row[csf("determination_id")]] .= $color_type[$row[csf("color_type_id")]].",";
		}
	}
	// echo "<pre>"; print_r($fso_data); die;   
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (291222,291223)");
    oci_commit($con);

	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<div align="left">
		<fieldset style="width:2510px;">
			<table width="2500" cellspacing="0" cellpadding="0" border="0" rules="all" >
				<tr class="form_caption">
					<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="22" align="center"><? echo $company_arr[$company_name]; ?></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" >
				<thead>	
					<tr>
						<th width="30">SL</th>
						<th width="120">Multi Challan No</th>
						<th width="100">Delivery To</th>
						<th width="80">Delivery Date</th>
						<th width="120">Delivery Challan</th>
						<th width="150">Textile ref.</th>
						<th width="100">Booking Buyer</th>
						<th width="120">Style No</th>
						<th width="150">Batch No</th>
						<th width="100">Color</th>
						<th width="100">Status</th>
						<th width="100">Shade %</th>

						<th width="100">Fabric Type</th>
						<th width="150">Fabric Description</th>
						<th width="80">F.GSM</th>
						<th width="80">Fabric Dia</th>
						<th width="100">No Of Roll</th>
						<th width="100">Finish Qty/Kg</th>
						<th width="100">Grey Qty/Kg</th>
						<th width="100">Process Loss</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Delivery Factory</th>
						<th width="100">Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:2500px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" id="tbl_list_search">
					<?
					$i=1;
					ksort($data_array);
					foreach ($data_array as $multi_challan_no => $multi_challan_no_v) 
					{
						foreach ($multi_challan_no_v as $issue_date => $issue_date_data) 
						{
							foreach ($issue_date_data as $issue_no => $issue_no_data) 
							{
								foreach ($issue_no_data as $fso_no => $fso_no_data) 
								{
									foreach ($fso_no_data as $batch_id => $batch_id_data) 
									{
										foreach ($batch_id_data as $prod_id => $row) 
										{
											if ($i%2==0){
												$bgcolor="#E9F3FF";
											}
											else{
												$bgcolor="#FFFFFF";
											}
											
											$grey_qty = $production_data_arr[$batch_id][$prod_id]['grey_qty'];
											$fin_qty = $production_data_arr[$batch_id][$prod_id]['fin_qty'];
											$no_of_roll = $production_data_arr[$batch_id][$prod_id]['no_of_roll'];

											$process_loss  = (($grey_qty - $fin_qty) * 100 ) / $fin_qty;

											$color_type_names = implode(",",array_filter(array_unique(explode(",",chop($fso_data[$fso_no][$row['color_id']][$row["detarmination_id"]],",")))));
											?>						
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30" ><? echo $i; ?></td>
												<td width="120" class="word_wrap_break"  align="center"><p><? echo $multi_challan_no;?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $row['delivery_to'];?></p></td>
												<td width="80" class="word_wrap_break"  align="center"><p><? echo $issue_date;?></p></td>
												<td width="120" class="word_wrap_break"  align="center"><p><? echo $issue_no;?></p></td>
												<td width="150" class="word_wrap_break"  align="center"><p><? echo $fso_no;?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $buyer_arr[$row['booking_buyer']];?></p></td>
												<td width="120" class="word_wrap_break"  align="center"><p><? echo $row['style_ref_no'];?></p></td>
												<td width="150" class="word_wrap_break"  align="center"><p><? echo $row['batch_no']?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $color_arr[$row['color_id']];?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $color_type_names?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $shade_sql_data[$batch_id];?></p></td>

												<td width="100" class="word_wrap_break" align="center"><p><? echo  $construction_arr[$row['detarmination_id']];?></p></td>
												<td width="150" class="word_wrap_break" align="center"><p><? echo  $composition_arr[$row['detarmination_id']];?></p></td>
												<td width="80" class="word_wrap_break" align="center"><p><? echo  $row['gsm'];?></p></td>
												<td width="80" class="word_wrap_break" align="center"><p><? echo  $row['dia_width'];?></p></td>
												<td width="100" class="word_wrap_break"  align="right"><p><? echo $no_of_roll; ?></p></td>
												<td width="100" class="word_wrap_break"  align="right"><p><? echo number_format($fin_qty,2);?></p></td>
												<td width="100" class="word_wrap_break"  align="right"><p><? echo number_format($grey_qty,2);?></p></td>
												<td width="100" class="word_wrap_break"  align="right" title="Process Loss  = ((Grey Qty - Finish Qty) x 100 ) / Finish Qty"><p><? if ($process_loss>0) echo number_format($process_loss,2); else echo '0.00'; ?></p></td>
												<td width="100" class="word_wrap_break" align="right"><p><? echo number_format($row['issue_qnty'],2)?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo $company_arr[$row['company_id']]?></p></td>
												<td width="100" class="word_wrap_break"  align="center"><p><? echo implode(",",array_filter(explode(",",chop($row['remarks']))));?></p></td>										
											</tr>
											<?
											$i++;
											$total_issue_qnty+=$row['issue_qnty']; 
											
										}
									}
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table class="rpt_table" width="2480" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <tfoot>
	                <tr>
	                    <th width="30"></th>
						<th width="120"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="120"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>

						<th width="100"></th>
						<th width="150"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100" align="right"><strong>Total</strong></th>
						<th width="100" align="right" id="value_total_issue_qnty"><strong><? echo number_format($total_issue_qnty,2,".",""); ?></strong></th>
						<th width="100"></th>
						<th width="100"></th>
	                </tr>
	            </tfoot>
	        </table>
	    </fieldset>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
	exit();
}

?>