<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	//echo "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name";
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( $dropdown_name, 150,$location_arr,"", 1, "-- Select Location --", $selected, "" );	
	exit();
}



if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=17 and report_id=175 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#Print').hide();\n";
	echo "$('#btn_print2').hide();\n";
	echo "$('#btn_print3').hide();\n";
	echo "$('#btn_print4').hide();\n";
	

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==86){echo "$('#Print').show();\n";}
			if($id==84){echo "$('#btn_print2').show();\n";}
			if($id==85){echo "$('#btn_print3').show();\n";}
			if($id==160){echo "$('#btn_print4').show();\n";}
			
		}
	}
	exit();	
}


if ($action == "check_conversion_rate") 
{
	$data = explode("**", $data);
	if ($db_type == 0)
	 {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} 
	else 
	{
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	//$exchange_rate = set_conversion_rate($data[0], $conversion_date);
	echo $exchange_rate;
	exit();
}



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
	if ($action=="job_popup")
	{
		echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
		$data=explode("_",$data);
		?>
		<script>
			function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}
			
			function fnc_load_party_popup(type,within_group)
			{
				//alert();
				var company = $('#cbo_company_name').val();
				var party_name = $('#cbo_party_name').val();
				var location_name = $('#cbo_location_name').val();
				var within_group = $('#cbo_within_group').val();
				load_drop_down( 'trims_bill_issue_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td_job' );
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0)
				{
					$('#search_by_td').html('System ID');
				}
				else if(val==2)
				{
					$('#search_by_td').html('W/O No');
				}
				else if(val==4)
				{
					$('#search_by_td').html('Buyer Po');
				}
				else if(val==5)
				{
					$('#search_by_td').html('Buyer Style');
				}
				else if(val==6)
				{
					$('#search_by_td').html('Delivery No');
				}
			}
			function a(i)
			{
				alert();
			}
		</script>
	</head>
	<body onLoad="fnc_load_party_popup(<? echo "$data[0]";?>,<? echo "$data[3]";?>)">
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>
	                    <th width="140">Party Name</th>
	                    <th width="100">Search By</th>
	                    <th width="100" id="search_by_td">System ID</th>
	                    <th width="80">Section</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",0 ); ?>
	                    </td>
	                    <td id="buyer_td_job">
	                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
	                        ?>
	                    </td>
	                    <td>
                    		<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style",6=>"Delivery No");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td><? echo create_drop_down( "cbo_section", 80, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'trims_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

	if($action=="create_job_search_list_view")
	{	
		$data=explode('_',$data);
		$party_id=str_replace("'","",$data[1]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		$section_id =$data[9];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		//echo $search_type; die;
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
		/*if($search_str!="")
		{
			$search_com_cond="and a.job_no_prefix_num='$search_str'";  $search_str
		}*/
		
		if($search_str!="") $job_cond=" and a.bill_no_prefix_num = '$search_str' ";
		

		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
		if($section_id!=0) $section_id_cond=" and a.section_id='$section_id'"; else $section_id_cond="";

		if($db_type==0)
		{ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
		if($within_group==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}
		
		$po_ids='';
		
		
		if($db_type==0) 
		{
			$ins_year_cond="year(a.insert_date)";
		}
		else if($db_type==2)
		{
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.bill_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $search_com_cond=" and a.challan_no = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.bill_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
				else if ($search_by==6) $search_com_cond=" and a.challan_no like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.bill_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
				else if ($search_by==6) $search_com_cond=" and a.challan_no like '%$search_str'";  
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.bill_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
				else if ($search_by==6) $search_com_cond=" and a.challan_no like '%$search_str%'";   
			}
		}

		if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
		{
			if($db_type==0) $id_cond="group_concat(b.id) as id";
			else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

			$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");// and a.trims_job=b.job_no_mst
		}

		if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
		if ($job_dtls_ids!="")
		{
			$job_dtls_ids=explode(",",$job_dtls_ids);
			$job_dtls_idsCond=""; $jobDtlsCond="";
			//echo count($job_dtls_ids); die;
			if($db_type==2 && count($job_dtls_ids)>=999)
			{
				$chunk_arr=array_chunk($job_dtls_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($job_dtls_idsCond=="")
					{
						$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
					}
					else
					{
						$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
					}
				}
				$job_dtls_idsCond.=")";
			}
			else
			{
				$ids=implode("','",$job_dtls_ids);
				$job_dtls_idsCond.=" and b.job_dtls_id in ('$ids') ";
			}
		}
		else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
		{
			echo "Not Found"; die;
		}
		//echo $job_dtls_idsCond;
		if($db_type==0) $order_nos=" ,group_concat(b.order_no) as order_no";
		else if($db_type==2) $order_nos=" ,rtrim(xmlagg(xmlelement(e,b.order_no,',').extract('//text()') order by b.id).GetClobVal(),',') as order_no";


	
		/*$sql= "select a.id, a.trims_bill as trims_job, a.bill_no_prefix_num as job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,a.bill_date,b.order_no,b.section,a.is_posted_account $order_nos
		from trims_bill_mst a, trims_bill_dtls b
		where a.entry_form=276 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $buyer $withinGroup $search_com $job_dtls_idsCond $section_id_cond 
		group by a.id, a.trims_bill, a.bill_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id,a.bill_date,b.order_no,b.section,a.is_posted_account
		order by a.id DESC";*/
		
		
		$sql= "select a.id, a.trims_bill as trims_job, a.bill_no_prefix_num as job_no_prefix_num, $ins_year_cond as year, a.company_id, a.challan_no, a.location_id, a.party_id,a.bill_date,b.section,a.is_posted_account $order_nos
		from trims_bill_mst a, trims_bill_dtls b
		where a.entry_form=276 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $buyer $withinGroup $search_com $job_dtls_idsCond $section_id_cond 
		group by a.id, a.trims_bill, a.bill_no_prefix_num, a.insert_date, a.company_id, a.challan_no, a.location_id, a.party_id,a.bill_date,b.section,a.is_posted_account
		order by a.id DESC";
		// echo $sql;
		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="120">System ID</th>
	            <th width="120">Delivery No</th>
	            <th width="120">Section</th>
	            <th width="40">Year</th>
	            <th width="280">W/O No</th>
	            <th>Delivery Date</th>
	        </thead>
	        </table>
	        <div style="width:800px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                //$order_nos=implode(",",array_unique(explode(",",$row[csf('order_no')])));
	                if($db_type==2 && $row[csf('order_no')]!="") $order_nos = $row[csf('order_no')]->load();
	                $order_nos=implode(",",array_unique(explode(",",$order_nos)));
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_job')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120"><? echo $row[csf('trims_job')]; ?></td>
	                    <td width="120"><? echo $row[csf('challan_no')]; ?></td>
	                    <td width="120"><? echo $trims_section[$row[csf('section')]]; ?></td>
	                    <td width="40" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="280"><? echo $order_nos; ?></td>
	                    <td style="text-align:center;"><? echo change_date_format($row[csf('bill_date')]); ?></td>
	                </tr>
					<? 
	                $i++; 
	            } 
	            ?>
	        </tbody>
	    </table>
		<?    
		exit();
	}

/*function js_set_value(str,curr) 
		{  
			var delv_id = $('#hidden_delv_id'+str).val();
			alert("dev_id"+delv_id);
			if(currency_id.length >0)
			{
				alert("cuu"+jQuery.inArray( currency_id, curr )+"selec"+jQuery.inArray( selected_id, delv_id )+"selc len"+selected_id.length)
				if( jQuery.inArray( currency_id, curr ) == 1 && jQuery.inArray( selected_id, delv_id ) == 1 && selected_id.length > 0) 
				{
					//currency_id.splice(0,1);
					currency_id.remove(curr);
				}
				if(jQuery.inArray( currency_id, curr ) == 1)
				{
					currency_id.push(curr);
				}
			}
			else
			{
				currency_id.push(curr);
			}
			alert(currency_id);
			if(currency_id[0] != curr)
			{
				alert("Other Currency not Allowed");
				return;	
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			alert("in arr"+jQuery.inArray( selected_id, delv_id ));
			if( jQuery.inArray( selected_id, delv_id ) == -1 ) {
				selected_id.push( delv_id );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == delv_id ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id ='';  //var id_dtls = ''; 
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			//alert(id);
			$('#all_delivery_ids').val( id );	
			
			
		}*/
			



if ($action=="devivery_workorder_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
    $selected_id = $_REQUEST['selected_id'];
    $updated_id = $_REQUEST['updated_id'];
	?>
	<script>

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array;
		var currency_id = new Array;
		var party_id 	= new Array;
		var select_unselect=new Array;

		var delv_id_chk_arr = new Array;
		var delv_id_push_pop = new Array;
		
		
		function js_set_value(str,curr,party) 
		{

            var prevID = $('#all_delivery_ids').val();
            if(prevID != ''){
                prevID = prevID.split(',');
                $.each(prevID, function(index, val){
                    selected_id.push(val);
                    delv_id_push_pop[val] = val;
                });
            }
			var delv_id = $('#hidden_delv_id'+str).val();
			if(!delv_id_push_pop[delv_id])
				delv_id_push_pop[delv_id]=delv_id;
			else delv_id_push_pop[delv_id]=0;


			if(party_id.includes(party)==0 && party_id.length>0) 
			{
				alert("Party Mixed Not Allow"); return;
			}else{
				//console.log('else');
				party_id.push(party);
			}

			

			//alert(curr);
			//alert(jQuery.inArray( select_unselect, delv_id ));
			var value=0;
			for(var i=0;i <select_unselect.length ;i++)
			  {
			    
			    if(select_unselect[i]) value=select_unselect[i];
			  }
			//  alert(value);
			if(!select_unselect[curr] && (value==0 || value==curr ))
			{
				//alert(22);
				select_unselect[curr]=curr;
				delv_id_chk_arr[delv_id]=delv_id;
			}
			else
			{
				if(delv_id_chk_arr[delv_id]==delv_id)
				select_unselect[curr]=0;
				//alert(select_unselect);
			}

			//alert(select_unselect.join());return;
			if(currency_id.length >0)
			{
				if( jQuery.inArray( currency_id, curr ) == 1 && jQuery.inArray( selected_id, delv_id ) == 1) 
				{
					currency_id.push(curr);
				}
			}
			else
			{
				currency_id.push(curr);
			}

			var value=0;
			for(var i=0;i <select_unselect.length ;i++)
			  {
			    
			    if(select_unselect[i])value=select_unselect[i];
			  }
			//alert(select_unselect+" value"  );
			if(select_unselect[value] != curr && value>0)
			{
				alert("Other Currency not Allowed");
				 delv_id_push_pop[delv_id]=0;
				return;	
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selected_id, delv_id ) == -1 ) {
				selected_id.push( delv_id );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == delv_id ) break;
				}
				selected_id.splice( i, 1 );
				 

			}
            selected_id = unique(selected_id);
			var id ='';  //var id_dtls = ''; 
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			var new_id_arr=id.split(',');
			var new_ids="";
			for(var i=0;i <new_id_arr.length ;i++)
			{
				//alert(jQuery.inArray(delv_id_push_pop,new_id_arr[i]));
				//alert(delv_id_push_pop+" "+new_id_arr[i])
				if( delv_id_push_pop[new_id_arr[i]] ){if(new_ids=="")new_ids=new_id_arr[i];else new_ids+=','+new_id_arr[i];}
					 
			}
			//alert(new_ids);
			$('#all_delivery_ids').val( new_ids );	
		}
			
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_bill_issue_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}

        function unique(array){
            return array.filter(function(el, index, arr) {
                return index === arr.indexOf(el);
            });
        }
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Delivery ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Challan No.');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>               	 
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="100">Within Group</th>
	                    <th width="140">Party Name</th>
	                    <th width="100">Search By</th>
	                    <th width="100" id="search_by_td">Delivery ID</th>
	                    <th width="100">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td>
	                    <input type="hidden" id="selected_id" value="<?=$selected_id?>">
	                    <input type="hidden" id="ex_updated_id" value="<?=$updated_id?>">
	                    <input type="hidden" id="selected_job" value=""><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
	                    </td>
	                    <td id="buyer_td">
	                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);",1 );   	 
	                        ?>
	                    </td>
	                    <td>
							<?
	                            $search_by_arr=array(1=>"Delivery ID",2=>"W/O No",3=>"Challan No.",4=>"Buyer Po",5=>"Buyer Style");
	                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $data[2]; ?>+'_'+document.getElementById('selected_id').value+'_'+document.getElementById('ex_updated_id').value+'_'+<? echo $data[1]; ?>, 'create_delivery_search_list_view', 'search_div', 'trims_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="8" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
	if($action=="create_delivery_search_list_view")
	{	
		$data=explode('_',$data);
        $selected_id = explode(',', $data[10]);
        $update_id =  $data[11];
		$party_id=str_replace("'","",$data[9]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		$location =$data[12];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}


		if($search_by==2) { // if search by W/O no, show result from all year
			$year_cond="";
		}
		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		if($location!=0) $location_id=" and a.location_id='$location'";
		//echo $party_id; die;
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num='$search_str'";
				else if($search_by==3) $search_com="and a.challan_no='$search_str'";
				else if($search_by==2) $search_com="and b.order_no='$search_str'";
				else if ($search_by==4) $search_com=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '$search_str%'";
				else if($search_by==3) $search_com="and a.challan_no '$search_str%'";
				else if($search_by==2) $search_com="and b.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str'";
				else if($search_by==3) $search_com="and a.challan_no like '%$search_str'";
				else if($search_by==2) $search_com="and b.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str'";  
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str%'";
				else if($search_by==3) $search_com="and a.challan_no like '%$search_str%'";
				else if($search_by==2) $search_com="and b.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str%'";   
			}
		}

		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

		if($db_type==0)
		{ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
		if($within_group==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}

		if($db_type==0) 
		{
			$ins_year_cond="year(a.insert_date)";
		}
		else if($db_type==2)
		{
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}

        $id_arr = [];
        if($update_id != ""){
            $id_arr = return_library_array("select a.id, a.trims_del from trims_delivery_mst a, trims_bill_dtls b where b.challan_no = a.trims_del and b.mst_id = $update_id and b.status_active  = 1 and b.is_deleted = 0 group by a.id, a.trims_del", "trims_del", "id");
        }


		$delevery_qty_trims_arr=array();
		$pre_delevery_sql ="Select order_no, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by order_no";
		$pre_sql_delevery_res=sql_select($pre_delevery_sql);
		foreach ($pre_sql_delevery_res as $row)
		{
			$delevery_qty_trims_arr[$row[csf("order_no")]]['delevery_qty']=$row[csf("delevery_qty")];
		}
		//print_r($delevery_qty_trims_arr); 
		unset($pre_sql_delevery_res);		
			
		$bill_qty_trims_arr=array();
		$pre_sql ="Select order_no, sum(quantity) as bill_qty  from trims_bill_dtls where status_active=1 and is_deleted=0 group by order_no";
		$pre_sql_res=sql_select($pre_sql);
		foreach ($pre_sql_res as $row)
		{
			$bill_qty_trims_arr[$row[csf("order_no")]]['bill_qty']=$row[csf("bill_qty")];
		}
		//print_r($bill_qty_trims_arr); 
		unset($pre_sql_res);


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
		
		$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no,b.order_no, a.gate_pass_no ,$ins_year_cond as year from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $location_id $delivery_date $company $withinGroup $search_com $party_id_cond $year_cond $job_dtls_idsCond  group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no,b.order_no, a.gate_pass_no ,a.insert_date order by a.id DESC";
		/*echo $sql;*/ //and b.delevery_status!=3
		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="130">Work Order No</th>
                <th width="130">Delivery No</th>
                <th width="115">Party Name</th>
	            <th width="100">Delivery Date</th>
	            <th width="80">Year</th>
	            <th width="170">Challan No.</th>
	            <th>Currency.</th>
	        </thead>
	        </table>
	     <div style="width:900px; max-height:260px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
				
					$delevery_qty=$delevery_qty_trims_arr[$row[csf("order_no")]]['delevery_qty'];
					$bill_qty=$bill_qty_trims_arr[$row[csf("order_no")]]['bill_qty'];
					$balance_qty=($delevery_qty-$bill_qty);

					if ($row[csf('within_group')]==1) {
						$party=$companyArr[$row[csf('party_id')]];
					}else{
						$party=$buyerArr[$row[csf('party_id')]];
					}

					//$party=($row[csf('within_group')]==1)?$companyArr[$row[csf('party_id')]]:$buyerArr[$row[csf('party_id')]];
				
				    // echo $delevery_qty."=".$bill_qty;
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($balance_qty>0 || isset($id_arr[$row[csf('trims_del')]]))
					{
                        if(in_array($row[csf('id')], $selected_id)){
	                ?>

                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration: none; cursor: pointer; background-color: yellow;" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i;?>,<? echo $row[csf('currency_id')];?>,<? echo $row[csf('party_id')];?>)" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="130" style="text-align:center;"><? echo $row[csf('order_no')]; ?></td>
                            <td width="130" style="text-align:center;"><? echo $row[csf('trims_del')]; ?></td>
                            <td width="130" style="text-align:center;"><? echo $party; ?></td>
                            <td width="100" style="text-align:center;"><? echo $row[csf('delivery_date')]; ?></td>
                            <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                            <td width="170"><? echo $row[csf('challan_no')]; ?></td>
                            <td style="text-align:center;"><? echo $currency[$row[csf('currency_id')]]; ?>
                                <input type="hidden" name="hidden_delv_id<? echo $i; ?>" id="hidden_delv_id<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:70px">
                            </td>
                        </tr>
					<?
                        }else{
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>,<? echo $row[csf('currency_id')];?>,<? echo $row[csf('party_id')];?>)" >
                                <td width="30"><? echo $i; ?></td>
                                <td width="130" style="text-align:center;"><? echo $row[csf('order_no')]; ?></td>
                                <td width="130" style="text-align:center;"><? echo $row[csf('trims_del')]; ?></td>
                                <td width="130" style="text-align:center;"><? echo $party; ?></td>
                                <td width="100" style="text-align:center;"><? echo $row[csf('delivery_date')]; ?></td>
                                <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                                <td width="170"><? echo $row[csf('challan_no')]; ?></td>
                                <td style="text-align:center;"><? echo $currency[$row[csf('currency_id')]]; ?>
                                    <input type="hidden" name="hidden_delv_id<? echo $i; ?>" id="hidden_delv_id<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:70px">
                                </td>
                            </tr>
                            <?
                        }
					 $i++;
					}
	                
	            } 
	            ?>
	        </tbody>
	    </table>
        </div>
        <table style="width:100%; float:left" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:53%;" align="center">
                        <input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
	    				<input type="hidden"  id="all_delivery_ids" value="<?=implode(',',$selected_id)?>"/>
                    </div>
                </div>
            </td>
        </tr>
    	</table>
        
	   
		<?    
		exit();
	}
 
	if ($action=="load_delivery_data_to_form")
	{
		
		$data = explode("**", $data);
		$cbo_currency=$data[1];
		$txt_bill_date=$data[2];
		$cbo_company_name=$data[3];
		//echo $cbo_currency; die;
		
		
		$sql="select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no from trims_delivery_mst a where a.entry_form=208 and a.id in ($data[0]) and a.status_active=1 ";

		// $exchange_arr=return_library_array( "select id,exchange_rate from subcon_ord_mst", "id", "exchange_rate" );
		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$challan_nos .=$row[csf("challan_no")].",";
			$received_ids .=$row[csf("received_id")].",";
			$order_ids .=$row[csf("order_id")].",";
			$trims_dels .=$row[csf("trims_del")].",";
		}
		
		$order_id=implode(",",array_unique(explode(",",chop($order_ids,","))));
		$challan_no=implode(",",array_unique(explode(",",chop($challan_nos,","))));
		$received_id=implode(",",array_unique(explode(",",chop($received_ids,","))));
		$trims_del=implode(",",array_unique(explode(",",chop($trims_dels,","))));
		//$order_id=implode(",",array_unique(explode(','$order_ids)));
		foreach ($nameArray as $row)
		{
			echo "document.getElementById('delivery_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('received_id').value 				= '".$received_id."';\n";
			echo "document.getElementById('hid_order_id').value 			= '".$order_id."';\n";  
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			echo "document.getElementById('txt_challan_no').value 			= '".$trims_del."';\n";
			echo "document.getElementById('cbo_Wo_Currency').value 			= '".$row[csf("currency_id")]."';\n";
			
				//if($row[csf("currency_id")]==2 && $cbo_currency==1)
				//{
					
			if ($db_type == 0)
			{
				$conversion_date = change_date_format($txt_bill_date, "Y-m-d", "-", 1);
			} 
			else 
			{
				$conversion_date = change_date_format($txt_bill_date, "d-M-y", "-", 1);
			}
			$exchange_rate = set_conversion_rate($row[csf("currency_id")], $conversion_date, $cbo_company_name);
			echo "document.getElementById('txt_exchange_rate').value 			= '".$exchange_rate."';\n";
					//echo "$('#txt_exchange_rate').attr('disabled','true')".";\n";
				//} 
			// echo "document.getElementById('txt_exchange_rate').value 		= '".$exchange_arr[$row[csf("received_id")]]."';\n"; 
			echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
			echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
			echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
			echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
			echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_location_name').attr('disabled','true')".";\n";
		}
		exit();	
	}
	
	if ($action=="load_mst_php_data_to_form")
	{
		
		$nameArray=sql_select( "SELECT id, entry_form, trims_bill, bill_no_prefix, is_posted_account, bill_no_prefix_num, company_id,  location_id , within_group,  party_id, party_location ,bill_date,order_id, received_id, job_id, challan_no, bill_no,remarks,exchange_rate,wo_currency_id,currency_id,up_charge, discount, upcharge_remarks, discount_remarks
		 from trims_bill_mst  where entry_form=276 and id=$data and status_active=1 order by id DESC" );
        $getChallanIdaArr = return_library_array("select a.id, a.trims_del from trims_delivery_mst a, trims_bill_mst b, trims_bill_dtls c where b.id = c.mst_id and c.challan_no = a.trims_del and c.status_active = 1 and c.is_deleted = 0 and b.id = $data", "trims_del", "id");
		if(count($getChallanIdaArr) > 0)
            $del_challan_id = implode(',', $getChallanIdaArr);
        else
            $del_challan_id = "";
        foreach ($nameArray as $row)
		{	
			echo "document.getElementById('txt_bill_no').value 				= '".$row[csf("trims_bill")]."';\n";
			echo "document.getElementById('delivery_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('received_id').value 				= '".$row[csf("received_id")]."';\n";
			echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";  
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("challan_no")]."';\n";  
			echo "$('#txt_challan_no').prop('title', '".$row[csf("challan_no")]."');\n"; 
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
			echo "document.getElementById('cbo_currency').value 			= '".$row[csf("currency_id")]."';\n";
			
			
			//echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
			//echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";
			
			echo "fnc_load_party(1,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
			echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_location').value		= ".$row[csf("party_location")].";\n";
			
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_location_name').attr('disabled','true')".";\n";
			echo "$('#cbo_party_location').attr('disabled','true')".";\n";
			
			echo "document.getElementById('txt_bill_no_manual').value		= '".$row[csf("bill_no")]."';\n";
			echo "document.getElementById('txt_bill_date').value			= '".change_date_format($row[csf("bill_date")])."';\n"; 
//			echo "$('#txt_challan_no').attr('disabled','true')".";\n";
			
			echo "document.getElementById('txt_exchange_rate').value 		= '".$row[csf("exchange_rate")]."';\n";
			echo "document.getElementById('txt_remarks').value			= '".$row[csf("remarks")]."';\n";
			
			echo "document.getElementById('cbo_Wo_Currency').value		= '".$row[csf("wo_currency_id")]."';\n";
			echo "document.getElementById('txt_upcharge').value			= '".$row[csf("up_charge")]."';\n";
			echo "document.getElementById('txt_discount').value			= '".$row[csf("discount")]."';\n";
			echo "document.getElementById('txt_up_remarks').value		= '".$row[csf("upcharge_remarks")]."';\n";
			echo "document.getElementById('txt_discount_remarks').value	= '".$row[csf("discount_remarks")]."';\n";
            if($row[csf("is_posted_account")] == 1){
                echo "$('#bill_posted_msg').text('Bill posted in accounts so update, delete is not possible.');\n";
                echo "$('#txt_challan_no').prop('disabled', true);\n";
            }else{
                echo "$('#bill_posted_msg').text('');\n";
                echo "$('#txt_challan_no').prop('disabled', false);\n";
            }
		}
        echo "document.getElementById('challan_selected_id').value 	= '".$del_challan_id."';\n";

		exit();	
	}	

if( $action=='order_dtls_list_view' )
{
	//echo $data; die;
	$data=explode('**',$data);
	$exchange_rate=str_replace("'","",$data[4]);
	$Wo_Currency=str_replace("'","",$data[5]);
	$cbo_currency=trim(str_replace("'","",$data[6]));
	$company_name=trim(str_replace("'","",$data[7]));
	$update_id=trim(str_replace("'","",$data[8]));

	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$challan_arrey=return_library_array( "select id,trims_del from  trims_delivery_mst where status_active=1 and is_deleted=0",'id','trims_del');
	
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_name and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;



	$tblRow=0;
	if($data[0]==1)
	{
        $previous_id = array();
        if($update_id != ""){
            $id_arr = return_library_array("select a.id, a.trims_del from trims_delivery_mst a, trims_bill_dtls b where b.challan_no = a.trims_del and b.mst_id = $update_id and b.status_active  = 1 and b.is_deleted = 0 group by a.id, a.trims_del", "trims_del", "id");
            $data[1] = explode(',', $data[1]);
            foreach($id_arr as $key => $val1){
                if(in_array($val1, $data[1]) == false){
                    unset($id_arr[$key]);
                }
            }
            foreach ($data[1] as $key => $val){
                if(in_array($val, $id_arr)){
                    unset($data[1][$key]);
                }
            }
            $data[1] = implode(",",$data[1]);
        }
        if(count($id_arr) > 0)
            $challan_no = "'".implode("','", array_flip($id_arr))."'";
        else
            $challan_no = '';
		$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id,  a.order_id, a.order_no, a.buyer_po_id, a.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.order_uom, a.order_quantity,a.delevery_qty, a.claim_qty, a.remarks, a.delevery_status,a.color_name,a.size_name,b.currency_id,b.received_id, a.break_down_details_id , c.description, c.gmts_color_id, c.gmts_size_id, c.color_id, c.size_id, c.qnty as workoder_qty, (c.amount/c.qnty) as receive_rate, c.rate as order_receive_rate , c.amount,c.style,d.item_group as trim_group from trims_delivery_dtls a,trims_delivery_mst b , subcon_ord_breakdown c, subcon_ord_dtls d  where a.mst_id=b.id and a.break_down_details_id=c.id and c.mst_id=d.id and d.mst_id=a.received_id and a.mst_id in($data[1]) and a.delevery_qty>0  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by a.id ASC";
        $sqlexisting = "select a.received_id, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.bill_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id,d.qnty as workoder_qty,c.buyer_po_id,c.buyer_po_no , d.amount, (d.amount/d.qnty) as wo_rcv_rate, d.rate as wo_rate ,d.style, e.item_group as trim_group from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c,subcon_ord_breakdown d , subcon_ord_dtls e where a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id  and d.mst_id=e.id and e.mst_id=c.received_id and b.mst_id=$update_id and b.challan_no in ($challan_no) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  order by b.id ASC";
	}
	else
	{
		/* $sql = "select a.received_id, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id from trims_bill_mst a, trims_bill_dtls b where a.id=b.mst_id and b.mst_id=$data[1] order by b.id ASC";*/
		
		  $sql = "select a.received_id, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.bill_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id,d.qnty as workoder_qty,c.buyer_po_id,c.buyer_po_no , d.amount, (d.amount/d.qnty) as wo_rcv_rate, d.rate as wo_rate ,d.style, e.item_group as trim_group from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c,subcon_ord_breakdown d , subcon_ord_dtls e where a.id=b.mst_id and c.id=b.production_dtls_id and c.break_down_details_id=d.id  and d.mst_id=e.id and e.mst_id=c.received_id and b.mst_id=$data[1] and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  order by b.id ASC";
	}
	//echo  $sql ;

	$delevery_qty_trims_arr=array();
	$pre_sql ="Select production_dtls_id, sum(quantity) as delevery_qty  from trims_bill_dtls where status_active=1 and is_deleted=0 group by production_dtls_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("production_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
		
	}
	
	unset($pre_sql_res);



	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$received_ids .=$row[csf('received_id')].",";
	}
	$received_ids=chop($received_ids,",");
	
	$order_sql = "select a.id, a.currency_id,b.id as dtls_id, c.job_no_mst, c.qnty, c.rate, c.amount, c.id as break_id from subcon_ord_mst a , subcon_ord_dtls b , subcon_ord_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.id in($received_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$order_data_array=sql_select($order_sql); $curr_arr=array(); $order_dtls_arr=array();
	foreach($order_data_array as $rows)
	{
		$curr_arr[$rows[csf('id')]]['currency']=$rows[csf('currency_id')];
		$order_dtls_arr[$rows[csf('id')]][$rows[csf('break_id')]]['qnty']=$rows[csf('qnty')];
		$order_dtls_arr[$rows[csf('id')]][$rows[csf('break_id')]]['rate']=$rows[csf('amount')]/$rows[csf('qnty')];
		$order_dtls_arr[$rows[csf('id')]][$rows[csf('break_id')]]['amount']=$rows[csf('amount')];
	}
	//echo "<pre>"; print_r($curr_arr);



	if($db_type==0)
	{
		$conversion_date=change_date_format($data[3], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[3], "d-M-y", "-",1);
	}
	//$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate($Wo_Currency, $conversion_date, $company_name);

	
	if($data[0]==1)
	{
	if($update_id != "")
	{
        $data_array_ex = sql_select($sqlexisting);
        foreach($data_array_ex as $row)
		{
			$exchange_rate=$exchange_rate;
			$Wo_Currency=$Wo_Currency;
			$cbo_currency=$cbo_currency;
			$Wo_rate=$row[csf('wo_rate')];

			//$row[csf('order_receive_rate')]*number_format($exchange_rate,2)
			//echo $exchange_rate.'Wo_Currency'.$Wo_Currency.'cbo_currency'.$cbo_currency; die;
			if($Wo_Currency==2 && $cbo_currency==1)
			{
				$bill_rate=$Wo_rate*$exchange_rate;
			}
			else if($Wo_Currency==1 && $cbo_currency==1)
			{
				$bill_rate=$Wo_rate*1;
			}
			else if($Wo_Currency==2 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate*1;
			}
			else if($Wo_Currency==1 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate/$exchange_rate;
			}
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            	<td><input id="txtWorkOrder_<? echo $tblRow; ?>" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_no')]; ?>" readonly/>
            		<input id="txtWorkOrderID_<? echo $tblRow; ?>" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_id')]; ?>" readonly/>
            	</td>
            	<td><input id="txtbuyerPO_<? echo $tblRow; ?>" name="txtbuyerPO[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_no')]; ?>" readonly />
            		<input id="txtbuyerPOID_<? echo $tblRow; ?>" name="txtbuyerPOID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_id')]; ?>" readonly/>
            	</td>
                <td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"",1,'','','','','','',"cboSection[]"); ?></td>
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
                <td><input id="txtStyle_<? echo $tblRow; ?>" name="txtStyle[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('style')]; ?>" readonly/></td>
                <td><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('item_description')]; ?>" readonly/></td>
                <td><input id="txtChallan_<? echo $tblRow; ?>" name="txtChallan[]" type="text" class="text_boxes" style="width:150px" placeholder="Display" value="<? echo $row[csf('challan_no')]; ?>" readonly/></td>
				<td>
					<input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $color_arr[$row[csf('gmts_color_id')]]; ?>" readonly/>
                	<input id="txtgmtscolorId_<? echo $tblRow; ?>" name="txtgmtscolorId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $size_arr[$row[csf('gmts_size_id')]]; ?>" readonly/>
					<input id="txtgmtssizeId_<? echo $tblRow; ?>" name="txtgmtssizeId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_size_id')]; ?>"/>
				</td>
                <td>
					<input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" readonly/>
                	<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $size_arr[$row[csf('size_id')]]; ?>" readonly/>
					<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('size_id')]; ?>"/>
				</td>
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('order_uom')],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                 <td><input id="txtWorkOrderQuantity_<? echo $tblRow; ?>" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" value="<? echo $row[csf('workoder_qty')]; ?>" placeholder="" readonly /></td>
                <td><input id="txtTotDelQuantity_<? echo $tblRow; ?>" name="txtTotDelQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" readonly value="<? echo $row[csf('total_delv_qty')]; ?>" /></td>
                <td title="<? echo $row[csf("production_dtls_id")];?>"><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" type="text"  value="<? echo $CumDelvQty=($delevery_qty_trims_arr[$row[csf("production_dtls_id")]]['delevery_qty']-$row[csf('delevery_qty')]); ?>"  class="text_boxes_numeric" style="width:60px" readonly  /></td>
                <td><input id="txtQty_<? echo $tblRow; ?>" name="txtQty[]" type="text"  class="text_boxes_numeric" style="width:60px"  onKeyUp="calculate_amount(<? echo $tblRow; ?>);calculate_total(<? echo $tblRow; ?>);"  value="<? echo $row[csf('delevery_qty')]; ?>" /></td>
                <td style="display:none"><? echo create_drop_down( "cboCurrency_".$tblRow, 80, $currency,"", 1, "-- Select --",$wo_currency,1, 1,'','','','','','',"cboCurrency[]"); ?></td>
                <td style="display:none"><input id="txtExRate_<? echo $tblRow; ?>" name="txtExRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly value="<? echo number_format($exchange_rate,2); ?>" /></td>
                <td><input id="txtWoRate_<? echo $tblRow; ?>" name="txtWoRate[]" type="text" style="width:70px" value="<? echo $row[csf('wo_rate')]; ?>"   class="text_boxes_numeric" readonly  /></td>
                <td><input id="txtBillRate_<? echo $tblRow; ?>" name="txtBillRate[]" type="text"  class="text_boxes_numeric" style="width:57px" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" value="<? echo $row[csf('bill_rate')]; ?>"  readonly /></td>
                <td><input id="txtBillAmount_<? echo $tblRow; ?>" name="txtBillAmount[]" type="text" value="<? echo $row[csf('bill_amount')]; ?>"  class="text_boxes_numeric" style="width:77px" readonly />


                </td>
                <td style="display:none"><input id="txtDomBillAmount_<? echo $tblRow; ?>" name="txtDomBillAmount[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly  />
                 <input type="hidden" name="hdnDtlsUpdateId[]" value="<? echo $row[csf('id')]; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>">

                    <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                    <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('receive_dtls_id')]; ?>">
                    <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnDeleveryDtlsId[]" id="hdnDeleveryDtlsId_<? echo $tblRow; ?> " value="<? echo $row[csf('production_dtls_id')];  ?>">
                </td>
            </tr>
			<?
		}
	}
	if(count($data_array) > 0)
	{
		//$exchange_rate=''; $wo_currency='';
		
		
		//echo "mahbub";die;
		
		foreach($data_array as $row)
		{
			/*if($data[0]==1)
			{
				$hdnDeleveryDtlsId =$row[csf('id')];
			}
			else
			{
				$hdnDeleveryDtlsId='';
			}
			$wo_currency=$curr_arr[$row[csf('received_id')]]['currency'];
			if($db_type==0)
			{
				$conversion_date=change_date_format($data[3], "Y-m-d", "-",1);
			}
			else
			{
				$conversion_date=change_date_format($data[3], "d-M-y", "-",1);
			}
			//$conversion_date=date("Y/m/d");
			$exchange_rate=set_conversion_rate( $wo_currency, $conversion_date );
			//echo $row[csf('received_id')]."==".$exchange_rate."==".$wo_currency."==".$conversion_date;*/
			
			//echo $exchange_rate.'Wo_Currency'.$Wo_Currency.'cbo_currency'.$cbo_currency; die;
			$exchange_rate=$exchange_rate;
			$Wo_Currency=$Wo_Currency;
			$cbo_currency=$cbo_currency;
			$Wo_rate=$row[csf('order_receive_rate')];
			
			//$row[csf('order_receive_rate')]*number_format($exchange_rate,2)
			//echo $exchange_rate.'Wo_Currency'.$Wo_Currency.'cbo_currency'.$cbo_currency; die;
			if($Wo_Currency==2 && $cbo_currency==1) //2/1
			{
				$bill_rate=$Wo_rate*$exchange_rate;
				$bill_rate=number_format($bill_rate,6,'.','');
			}
			else if($Wo_Currency==1 && $cbo_currency==1)
			{
				$bill_rate=$Wo_rate*1;
			}
			else if($Wo_Currency==2 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate*1;
			}
			/*else if($Wo_Currency==1 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate/$exchange_rate;
				$bill_rate=number_format($bill_rate,6,'.','');
			}*/
			
			//echo $bill_rate.""; 
			//die;
			
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            	<td><input id="txtWorkOrder_<? echo $tblRow; ?>" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_no')]; ?>" readonly/>
            		<input id="txtWorkOrderID_<? echo $tblRow; ?>" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_id')]; ?>" readonly/>
            	</td>
            	<td><input id="txtbuyerPO_<? echo $tblRow; ?>" name="txtbuyerPO[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_no')]; ?>" readonly />
            		<input id="txtbuyerPOID_<? echo $tblRow; ?>" name="txtbuyerPOID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_id')]; ?>" readonly/>
            	</td>
                <td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"",1,'','','','','','',"cboSection[]"); ?></td>
                <td><? echo create_drop_down( "cboItemGroup_".$tblRow, 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
                <td><input id="txtStyle_<? echo $tblRow; ?>" name="txtStyle[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('style')]; ?>" readonly/></td>
                <td><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('description')]; ?>" readonly/></td>
                <td><input id="txtChallan_<? echo $tblRow; ?>" name="txtChallan[]" type="text" class="text_boxes" style="width:150px" placeholder="Display" value="<? echo $challan_arrey[$row[csf('mst_id')]];?>" readonly/></td>
				<td>
					<input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $color_arr[$row[csf('gmts_color_id')]]; ?>" readonly/>
                	<input id="txtgmtscolorId_<? echo $tblRow; ?>" name="txtgmtscolorId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $size_arr[$row[csf('gmts_size_id')]]; ?>" readonly/>
					<input id="txtgmtssizeId_<? echo $tblRow; ?>" name="txtgmtssizeId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_size_id')]; ?>" readonly/> 
				</td>
                <td>
					<input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" readonly/>
                	<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $size_arr[$row[csf('size_id')]]; ?>" readonly/>
					<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('size_id')]; ?>" readonly/>
				</td>
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('order_uom')],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                 <td><input id="txtWorkOrderQuantity_<? echo $tblRow; ?>" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" value="<? echo $row[csf('workoder_qty')]; ?>" placeholder="" readonly /></td>
                <td><input id="txtTotDelQuantity_<? echo $tblRow; ?>" name="txtTotDelQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" readonly value="<? echo $row[csf('delevery_qty')]; ?>" /></td>
                <td title="<?  echo $row[csf('id')];?> "><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" type="text"  value="<? if($delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty']!=''){echo $delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty'];}else{echo "0";} ?>"  class="text_boxes_numeric" style="width:60px" readonly  /></td>
                <td><input id="txtQty_<? echo $tblRow; ?>" name="txtQty[]" type="text"  class="text_boxes_numeric" style="width:60px"  value="<? 
				
				if($variable_set_invent[0][csf('over_rcv_payment')]==1)
				{
					if($delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty']!='')
					{ 
						$PrevQty=$delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty'];
					}
					else
					{ 
						$PrevQty="0";
					} 
					echo ($row[csf('delevery_qty')]-$PrevQty); 
				}
				else
				{
					if($delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty']!='')
					{ 
						$PrevQty=$delevery_qty_trims_arr[$row[csf('id')]]['delevery_qty'];
					}
					else
					{ 
						$PrevQty="0";
					} 
					echo ($row[csf('delevery_qty')]-$PrevQty);
				}
				 ?>" onKeyUp="calculate_amount(<? echo $tblRow; ?>);calculate_total(<? echo $tblRow; ?>);" /></td>
                
                <td style="display:none"><? echo create_drop_down( "cboCurrency_".$tblRow, 80, $currency,"", 1, "-- Select --",$wo_currency,1, 1,'','','','','','',"cboCurrency[]"); ?></td>
                <td style="display:none"><input id="txtExRate_<? echo $tblRow; ?>" name="txtExRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly value="<? echo number_format($exchange_rate,2); ?>" /></td>
                <td title="<? echo  number_format($exchange_rate,2);?>"><input id="txtWoRate_<? echo $tblRow; ?>" name="txtWoRate[]" type="text" style="width:70px" value=" <? echo $row[csf('order_receive_rate')] ?>"  class="text_boxes_numeric" readonly  /></td> 
                
                <td title="<? echo $data[4];?>"><input id="txtBillRate_<? echo $tblRow; ?>" value=" <? echo $bill_rate; ?>"  name="txtBillRate[]" type="text"  class="text_boxes_numeric" style="width:57px" onKeyUp="calculate_amount(<? echo $tblRow; ?>);"   readonly/></td> 
                                                               
                <td><input id="txtBillAmount_<? echo $tblRow; ?>" name="txtBillAmount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly /></td>
                <td style="display:none"><input id="txtDomBillAmount_<? echo $tblRow; ?>" name="txtDomBillAmount[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly  />
                	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>">
                    <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                    <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('receive_dtls_id')]; ?>">
                    <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnDeleveryDtlsId[]" id="hdnDeleveryDtlsId_<? echo $tblRow; ?> " value="<? echo $row[csf('id')]; ?>">
                </td> 
            </tr>
			<?
		}
	}
	elseif(count($data_array) == 0 && $update_id == "")
	{
		?>		
		<tr id="row_1">
			<td><input id="txtWorkOrder_1" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
	    		<input id="txtWorkOrderID_1" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
	    	</td>
	    	<td><input id="txtbuyerPO_1" name="txtbuyerPO[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
	    		<input id="txtbuyerPOID_1" name="txtbuyerPOID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
	    	</td>
	        <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
	        <td><? echo create_drop_down( "cboItemGroup_1", 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
	        <td><input id="txtStyle_1" name="txtStyle[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
	        <td><input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
	        <td><input id="txtChallan_1" name="txtChallan[]" type="text" class="text_boxes" style="width:150px" placeholder="Display"/></td>
			<td>
				<input id="txtgmtscolor_1" name="txtgmtscolor[]" type="text" class="text_boxes" style="width:90px" placeholder="Display"/>
	        	<input id="txtgmtscolorId_1" name="txtgmtscolorId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display"/>
			</td>
	        <td>
				<input id="txtgmtssize_1" name="txtgmtssize[]" type="text" class="text_boxes" style="width:90px" placeholder="Display"/>
				<input id="txtgmtssizeId_1" name="txtgmtssizeId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display"/>
			</td>
	        <td>
				<input id="txtcolor_1" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
	        	<input id="txtcolorID_1" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
			</td>
	        <td>
				<input id="txtsize_1" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
				<input id="txtsizeID_1" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
			</td>
	        <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
	         <td><input id="txtWorkOrderQuantity_1" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" placeholder="" readonly /></td>
	        <td><input id="txtTotDelQuantity_1" name="txtTotDelQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty2(1,'0',1)" placeholder="" readonly /></td>
	        <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
	        <td><input id="txtQty_1" name="txtQty[]" type="text"  class="text_boxes_numeric" style="width:70px" readonly /></td>
	        <td style="display:none"><? echo create_drop_down( "cboCurrency_1", 80, $currency,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboCurrency[]"); ?>
	       	</td>
	        <td style="display:none"><input id="txtExRate_1" name="txtExRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
	        <td><input id="txtWoRate_1" name="txtWoRate[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
	        <td><input id="txtBillRate_1" name="txtBillRate[]" type="text"  class="text_boxes_numeric" style="width:70px"  /></td> 
	        <td><input id="txtBillAmount_1" name="txtBillAmount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly />
	        </td>
	        <td style="display:none"><input id="txtDomBillAmount_1" name="txtDomBillAmount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly />
	        	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
	            <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
	            <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_1">
	            <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_1">
	            <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_1">
	            <input type="hidden" name="hdnDeleveryDtlsId[]" id="hdnDeleveryDtlsId_1">
	        </td> 
        </tr> 
		<?
	}
	}
	else
	{
		//$exchange_rate=''; $wo_currency='';
		foreach($data_array as $row)
		{
			/*if($data[0]==1)
			{
				$hdnDeleveryDtlsId =$row[csf('id')];
			}
			else
			{
				$hdnDeleveryDtlsId='';
			}
			$wo_currency=$curr_arr[$row[csf('received_id')]]['currency'];
			if($db_type==0)
			{
				$conversion_date=change_date_format($data[3], "Y-m-d", "-",1);
			}
			else
			{
				$conversion_date=change_date_format($data[3], "d-M-y", "-",1);
			}
			//$conversion_date=date("Y/m/d");
			$exchange_rate=set_conversion_rate( $wo_currency, $conversion_date );
			//echo $row[csf('received_id')]."==".$exchange_rate."==".$wo_currency."==".$conversion_date;*/
			
			$exchange_rate=$exchange_rate;
			$Wo_Currency=$Wo_Currency;
			$cbo_currency=$cbo_currency;
			$Wo_rate=$row[csf('wo_rate')];
			
			//$row[csf('order_receive_rate')]*number_format($exchange_rate,2)
			//echo $exchange_rate.'Wo_Currency'.$Wo_Currency.'cbo_currency'.$cbo_currency; die;
			if($Wo_Currency==2 && $cbo_currency==1)
			{
				$bill_rate=$Wo_rate*$exchange_rate;
			}
			else if($Wo_Currency==1 && $cbo_currency==1)
			{
				$bill_rate=$Wo_rate*1;
			}
			else if($Wo_Currency==2 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate*1;
			}
			else if($Wo_Currency==1 && $cbo_currency==2)
			{
				$bill_rate=$Wo_rate/$exchange_rate;
			}
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            	<td><input id="txtWorkOrder_<? echo $tblRow; ?>" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_no')]; ?>" readonly/>
            		<input id="txtWorkOrderID_<? echo $tblRow; ?>" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('order_id')]; ?>" readonly/>
            	</td>
            	<td><input id="txtbuyerPO_<? echo $tblRow; ?>" name="txtbuyerPO[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_no')]; ?>" readonly />
            		<input id="txtbuyerPOID_<? echo $tblRow; ?>" name="txtbuyerPOID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('buyer_po_id')]; ?>" readonly/>
            	</td>
                <td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"",1,'','','','','','',"cboSection[]"); ?></td>
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
                <td><input id="txtStyle_<? echo $tblRow; ?>" name="txtStyle[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('style')]; ?>" readonly/></td>
                <td><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('item_description')]; ?>" readonly/></td>
                <td><input id="txtChallan_<? echo $tblRow; ?>" name="txtChallan[]" type="text" class="text_boxes" style="width:150px" placeholder="Display" value="<? echo $row[csf('challan_no')]; ?>" readonly/></td>
				<td>
					<input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $color_arr[$row[csf('gmts_color_id')]]; ?>" readonly/>
                	<input id="txtgmtscolorId_<? echo $tblRow; ?>" name="txtgmtscolorId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $size_arr[$row[csf('gmts_size_id')]]; ?>" readonly/>
					<input id="txtgmtssizeId_<? echo $tblRow; ?>" name="txtgmtssizeId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display" value="<? echo $row[csf('gmts_size_id')]; ?>"/>
				</td>
                <td>
					<input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $color_arr[$row[csf('color_id')]]; ?>" readonly/>
                	<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('color_id')]; ?>" readonly/>
				</td>
                <td>
					<input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $size_arr[$row[csf('size_id')]]; ?>" readonly/>
					<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display" value="<? echo $row[csf('size_id')]; ?>"/>
				</td>	 
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('order_uom')],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                 <td><input id="txtWorkOrderQuantity_<? echo $tblRow; ?>" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" value="<? echo $row[csf('workoder_qty')]; ?>" placeholder="" readonly /></td>
                <td><input id="txtTotDelQuantity_<? echo $tblRow; ?>" name="txtTotDelQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" readonly value="<? echo $row[csf('total_delv_qty')]; ?>" /></td>
                <td title="<? echo $row[csf("production_dtls_id")];?>"><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" type="text"  value="<? echo $CumDelvQty=($delevery_qty_trims_arr[$row[csf("production_dtls_id")]]['delevery_qty']-$row[csf('delevery_qty')]); ?>"  class="text_boxes_numeric" style="width:60px" readonly  /></td>
                <td><input id="txtQty_<? echo $tblRow; ?>" name="txtQty[]" type="text"  class="text_boxes_numeric" style="width:60px"  onKeyUp="calculate_amount(<? echo $tblRow; ?>);calculate_total(<? echo $tblRow; ?>);"  value="<? echo $row[csf('delevery_qty')]; ?>" /></td>
                <td style="display:none"><? echo create_drop_down( "cboCurrency_".$tblRow, 80, $currency,"", 1, "-- Select --",$wo_currency,1, 1,'','','','','','',"cboCurrency[]"); ?></td>
                <td style="display:none"><input id="txtExRate_<? echo $tblRow; ?>" name="txtExRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly value="<? echo number_format($exchange_rate,2); ?>" /></td>
                <td><input id="txtWoRate_<? echo $tblRow; ?>" name="txtWoRate[]" type="text" style="width:70px" value="<? echo $row[csf('wo_rate')]; ?>"   class="text_boxes_numeric" readonly  /></td> 
                <td><input id="txtBillRate_<? echo $tblRow; ?>" name="txtBillRate[]" type="text"  class="text_boxes_numeric" style="width:57px" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" value="<? echo $row[csf('bill_rate')]; ?>"  readonly /></td>                                                
                <td><input id="txtBillAmount_<? echo $tblRow; ?>" name="txtBillAmount[]" type="text" value="<? echo $row[csf('bill_amount')]; ?>"  class="text_boxes_numeric" style="width:77px" readonly />
                
               
                </td>
                <td style="display:none"><input id="txtDomBillAmount_<? echo $tblRow; ?>" name="txtDomBillAmount[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly  />
                 <input type="hidden" name="hdnDtlsUpdateId[]" value="<? echo $row[csf('id')]; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>">
                	
                    <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                    <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('receive_dtls_id')]; ?>">
                    <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" name="hdnDeleveryDtlsId[]" id="hdnDeleveryDtlsId_<? echo $tblRow; ?> " value="<? echo $row[csf('production_dtls_id')];  ?>">
                </td> 
            </tr>
			<?
		}
	}
	exit();
}

/*if($action=="check_conversion_rate")
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
	//$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data, $conversion_date );
	echo $exchange_rate;
	exit();	
}*/

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_name and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	//echo "10**".$variable_set_invent[0][csf('over_rcv_payment')];die;
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TBI', date("Y",time()), 5, "select bill_no_prefix,bill_no_prefix_num from trims_bill_mst where entry_form=276 and company_id=$cbo_company_name $insert_date_con order by id desc ", "bill_no_prefix", "bill_no_prefix_num" ));
		
		//echo $new_bill_no."**"."select bill_no_prefix,bill_no_prefix_num from trims_bill_mst where entry_form=276 and company_id=$cbo_company_name $insert_date_con order by id desc "; 
		//print_r($new_bill_no);
		//die;

		if(str_replace("'",'',$bill_no_manual)=="")
		{
			$bill_no_manual=$new_bill_no[0];
		}
		else
		{
			$bill_no_manual=str_replace("'",'',$bill_no_manual);
		}
		if($db_type==0)
		{
			$txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date), "", "",1);
		}
		
		//echo "10**".$rID."**".$rID2; die;
		
		$id=return_next_id("id","trims_bill_mst",1);
		$id1=return_next_id( "id", "trims_bill_dtls",1) ;
		
		$field_array="id, entry_form, trims_bill, bill_no_prefix, bill_no_prefix_num, company_id,  location_id , within_group,  party_id, party_location ,bill_date, order_id, received_id, job_id, challan_no, bill_no,remarks,exchange_rate,currency_id,wo_currency_id, bill_amount, up_charge, discount, net_bill_amount, upcharge_remarks, discount_remarks, inserted_by, insert_date";

		$data_array="(".$id.", 276, '".$new_bill_no[0]."', '".$new_bill_no[1]."', '".$new_bill_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$txt_bill_date."', '".$hid_order_id."', '".$hid_recv_id."', '".$hid_job_id."', '".$txt_challan_no."', '".$bill_no_manual."','".$txt_remarks."','".$txt_exchange_rate."','".$cbo_currency."','".$cbo_Wo_Currency."','".$txtBillAmount."','".$txt_upcharge."','".$txt_discount."','".$txt_net_Amount."','".$txt_up_remarks."','".$txt_discount_remarks."',".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_bill_no[0];
		$field_array2="id, mst_id,  order_id, order_no, section, item_description, challan_no, gmts_color_id, gmts_size_id, color_id, size_id, order_uom, total_delv_qty, previous_bill_qty, quantity, wo_rate, bill_rate, bill_amount,job_dtls_id, production_dtls_id, inserted_by, insert_date";
		
		$data_array2= "";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{	
			$txtWorkOrder			= "txtWorkOrder_".$i; 
			$txtWorkOrderID			= "txtWorkOrderID_".$i; 
			$cboSection				= "cboSection_".$i;
			$txtdescription			= "txtdescription_".$i;
			$txtChallan				= "txtChallan_".$i;
			$txtgmtscolorId			= "txtgmtscolorId_".$i;
			$txtgmtssizeId			= "txtgmtssizeId_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboUom					= "cboUom_".$i;
			$txtTotDelQuantity 		= "txtTotDelQuantity_".$i;
			$txtPrevQty				= "txtPrevQty_".$i;
			$txtQty 				= "txtQty_".$i;
			$txtWoRate 				= "txtWoRate_".$i;			
			$txtBillRate 			= "txtBillRate_".$i;
			$txtBillAmount 			= "txtBillAmount_".$i;
			
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
			$hdnJobDtlsId 			= "hdnJobDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnProductionDtlsId 	= "hdnProductionDtlsId_".$i;
			$hdnDeleveryDtlsId 		= "hdnDeleveryDtlsId_".$i;
			$txtWorkOrderQuantity 	= "txtWorkOrderQuantity_".$i;
			
			$WorkOrderQuantity=trim(str_replace("'",'',$$txtWorkOrderQuantity))*1;
			$CurQty=trim(str_replace("'",'',$$txtQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			$TotDelQuantity=trim(str_replace("'",'',$$txtTotDelQuantity))*1;

			$Cur_Bl_Qty =$CurQty;
			$prev_Bl_Qty=$PrevQty;
			$total_Bl_Qty=$prev_Bl_Qty+$Cur_Bl_Qty;
			$woBlQnty=$WorkOrderQuantity;
 			
			$WoRate=trim(str_replace("'",'',$$txtWoRate))*1;
			$billQty=trim(str_replace("'",'',$$txtQty))*1;
			$domestic_rate=$WoRate*$txt_exchange_rate;
			$domestic_amount=$billQty*$domestic_rate;
			
			////////////////////////// over_receive_limit_qnty start
			//echo "10**".$Cur_Do_Qty."prev_Do_Qty".$prev_Do_Qty."total_Do_Qty".$total_Do_Qty."woDoQnty".$woDoQnty."over_receive_limit_qnty".$over_receive_limit_qnty."allow_total_val".$allow_total_val; 
 			//1prev_Do_Qty 2 total_Do_Qty 3 woDoQnty 3.5 over_receive_limit_qnty 0.7 allow_total_val 4.2
			
			if($variable_set_invent[0][csf('over_rcv_payment')]==2)
			{
				$over_receive_limit_qnty =$WorkOrderQuantity;			
				$allow_total_val =$WorkOrderQuantity;
				$overRecvLimitMsg="Total Delivery Quantity =($over_receive_limit_qnty.)";
				if($WorkOrderQuantity<$total_Bl_Qty) 
				{
					$over_msg = ($variable_set_invent[0][csf('over_rcv_payment')]==2)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than Wo Quantity.\n\nBill/quantity = $total_Bl_Qty \n$overRecvLimitMsg $over_msg";
					disconnect($con);
					die;
				}
			}
			else
			{
				$over_receive_limit_qnty =$TotDelQuantity;			
				$allow_total_val =$TotDelQuantity;
				$overRecvLimitMsg="Total Delivery Quantity =($over_receive_limit_qnty.)";
				if($TotDelQuantity<$total_Bl_Qty) 
				{
					$over_msg = ($variable_set_invent[0][csf('over_rcv_payment')]==2)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than Total Delivery Quantity.\n\nBill/quantity = $total_Bl_Qty \n$overRecvLimitMsg $over_msg";
					disconnect($con);
					die;
				}
				
				/*if($allow_total_val<$total_Bl_Qty) 
				{
					$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than WO quantity.\n\nWO/quantity = $woBlQnty \n$overRecvLimitMsg $over_msg";
					die;
				}*/
			}
			
			////////////////////////////////////////////// over_receive_limit_qnty end
			//if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$field_array2="id, mst_id,  order_id, order_no, section, item_description, challan_no, gmts_color_id, gmts_size_id, color_id, size_id, order_uom, total_delv_qty, previous_bill_qty, quantity, wo_rate, bill_rate, bill_amount,job_dtls_id, production_dtls_id,domestic_rate,domestic_amount, inserted_by, insert_date";
			$data_array2 .="(".$id1.",".$id.",".$$txtWorkOrderID.",".$$txtWorkOrder.",".$$cboSection.",".$$txtdescription.",".$$txtChallan.",".$$txtgmtscolorId.",".$$txtgmtssizeId.",".$$txtcolorID.",".$$txtsizeID.",".$$cboUom.",".str_replace(",",'',$$txtTotDelQuantity).",".str_replace(",",'',$$txtPrevQty).",".str_replace(",",'',$$txtQty).",".str_replace(",",'',$$txtWoRate).",".str_replace(",",'',$$txtBillRate).",".str_replace(",",'',$$txtBillAmount).",".str_replace(",",'',$$hdnJobDtlsId).",".str_replace(",",'',$$hdnDeleveryDtlsId).",".$domestic_rate.",".$domestic_amount.",'".$user_id."','".$pc_date_time."')";
			
			$id1=$id1+1; $add_commaa++;
		}
		$flag=1;
		$rID=sql_insert("trims_bill_mst",$field_array,$data_array,1);
		if($rID) $flag=1; else $flag=0;
		// echo "10**INSERT INTO trims_bill_dtls (".$field_array2.") VALUES ".$data_array2;die;
		if($flag)
		{
			$rID2=sql_insert("trims_bill_dtls",$field_array2,$data_array2,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		
		//echo "10**".$rID."**".$rID2."**".$flag; die;
	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$bill_no_manual);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$bill_no_manual);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$bill_no_manual);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$bill_no_manual);
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
		
		
		$bill_mst_id=str_replace("'",'',$update_id);
        $sql_select_prev_dtls_id = return_library_array("select id, mst_id from trims_bill_dtls where mst_id=$update_id and status_active = 1 and is_deleted = 0", "id", "mst_id");
		$nameArray= sql_select("select is_posted_account from trims_bill_mst where id='$bill_mst_id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		if($posted_account==1)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			exit();
		}
        if($db_type==0)
		{
			$txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date), "", "",1);
		}

		$field_array="company_id*location_id*within_group*party_id*party_location*bill_date*order_id*received_id*job_id*challan_no*bill_no*remarks*exchange_rate*currency_id*wo_currency_id*bill_amount*up_charge*discount*net_bill_amount*upcharge_remarks*discount_remarks*updated_by*update_date";	
		$data_array="'".$cbo_company_name."'*'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$txt_bill_date."'*'".$hid_order_id."'*'".$hid_recv_id."'*'".$hid_job_id."'*'".$txt_challan_no."'*'".$bill_no_manual."'*'".$txt_remarks."'*'".$txt_exchange_rate."'*'".$cbo_currency."'*'".$cbo_Wo_Currency."'*'".$txtBillAmount."'*'".$txt_upcharge."'*'".$txt_discount."'*'".$txt_net_Amount."'*'".$txt_up_remarks."'*'".$txt_discount_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array2="order_id*order_no*section*item_description*challan_no*gmts_color_id*gmts_size_id*color_id*size_id*order_uom*total_delv_qty*previous_bill_qty*quantity* wo_rate*bill_rate*bill_amount*domestic_rate*domestic_amount*updated_by*update_date";
        $field_array3="id, mst_id,  order_id, order_no, section, item_description, challan_no, gmts_color_id, gmts_size_id, color_id, size_id, order_uom, total_delv_qty, previous_bill_qty, quantity, wo_rate, bill_rate, bill_amount,job_dtls_id, production_dtls_id,domestic_rate,domestic_amount, inserted_by, insert_date";
		$id3=return_next_id( "id", "trims_bill_dtls",1) ;
		$add_comma=0;	$flag="";
		for($i=1; $i<=$total_row; $i++)
		{	
		
			$txtWorkOrder			= "txtWorkOrder_".$i; 
			$txtWorkOrderID			= "txtWorkOrderID_".$i; 
			$cboSection				= "cboSection_".$i;
			$txtdescription			= "txtdescription_".$i;
			$txtChallan				= "txtChallan_".$i;
			$txtgmtscolorId			= "txtgmtscolorId_".$i;
			$txtgmtssizeId			= "txtgmtssizeId_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboUom					= "cboUom_".$i;
			$txtTotDelQuantity 		= "txtTotDelQuantity_".$i;
			$txtPrevQty				= "txtPrevQty_".$i;
			$txtQty 				= "txtQty_".$i;
			$txtWoRate 				= "txtWoRate_".$i;			
			$txtBillRate 			= "txtBillRate_".$i;
			$txtBillAmount 			= "txtBillAmount_".$i;
			
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
			$hdnJobDtlsId 			= "hdnJobDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnProductionDtlsId 	= "hdnProductionDtlsId_".$i;
			$hdnDeleveryDtlsId 		= "hdnDeleveryDtlsId_".$i;
			$txtWorkOrderQuantity 	= "txtWorkOrderQuantity_".$i;
			
			
			$WorkOrderQuantity=trim(str_replace("'",'',$$txtWorkOrderQuantity))*1;
			$CurQty=trim(str_replace("'",'',$$txtQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			$TotDelQuantity=trim(str_replace("'",'',$$txtTotDelQuantity))*1;
			$Cur_Bl_Qty =$CurQty;
			$prev_Bl_Qty=$PrevQty;
			$total_Bl_Qty=$prev_Bl_Qty+$Cur_Bl_Qty;
			$woBlQnty=$WorkOrderQuantity;
			
			
			$WoRate=trim(str_replace("'",'',$$txtWoRate))*1;
			$billQty=trim(str_replace("'",'',$$txtQty))*1;
			$domestic_rate=$WoRate*$txt_exchange_rate;
			$domestic_amount=$billQty*$domestic_rate;
			
			
			if($variable_set_invent[0][csf('over_rcv_payment')]==2)
			{
				$over_receive_limit_qnty =$WorkOrderQuantity;			
				$allow_total_val =$WorkOrderQuantity;
				$overRecvLimitMsg="Total Delivery Quantity =($over_receive_limit_qnty.)";
				if($WorkOrderQuantity<$total_Bl_Qty) 
				{
					$over_msg = ($variable_set_invent[0][csf('over_rcv_payment')]==2)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than Wo Quantity.\n\nBill/quantity = $total_Bl_Qty \n$overRecvLimitMsg $over_msg";
					disconnect($con);
					die;
				}
			}
			else
			{
				$over_receive_limit_qnty =$TotDelQuantity;			
				$allow_total_val =$TotDelQuantity;
				$overRecvLimitMsg="Total Delivery Quantity =($over_receive_limit_qnty.)";
				if($TotDelQuantity<$total_Bl_Qty) 
				{
					$over_msg = ($variable_set_invent[0][csf('over_rcv_payment')]==2)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than Total Delivery Quantity.\n\nBill/quantity = $total_Bl_Qty \n$overRecvLimitMsg $over_msg";
					disconnect($con);
					die;
				}
				
				/*if($allow_total_val<$total_Bl_Qty) 
				{
					$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					echo "40**Bill. quantity can not be greater than WO quantity.\n\nWO/quantity = $woBlQnty \n$overRecvLimitMsg $over_msg";
					die;
				}*/
			}
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
                 unset($sql_select_prev_dtls_id[str_replace("'",'',$$hdnDtlsUpdateId)]);
 		        $data_array2[$aa]=explode("*",("".$$txtWorkOrderID."*".$$txtWorkOrder."*".$$cboSection."*".$$txtdescription."*".$$txtChallan."*".$$txtgmtscolorId."*".$$txtgmtssizeId."*".$$txtcolorID."*".$$txtsizeID."*".$$cboUom."*".str_replace(",",'',$$txtTotDelQuantity)."*".str_replace(",",'',$$txtPrevQty)."*".str_replace(",",'',$$txtQty)."*".str_replace(",",'',$$txtWoRate)."*".str_replace(",",'',$$txtBillRate)."*".str_replace(",",'',$$txtBillAmount)."*".str_replace(",",'',$domestic_rate)."*".str_replace(",",'',$domestic_amount)."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
				
			}else{
                if ($add_commaa!=0) $data_array3 .=",";
			    $data_array3 .="(".$id3.",".$update_id.",".$$txtWorkOrderID.",".$$txtWorkOrder.",".$$cboSection.",".$$txtdescription.",".$$txtChallan.",".$$txtgmtscolorId.",".$$txtgmtssizeId.",".$$txtcolorID.",".$$txtsizeID.",".$$cboUom.",".str_replace(",",'',$$txtTotDelQuantity).",".str_replace(",",'',$$txtPrevQty).",".str_replace(",",'',$$txtQty).",".str_replace(",",'',$$txtWoRate).",".str_replace(",",'',$$txtBillRate).",".str_replace(",",'',$$txtBillAmount).",".str_replace(",",'',$$hdnJobDtlsId).",".str_replace(",",'',$$hdnDeleveryDtlsId).",".str_replace(",",'',$domestic_rate).",".str_replace(",",'',$domestic_amount).",'".$user_id."','".$pc_date_time."')";
			    $id3=$id3+1; $add_commaa++;
			}
		}
        $field_arr4="status_active*is_deleted*updated_by*update_date";
        if(count($sql_select_prev_dtls_id) > 0){
            foreach ($sql_select_prev_dtls_id as $key => $val){
                $inv_transaction_data_arr[$key]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
                $deleted_id_arr[] = $key;
            }
            $rID4=execute_query(bulk_update_sql_statement("trims_bill_dtls","id",$field_arr4,$inv_transaction_data_arr,$deleted_id_arr));
        }

		$rID=sql_update("trims_bill_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;

		if($data_array2!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "trims_bill_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}
        if($data_array3!=""){
            $rID3=sql_insert("trims_bill_dtls",$field_array3,$data_array3,1);
			if($rID3) $flag=1; else $flag=0;
        }
         if(count($sql_select_prev_dtls_id) > 0){
            if($rID4) $flag=1; else $flag=0;
         }
		//echo "10**".$rID.'='.$rID2; die;
		//if($rID4) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT"); 
				echo "1**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_bill_no_manual); 
				
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_bill_no_manual);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_bill_no_manual);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_bill_no_manual);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		
		$bill_mst_id=str_replace("'",'',$update_id);
		$nameArray= sql_select("select is_posted_account from trims_bill_mst where id='$bill_mst_id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		if($posted_account==1)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			exit();
		}
		
		
		$update_id=str_replace("'","",$update_id);
		$deleted_id_arr=array();
		for($i=1;$i<=$total_row; $i++)
		{
			$updateIdDtls="hdnDtlsUpdateId_".$i;
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$inv_transaction_data_arr[str_replace("'",'',$$updateIdDtls)]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$deleted_id_arr[]=str_replace("'",'',$$updateIdDtls);
			}
		}
		
		
		$field_arr="status_active*is_deleted*updated_by*update_date";
		$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("trims_bill_mst",$field_arr,$data_arr,"id",$update_id,1);
		$rID1=execute_query(bulk_update_sql_statement("trims_bill_dtls","id",$field_arr,$inv_transaction_data_arr,$deleted_id_arr));
		//echo "10**".$rID.'='.$rID1; die;
		//print_r($deleted_id_arr);
		
		
		// die;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die; 
	}
}
if($action=="challan_print") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
	//	if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		if($company_data[csf('country_id')]!=0)$country = $country_full_name[$company_data[csf("country_id")]].'.';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	?>
	<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 230%;
			}
			.opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			#table_3{background-image:url(../../../img/bg-3.jpg);}
			#party_location br{
				display: none;
			}
		</style>
		<?
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
		$sql_mst =sql_select("SELECT id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate, up_charge, discount,upcharge_remarks, discount_remarks from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	
		$i = 1;
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$total_ammount = 0; $total_quantity=0;

		$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
		 $sql = "select a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate as bill_actual_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id, d.style, e.item_group,e.buyer_po_no, e.mst_id as rcv_id from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.id=b.mst_id and b.mst_id=$data[1] and c.id = b.production_dtls_id and c.break_down_details_id = d.id and d.mst_id = e.id and e.mst_id = c.received_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1  order by b.id ASC";

		//  echo $sql;
		$data_array=sql_select($sql);
		$order_arr=array();
		$order_number_po=''; $delevery_dtls_ids='';
		$order_number_sql=sql_select($sql);
		foreach ($order_number_sql as $row)
		{
			$order_arr[]="'".$row[csf("order_no")]."'";
			$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';
		}
		//unset($order_sql_res);
		$order_number_po=implode(",",array_unique($order_arr));
		//echo $delevery_dtls_ids.'==';
		$delevery_dtls_ids=chop($delevery_dtls_ids,',');
		//echo $delevery_dtls_ids;
		$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));

		$internalRef_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			//$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
			$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		}
		unset($po_sql_res);


		//echo $order_number_po;
		$buyer_po_arr=array(); $intRef_arr=array();
		$order_buyer_po='';
		
		$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,c.trims_del,c.delivery_date, d.trims_ref,d.buyer_tb from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

         //echo $order_sql;die();
		//$order_sql ="select b.buyer_buyer,b.buyer_po_id,d.id from subcon_ord_mst a, subcon_ord_dtls b ,trims_job_card_mst c, trims_job_card_dtls d where a.id=b.mst_id and a.id=c.received_id and c.id=d.mst_id and a.entry_form='255' and b.order_no in ($order_number_po) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.buyer_buyer,b.buyer_po_id,d.id "; 
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$buyer_po_arr['buyer_buyer']=$row[csf("buyer_buyer")];
			//$piArray[$row['buyer_po_id']]['grouping']
			$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
			$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
			$buyer_tb_arr[$row[csf("id")]]['buyer_tb'] =$row[csf("buyer_tb")];
			$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
		}
	

		//$challan_date_arr=return_library_array( "select delivery_date,trims_del from trims_delivery_mst",'trims_del','delivery_date');
		
		if($data[2]==1)
		{  
			$order_buyer_po=implode(",",array_unique($buyer_po_arr['buyer_buyer']));
			$buyer_po_ids=implode(",",array_unique($buyer_po_arr['buyer_po_id']));
			$buyer_name='';
			$order_id=array_unique(explode(",",$order_buyer_po));
			foreach($order_id as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
			}
			$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

			/*$internalRef='';
			$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
			foreach($buyer_po_id as $val)
			{
				if($internalRef=="") $internalRef=$internalRef_arr[$val]['internalRef']; else $internalRef.=",".$internalRef_arr[$val]['internalRef'];
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));*/
		}
		else 
		{ 
			$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
		} 
	
	
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
	

	$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
	$fac_merchant=$fac_merchant_arr[$data_array[0][csf("rcv_id")]];

	$buying_merchant_arr=return_library_array( "select id, buying_merchant from subcon_ord_mst",'id','buying_merchant');
	$buying_merchant=$buying_merchant_arr[$data_array[0][csf("rcv_id")]];

	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
	    <div style="width:1800px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?>
	                	<span style="font-size:x-large; position: absolute; right: 25px; top: 17px;">
	                		<? echo $cid;?><sup><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo  'nd';} else{ echo 'rd';} ?></sup> Copy
	                	</span>
	                </strong>
	                </td>
	            </tr>
	            <tr>
					<td  colspan="5" align="center">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city,contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
								Head Office: House # <?php echo $result[csf('plot_no')]; ?>
								Road # <?php echo $result[csf('road_no')]; ?>
								Sector # <?php echo $result[csf('block_no')]; ?>
								<?php echo $result[csf('zip_code')]; ?>,
								<?php echo $country; ?>
								<br/>
								Factory Address : <? echo $result[csf('city')]; ?><br>
								Email : <? echo $result[csf('email')]; ?><br>
								Mobile : <? echo $result[csf('contact_no')]; ?><br>
								Vat No : <? echo $result[csf('vat_number')]; ?> 
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td style="font-size:20px;" align="center" colspan="5">
						<strong><? echo $data[3]; ?></strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td style="font-size:20px;" align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			
			<table class="rpt_table" width="1200px" cellspacing="1" >
	            <tr>
	                <td valign="top" width="150"><b>Party Name</b></td>
	                <td valign="top" width="250">: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                
	                <td valign="top" width="150"><b>Bill No.</b> </td>
	                <td valign="top" width="250">:<b> <? echo $data[5]; ?></b></td>
					<td valign="top" width="150">&nbsp;</td>
					<td valign="top" width="250">&nbsp;</td>
					
	            </tr>
				<tr>
					<td valign="top" >Address</td>
	                <td valign="top" colspan="5" id="party_location">: <? echo $party_location; ?></td>
				</tr>
	            <tr>
	                <td valign="top" >Bill Date</td>
	                <td valign="top" >: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
					<!-- <td valign="top" width="250">&nbsp;</td> -->
					<td valign="top" >Buyer Name</td>
	                <td valign="top">: <? echo $buyer_name; ?></td>
	                <!-- <td valign="top" width="250">&nbsp;</td> -->
	                <td valign="top" >Buyer PO No</td>
	                <td valign="top" >: <? ?></td>
	            </tr>
	            
	            <tr>
				<td valign="top" >Bill Currency</td>
	                <td valign="top" >: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	                <!-- <td valign="top" width="250">&nbsp;</td> -->
	                <td valign="top" >Factory&nbsp;Merchant.</td>
	                <td valign="top" >: <? echo $fac_merchant; ?></td>
					<!-- <td valign="top" width="250">&nbsp;</td> -->
	                <td valign="top">Buyer TB</td>
	                <td valign="top" >: <?echo $buyer_tb_arr[$row[csf("id")]]['buyer_tb'];?></td>
	            </tr>
				<tr>
				    <td valign="top" width="100">Buying Merchant</td>
	                <td valign="top" width="150">: <? echo $buying_merchant;?></td>
	            </tr>
	        
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr>
		            	<th width="30">SL</th>
	                    <th width="120">Item Group</th>
	                    <th width="120">Section</th>
	                    <th width="100">Buyer PO</th>
						<th width="100">Style Ref.</th>
	                    <th width="130">Item Description</th>
		                <th width="140">Challan No</th>
		                <th width="95">Challan Date</th>
		                <? if($data[2]==1){?>
							<th width="100">Internal Ref. No.</th>
						<? }?>
						<th width="100">Trims Ref.</th>
		                <th width="140">WO NO</th>
						<th width="100">Gmts Color</th>
		                <th width="100">Gmts Size</th>
	                    <th width="100">Item Color</th>
		                <th width="100">Item Size</th>				
		                <th width="60">UOM</th>
	                    <th width="80">Bill Qnty</th>
		                <th width="80">Bill Rate</th>
		                <th width="100">Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}
					$i=1;

					foreach($data_array as $row)
					{
						$internalRef=''; $trims_ref='';
						$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
						foreach ($del_dtls_ids as  $value) {
							$internalRef.=$intRef_arr[$value]['internalRef'].',';
							$trims_ref.=$trims_ref_arr[$value]['trims_ref'].',';
						}
						$internalRef=implode(",",array_unique(explode(",",$internalRef)));
						$trims_ref=implode(",",array_unique(explode(",",$trims_ref)));
						?>
	                    <tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $item_group_arr[$row[csf('item_group')]];  ?></td>
	                    <td><?php echo $trims_section[$row[csf('section')]];  ?></td>
	                    <td><?php echo $row[csf('buyer_po_no')];  ?></td>
						<td style="word-break: break-word;"><? echo $row[csf('style')]; ?></td>
	                    <td  style="word-break: break-word;" ><?php echo $row[csf('item_description')]; ?></td>
		                <td><?php echo $row[csf('challan_no')];  ?></td>
		                <td><?php echo change_date_format($challan_date_arr[$row[csf('challan_no')]]);  ?></td>
		                <? if($data[2]==1){?>
							<td><?php echo chop($internalRef,',');  ?></td>
						<? }?>
		                <td><?php echo chop($trims_ref,',');  ?></td>
		                <td><?php echo $row[csf('order_no')];  ?></td>
						<td style="word-break: break-word;"><?php echo $color_arr[$row[csf('gmts_color_id')]]; ?> </td>
		                <td style="word-break: break-word;"><?php echo $size_arr[$row[csf('gmts_size_id')]]; ?></td>
	                    <td style="word-break: break-word;"><?php echo $color_arr[$row[csf('color_id')]]; ?> </td>
		                <td style="word-break: break-word;"><?php echo $size_arr[$row[csf('size_id')]]; ?></td>				
		                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
	                    <td align="right"><?php echo  number_format($row[csf('delevery_qty')],4);$total_quantity += $row[csf('delevery_qty')]; ?></td>
		                <td align="right" title="<? echo $row[csf('bill_actual_rate')]; ?>"><?php echo number_format($row[csf('bill_actual_rate')],6);  ?></td>
		                <td align="right"><?php  $bill_ammount=$row[csf('delevery_qty')]*$row[csf('bill_actual_rate')];
		                echo number_format($bill_ammount,4); $total_ammount+= $bill_ammount; ?></td>
	                    </tr>
					<? 
					$i++;
	                } 
	                ?>
	            </tbody> 
	            <!--<tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	            <? //if($data[2]==1) $colspan=10; else $colspan=9;?>
	            <td align="right" colspan="<? //echo $colspan; ?>" ><b>Total</b></td>
	            <td align="right"><b><? //echo $format_total_amount=$total_quantity; ?></b></td>
	            <td align="right"></td>
	            <td align="right"><b><? //echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? //echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>-->
	        </table>
             <table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	            <tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
                 		<td width="30">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
	                    <td width="130">&nbsp;</td>
		                <td width="140">&nbsp;</td>
		                <td width="95">&nbsp;</td>
		                <? if($data[2]==1){?>
							<td width="100">&nbsp;</td>
						<? }?>
						<td width="100">&nbsp;</td>
		                <td width="140">&nbsp;</td>	
						<td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>	
	                    <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>			
		                <td width="60" align="right"><b>Total</b></td>
	                    <td width="80" align="right"><? echo $format_total_amount=number_format($total_quantity,4,'.','');  ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="100" align="right"><b><? echo $format_total_amount=number_format($total_ammount,4,'.',''); ?></b></td>
	            </tr> 
				<?
				if($data[2]==1){
					$colspan = 12;
					$colspan_net = 17;
					$in_world=19;
				}
				else{
					$colspan = 11;
					$colspan_net = 16;
					$in_world=19;
				}
				if($sql_mst[0][csf("discount")]>0 || $sql_mst[0][csf("up_charge")]>0)
				{
					
				?>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan;?>" align="left"></td>
					<td colspan="2" align="right"><b>Upcharge Remarks:</b></td>
					<td colspan="3" align="left" ><b><? echo $sql_mst[0][csf("upcharge_remarks")]?></b></td>
					<td><b>Up Charge</b></td>
					<td align="right"><b><? echo number_format($sql_mst[0][csf("up_charge")],4)?></b></td>
				</tr>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan;?>" align="left"></td>
					<td colspan="2"  align="right"><b>Discount Remarks:</b></td>
					<td colspan="3" align="left"><b><? echo $sql_mst[0][csf("discount_remarks")] ?></b></td>
					<td><b>Discount</b></td>
					<td align="right"><b><? echo number_format($sql_mst[0][csf("discount")],4)?></b></td>
				</tr>
				<tr bgcolor="#CCCCCC" style="font-size:15px">
					<td colspan="<?=$colspan_net;?>" align="left"></td>
					<td><b>Net Total</b></td>
					<td align="right"><b><? $format_total_amount = ($total_ammount+$sql_mst[0][csf("up_charge")]-$sql_mst[0][csf("discount")]);
					 echo number_format($format_total_amount,4)?></b></td>
				</tr>
				<?}?>
				 
				<tr>
					<td colspan="<?=$in_world?>" align="left"><b>In Word: <? echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>
	        </table>
	        <?php echo signature_table(167, $data[0], "1700px", "", 70);?>
		</div>
   	<?
	}
	exit();
 }
if($action=="challan_print_backup") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
	//	if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		if($company_data[csf('country_id')]!=0)$country = $country_full_name[$company_data[csf("country_id")]].'.';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	?>
	<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 230%;
			}
			.opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			#table_3{background-image:url(../../../img/bg-3.jpg);}
			
		</style>
		<?
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
		$sql_mst =sql_select(" select id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	
		$i = 1;
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$total_ammount = 0; $total_quantity=0;

		$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
		 $sql = "select a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.gmts_color_id, b.gmts_size_id, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate as bill_actual_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id, d.style, e.item_group,e.buyer_po_no, e.mst_id as rcv_id from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.id=b.mst_id and b.mst_id=$data[1] and c.id = b.production_dtls_id and c.break_down_details_id = d.id and d.mst_id = e.id and e.mst_id = c.received_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1  order by b.id ASC";

		//  echo $sql;
		$data_array=sql_select($sql);
		$order_arr=array();
		$order_number_po=''; $delevery_dtls_ids='';
		$order_number_sql=sql_select($sql);
		foreach ($order_number_sql as $row)
		{
			$order_arr[]="'".$row[csf("order_no")]."'";
			$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';
		}
		//unset($order_sql_res);
		$order_number_po=implode(",",array_unique($order_arr));
		//echo $delevery_dtls_ids.'==';
		$delevery_dtls_ids=chop($delevery_dtls_ids,',');
		//echo $delevery_dtls_ids;
		$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));

		$internalRef_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			//$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
			$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		}
		unset($po_sql_res);


		//echo $order_number_po;
		$buyer_po_arr=array(); $intRef_arr=array();
		$order_buyer_po='';
		
		$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,c.trims_del,c.delivery_date, d.trims_ref from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";


		//$order_sql ="select b.buyer_buyer,b.buyer_po_id,d.id from subcon_ord_mst a, subcon_ord_dtls b ,trims_job_card_mst c, trims_job_card_dtls d where a.id=b.mst_id and a.id=c.received_id and c.id=d.mst_id and a.entry_form='255' and b.order_no in ($order_number_po) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.buyer_buyer,b.buyer_po_id,d.id "; 
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$buyer_po_arr['buyer_buyer']=$row[csf("buyer_buyer")];
			//$piArray[$row['buyer_po_id']]['grouping']
			$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
			$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
			$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
		}

		//$challan_date_arr=return_library_array( "select delivery_date,trims_del from trims_delivery_mst",'trims_del','delivery_date');
		
		if($data[2]==1)
		{  
			$order_buyer_po=implode(",",array_unique($buyer_po_arr['buyer_buyer']));
			$buyer_po_ids=implode(",",array_unique($buyer_po_arr['buyer_po_id']));
			$buyer_name='';
			$order_id=array_unique(explode(",",$order_buyer_po));
			foreach($order_id as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
			}
			$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

			/*$internalRef='';
			$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
			foreach($buyer_po_id as $val)
			{
				if($internalRef=="") $internalRef=$internalRef_arr[$val]['internalRef']; else $internalRef.=",".$internalRef_arr[$val]['internalRef'];
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));*/
		}
		else 
		{ 
			$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
		} 
	
	
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
	

	$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
	$fac_merchant=$fac_merchant_arr[$data_array[0][csf("rcv_id")]];

	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
	    <div style="width:1700px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?>
	                	<span style="font-size:x-large; position: absolute; right: 25px; top: 17px;">
	                		<? echo $cid;?><sup><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo  'nd';} else{ echo 'rd';} ?></sup> Copy
	                	</span>
	                </strong>
	                </td>
	            </tr>
	            <tr>
					<td  colspan="5" align="center">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city,contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
								Head Office: House # <?php echo $result[csf('plot_no')]; ?>
								Road # <?php echo $result[csf('road_no')]; ?>
								Sector # <?php echo $result[csf('block_no')]; ?>
								<?php echo $result[csf('zip_code')]; ?>,
								<?php echo $country; ?>
								<br/>
								Factory Address : <? echo $result[csf('city')]; ?><br>
								Email : <? echo $result[csf('email')]; ?><br>
								Mobile : <? echo $result[csf('contact_no')]; ?><br>
								Vat No : <? echo $result[csf('vat_number')]; ?> 
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td style="font-size:20px;" align="center" colspan="5">
						<strong><? echo $data[3]; ?></strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td style="font-size:20px;" align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><b>Party Name</b></td>
	                <td valign="top" width="150">: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><b>Bill No.</b> </td>
	                <td valign="top">:<b> <? echo $data[5]; ?></b></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Bill Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            
	            <tr>
	            	<td valign="top" width="120">Buyer Name</td>
	                <td valign="top">: <? echo $buyer_name; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Buyer PO No</td>
	                <td valign="top" width="150">: <? ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">Bill Currency</td>
	                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Factory Merchant.</td>
	                <td valign="top" width="150">: <? echo $fac_merchant; ?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr>
		            	<th width="30">SL</th>
	                    <th width="120">Item Group</th>
	                    <th width="120">Section</th>
	                    <th width="100">Buyer PO</th>
						<th width="100">Style Ref.</th>
	                    <th width="130">Item Description</th>
		                <th width="140">Challan No</th>
		                <th width="95">Challan Date</th>
		                <? if($data[2]==1){?>
							<th width="100">Internal Ref. No.</th>
						<? }?>
						<th width="100">Trims Ref.</th>
		                <th width="140">WO NO</th>
						<th width="100">Gmts Color</th>
		                <th width="100">Gmts Size</th>
	                    <th width="100">Item Color</th>
		                <th width="100">Item Size</th>				
		                <th width="60">UOM</th>
	                    <th width="80">Bill Qnty</th>
		                <th width="80">Bill Rate</th>
		                <th width="100">Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}
					$i=1;

					foreach($data_array as $row)
					{
						$internalRef=''; $trims_ref='';
						$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
						foreach ($del_dtls_ids as  $value) {
							$internalRef.=$intRef_arr[$value]['internalRef'].',';
							$trims_ref.=$trims_ref_arr[$value]['trims_ref'].',';
						}
						$internalRef=implode(",",array_unique(explode(",",$internalRef)));
						$trims_ref=implode(",",array_unique(explode(",",$trims_ref)));
						?>
	                    <tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $item_group_arr[$row[csf('item_group')]];  ?></td>
	                    <td><?php echo $trims_section[$row[csf('section')]];  ?></td>
	                    <td><?php echo $row[csf('buyer_po_no')];  ?></td>
						<td style="word-break: break-word;"><? echo $row[csf('style')]; ?></td>
	                    <td  style="word-break: break-word;" ><?php echo $row[csf('item_description')]; ?></td>
		                <td><?php echo $row[csf('challan_no')];  ?></td>
		                <td><?php echo change_date_format($challan_date_arr[$row[csf('challan_no')]]);  ?></td>
		                <? if($data[2]==1){?>
							<td><?php echo chop($internalRef,',');  ?></td>
						<? }?>
		                <td><?php echo chop($trims_ref,',');  ?></td>
		                <td><?php echo $row[csf('order_no')];  ?></td>
						<td style="word-break: break-word;"><?php echo $color_arr[$row[csf('gmts_color_id')]]; ?> </td>
		                <td style="word-break: break-word;"><?php echo $size_arr[$row[csf('gmts_size_id')]]; ?></td>
	                    <td style="word-break: break-word;"><?php echo $color_arr[$row[csf('color_id')]]; ?> </td>
		                <td style="word-break: break-word;"><?php echo $size_arr[$row[csf('size_id')]]; ?></td>				
		                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
	                    <td align="right"><?php echo $row[csf('delevery_qty')];$total_quantity += $row[csf('delevery_qty')]; ?></td>
		                <td align="right" title="<? echo $row[csf('bill_actual_rate')]; ?>"><?php echo number_format($row[csf('bill_actual_rate')],6);  ?></td>
		                <td align="right"><?php  $bill_ammount=$row[csf('delevery_qty')]*$row[csf('bill_actual_rate')];
		                echo number_format($bill_ammount,4); $total_ammount+= $bill_ammount; ?></td>
	                    </tr>
					<? 
					$i++;
	                } 
	                ?>
	            </tbody> 
	            <!--<tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	            <? //if($data[2]==1) $colspan=10; else $colspan=9;?>
	            <td align="right" colspan="<? //echo $colspan; ?>" ><b>Total</b></td>
	            <td align="right"><b><? //echo $format_total_amount=$total_quantity; ?></b></td>
	            <td align="right"></td>
	            <td align="right"><b><? //echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? //echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>-->
	        </table>
             <table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	            <tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
                        <td width="30">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
	                    <td width="130">&nbsp;</td>
		                <td width="140">&nbsp;</td>
		                <td width="95">&nbsp;</td>
		                <? if($data[2]==1){?>
							<td width="100">&nbsp;</td>
						<? }?>
						<td width="100">&nbsp;</td>
		                <td width="140">&nbsp;</td>	
	                    <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>				
		                <td width="60" align="right"><b>Total</b></td>
	                    <td width="80" align="right"><? echo $format_total_amount=$total_quantity; ?></td>
		                <td width="80">&nbsp;</td>
		               <td width="100" align="right"><b><? echo $format_total_amount=number_format($total_ammount,4,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="13" align="left"><b>In Word: <? echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>
	        </table>
	        <?php echo signature_table(167, $data[0], "1700px", "", 70);?>
		</div>
   	<?
	}
	exit();
 }

if($action=="challan_print2") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	?>
	<style type="text/css">
		.opacity_1
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 20%;
		}	
		.opacity_2
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 230%;
		}
		.opacity_3
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 20%;
		}
		@media print {
			.page-break	{ display: block; page-break-after: always;}
		}
		
		#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
		#table_1{background-image:url(../../../img/bg-1.jpg);}
		#table_2{background-image:url(../../../img/bg-2.jpg); }
		#table_3{background-image:url(../../../img/bg-3.jpg);}
			
	</style>
		<?
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
		$sql_mst =sql_select(" select id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	
		$i = 1;
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$total_ammount = 0; $total_quantity=0;
		$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
	 	$sql = "select a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate, b.bill_amount, b.production_dtls_id, a.inserted_by from trims_bill_mst a, trims_bill_dtls b where a.id=b.mst_id and b.mst_id=$data[1] and a.status_active=1 and b.status_active=1 order by b.id ASC";
		$data_array=sql_select($sql);
	    $inserted_by=$data_array[0][csf("inserted_by")];

		$order_arr=array();
		$production_dtls_id_arr=array();
		$order_number_po=''; $delevery_dtls_ids='';
		$order_number_sql=sql_select($sql);

		foreach ($order_number_sql as $row)
		{
			$order_arr[]="'".$row[csf("order_no")]."'";
			$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';

			$production_dtls_id_arr[] = $row[csf('production_dtls_id')];
		}
		//unset($order_sql_res);
		$order_number_po=implode(",",array_unique($order_arr));
		//echo $delevery_dtls_ids.'==';
		$delevery_dtls_ids=chop($delevery_dtls_ids,',');
		//echo $delevery_dtls_ids;
		$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));


		$production_dtls_id_arr = array_unique($production_dtls_id_arr);
		$delevery_dtls_con=where_con_using_array($production_dtls_id_arr,0,"a.id");


		$internalRef_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			//$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
			$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		}
		unset($po_sql_res);


		//echo $order_number_po;
		$buyer_po_arr=array(); $intRef_arr=array();
		$order_buyer_po='';
		
		//$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,b.buyer_po_no,b.buyer_style_ref,c.trims_del,c.delivery_date,d.subcon_job, d.order_no, b.item_group, d.trims_ref ,b.id as ord_dtls_id from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d, subcon_ord_breakdown e where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and d.subcon_job=e.job_no_mst and b.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		 $order_sql = "SELECT a.id,b.buyer_po_id,b.buyer_buyer,b.buyer_po_no,b.buyer_style_ref,c.trims_del,c.delivery_date,d.subcon_job, d.order_no, b.item_group, d.trims_ref ,b.id as ord_dtls_id, d.buying_merchant from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d, subcon_ord_breakdown e where b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and d.subcon_job=e.job_no_mst and b.id=e.mst_id $delevery_dtls_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			//$buyer_po_arr['buyer_buyer']=$row[csf("buyer_buyer")];
			//$piArray[$row['buyer_po_id']]['grouping']
			$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
			$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
			$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
			
			$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
			$buyer_po_arr[$row[csf("id")]]['item_group']=$row[csf("item_group")];
			$buyer_po_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
			$buyer_po_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
			$buyer_po_arr[$row[csf("id")]]['subcon_job']=$row[csf("subcon_job")];
			$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		}

		$bill_array = array();
		foreach ($data_array as $row) {
			$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
			foreach ($del_dtls_ids as  $value) {
				if($data[2]==1){ 
					$buyer_buyer=$buyer_arr[$buyer_po_arr[$value]['buyer_buyer']];
				}else{
					$buyer_buyer=$buyer_po_arr[$value]['buyer_buyer'];
				}
				$buyer_po_no=$buyer_po_arr[$value]['buyer_po_no'];
				$item_group=$buyer_po_arr[$value]['item_group'];
				$buyer_style_ref=$buyer_po_arr[$value]['buyer_style_ref'];
				$subcon_job=$buyer_po_arr[$value]['subcon_job'];
				$order_no=$buyer_po_arr[$value]['order_no'];
				$internalRef=$intRef_arr[$value]['internalRef'];
				$trims_ref=$trims_ref_arr[$value]['trims_ref'];
			}

			$bill_rate = number_format($row[csf('bill_rate')],4);

			$bill_array[$row[csf('section')]][$subcon_job][$buyer_po_no][$buyer_buyer][$item_group][$buyer_style_ref][$order_no][$row[csf('challan_no')]][$row[csf('item_description')]][$row[csf('order_uom')]][$bill_rate]['delevery_qty'] += $row[csf('delevery_qty')];
		}

		//$challan_date_arr=return_library_array( "select delivery_date,trims_del from trims_delivery_mst",'trims_del','delivery_date');
		
		if($data[2]==1)
		{  
			$order_buyer_po=implode(",",array_unique($buyer_po_arr['buyer_buyer']));
			$buyer_po_ids=implode(",",array_unique($buyer_po_arr['buyer_po_id']));
			$buyer_name='';
			$order_id=array_unique(explode(",",$order_buyer_po));
			foreach($order_id as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
			}
			$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

			/*$internalRef='';
			$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
			foreach($buyer_po_id as $val)
			{
				if($internalRef=="") $internalRef=$internalRef_arr[$val]['internalRef']; else $internalRef.=",".$internalRef_arr[$val]['internalRef'];
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));*/
		}
		else 
		{ 
			$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
		} 
	
	//echo "<pre>";
	//print_r($buyer_po_arr);

	// $buying_merchant_arr=return_library_array( "select id, buying_merchant from subcon_ord_mst",'id','buying_merchant');
	// $fac_merchant=$buying_merchant_arr[$data_array[0][csf("rcv_id")]];
	
	
	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
	$k=0;	
	$copy_no=array(1); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
        
	    <div style="width:1425px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?></strong> <br>
	                	<span style="font-size:large;">Export Oriented Apparel Accessories Manufacturer</span>
	                </td>
	            </tr>
	            <tr>
					<td colspan="5" align="center">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('city')]; ?><br>
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td style="font-size:20px;" align="center" colspan="5">
						<strong>BILL</strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td style="font-size:20px;" align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><b>Customer Name</b></td>
	                <td valign="top" width="150">: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><b>Bill No.</b> </td>
	                <td valign="top">:<b> <? echo $data[5]; ?></b></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top" width="150">: <? echo $party_location; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Bill Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
					<td valign="top" width="120">Buyer's Buyer</td>
	                <td valign="top" width="150">: <?php echo $buyer_buyer; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	            	<td valign="top" width="100">Bill Currency</td>
	                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1" style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr>
		            	<th width="30">SL</th>
	                    <th width="100">Section</th>
	                    <th width="150">Internal Job No</th>
	                    <th width="150">Customer Ref No</th>
	                    <th width="80">Buyer's PO</th>
	                    <th width="120">Buyer's Style Ref</th>
	                    <th width="140">Challan No</th>
	                    <th width="100">Item Group</th>
	                    <th width="150">Item Description</th>
		                <th width="50">UOM</th>
	                    <th width="90">Bill Quantity</th>
		                <th width="80">Bill Rate</th>
		                <th>Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}

					$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
					$currency_sign = $currency_sign_arr[$sql_mst[0][csf("currency_id")]];
					$currency_id = $sql_mst[0][csf("currency_id")];
					//$mcurrency, $dcurrency;
					$dcurrency="";
					if($currency_id==1){
						$mcurrency='Taka';
						$dcurrency='Paisa';
					}else if($currency_id==2){
						$mcurrency='USD';
						$dcurrency='CENTS';
					}else if($currency_id==3){
						$mcurrency='EURO';
						$dcurrency='CENTS';
					}

	                $sl = 1;
	                foreach($bill_array as $section => $sectionArr) {
	                	foreach ($sectionArr as $subcon_job => $subconArr) {
					        foreach ($subconArr as $buyer_po_no => $buyerPoArr) {
					            foreach ($buyerPoArr as $buyer_buyer => $buyerBuyerArr) {
					            	foreach ($buyerBuyerArr as $item_group => $item_groupArr) {
						                foreach ($item_groupArr as $buyer_style => $buyerStyleArr) {
						                    foreach ($buyerStyleArr as $order_no => $orderNoArr) {
						                        foreach ($orderNoArr as $challan_no => $challanArr) {
						                            foreach ($challanArr as $item_description => $itemDescArr) {
						                                foreach ($itemDescArr as $order_uom => $orderUomArr) {
						                                    foreach ($orderUomArr as $bill_rate => $row) {
						                                        if ($sl % 2 == 0) {
						                                            $bgcolor = "#E9F3FF";
						                                        }
						                                        else {
						                                            $bgcolor = "#FFFFFF";
						                                        }
						                                        ?>
						                                        <tr bgcolor="<?php echo $bgcolor; ?>" id="row_<? echo $sl; ?>" align="center">
						                                            <td width="30"><?php echo $sl; ?></td>
						                                            <td width="100"><?php echo $trims_section[$section]; ?></td>
						                                            <td width="150"><?php echo $subcon_job; ?></td>
						                                            <td width="150"><?php echo $order_no; ?></td>
						                                            <td width="80" style="word-break: break-word;"><p><?php echo $buyer_po_no; ?></p></td>
						                                            <td width="120" style="word-break: break-word;"><p><?php echo $buyer_style_ref; ?></p></td>
						                                            <td width="140"><?php echo $challan_no; ?></td>
						                                            <td width="100"><?php echo $item_group_arr[$item_group]; ?></td>
						                                            <td width="150" style="word-break: break-word;"><?php echo $item_description; ?></td>
						                                            <td width="50"><?php echo $unit_of_measurement[$order_uom]; ?></td>
						                                            <td width="90" align="right">
						                                            	<?php
						                                            		echo $row['delevery_qty'];
						                                            		$total_quantity += $row['delevery_qty'];
						                                            	?>
						                                            </td>
						                                            <td width="80" align="right"><?php echo $bill_rate; ?></td>
						                                            <td align="right">
						                                            	<span align="left"><?php echo $currency_sign; ?></span>
						                                            	<?php
						                                            		$bill_ammount=$row['delevery_qty']*$bill_rate;
						                                            		echo $bill_ammount;
						                                            		$total_ammount+= $bill_ammount;
						                                            	?>
						                                        	</td>
						                                        </tr>

						                                        <?php
						                                        $sl++;
						                                    }
						                                }
						                            }
						                        }
						                    }
						                }
						            }
					            }
					        }
					    }
	                }
	                ?>
	            </tbody> 
	            <tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	                    <td width="30">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
	                    <td width="130">&nbsp;</td>
	                    <td width="130">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="140">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
	                    <td width="150"></td>
		                <td width="50" align="right"><b>Total</b></td>
	                    <td width="90" align="right"><b><? echo $format_total_amount=$total_quantity; ?></td>
		                <td width="80">&nbsp;</td>
		               <td align="right"><b><span align="left"><?php echo $currency_sign; ?></span><? echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>
	            <!--<tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	            <? //if($data[2]==1) $colspan=10; else $colspan=9;?>
	            <td align="right" colspan="<? //echo $colspan; ?>" ><b>Total</b></td>
	            <td align="right"><b><? //echo $format_total_amount=$total_quantity; ?></b></td>
	            <td align="right"></td>
	            <td align="right"><b><? //echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? //echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>-->
	        </table>
            
	        <?php echo signature_table(167, $data[0], "1400px","",70,$inserted_by);?>
		</div>
   	<?
	}
	exit();
}
if($action=="challan_print35555555") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
	//	if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		if($company_data[csf('country_id')]!=0)$country = $country_full_name[$company_data[csf("country_id")]].'.';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	?>
	   <style type="text/css">
 			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
		</style>
		<?
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
		$sql_mst =sql_select(" select id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	
		$i = 1;
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$total_ammount = 0; $total_quantity=0;
		$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
		 $sql = "select a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate as bill_actual_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id, d.style, e.item_group,e.buyer_po_no from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.id=b.mst_id and b.mst_id=$data[1] and c.id = b.production_dtls_id and c.break_down_details_id = d.id and d.mst_id = e.id and e.mst_id = c.received_id order by b.id ASC";
		 //echo $sql;
		$data_array=sql_select($sql);
		$order_arr=array();
		$order_number_po=''; $delevery_dtls_ids='';
		$order_number_sql=sql_select($sql);
		foreach ($order_number_sql as $row)
		{
			$order_arr[]="'".$row[csf("order_no")]."'";
			$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';
		}
		//unset($order_sql_res);
		$order_number_po=implode(",",array_unique($order_arr));
		//echo $delevery_dtls_ids.'==';
		$delevery_dtls_ids=chop($delevery_dtls_ids,',');
		//echo $delevery_dtls_ids;
		$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));

		$internalRef_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			//$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
			$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		}
		unset($po_sql_res);


		//echo $order_number_po;
		$buyer_po_arr=array(); $intRef_arr=array();
		$order_buyer_po='';
		
		$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,c.trims_del,c.delivery_date, d.trims_ref from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";


		//$order_sql ="select b.buyer_buyer,b.buyer_po_id,d.id from subcon_ord_mst a, subcon_ord_dtls b ,trims_job_card_mst c, trims_job_card_dtls d where a.id=b.mst_id and a.id=c.received_id and c.id=d.mst_id and a.entry_form='255' and b.order_no in ($order_number_po) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.buyer_buyer,b.buyer_po_id,d.id "; 
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$buyer_po_arr['buyer_buyer']=$row[csf("buyer_buyer")];
			//$piArray[$row['buyer_po_id']]['grouping']
			$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
			$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
			$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
		}

		//$challan_date_arr=return_library_array( "select delivery_date,trims_del from trims_delivery_mst",'trims_del','delivery_date');
		
		if($data[2]==1)
		{  
			$order_buyer_po=implode(",",array_unique($buyer_po_arr['buyer_buyer']));
			$buyer_po_ids=implode(",",array_unique($buyer_po_arr['buyer_po_id']));
			$buyer_name='';
			$order_id=array_unique(explode(",",$order_buyer_po));
			foreach($order_id as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
			}
			$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

			/*$internalRef='';
			$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
			foreach($buyer_po_id as $val)
			{
				if($internalRef=="") $internalRef=$internalRef_arr[$val]['internalRef']; else $internalRef.=",".$internalRef_arr[$val]['internalRef'];
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));*/
		}
		else 
		{ 
			$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
		} 
	
	
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
	
	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
	    <div style="width:1700px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?>
	                	<span style="font-size:x-large; position: absolute; right: 25px; top: 17px;">
	                		<? echo $cid;?><sup><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo  'nd';} else{ echo 'rd';} ?></sup> Copy
	                	</span>
	                </strong>
	                </td>
	            </tr>
	            <tr>
					<td  colspan="5" align="center" style="font-size:20px;">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city,contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
								Head Office: House # <?php echo $result[csf('plot_no')]; ?>
								Road # <?php echo $result[csf('road_no')]; ?>
								Sector # <?php echo $result[csf('block_no')]; ?>
								<?php echo $result[csf('zip_code')]; ?>,
								<?php echo $country; ?>
								<br/>
								Factory Address : <? echo $result[csf('city')]; ?><br>
								Email : <? echo $result[csf('email')]; ?><br>
								Mobile : <? echo $result[csf('contact_no')]; ?><br>
								Vat No : <? echo $result[csf('vat_number')]; ?> 
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td style="font-size:20px;" align="center" colspan="5">
						<strong><? echo $data[3]; ?></strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td style="font-size:20px;" align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100" style="font-size:20px;" ><b>Party Name</b></td>
	                <td valign="top" width="150" style="font-size:20px;">: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                <td valign="top" width="250" style="font-size:20px;">&nbsp;</td>
	                <td valign="top" width="120" style="font-size:20px;"><b>Bill No.</b> </td>
	                <td valign="top" style="font-size:20px;">:<b> <? echo $data[5]; ?></b></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120" style="font-size:20px;">Address</td>
	                <td valign="top" style="font-size:20px;">: <? echo $party_location; ?></td>
	                <td valign="top" width="250" style="font-size:20px;">&nbsp;</td>
	                <td valign="top" width="100" style="font-size:20px;">Bill Date</td>
	                <td valign="top" width="150" style="font-size:20px;">: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            
	            <tr>
	            	<td valign="top" width="120" style="font-size:20px;">Buyer Name</td>
	                <td valign="top" style="font-size:20px;">: <? echo $buyer_name; ?></td>
	                <td valign="top" width="250" style="font-size:20px;">&nbsp;</td>
	                <td valign="top" width="100" style="font-size:20px;">Buyer PO No</td>
	                <td valign="top" width="150" style="font-size:20px;">: <? ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100" style="font-size:20px;">Bill Currency</td>
	                <td valign="top" width="150" style="font-size:20px;">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr>
		            	<th width="30" style="font-size:20px;">SL</th>
	                    <th width="120" style="font-size:20px;">Item Group</th>
	                    <th width="120" style="font-size:20px;">Section</th>
	                    <th width="100" style="font-size:20px;">Buyer PO</th>
						<th width="100" style="font-size:20px;">Style Ref.</th>
	                    <th width="130" style="font-size:20px;">Item Description</th>
		                <th width="140" style="font-size:20px;">Challan No</th>
		                <th width="95" style="font-size:20px;">Challan Date</th>
		                <th width="140" style="font-size:20px;">WO NO</th>	
		                <th width="100" style="font-size:20px;">Size</th>				
		                <th width="60" style="font-size:20px;">UOM</th>
	                    <th width="80" style="font-size:20px;">Bill Qnty</th>
		                <th width="80" style="font-size:20px;">Bill Rate</th>
		                <th width="100" style="font-size:20px;">Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}
					$i=1;

					foreach($data_array as $row)
					{
						$internalRef=''; $trims_ref='';
						$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
						foreach ($del_dtls_ids as  $value) {
							$internalRef.=$intRef_arr[$value]['internalRef'].',';
							$trims_ref.=$trims_ref_arr[$value]['trims_ref'].',';
						}
						$internalRef=implode(",",array_unique(explode(",",$internalRef)));
						$trims_ref=implode(",",array_unique(explode(",",$trims_ref)));
						?>
	                    <tr>
	                    <td style="font-size:20px;"><?php echo $i; ?></td>
	                    <td style="font-size:20px;"><?php echo $item_group_arr[$row[csf('item_group')]];  ?></td>
	                    <td style="font-size:20px;"><?php echo $trims_section[$row[csf('section')]];  ?></td>
	                    <td style="font-size:20px;"><?php echo $row[csf('buyer_po_no')];  ?></td>
						<td style="word-break: break-word;"><? echo $row[csf('style')]; ?></td>
	                    <td  style="word-break: break-word;" ><?php echo $row[csf('item_description')]; ?></td>
		                <td style="font-size:20px;"><?php echo $row[csf('challan_no')];  ?></td>
		                <td style="font-size:20px;"><?php echo change_date_format($challan_date_arr[$row[csf('challan_no')]]);  ?></td>
		                <td style="font-size:20px;"><?php echo $row[csf('order_no')];  ?></td>	
		                <td style="word-break: break-word;"><?php echo $size_arr[$row[csf('size_id')]]; ?></td>				
		                <td style="font-size:20px;"><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
	                    <td style="font-size:20px;" align="right"><?php echo $row[csf('delevery_qty')];$total_quantity += $row[csf('delevery_qty')]; ?></td>
		                <td style="font-size:20px;" align="right" title="<? echo $row[csf('bill_actual_rate')]; ?>"><?php echo $row[csf('bill_actual_rate')];  ?></td>
		                <td style="font-size:20px;" align="right"><?php  $bill_ammount=$row[csf('delevery_qty')]*$row[csf('bill_actual_rate')];
		                echo number_format($bill_ammount,4); $total_ammount+= $bill_ammount; ?></td>
	                    </tr>
					<? 
					$i++;
	                } 
	                ?>
	            </tbody> 
	            <!--<tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	            <? //if($data[2]==1) $colspan=10; else $colspan=9;?>
	            <td align="right" colspan="<? //echo $colspan; ?>" ><b>Total</b></td>
	            <td align="right"><b><? //echo $format_total_amount=$total_quantity; ?></b></td>
	            <td align="right"></td>
	            <td align="right"><b><? //echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? //echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>-->
	        </table>
             <table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	            <tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
                        <td width="30" style="font-size:20px;">&nbsp;</td>
	                    <td width="120" style="font-size:20px;">&nbsp;</td>
	                    <td width="120" style="font-size:20px;">&nbsp;</td>
	                    <td width="100" style="font-size:20px;">&nbsp;</td>
						<td width="100" style="font-size:20px;">&nbsp;</td>
	                    <td width="130" style="font-size:20px;">&nbsp;</td>
		                <td width="140" style="font-size:20px;">&nbsp;</td>
		                <td width="95" style="font-size:20px;">&nbsp;</td>
		                <td width="140" style="font-size:20px;">&nbsp;</td>	
		                <td width="100" style="font-size:20px;">&nbsp;</td>				
		                <td width="60" style="font-size:20px;" align="right"><b>Total</b></td>
	                    <td width="80" style="font-size:20px;" align="right"><b><? echo $format_total_amount=$total_quantity; ?></td>
		                <td width="80" style="font-size:20px;">&nbsp;</td>
		               <td width="100" style="font-size:20px;" align="right"><b><? echo $format_total_amount=number_format($total_ammount,4,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="10" align="left" style="font-size:20px;"><b>In Word: <? echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>
	        </table>
	        <?php echo signature_table(167, $data[0], "1200px");?>
		</div>
   	<?
	}
	exit();
}
 
 
if($action=="challan_print3") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
	//	if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		if($company_data[csf('country_id')]!=0)$country = $country_full_name[$company_data[csf("country_id")]].'.';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	?>
	   <style type="text/css">
 			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
		</style>
		<?
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
		$sql_mst =sql_select(" select id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	
		$i = 1;
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$total_ammount = 0; $total_quantity=0;
		$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
		 $sql = "select a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate as bill_actual_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id, d.style, e.item_group,e.buyer_po_no from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.id=b.mst_id and b.mst_id=$data[1] and c.id = b.production_dtls_id and c.break_down_details_id = d.id and d.mst_id = e.id and e.mst_id = c.received_id  and a.status_active=1  and b.status_active=1  and c.status_active=1  and d.status_active=1  and e.status_active=1 order by b.id ASC";
		 //echo $sql;
		$data_array=sql_select($sql);
		$order_arr=array();
		$order_number_po=''; $delevery_dtls_ids='';
		$order_number_sql=sql_select($sql);
		foreach ($order_number_sql as $row)
		{
			$order_arr[]="'".$row[csf("order_no")]."'";
			$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';
		}
		//unset($order_sql_res);
		$order_number_po=implode(",",array_unique($order_arr));
		//echo $delevery_dtls_ids.'==';
		$delevery_dtls_ids=chop($delevery_dtls_ids,',');
		//echo $delevery_dtls_ids;
		$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));

		$internalRef_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			//$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
			$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		}
		unset($po_sql_res);


		//echo $order_number_po;
		$buyer_po_arr=array(); $intRef_arr=array();
		$order_buyer_po='';
		
		$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,c.trims_del,c.delivery_date, d.trims_ref from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";


		//$order_sql ="select b.buyer_buyer,b.buyer_po_id,d.id from subcon_ord_mst a, subcon_ord_dtls b ,trims_job_card_mst c, trims_job_card_dtls d where a.id=b.mst_id and a.id=c.received_id and c.id=d.mst_id and a.entry_form='255' and b.order_no in ($order_number_po) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.buyer_buyer,b.buyer_po_id,d.id "; 
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$buyer_po_arr['buyer_buyer']=$row[csf("buyer_buyer")];
			//$piArray[$row['buyer_po_id']]['grouping']
			$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
			$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
			$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
		}

		//$challan_date_arr=return_library_array( "select delivery_date,trims_del from trims_delivery_mst",'trims_del','delivery_date');
		
		if($data[2]==1)
		{  
			$order_buyer_po=implode(",",array_unique($buyer_po_arr['buyer_buyer']));
			$buyer_po_ids=implode(",",array_unique($buyer_po_arr['buyer_po_id']));
			$buyer_name='';
			$order_id=array_unique(explode(",",$order_buyer_po));
			foreach($order_id as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
			}
			$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));

			/*$internalRef='';
			$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
			foreach($buyer_po_id as $val)
			{
				if($internalRef=="") $internalRef=$internalRef_arr[$val]['internalRef']; else $internalRef.=",".$internalRef_arr[$val]['internalRef'];
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));*/
		}
		else 
		{ 
			$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
		} 
	
	
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
	
	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
	    <div style="width:1700px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?>
	                	<span style="font-size:x-large; position: absolute; right: 25px; top: 17px;">
	                		<? echo $cid;?><sup><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo  'nd';} else{ echo 'rd';} ?></sup> Copy
	                	</span>
	                </strong>
	                </td>
	            </tr>
	            <tr>
					<td  colspan="5" align="center" style="font-size:25px;">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city,contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
								Head Office: House # <?php echo $result[csf('plot_no')]; ?>
								Road # <?php echo $result[csf('road_no')]; ?>
								Sector # <?php echo $result[csf('block_no')]; ?>
								<?php echo $result[csf('zip_code')]; ?>,
								<?php echo $country; ?>
								<br/>
								Factory Address : <? echo $result[csf('city')]; ?><br>
								Email : <? echo $result[csf('email')]; ?><br>
								Mobile : <? echo $result[csf('contact_no')]; ?><br>
								Vat No : <? echo $result[csf('vat_number')]; ?> 
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td style="font-size:25px;" align="center" colspan="5">
						<strong><? echo $data[3]; ?></strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td style="font-size:25px;" align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr style="font-size:25px">
	                <td valign="top" width="100" style="font-size:25px;" ><b>Party Name</b></td>
	                <td valign="top" width="150" style="font-size:25px;">: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                <td valign="top" width="250" style="font-size:25px;">&nbsp;</td>
	                <td valign="top" width="120" style="font-size:25px;"><b>Bill No.</b> </td>
	                <td valign="top" style="font-size:25px;">:<b> <? echo $data[5]; ?></b></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120" style="font-size:25px;">Address</td>
	                <td valign="top" style="font-size:25px;">: <? echo $party_location; ?></td>
	                <td valign="top" width="250" style="font-size:25px;">&nbsp;</td>
	                <td valign="top" width="100" style="font-size:25px;">Bill Date</td>
	                <td valign="top" width="150" style="font-size:25px;">: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            
	            <tr>
	            	<td valign="top" width="120" style="font-size:25px;">Buyer Name</td>
	                <td valign="top" style="font-size:25px;">: <? echo $buyer_name; ?></td>
	                <td valign="top" width="250" style="font-size:25px;">&nbsp;</td>
	                <td valign="top" width="100" style="font-size:25px;">Buyer PO No</td>
	                <td valign="top" width="150" style="font-size:25px;">: <? ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100" style="font-size:25px;">Bill Currency</td>
	                <td valign="top" width="150" style="font-size:25px;">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr style="font-size:25px">
		            	<th width="30" style="font-size:25px;">SL</th>
	                    <th width="120" style="font-size:25px;">Item Group</th>
	                    <th width="120" style="font-size:25px;">Section</th>
	                    <th width="100" style="font-size:25px;">Buyer PO</th>
						<th width="100" style="font-size:25px;">Style Ref.</th>
	                    <th width="130" style="font-size:25px;">Item Description</th>
		                <th width="140" style="font-size:25px;">Challan No</th>
		                <th width="95" style="font-size:25px;">Challan Date</th>
		                <th width="140" style="font-size:25px;">WO NO</th>	
		                <th width="100" style="font-size:25px;">Size</th>				
		                <th width="60" style="font-size:25px;">UOM</th>
	                    <th width="80" style="font-size:25px;">Bill Qnty</th>
		                <th width="80" style="font-size:25px;">Bill Rate</th>
		                <th width="100" style="font-size:25px;">Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}
					$i=1;

					foreach($data_array as $row)
					{
						$internalRef=''; $trims_ref='';
						$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
						foreach ($del_dtls_ids as  $value) {
							$internalRef.=$intRef_arr[$value]['internalRef'].',';
							$trims_ref.=$trims_ref_arr[$value]['trims_ref'].',';
						}
						$internalRef=implode(",",array_unique(explode(",",$internalRef)));
						$trims_ref=implode(",",array_unique(explode(",",$trims_ref)));
						?>
	                    <tr style="font-size:25px">
	                    <td style="font-size:25px;"><?php echo $i; ?></td>
	                    <td style="font-size:25px;"><?php echo $item_group_arr[$row[csf('item_group')]];  ?></td>
	                    <td style="font-size:25px;"><?php echo $trims_section[$row[csf('section')]];  ?></td>
	                    <td style="font-size:25px;"><?php echo $row[csf('buyer_po_no')];  ?></td>
						<td style="word-break: break-word;font-size:25px;"><? echo $row[csf('style')]; ?></td>
	                    <td  style="word-break: break-word;font-size:25px;" ><?php echo $row[csf('item_description')]; ?></td>
		                <td style="font-size:25px;"><?php echo $row[csf('challan_no')];  ?></td>
		                <td style="font-size:25px;"><?php echo change_date_format($challan_date_arr[$row[csf('challan_no')]]);  ?></td>
		                <td style="font-size:25px;"><?php echo $row[csf('order_no')];  ?></td>	
		                <td style="word-break: break-word;font-size:25px;"><?php echo $size_arr[$row[csf('size_id')]]; ?></td>				
		                <td style="font-size:25px;"><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
	                    <td style="font-size:25px;" align="right"><?php echo $row[csf('delevery_qty')];$total_quantity += $row[csf('delevery_qty')]; ?></td>
		                <td style="font-size:25px;" align="right" title="<? echo $row[csf('bill_actual_rate')]; ?>"><?php echo $row[csf('bill_actual_rate')];  ?></td>
		                <td style="font-size:25px;" align="right"><?php  $bill_ammount=$row[csf('delevery_qty')]*$row[csf('bill_actual_rate')];
		                echo number_format($bill_ammount,4); $total_ammount+= $bill_ammount; ?></td>
	                    </tr>
					<? 
					$i++;
	                } 
	                ?>
	            </tbody> 
	            <!--<tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:15px"> 
	            <? //if($data[2]==1) $colspan=10; else $colspan=9;?>
	            <td align="right" colspan="<? //echo $colspan; ?>" ><b>Total</b></td>
	            <td align="right"><b><? //echo $format_total_amount=$total_quantity; ?></b></td>
	            <td align="right"></td>
	            <td align="right"><b><? //echo $format_total_amount=number_format($total_ammount,2,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="12" align="left"><b>In Word: <? //echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>-->
	        </table>
             <table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	            <tfoot>
	            <tr bgcolor="#CCCCCC" style="font-size:25px"> 
                        <td width="30" style="font-size:25px;">&nbsp;</td>
	                    <td width="120" style="font-size:25px;">&nbsp;</td>
	                    <td width="120" style="font-size:25px;">&nbsp;</td>
	                    <td width="100" style="font-size:25px;">&nbsp;</td>
						<td width="100" style="font-size:25px;">&nbsp;</td>
	                    <td width="130" style="font-size:25px;">&nbsp;</td>
		                <td width="140" style="font-size:25px;">&nbsp;</td>
		                <td width="95" style="font-size:25px;">&nbsp;</td>
		                <td width="140" style="font-size:25px;">&nbsp;</td>	
		                <td width="100" style="font-size:25px;">&nbsp;</td>				
		                <td width="60" style="font-size:25px;" align="right"><b>Total</b></td>
	                    <td width="80" style="font-size:25px;" align="right"><b><? echo $format_total_amount=$total_quantity; ?></td>
		                <td width="80" style="font-size:25px;">&nbsp;</td>
		               <td width="100" style="font-size:25px;" align="right"><b><? echo $format_total_amount=number_format($total_ammount,4,'.',''); ?></b></td>
	            </tr>
				<tr>
					<td colspan="10" align="left" style="font-size:25px;"><b>In Word: <? echo  number_to_words(number_format($format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
				</tr>
	        </tfoot>
	        </table>
	        <?php echo signature_table(167, $data[0], "1700px");?>
		</div>
   	<?
	}
	exit();
}
 
if($action=="challan_print4") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_full_name[$company_data[csf("country_id")]].'.';else $country='';
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
	$sql_mst =sql_select(" SELECT id, bill_no_prefix, bill_no_prefix_num,trims_bill, entry_form, company_id, location_id, party_id, currency_id, within_group, bill_date, party_location,  order_id, received_id, job_id, quantity, challan_no, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, bill_no,  remarks, exchange_rate, bill_amount, up_charge, discount, net_bill_amount,upcharge_remarks, discount_remarks from trims_bill_mst where entry_form=276 and id=$data[1] and status_active=1  order by id desc");
	$inserted_by=$sql_mst[0][csf("inserted_by")];
	$i = 1;
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$total_ammount = 0; $total_quantity=0;
	$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
	$sql = "SELECT a.received_id,a.bill_date, b.id, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate as bill_actual_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id, d.style, e.item_group,e.buyer_po_no from trims_bill_mst a, trims_bill_dtls b,trims_delivery_dtls c, subcon_ord_breakdown d, subcon_ord_dtls e where a.id=b.mst_id and b.mst_id=$data[1] and c.id = b.production_dtls_id and c.break_down_details_id = d.id and d.mst_id = e.id and e.mst_id = c.received_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 order by b.id ASC";
	//echo $sql;
	$data_array=sql_select($sql);
	$order_arr=array();
	$order_number_po=''; $delevery_dtls_ids='';
	$order_number_sql=sql_select($sql);
	foreach ($order_number_sql as $row)
	{
		$order_arr[]="'".$row[csf("order_no")]."'";
		$delevery_dtls_ids .=$row[csf("production_dtls_id")].',';
	}
	//unset($order_sql_res);
	$order_number_po=implode(",",array_unique($order_arr));
	$delevery_dtls_ids=chop($delevery_dtls_ids,',');
	$delevery_dtls_ids=implode(",",array_unique(explode(",",$delevery_dtls_ids)));

	$internalRef_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$internalRef_arr[$row[csf("id")]]['internalRef'] =$row[csf("grouping")];
		$po_no_arr[$row[csf("id")]]['po_number'] =$row[csf("po_number")];
	}
	unset($po_sql_res);

	//echo $order_number_po;
	$buyer_po_arr=array(); $intRef_arr=array();
	$order_buyer_po='';
	
	$order_sql = "select a.id,b.buyer_po_id,b.buyer_buyer,c.trims_del,c.delivery_date, d.trims_ref from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c,subcon_ord_mst d where a.id in($delevery_dtls_ids) and b.order_no in ($order_number_po) and  a.receive_dtls_id=b.id and a.mst_id=c.id and b.mst_id=d.id and a.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo$order_sql ;
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("buyer_buyer")]]=$row[csf("buyer_buyer")];
		$buyer_po_id_arr[$row[csf("buyer_po_id")]]=$row[csf("buyer_po_id")];
		$intRef_arr[$row[csf("id")]]['internalRef'] =$internalRef_arr[$row[csf("buyer_po_id")]]['internalRef'];
		$trims_ref_arr[$row[csf("id")]]['trims_ref'] =$row[csf("trims_ref")];
		$challan_date_arr[$row[csf("trims_del")]]=$row[csf("delivery_date")];
	}

	if($data[2]==1)
	{  
		$order_buyer_po=array_unique($buyer_po_arr);
		$buyer_po_ids=array_unique($buyer_po_id_arr);
		$buyer_name='';$buyer_po_name='';
		foreach($order_buyer_po as $val)
		{
			if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}
		foreach($buyer_po_ids as $val)
		{
			if($buyer_po_name=="") $buyer_po_name=$po_no_arr[$val]['po_number']; else $buyer_po_name.=",".$po_no_arr[$val]['po_number'];
		}
	}
	else 
	{ 
		$buyer_name=$order_buyer_po=implode(",",array_unique($buyer_po_arr));  
	} 

	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
	}
	
	$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	?>
		<style type="text/css">
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
	 	</style>
	<?
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
	 	$total_quantity=$total_ammount=$format_total_amount=0;
		$k++;
		?>
	    <div style="width:1200px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="3" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	                <td colspan="5" align="center"  style="font-size:xx-large; position: relative;">
	                	<strong><? echo $company_arr[$data[0]]; ?>
	                	<span style="font-size:x-large; position: absolute; right: 25px; top: 17px;">
	                		<? echo $cid;?><sup><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo  'nd';} else{ echo 'rd';} ?></sup> Copy
	                	</span>
	                </strong>
	                </td>
	            </tr>
	            <tr>
					<td  colspan="5" align="center" >
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, city, zip_code, email, website, contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
								Head Office: House # <?php echo $result[csf('plot_no')]; ?>
								Road # <?php echo $result[csf('road_no')]; ?>
								Sector # <?php echo $result[csf('block_no')]; ?>
								<?php echo $result[csf('zip_code')]; ?>,
								<?php echo $country; ?>
								<br/>
								TEL: <? echo $result[csf('contact_no')]; ?>, 
								Email: <? echo $result[csf('email')]; ?>, 
								Website: <? echo $result[csf('website')]; ?> 
							<?
						}
						?> 
					</td>
				</tr>
				<tr>
	            	
	            	<td  align="center" colspan="5">
						<strong><? echo $data[3]; ?></strong>
	                </td>
	            </tr> 
	            <tr>
	            	<td colspan="2" rowspan="2">&nbsp;</td>
	            	<td  align="center" colspan="6">&nbsp; </td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr >
	                <td valign="top" width="120"  ><b>Party Name</b></td>
	                <td valign="top" width="200" >: <b><? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></b></td>
	                <td valign="top" width="250" >&nbsp;</td>
	                <td valign="top" width="120" ><b>Bill No.</b> </td>
	                <td valign="top" >:<b> <? echo $data[5]; ?></b></td>
	            </tr>
	            <tr>
	            	<td >Address</td>
	                <td >: <? echo $party_location; ?></td>
	                <td >&nbsp;</td>
	                <td >Bill Date</td>
	                <td >: <? echo change_date_format($sql_mst[0][csf("bill_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            
	            <tr>
	            	<td >Buyer Name</td>
	                <td >: <? echo $buyer_name; ?></td>
	                <td >&nbsp;</td>
	                <td >Buyer PO No</td>
	                <td >: <?echo $buyer_po_name; ?></td>
	            </tr>
	            <tr>
	            	<td >Remarks</td>
	                <td >: <? echo $data[7]; ?></td>
					<td >&nbsp;</td>
	            	<td >Bill Currency</td>
	                <td >: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
	            </tr>
	      	</table>
	         <br>
	      	<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1"  style="word-break:break-all;margin:0 auto;">
	      		<thead>
		            <tr >
		            	<th width="30" >SL</th>
	                    <th width="100" >Item Group</th>
	                    <th width="100" >Section</th>
						<th width="100" >Style Ref.</th>
	                    <th width="110" >Item Description</th>
		                <th width="110" >Challan No</th>
		                <th width="95" >Challan Date</th>	
		                <th width="110" >WO NO</th>	
						<th width="80" >Item Color</th>
		                <th width="50" >Size</th>				
		                <th width="50" >UOM</th>
	                    <th width="70" >Bill Qnty</th>
		                <th width="70" >Bill Rate</th>
		                <th >Bill Amount</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
					if($sql_mst[0][csf("currency_id")]==1)
					{
						$uom_unit="Taka";
	               		$uom_gm="Paisa";
					}
					else if($sql_mst[0][csf("currency_id")]==2)
					{
						$uom_unit="USD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==3)
					{
						$uom_unit="Euro";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==4)
					{
						$uom_unit="CHF";
	               		$uom_gm="centime/Rappen";
					}
					else if($sql_mst[0][csf("currency_id")]==5)
					{
						$uom_unit="SGD";
	               		$uom_gm="Cent";
					}
					else if($sql_mst[0][csf("currency_id")]==6)
					{
						$uom_unit="Pound";
	               		$uom_gm="Penny";
					}
					else if($sql_mst[0][csf("currency_id")]==7)
					{
						$uom_unit="YEN";
	               		$uom_gm="Sen";
					}

					$i=1;
					foreach($data_array as $row)
					{
						$internalRef=''; $trims_ref='';
						$del_dtls_ids=array_unique(explode(",",$row[csf('production_dtls_id')]));
						foreach ($del_dtls_ids as  $value) {
							$internalRef.=$intRef_arr[$value]['internalRef'].',';
							$trims_ref.=$trims_ref_arr[$value]['trims_ref'].',';
						}
						$internalRef=implode(",",array_unique(explode(",",$internalRef)));
						$trims_ref=implode(",",array_unique(explode(",",$trims_ref)));
						?>
	                    <tr >
							<td ><?php echo $i; ?></td>
							<td ><?php echo $item_group_arr[$row[csf('item_group')]];  ?></td>
							<td ><?php echo $trims_section[$row[csf('section')]];  ?></td>
							<!-- <td ><?php echo $row[csf('buyer_po_no')];  ?></td> -->
							<td style="word-break: break-word;"><? echo $row[csf('style')]; ?></td>
							<td  style="word-break: break-word;" ><?php echo $row[csf('item_description')]; ?></td>
							<td ><?php echo $row[csf('challan_no')];  ?></td>
							<td ><?php echo change_date_format($challan_date_arr[$row[csf('challan_no')]]);  ?></td>
							<td ><?php echo $row[csf('order_no')];  ?></td>	
							<td style="word-break: break-word;"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>				
							<td style="word-break: break-word;"><?php echo $size_arr[$row[csf('size_id')]]; ?></td>				
							<td ><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
							<td  align="right"><?php echo $row[csf('delevery_qty')];$total_quantity += $row[csf('delevery_qty')]; ?></td>
							<td  align="right" title="<? echo $row[csf('bill_actual_rate')]; ?>"><?php echo $row[csf('bill_actual_rate')];  ?></td>
							<td  align="right"><?php  $bill_ammount=$row[csf('delevery_qty')]*$row[csf('bill_actual_rate')];
		                echo number_format($bill_ammount,4); $total_ammount+= $bill_ammount; ?></td>
	                    </tr>
					<? 
					$i++;
	                } 
	                ?>
					<tr bgcolor="#CCCCCC" > 				
						<td colspan="11" align="right"><b>Total</b></td>
						<td align="right"><b><? echo $format_total_amount=$total_quantity; ?></td>
						<td  >&nbsp;</td>
						<td align="right"><b><? echo $format_total_amount=number_format($total_ammount,4,'.',''); ?></b></td>
					</tr>
					<tr > 				
						<td colspan="12">Upcharge Remarks: <? echo $sql_mst[0][csf("upcharge_remarks")]; ?></td>
						<td align="right">Upcharge</td>
						<td align="right"><b><? echo number_format($sql_mst[0][csf("up_charge")],2,'.',''); ?></b></td>
					</tr>
					<tr > 				
						<td colspan="12" >Discount Remarks: <? echo $sql_mst[0][csf("discount_remarks")]; ?></td>
						<td align="right">Discount</td>
						<td align="right"><b><? echo number_format($sql_mst[0][csf("discount")],2,'.',''); ?></b></td>
					</tr>
					<tr > 				
						<td colspan="13" align="right"><b>Net Total</b></td>
						<td align="right"><b><? echo $net_format_total_amount=number_format($format_total_amount+$sql_mst[0][csf("up_charge")]-$sql_mst[0][csf("discount")],4,'.',''); ?></b></td>
					</tr>
					<tr bgcolor="#CCCCCC" > 				
						<td colspan="13" align="right"><b>Exchange Rate BDT: <?echo $sql_mst[0][csf("exchange_rate")]; ?></b></td>
						<td align="right"><b><? echo number_format($net_format_total_amount*$sql_mst[0][csf("exchange_rate")],4,'.',''); ?></b></td>
					</tr>
					<tr>
						<td rowspan="2" colspan="2" ><b>Amount <br>(In Word)</br></td>
						<td colspan="12" ><b>In Word: <? echo  number_to_words(number_format($net_format_total_amount,2, '.', ''),$uom_unit,$uom_gm); ?></b></td>
					</tr>
					<tr>
						<td colspan="12" ><b>BDT (Taka): <? echo  number_to_words(number_format($net_format_total_amount*$sql_mst[0][csf("exchange_rate")],2, '.', ''),"Taka","Paisa"); ?></b></td>
					</tr>
				</tbody> 
	        </table>
			<br>
			<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
				<tbody>
					<tr>
						<td align="center">No Of Delivery Challan: <?=count(explode(',',$data[8]));?></td>
					</tr>
				</tbody>
			</table>
			<br>
	        <?php 
				echo get_spacial_instruction($data[1],"1200px",276);
				echo signature_table(167, $data[0], "1200px",'',70,$user_lib_name[$inserted_by]);
			?>
		</div>
   	<?
	}
	exit();
}
 
//if($action=="check_conversion_rate")
//{
	//$data=explode("**",$data);
	
	/*if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}*/
	//$conversion_date=date("Y/m/d");
	//$exchange_rate=set_conversion_rate( $data, $conversion_date );
	//echo $exchange_rate;
	//exit();	
//}

if($action=="check_uom")
{
	$uom=return_field_value( "order_uom","lib_item_group","id='$data'");
	echo $uom;
	exit();	
}	
?>