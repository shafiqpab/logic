<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*if ($action=="load_drop_down_po_company")
{
	if($data ==1){
		echo create_drop_down( "cbo_lccompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/bill_processing_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	}else{
		echo create_drop_down( "cbo_lccompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "" );
	}

}
*/

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
		$withinGroupCond = "and e.within_group=$within_group";
		$wg_cond    = "and c.within_group=$within_group";
		$wg_cond_2    = "and e.within_group=$within_group";
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
		$booking_no_show_cond= "and a.booking_no like'%$txt_booking_no_show%'";
		$booking_no_show_cond2= "and c.sales_booking_no like'%$txt_booking_no_show%'";
	}else{
		$booking_no_show_cond="";
		$booking_no_show_cond2="";
	}
	if($txt_booking_no!="")
	{
		$booking_id_cond= "and a.booking_id in('$txt_booking_no')";
		$booking_id_cond2= "and c.booking_id in('$txt_booking_no')";
		$booking_no_show_cond="";
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
		$challan_no_cond= "and a.issue_number like'%$txt_challan_no%'";
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

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if($hdn_fso_id!="" || $txt_fso_no!="" || $within_group>0 || $cbo_buyer_name>0 || $txt_booking_no_show!="" || $txt_booking_no!="")
	{
		$salesResult=sql_select("SELECT c.id,c.sales_booking_no, c.booking_id, c.within_group,  c.po_job_no, c.buyer_id, c.po_buyer, c.job_no,c.style_ref_no, c.id as fso_id from  fabric_sales_order_mst c where  c.company_id=$company_name $wg_cond $fso_id_cond $fso_no_cond $booking_id_cond2 $booking_no_show_cond2 $buyer_cond group by c.id,c.sales_booking_no, c.booking_id, c.within_group , c.po_job_no, c.buyer_id, c.po_buyer, c.job_no,c.style_ref_no, c.id");
		$fsoIds="";
		foreach ($salesResult as $row)
		{
			$sallesDataArr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			$sallesDataArr[$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
			$sallesDataArr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$sallesDataArr[$row[csf('id')]]['po_job_no']=$row[csf('po_job_no')];
			$sallesDataArr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$sallesDataArr[$row[csf('id')]]['party']=$row[csf('buyer_id')];
			$sallesDataArr[$row[csf('id')]]['po_buyer']=$row[csf('po_buyer')];
			$sallesDataArr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$sallesDataArr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			$sallesDataArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$fsoIds.= "'".$row[csf('id')]."',";
		}
		$fsoIds=chop($fsoIds,',');
		$fsoIdsCond= "and b.order_id in($fsoIds)";
	}
	else
	{
		if ($fsoIds!="") {$fsoIdsCond= "and b.order_id in($fsoIds)";}else{$fsoIdsCond="";}
	}

	$mainQueryResult=sql_select("SELECT a.company_id,a.id,a.issue_number,a.issue_date,a.booking_no,a.store_id,a.location_id,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.order_id ,sum(b.issue_qnty) as issue_qnty ,sum(b.no_of_roll) as no_of_roll,e.extention_no,e.working_company_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and a.entry_form=318  and b.batch_id = e.id and b.prod_id = d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $year_cond $delv_date_cond $booking_id_cond $booking_no_show_cond $batch_id_cond $batch_no_cond $fsoIdsCond $challan_no_cond group by a.company_id,a.id,a.issue_number,a.issue_date,a.booking_no,a.store_id,a.location_id,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color,b.order_id,e.extention_no,e.working_company_id"); 

	if($hdn_fso_id=="" || $txt_fso_no=="" || $within_group==0)
	{
		$fsoIds="";
		foreach ($mainQueryResult as $row)
		{
			$fsoIds.= "'".$row[csf('order_id')]."',";
		}
		$fsoIds=chop($fsoIds,',');
		$salesResult=sql_select("SELECT c.id,c.sales_booking_no, c.booking_id, c.within_group,  c.po_job_no, c.buyer_id, c.po_buyer, c.job_no,c.style_ref_no, c.id as fso_id from  fabric_sales_order_mst c where  c.company_id=$company_name $wg_cond $fso_id_cond $fso_no_cond $booking_id_cond2 $booking_no_show_cond2 and c.id in($fsoIds) group by c.id,c.sales_booking_no, c.booking_id, c.within_group , c.po_job_no, c.buyer_id, c.po_buyer, c.job_no,c.style_ref_no, c.id");
		foreach ($salesResult as $row)
		{
			$sallesDataArr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			$sallesDataArr[$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
			$sallesDataArr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$sallesDataArr[$row[csf('id')]]['po_job_no']=$row[csf('po_job_no')];
			$sallesDataArr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$sallesDataArr[$row[csf('id')]]['party']=$row[csf('buyer_id')];
			$sallesDataArr[$row[csf('id')]]['po_buyer']=$row[csf('po_buyer')];
			$sallesDataArr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$sallesDataArr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			$sallesDataArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}

	$orderIds="";
	foreach ($mainQueryResult as $row) 
	{
		$orderIds.= "'".$row[csf('order_id')]."',";	
	}

	$orderIds_all=rtrim($orderIds,","); 
	$orderIds_alls=explode(",",$orderIds_all);
	$orderIds_alls=array_chunk($orderIds_alls,999); 
	$orderIds_conds=" and";
	foreach($orderIds_alls as $dtls_id)
	{
		if($orderIds_conds==" and")  $orderIds_conds.="(b.order_id in(".implode(',',$dtls_id).")"; else $orderIds_conds.=" or b.order_id in(".implode(',',$dtls_id).")";
	}
	$orderIds_conds.=")";
	
	$sql_data=sql_select("SELECT a.id as roll_table_id, a.barcode_no,a.roll_no,b.batch_id,e.batch_no, b.body_part_id, d.detarmination_id, a.qnty ,a.reject_qnty , a.dtls_id, b.trans_id, a.roll_id, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.trans_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.floor, b.room, b.rack_no, b.shelf_no, b.width_type, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, null as recv_number,f.company_id,f.id as issue_id,b.order_id,c.booking_entry_form,c.within_group,c.booking_without_order as booking_without_order_sales from inv_issue_master f, pro_roll_details a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e where f.id=b.mst_id and f.id=a.mst_id and a.dtls_id=b.id and a.entry_form=318 and a.po_breakdown_id = c.id and b.batch_id = e.id and b.prod_id = d.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.company_id=$company_name $orderIds_conds and a.is_returned!=1");	

	$barcode_NOs="";
	foreach($sql_data as $row)
	{
		$barcode_NOs.=$row[csf("barcode_no")].",";

		$sale_booking_no_sm_smn=explode('-', $row[csf("sales_booking_no")]);
		$sale_booking_no_sm_smn[1];
		$sales_booking_entry_from[$row[csf("job_no")]]['booking_entry_form']=$row[csf("booking_entry_form")];
		$sales_booking_entry_from[$row[csf("job_no")]]['within_group']=$row[csf("within_group")];
		$sales_booking_entry_from[$row[csf("job_no")]]['booking_without_order_sales']=$row[csf("booking_without_order_sales")];

		if ($row[csf("within_group")]==1 && $row[csf("booking_without_order_sales")]==0) 
		{
			$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		}
		if ($row[csf("within_group")]==1 && $row[csf("booking_without_order_sales")]==1) 
		{
			$non_order_booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		}

	}
	$barcode_Nos_all=rtrim($barcode_NOs,","); 
	$barcode_Nos_alls=explode(",",$barcode_Nos_all);
	$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
	$barcode_no_conds=" and";
	foreach($barcode_Nos_alls as $dtls_id)
	{
		if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
	}
	$barcode_no_conds.=")";

	$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
	FROM pro_roll_details a 
	WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
	$production_data_arr=array();
	foreach($production_sql_data as $value)
	{
		$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
	}
	$grey_used=array();
	foreach($sql_data as $row)
	{
		$ref_data = $row[csf('company_id')]."__".$row[csf('issue_id')]."__".$row[csf('order_id')]."__".$row[csf('batch_id')]."__".$row[csf('color_id')]."__".$row[csf('gsm')]."__".$row[csf('dia_width')]."__".$row[csf('detarmination_id')]."__".$row[csf('body_part_id')];
			
		$grey_used[$ref_data]['grey_qty']+=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
	}
	//var_dump($grey_used);

	$all_booking_ids= array_chunk($booking_id_arr, 999);
	$booking_ids_cond=" and(";
	foreach($all_booking_ids as $booking_ids)
	{
		if($booking_ids_cond==" and(") $booking_ids_cond.=" id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or id in(". implode(',', $booking_ids).")";
	}
	$booking_ids_cond.=")";

	if(!empty($all_booking_ids))
	{
		//$sql_booking_query = "SELECT a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";
		$booking_sql="SELECT booking_no, booking_type, is_short, company_id, po_break_down_id, item_category, fabric_source, job_no ,entry_form, is_approved
		from wo_booking_mst where booking_type in(1,4) $booking_ids_cond and is_short in(1,2) and status_active=1 and is_deleted=0";
	}
	$booking_type_result=sql_select($booking_sql);
	$booking_Arr=array();
	foreach($booking_type_result as $row) 
	{
		$booking_type_arr[$row[csf("booking_no")]]=$row[csf("booking_type")];
		$booking_is_short_arr[$row[csf("booking_no")]]=$row[csf("is_short")];

		$booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
        $booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
        $booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
	}
	$noOfbooking = count($non_order_booking_id_arr);
    if ($noOfbooking>0) 
    {
        $bookingCondition = '';        
        if($db_type == 2 && $noOfbooking > 1000)
        {
            $bookingCondition = " and (";
            $bookingArrNew = array_chunk($non_order_booking_id_arr,999);
            foreach($bookingArrNew as $prod)
            {
                $bookingCondition.=" id in('".implode("','",$prod)."') or";
            }
            $bookingCondition = chop($bookingCondition,'or');
            $bookingCondition .= ")";
        }
        else
        {
            $bookingCondition=" and id in('".implode("','",$non_order_booking_id_arr)."')";
        }

		$booking_sql="SELECT booking_no, company_id, po_break_down_id, item_category, fabric_source, job_no , is_approved, is_short, booking_type
		from WO_NON_ORD_SAMP_BOOKING_MST where booking_type=4 $bookingCondition and status_active=1 and is_deleted=0";
    }
    // echo $booking_sql;die;
    $booking_sql_dataArr = sql_select($booking_sql);
    $non_order_booking_Arr=array();
    foreach($booking_sql_dataArr as $row)
    {
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
    }

	ob_start();
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
</style>

<fieldset style="width:1790px;">
	<table width="1810" cellspacing="0" cellpadding="0" border="0" rules="all" >
		<tr class="form_caption">
			<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		</tr>
		<tr class="form_caption">
			<td colspan="28" align="center"><? echo $company_arr[$company_name]; ?></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1910" class="rpt_table" >
		<thead>	
				<tr>
					<th width="30">SL</th>
					<th width="150">Company</th>
					<th width="100">Party</th>
					<th width="120">Delivery ID</th>
					<th width="80">Delivery Date</th>
					<th width="120">Booking No</th>
					<th width="120">Job No</th>
					<th width="150">Buyer</th>
					<th width="150">Sales Order No</th>
					<th width="150">Batch No</th>
					<th width="250">Fabric Description</th>
					<th width="80">F.GSM</th>
					<th width="80">Fabric Dia</th>
					<th width="100">Fabric Color</th>
					<th width="80">Grey Wgt</th>
					<th width="80">Roll Qty(Kg)</th>
					<th>No Of Roll</th>
					</tr>
				</thead>
			</table>
			<div style="width:1930px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1910" class="rpt_table" id="tbl_list_search">
					<?

					// Fabric Sales Order Entry
				    $print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
				    $fReportId=explode(",",$print_report_format);
				    $fReportId=$fReportId[0];

					// Finish Fabric Roll Delivery To Garments
				    $frdg_print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=6 and report_id=216 and is_deleted=0 and status_active=1");
				    $frdgReportIds=explode(",",$frdg_print_report_format);
				    $frdgReportId=$frdgReportIds[0];
					$frdg_action='';
					if($frdgReportId==134)
					{
						$frdg_action='finish_delivery_print';
					}
					else if($frdgReportId==135)
					{
						$frdg_action='finish_delivery_print2';
					}
					else if($frdgReportId==136)
					{
						$frdg_action='finish_delivery_print3';
					}


					$i=1;
					foreach ($mainQueryResult as $row)
					{
						$sales_order_no=$sallesDataArr[$row[csf('order_id')]]['job_no'];	
						$sales_style_ref=$sallesDataArr[$row[csf('order_id')]]['style_ref_no'];	
						$sales_booking_id=$sallesDataArr[$row[csf('order_id')]]['booking_id'];	
						$sale_booking_no=$row[csf('booking_no')];	

						$booking_entry_form=$sales_booking_entry_from[$sales_order_no]['booking_entry_form'];
						$within_group=$sales_booking_entry_from[$sales_order_no]['within_group'];
						$booking_without_order_sales=$sales_booking_entry_from[$sales_order_no]['booking_without_order_sales'];

						if ($within_group==1 && $booking_without_order_sales==0) 
						{
							$booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
			                $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
			                $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
			                $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
			                $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
			                $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];
						}
						elseif ($within_group==1 && $booking_without_order_sales==1) 
						{
							$booking_company=$non_order_booking_Arr[$sale_booking_no]['booking_company_id'];
			                $booking_order_id=$non_order_booking_Arr[$sale_booking_no]['booking_order_id'];
			                $booking_fabric_natu=$non_order_booking_Arr[$sale_booking_no]['booking_fabric_natu'];
			                $booking_fabric_source=$non_order_booking_Arr[$sale_booking_no]['booking_fabric_source'];
			                $booking_job_no=$non_order_booking_Arr[$sale_booking_no]['booking_job_no'];
			                $is_approved_id=$non_order_booking_Arr[$sale_booking_no]['is_approved'];
						}

						if ($booking_company!="") 
						{
							// Budget Wise Fabric Booking and Main Fabric Booking V2
			                $print_report_format2=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
			                $fReportId2=explode(",",$print_report_format2);
			                $fReportId2=$fReportId2[0];

			                // Short Fabric Booking
			                $print_report_format3=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
			                $fReportId3=explode(",",$print_report_format3);
			                $fReportId3=$fReportId3[0];

			                // Sample with order
			                $print_report_format4=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
			                $fReportId4=explode(",",$print_report_format4);
			                $fReportId4=$fReportId4[0];

			                // Sample without order
			                $print_report_format5=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
			                $fReportId5=explode(",",$print_report_format5);
			                $fReportId5=$fReportId5[0];
			                // echo "SELECT format_id FROM lib_report_template WHERE template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1";
		            	}
						
						$fbReportId=0;
		                if ($booking_entry_form==86 || $booking_entry_form==118) 
		                {// Budget Wise Fabric Booking and Main Fabric Booking V2
		                    $fbReportId=$fReportId2;
		                }
		                else if($booking_entry_form==88)
		                {
		                    $fbReportId=$fReportId3;// Short Fabric Booking
		                }
		                else if($sale_booking_no_sm_smn[1]=='SM')
		                {
		                	$fbReportId=$fReportId4;// Sample with order
		                	$booking_entry_form='SM';
		                }
						else if($sale_booking_no_sm_smn[1]=='SMN')
		                {
		                	$fbReportId=$fReportId5;// Sample without order
		                	$booking_entry_form='SMN';
		                }



						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if($sallesDataArr[$row[csf('order_id')]]['within_group']==1)
						{
							$buyerName=$buyer_arr[$sallesDataArr[$row[csf('order_id')]]['po_buyer']];
							$partyName=$company_short_name_arr[$sallesDataArr[$row[csf('order_id')]]['party']];
						}
						else
						{
							$buyerName=$buyer_arr[$sallesDataArr[$row[csf('order_id')]]['buyer_id']];
							$partyName=$buyer_short_name_arr[$sallesDataArr[$row[csf('order_id')]]['party']];
						}

						$pop_ref = $row[csf('company_id')]."__".$row[csf('id')]."__".$row[csf('order_id')]."__".$row[csf('batch_id')]."__".$row[csf('color_id')]."__".$row[csf('gsm')]."__".$row[csf('dia_width')]."__".$row[csf('detarmination_id')]."__".$row[csf('body_part_id')];	

							
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" ><? echo $i; ?></td>
							<td width="150" class="word_wrap_break"  align="center"><p><? echo $company_arr[$row[csf('company_id')]];?></p></td>
							<td width="100" class="word_wrap_break"  align="center"><p><? echo $partyName;?></p></td>

							<? 
							 $data=$company_name.'*'.$row[csf('issue_number')].'*'.$row[csf('id')].'*'.$row[csf('issue_date')].'*'.$row[csf('store_id')].'*'.$row[csf('location_id')];

								echo "<td width='120' align='center'  class='word_wrap_break' ><p><a href='../../../inventory/finish_fabric/requires/finish_feb_roll_delivery_to_garments_controller.php?data=".$data."&action=$frdg_action' target='_blank'> ".$row[csf('issue_number')]." </a></p></td>";
	 							?>
							
							<td width="80" class="word_wrap_break"  align="center"><p><? echo change_date_format($row[csf('issue_date')]);?></p></td>
							<td width="120"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>



							<td width="120" class="word_wrap_break"><p><? echo $sallesDataArr[$row[csf('order_id')]]['po_job_no'];?></p></td>
							<td width="150" class="word_wrap_break"><p><? echo $buyerName;?></p></td>

							<td width="150"><? echo "<a href='##' onclick=\"generate_report(" . $company_name . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fReportId . "' )\">$sales_order_no</a>"; ?></td>

								<? $report_title='BATCH CARD'; $data=$company_name.'*'.$row[csf('batch_id')].'*'.$txt_batch_sl_no.'*'.$row[csf('batch_no')].'*'.$row[csf('extention_no')].'*'.$report_title.'*'.$sales_booking_id.'*'.$row[csf('working_company_id')].'*1';$fso_and_style=$sales_order_no.'<br>'.$sales_style_ref;

								  echo "<td width='150' align='center'><p><a href='../../../production/requires/batch_creation_controller.php?data=".$data."&action=batch_card_prog_wise' target='_blank'> ".$row[csf('batch_no')]." </a></p></td>";
	 							?>

							<td width="250" class="word_wrap_break" align="center"><p><? echo  $composition_arr[$row[csf('detarmination_id')]];?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $row[csf('gsm')];?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $row[csf('dia_width')];?></p></td>
							<td width="100" class="word_wrap_break" align="center" ><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>		
							<td width="80"  class="word_wrap_break" align="right" ><p><? echo number_format($grey_used[$pop_ref]['grey_qty'],2,".","");?></p></td>
							<td width="80"  class="word_wrap_break" align="right" ><p><? echo number_format($row[csf('issue_qnty')],2,".","");?></p></td>
							<td  class="word_wrap_break" align="right"><p><a href="##" onClick="openmypage_roll_qnty('<? echo $pop_ref;?>','roll_qnty_popup')"><?  echo number_format($row[csf('no_of_roll')],2,".","");?></a></p></td>
						</tr>
						<?
						$i++;
						$total_no_of_roll+=$row[csf('no_of_roll')];
						$total_grey_qnty+=$grey_used[$pop_ref]['grey_qty'];
						$total_issue_qnty+=$row[csf('issue_qnty')]; 
						 
					}
					?>
					<tr style="background-color:#D3D3D3;">
						<td colspan="14" align="right"><b>Total:</b></td>
						<td align="right" width="80"><b><? echo number_format($total_grey_qnty,2,".",""); ?></b></td>
						<td align="right" width="80"><b><? echo number_format($total_issue_qnty,2,".",""); ?></b></td>
						<td align="right"><b><? echo number_format($total_no_of_roll,2,".",""); ?></b></td>
					</tr>
				</table>
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


if($action=="roll_qnty_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$ref_data_arr =  explode("__", $ref_data);
	$company_id = $ref_data_arr[0];
	$issue_id = $ref_data_arr[1];
	$order_id = $ref_data_arr[2];
	$batch_id = $ref_data_arr[3];
	$color_id = $ref_data_arr[4];
	$gsm = $ref_data_arr[5];
	$dia_width = $ref_data_arr[6];
	$detarmination_id = $ref_data_arr[7];
	$body_part_id = $ref_data_arr[8];

	//$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			$(".flt").css("display","none");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
		}
		var tableFilters =
		{
			col_operation: {
				id: ["value_total_qty","value_total_rej","value_total_loss","value_total_gused"],
				col: [7,8.9,10],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

	</script>
	<fieldset style="width:910px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Roll Wise Details Info</b>
				</caption>
				<thead>
					<th width="40">SL</th>
					<th width="120">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="120">Batch No</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="60">Roll Qty.</th>
					<th width="60">Reject Qty.</th>
					<th width="50">Process Loss</th>
					<th>Grey Wgt.</th>
				</thead>
			</table>
			<div style="width:918px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?

						$composition_arr=array(); $constructtion_arr=array();
						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
						$data_array=sql_select($sql_deter);
						foreach( $data_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
						}
			
						$sql_data=sql_select("SELECT a.id as roll_table_id, a.barcode_no,a.roll_no,b.batch_id,e.batch_no, b.body_part_id, d.detarmination_id, a.qnty ,a.reject_qnty , a.dtls_id, b.trans_id, a.roll_id, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.trans_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.floor, b.room, b.rack_no, b.shelf_no, b.width_type, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, null as recv_number from inv_issue_master f, pro_roll_details a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e where f.id=b.mst_id and f.id=a.mst_id and a.dtls_id=b.id and a.entry_form=318 and a.po_breakdown_id = c.id and b.batch_id = e.id and b.prod_id = d.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$issue_id and f.company_id=$company_id and f.id=$issue_id and b.order_id=$order_id and b.batch_id=$batch_id and d.color=$color_id and d.gsm='$gsm' and d.dia_width='$dia_width' and d.detarmination_id=$detarmination_id and b.body_part_id=$body_part_id and a.is_returned!=1");	

						$barcode_NOs="";
						foreach($sql_data as $row)
						{
							$barcode_NOs.=$row[csf("barcode_no")].",";

						}
						$barcode_Nos_all=rtrim($barcode_NOs,","); 
						$barcode_Nos_alls=explode(",",$barcode_Nos_all);
						$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
						$barcode_no_conds=" and";
						foreach($barcode_Nos_alls as $dtls_id)
						{
							if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
						}
						$barcode_no_conds.=")";
						$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
						FROM pro_roll_details a 
						WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
						$production_data_arr=array();
						foreach($production_sql_data as $value)
						{
							$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
							$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
							$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
						}



						$i=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$prod_qty=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
							$qc_pass_qnty=$production_data_arr[$row[csf('barcode_no')]]['qc_pass_qnty'];
							$reject_qnty=$production_data_arr[$row[csf('barcode_no')]]['reject_qnty'];
							$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));
							$grey_used=$prod_qty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i;?></td>
								<td width="120"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="60"><? echo $row[csf('roll_no')]; ?></td>
								<td width="120"><? echo $row[csf('batch_no')]; ?></td>
								<td width="100"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
								<td width="100"><? echo $constructtion_arr[$row[csf('detarmination_id')]];?></td>
								<td width="100"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></td>
								<td width="60" align="right"><? echo  number_format($row[csf('qnty')],2,'.','');?></td>
								<td width="60" align="right"><? echo  number_format($reject_qnty,2,'.','');?></td>
								<td width="50" align="right"><? echo  number_format($processLoss,2,'.','');?></td>
								<td align="right"><? echo number_format($grey_used,2,'.','');?></td>
							</tr>

							<?
							$totalQnt+=$row[csf('qnty')];
							$totalRej+=$reject_qnty;
							$totalLoss+=$processLoss;
							$totalGused+=$grey_used;

							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40"></th>
					<th width="120"></th>
					<th width="60"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="60" id="value_total_qty" align="right"><? echo  number_format($totalQnt,2,'.','');?></th>
					<th width="60" id="value_total_rej" align="right"><? echo  number_format($totalRej,2,'.','');?></th>
					<th width="50" id="value_total_loss" align="right"><? echo  number_format($totalLoss,2,'.','');?></th>
					<th id="value_total_gused" align="right"><? echo  number_format($totalGused,2,'.','');?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<!-- <script>setFilterGrid('table_body',-1,tableFilters);</script> -->
	<?
	exit();
}

if($action=="report_generate_2")
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
		$challan_no_cond= "and a.issue_number like'%$txt_challan_no%'";
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


	$sql_data=sql_select("SELECT a.issue_date, a.issue_number, a.company_id, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.within_group, c.style_ref_no, b.batch_id,e.batch_no, d.color as color_id, d.detarmination_id, b.prod_id, d.gsm, d.dia_width, b.remarks, b.issue_qnty
   	from inv_issue_master a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c, product_details_master d, pro_batch_create_mst e 
   	where a.id=b.mst_id and a.entry_form=224 and a.fso_id=c.id and b.prod_id=d.id and b.batch_id=e.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $delv_date_cond $batch_no_cond $fso_no_cond $fso_id_cond $buyer_cond $wg_cond $booking_no_show_cond2 $booking_id_cond2 $challan_no_cond $year_cond");

	$barcode_NOs="";
	$data_array=array();
	foreach($sql_data as $row)
	{
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["issue_qnty"]+=$row[csf("issue_qnty")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["gsm"]=$row[csf("gsm")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["dia_width"]=$row[csf("dia_width")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["remarks"] .=$row[csf("remarks")].",";
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["batch_no"] =$row[csf("batch_no")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["color_id"] =$row[csf("color_id")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["sales_booking_no"] =$row[csf("sales_booking_no")];
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["company_id"] =$row[csf("company_id")];
		if($row[csf("within_group")] ==1){ 
			$booking_buyer= $row[csf("po_buyer")];
		}else{
			$booking_buyer= $row[csf("buyer_id")];
		}
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["booking_buyer"] =$booking_buyer;
		$data_array[$row[csf("issue_date")]][$row[csf("issue_number")]][$row[csf("job_no")]][$row[csf("batch_id")]][$row[csf("prod_id")]]["style_ref_no"] =$row[csf("style_ref_no")];

		$all_batch_id_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$all_fso_id_arr[$row[csf("fso_id")]]=$row[csf("fso_id")];
	}

	//print_r($all_batch_id_arr);die;

	$all_batch_id_arr = array_filter($all_batch_id_arr);
	if(!empty($all_batch_id_arr))
	{
		$all_batch_id_cond="";$batchCond="";
		$all_batch_id_cond2="";$batchCond2="";
		if($db_type==2 && count($all_batch_id_arr)>999)
		{
			$all_batch_id_arr_chunk=array_chunk($all_batch_id_arr,999);
			foreach ($all_batch_id_arr_chunk as $value) 
			{
				$batchCond .= " b.batch_id in (".implode(",",$value).") or "; 
				$batchCond2 .= " a.batch_id in (".implode(",",$value).") or "; 
			}
			$all_batch_id_cond.=" and (".chop($batchCond," or ").")";
			$all_batch_id_cond2.=" and (".chop($batchCond2," or ").")";
		}
		else{
			$all_batch_id_cond=" and b.batch_id in(". implode(",",$all_batch_id_arr).")";
			$all_batch_id_cond2=" and a.batch_id in(". implode(",",$all_batch_id_arr).")";
		}

		$production_sql_data=sql_select("SELECT b.prod_id, b.batch_id, b.no_of_roll, c.grey_used_qty as grey_qnty, c.quantity as finish_qnty, c.id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
		where a.id=b.mst_id and a.entry_form=7 and b.id=c.dtls_id and c.entry_form=7 and b.status_active=1 and b.is_deleted=0 and c.is_sales=1 $all_batch_id_cond");

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

		$shade_sql_data = return_library_array("SELECT a.batch_id, sum(b.ratio) as ratio from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, product_details_master c where a.id =b.mst_id and a.entry_form=59 and b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and b.sub_process_id=117 and c.item_category_id=6 $all_batch_id_cond2 group by a.batch_id", "batch_id", "ratio");
	}

		$all_fso_id_cond="";$fsoCond="";
		if($db_type==2 && count($all_fso_id_arr)>999)
		{
			$all_fso_id_arr_chunk=array_chunk($all_fso_id_arr,999);
			foreach ($all_fso_id_arr_chunk as $value) 
			{
				$fsoCond .= " mst_id in (".implode(",",$value).") or "; 
			}
			$all_fso_id_cond.=" and (".chop($fsoCond," or ").")";
		}
		else{
			$all_fso_id_cond=" and mst_id in(". implode(",",$all_fso_id_arr).")";
		}
		
		$fso_data_sql=sql_select("SELECT job_no_mst, color_id, determination_id, color_type_id from fabric_sales_order_dtls where status_active=1 and is_deleted=0 $all_fso_id_cond");
		foreach ($fso_data_sql as $row) {
			$fso_data[$row[csf("job_no_mst")]][$row[csf("color_id")]][$row[csf("determination_id")]] .= $color_type[$row[csf("color_type_id")]].",";
		}

	/* echo "<pre>";
	print_r($fso_data);
	die; */  

	ob_start();
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
	</style>

	<fieldset style="width:1790px;">
		<table width="2280" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="22" align="center"><? echo $company_arr[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2260" class="rpt_table" >
			<thead>	
				<tr>
					<th width="30">SL</th>
					<th width="80">Delivery Date</th>
					<th width="120">Delivery Challan</th>
					<th width="120">Booking No</th>
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
					<th width="100">remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:2280px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2260" class="rpt_table" id="tbl_list_search">
				<?
				$i=1;
				foreach ($data_array as $issue_date => $issue_date_data) 
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
										<td width="80" class="word_wrap_break"  align="center"><p><? echo $issue_date;?></p></td>
										<td width="120" class="word_wrap_break"  align="center"><p><? echo $issue_no;?></p></td>
										<td width="120" class="word_wrap_break"  align="center"><p><? echo $row['sales_booking_no'];?></p></td>
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
										<td width="100" class="word_wrap_break"  align="center" title="Process Loss  = ((Grey Qty - Finish Qty) x 100 ) / Finish Qty"><p><? echo number_format($process_loss,2);?></p></td>
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
				?>
				<tr style="background-color:#D3D3D3;">
					<td colspan="17" align="right"><b>Total:</b></td>
					<td align="right" width="80"><b>&nbsp;</b></td>
					<td align="right" width="80"><b><? echo number_format($total_issue_qnty,2,".",""); ?></b></td>
					<td align="right"><b>&nbsp;</b></td>
					<td align="right"><b>&nbsp;</b></td>
				</tr>
			</table>
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