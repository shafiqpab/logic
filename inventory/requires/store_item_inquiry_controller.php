<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:630px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'store_item_inquiry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:05px" id="search_div"></div>

		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	
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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,60,80","620","270",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "",'','0,0,0,0,0','',0) ;
   exit(); 
} 

if($action=="po_no_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$selected=1; $disable=0; $txtdisable=""; $caption="Order";
	if($txt_job_no!="")
	{
		$selected=3;
		$disable=1;
		$txtdisable="disabled";
		$caption="Job";
	}
							
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:675px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter <? echo $caption; ?> No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	<? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$disable );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
							$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"Internal Ref",5=>"File No");
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";						
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,$disable );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no; ?>" <? echo $txtdisable; ?> />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_order_no_search_list_view', 'search_div', 'store_item_inquiry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>

		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
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
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 
	else if($search_by==4) 
		$search_field="b.grouping"; 
	else if($search_by==5) 
		$search_field="b.file_no"; 			
	else 
		$search_field="a.job_no";
		
	if(trim($data[3])!="")
	{
		$search_field_cond=" and $search_field like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$arr=array(0=>$buyer_arr);
		
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_field_cond $buyer_id_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Buyer,Year,Job No,Style Ref. No, Po No, Internal Ref, File No, Shipment Date", "60,50,60,110,100,70,70","670","260",0, $sql , "js_set_value", "po_number", "", 1, "buyer_name,0,0,0,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,file_no,pub_shipment_date", "",'','0,0,0,0,0,0,0,3','',0) ;
	
   exit(); 
}

if($action=="program_no_popup")
{
	echo load_html_head_contents("Program Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$selected=1; $disable=0; $txtdisable=""; $caption="Program";
	if($txt_order_no!="")
	{
		$selected=2;
		$disable=1;
		$txtdisable="disabled";
		$caption="Order";
	}
	
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:675px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter <? echo $caption; ?> No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	<? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",$disable );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
							$search_by_arr=array(1=>"Program No",2=>"PO No",3=>"Job No",4=>"Booking No",5=>"Internal Ref",6=>"File No");
							$dd="change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../') ";						
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,$disable );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_order_no; ?>" <? echo $txtdisable; ?> />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_program_no_search_list_view', 'search_div', 'store_item_inquiry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>

		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_program_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	
	$po_array=array();
	$po_sql=sql_select("select id, job_no_mst, po_number, grouping, file_no from wo_po_break_down");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')]; 
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')]; 
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_id=$data[1]";
	}
	
	if(trim($data[3])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and b.id='".trim($data[3])."'";
		}
		else if($search_by==2 || $search_by==3 || $search_by==5 || $search_by==6)	
		{
			if($search_by==2) $search_field='po_number'; 
			else if($search_by==5) $search_field='grouping'; 
			else if($search_by==6) $search_field='file_no'; 
			else $search_field='job_no_mst';
			
			if($db_type==0)
			{
				$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","$search_field like '$search_string' and status_active=1 and is_deleted=0","po_id");
			}
			else
			{
				$po_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","$search_field like '$search_string' and status_active=1 and is_deleted=0","po_id");
			}
			$search_field_cond="and c.po_id in(".$po_id.")";
		}
		else if($search_by==4)
		{
			$search_field_cond="and a.booking_no like '$search_string'";
		}
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0)
	{
		$sql = "select a.id, a.booking_no, a.buyer_id, b.id as knit_id, group_concat(c.po_id) as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond group by a.id, a.booking_no, a.buyer_id, b.id order by b.id"; 
	}
	else
	{
		$sql = "select a.id, a.booking_no, a.buyer_id, b.id as knit_id, LISTAGG(c.po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond group by a.id, a.booking_no, a.buyer_id, b.id order by b.id";	
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="55">Plan Id</th>
			<th width="60">Program No</th>
			<th width="110">Booking No</th>
			<th width="60">Buyer</th>
			<th width="100">PO No</th>
			<th width="80">Job No</th>
			<th width="70">Internal Ref</th>
			<th>File No</th>
		</thead>
	</table>
	<div style="width:670px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 
					
				$po_id=array_unique(explode(",",$row[csf('po_id')]));
				$po_no=''; $job_no=''; $internal_ref=''; $file_nos='';
				foreach($po_id as $val)
				{
					if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];
					if($job_no=='') $job_no=$po_array[$val]['job_no'];
					if($internal_ref=="") $internal_ref=$po_array[$val]['grouping']; else $internal_ref.=",".$po_array[$val]['grouping'];
					if($file_nos=="") $file_nos=$po_array[$val]['file_no']; else $file_nos.=",".$po_array[$val]['file_no'];
				}
				
				$internal_ref=implode(",",array_unique(explode(",",$internal_ref)));
				$file_nos=implode(",",array_unique(explode(",",$file_nos)));
				
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('knit_id')]; ?>)"> 
					<td width="40"><? echo $i; ?></td>
					<td width="55" align="center"><? echo $row[csf('id')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('knit_id')]; ?></td>
					<td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>               
					<td width="60"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
					<td width="100"><p><? echo $po_no; ?></p></td>
					<td width="80"><p><? echo $job_no; ?></p></td>
					<td width="70"><p><? echo $internal_ref; ?>&nbsp;</p></td> 
					<td><p><? echo $file_nos; ?>&nbsp;</p></td>
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

if($action=="item_desc_popup")
{
	echo load_html_head_contents("Item Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$selected=1; $disable=0; $txtdisable=""; $caption="Product Id"; $booking_without_order=0;
	if($txt_order_no!="")
	{
		$selected=3;
		$disable=1;
		$txtdisable="disabled";
		$caption="Order No";
	}
	
	if($txt_order_no=="" && $txt_booking_no!="")
	{
		$booking_without_order=1;
		$caption="Booking No";
		$selected=3;
		$disable=1;
		$txtdisable="disabled";
	}
							
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:475px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter <? echo $caption; ?></th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    	<?
							if($item_category_id==1) 
							{
								$search_by_arr=array(1=>"Product Id",2=>"Product Details",3=>"Lot No");
							}
							else if(($item_category_id==2 || $item_category_id==3 || $item_category_id==4 || $item_category_id==13 || $item_category_id==14) && $booking_without_order==0)
							{
								$search_by_arr=array(1=>"Product Id",2=>"Product Details",3=>"Order No");
							}
							else if(($item_category_id==2 || $item_category_id==3 || $item_category_id==4 || $item_category_id==13 || $item_category_id==14) && $booking_without_order==1)
							{
								$search_by_arr=array(1=>"Product Id",2=>"Product Details",3=>"Booking No");
							}
							else 
							{
								$search_by_arr=array(1=>"Product Id",2=>"Product Details");
							}
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";						
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,$disable );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? if($booking_without_order==1) echo $txt_booking_no; else echo $txt_order_no; ?>" <? echo $txtdisable; ?> />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+'<? echo $item_category_id; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $booking_without_order; ?>'+'**'+'<? echo $cbo_year; ?>', 'create_product_search_list_view', 'search_div', 'store_item_inquiry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_product_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$item_category_id=$data[1];
	
	$search_by=$data[2];
	$search_string=trim($data[3]);
	$booking_without_order=$data[4];
	$year_id=$data[5];

	if($booking_without_order==1)
	{
		if($db_type==0)
		{
			if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
		}
		else if($db_type==2)
		{
			if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		}
		
		$sql = "select id, booking_no, booking_no_prefix_num, booking_date, buyer_id, delivery_date, $year_cond FROM wo_non_ord_samp_booking_mst WHERE company_id=$company_id and status_active =1 and is_deleted=0 and item_category in ($item_category_id) $search_field_cond $year_search_cond order by id"; 
		
		$search_field_cond=" and a.booking_no_prefix_num like '".$search_string."'";
		if($search_by==3 && ($item_category_id==2 || $item_category_id==3 || $item_category_id==13 || $item_category_id==14))
		{
			if($item_category_id==2 || $item_category_id==13) 
			{
				$item_category_ids="2,13";
				if($item_category_id==13) 
				{
					$entryForm="2,22";
				}
				else if($item_category_id==2) 
				{
					$entryForm="7,37";
				}
			}
			else if($item_category_id==3 || $item_category_id==14) 
			{
				if($item_category_id==3) 
				{
					$entryFormR="17";
				}
				else 
				{
					$entryFormR="23";
				}
				
				$item_category_ids="3,14";
			}
			
			$sql= "select c.id, c.product_name_details from wo_non_ord_samp_booking_mst a, inv_receive_master b, pro_grey_prod_entry_dtls d, product_details_master c where a.id=b.booking_id and b.id=d.mst_id and d.prod_id=c.id and b.booking_without_order=1 and b.entry_form in($entryForm) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and a.item_category in($item_category_ids) and c.item_category_id=$item_category_id $search_field_cond $year_search_cond group by c.id, c.product_name_details order by c.id";
		}
		else
		{
			$sql= "select c.id, c.product_name_details from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_trims_entry_dtls d, product_details_master c where a.id=b.booking_id and b.id=d.mst_id and d.prod_id=c.id and b.booking_without_order=1 and b.entry_form in(24) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and a.item_category=$item_category_id and c.item_category_id=$item_category_id $search_field_cond $year_search_cond group by c.id, c.product_name_details order by c.id";
		}
	}
	else
	{
		if(trim($data[3])!="")
		{
			if($search_by==1) 
				$search_field_cond=" and a.id=$search_string"; 
			else if($search_by==2) 
				$search_field_cond=" and a.product_name_details like '%".$search_string."%'"; 
				
			if($item_category_id==1 && $search_by==3)
			{
				$search_field_cond=" and a.lot like '".$search_string."%'";
			}	
				
			if($search_by==3 && ($item_category_id==2 || $item_category_id==3 || $item_category_id==4 || $item_category_id==13 || $item_category_id==14))
			{
				$search_field_cond=" and c.po_number like '".$search_string."%'";
				$sql= "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b, wo_po_break_down c where a.id=b.prod_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.item_category_id=$item_category_id $search_field_cond group by a.id, a.product_name_details order by a.id";
			}
			else
			{
				$sql= "select a.id, a.product_name_details, a.lot from product_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.item_category_id=$item_category_id $search_field_cond order by a.id";
			}
		}
		else
		{
			$sql= "select id, product_name_details, lot from product_details_master where status_active=1 and is_deleted=0 and company_id=$company_id and item_category_id=$item_category_id order by id";
		}
	}
	
	if($item_category_id==1)
	{
		echo create_list_view("tbl_list_search", "Product Id,Lot No,Product Details", "80,120","470","260",0, $sql , "js_set_value", "id", "", 1, "0,0,0", $arr , "id,lot,product_name_details", "",'','0,0,0','',0) ;
	}
	else
	{
		echo create_list_view("tbl_list_search", "Product Id,Product Details", "80","470","260",0, $sql , "js_set_value", "id", "", 1, "0,0", $arr , "id,product_name_details", "",'','0,0','',0) ;

	}
	
   exit(); 
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:475px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Booking No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
							<?
                                $search_by_arr=array(1=>"Booking No");
                                $dd="change_search_event(this.value, '0', '0', '../../') ";						
                                echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,$disable );
                            ?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_order_no; ?>" <? echo $txtdisable; ?> />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+'<? echo $item_category_id; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'create_booking_search_list_view', 'search_div', 'store_item_inquiry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_booking_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$item_category_id=$data[1];
	
	$search_by=$data[2];
	$search_string="%".trim($data[3]);
	$year_id=$data[4];
	
	$year_cond=""; $year_search_cond="";
	if($db_type==0) 
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond="YEAR(insert_date) as year"; 
	}
	else if($db_type==2) 
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond="to_char(insert_date,'YYYY') as year";
	}
	
	if(trim($data[3])!="")
	{
		$search_field_cond="and booking_no like '$search_string'";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$arr=array(2=>$buyer_arr);
	
	if($item_category_id==2 || $item_category_id==13) $item_category_id="2,13";
	else if($item_category_id==3 || $item_category_id==14) $item_category_id="3,14";
			
	$sql = "select id, booking_no, booking_no_prefix_num, booking_date, buyer_id, delivery_date, $year_cond FROM wo_non_ord_samp_booking_mst WHERE company_id=$company_id and status_active =1 and is_deleted=0 and item_category in ($item_category_id) $search_field_cond $year_search_cond order by id"; 
		
	echo create_list_view("tbl_list_search", "Booking No,Year,Buyer,Booking Date,Delivary date", "60,50,60,80","470","260",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,buyer_id,0,0", $arr , "booking_no_prefix_num,year,buyer_id,booking_date,delivery_date", "",'','0,0,0,3,3','',0) ;
	
   exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_id);
	$item_category_id=str_replace("'","",$cbo_item_category_id);
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	if($item_category_id==2 || $item_category_id==3 || $item_category_id==13 || $item_category_id==14 || $item_category_id==4)
	{
		$item_category_id_book=0;
		if($item_category_id==13) 
		{
			$entryFormR="2,22,51";
			$entryFormI="16,45";
			$entryFormT="13,80,81";
			$item_category_id_book="2,13";
		}
		else if($item_category_id==2) 
		{
			$entryFormR="7,37,52";
			$entryFormI="18,46";
			$entryFormT="15";
			$item_category_id_book="2,13";
		}
		else if($item_category_id==4) 
		{
			$entryFormR="24,73";
			$entryFormI="25,49";
			$entryFormT="78";
			$item_category_id_book="4";
		}
		else if($item_category_id==3) 
		{
			$entryFormR="17";
			$entryFormI="19";
			$entryFormT="0";
			$item_category_id_book="3,14";
		}
		else if($item_category_id==14) 
		{
			$entryFormR="23";
			$entryFormI="50";
			$entryFormT="0";
			$item_category_id_book="3,14";
		}
		
		if(str_replace("'","",trim($txt_order_no))!="")
		{
			$job_no=trim(str_replace("'","",$txt_job_no));
			if($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num=$job_no";
			$year_id=str_replace("'","",$cbo_year);

			$year_cond="";
			if($year_id!=0) 
			{
				if($db_type==0)
				{
					$year_cond=" and year(b.insert_date)=$year_id";
				}
				else if($db_type==2)
				{
					$year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id";
				}
			}
			
			$sql_po="select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$company_name and b.status_active=1 and b.is_deleted=0 and a.po_number=".trim($txt_order_no)." $year_cond $job_no_cond";
			$po_data=sql_select($sql_po);
			$buyer_id=$po_data[0][csf('buyer_name')];
			$po_id=$po_data[0][csf('id')];
			$grouping=$po_data[0][csf('grouping')];
			$file_no=$po_data[0][csf('file_no')];
			
			if($po_id=="") $po_id=0;
			
			$sql_recv="select r.recv_number, r.receive_date, a.rack, a.self, b.trans_type, b.quantity as qnty from inv_receive_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and b.entry_form in($entryFormR) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(1,4) and b.trans_type in(1,4) and a.prod_id=$txt_product_id and b.prod_id=$txt_product_id order by b.trans_type";
			
			$sql_iss="select r.issue_number, r.issue_date, a.rack, a.self, b.trans_type, b.quantity as qnty from inv_issue_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and b.entry_form in($entryFormI) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(2,3) and b.trans_type in(2,3) and a.prod_id=$txt_product_id and b.prod_id=$txt_product_id order by b.trans_type";
			
			$sql_trans="select r.transfer_system_id, r.transfer_date, a.rack, a.self, b.trans_type, b.quantity as qnty from inv_item_transfer_mst r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and b.entry_form in($entryFormT) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and r.transfer_criteria in(4,6,7) and a.transaction_type in(5,6) and b.trans_type in(5,6) and a.prod_id=$txt_product_id and b.prod_id=$txt_product_id order by b.trans_type";
			
			if(str_replace("'","",trim($txt_progarm_no))!="" && $item_category_id==13)
			{
				if($db_type==0)
				{
					$recv_id=return_field_value("group_concat(id) as id","inv_receive_master","booking_without_order=0 and booking_id=".trim($txt_progarm_no)." and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
				}
				else
				{
					$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=0 and booking_id=".trim($txt_progarm_no)." and status_active=1 and is_deleted=0 and entry_form in(2) and receive_basis=2","id");
				}
				
				if($recv_id=="") $recv_id=0;
				$sql_recvP="select r.recv_number, r.receive_date, b.trans_type, b.quantity as qnty from inv_receive_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in(2) and b.entry_form in(2) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(1) and b.trans_type in(1) and a.prod_id=$txt_product_id and r.booking_id=".trim($txt_progarm_no)." and b.prod_id=$txt_product_id and r.receive_basis=2
							union all
							select r.recv_number, r.receive_date, b.trans_type, b.quantity as qnty from inv_receive_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in(22) and b.entry_form in(22) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(1) and b.trans_type in(1) and a.prod_id=$txt_product_id and r.booking_id in($recv_id) and b.prod_id=$txt_product_id and r.receive_basis=9 
							union all
							select r.recv_number, r.receive_date, b.trans_type, b.quantity as qnty from inv_receive_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in(51) and b.entry_form in(51) and r.company_id=$company_name and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(4) and b.trans_type in(4) and a.prod_id=$txt_product_id and r.booking_id=".trim($txt_progarm_no)." and b.prod_id=$txt_product_id and r.receive_basis=3 order by trans_type";
				$resultProg=sql_select($sql_recvP);
				
				$sql_issProg="select r.issue_number, r.issue_date, a.rack, a.self, b.trans_type, b.quantity as qnty from inv_issue_master r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in(16) and b.entry_form in(16) and r.company_id=$company_name and a.requisition_no=".trim($txt_progarm_no)." and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and a.transaction_type in(2) and b.trans_type in(2) and a.prod_id=$txt_product_id and b.prod_id=$txt_product_id 							
							union all
							select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a, order_wise_pro_details b, inv_receive_master d where r.id=a.mst_id and a.id=b.trans_id and r.received_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in(45) and r.company_id=$company_name and b.po_breakdown_id=$po_id and ((d.booking_id=".trim($txt_progarm_no)." and d.receive_basis=2) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and a.item_category=$item_category_id and a.transaction_type in(3) and a.prod_id=$txt_product_id order by trans_type";
				$resultIssProg=sql_select($sql_issProg);
				
				$sql_transProg="select r.transfer_system_id, r.transfer_date, a.rack, a.self, b.trans_type, b.quantity as qnty from inv_item_transfer_mst r, inv_transaction a, order_wise_pro_details b where r.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and r.item_category=$item_category_id and b.entry_form in(13) and r.company_id=$company_name and a.program_no=".trim($txt_progarm_no)." and b.po_breakdown_id=$po_id and a.item_category=$item_category_id and r.transfer_criteria in(4) and a.transaction_type in(5,6) and b.trans_type in(5,6) and a.prod_id=$txt_product_id and b.prod_id=$txt_product_id order by b.trans_type";
				$resultTransProg=sql_select($sql_transProg);
			}
		}
		else 
		{
			$year_id=str_replace("'","",$cbo_year);
			$year_cond="";
			if($year_id!=0) 
			{
				if($db_type==0)
				{
					$year_cond=" and year(insert_date)=$year_id";
				}
				else if($db_type==2)
				{
					$year_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id";
				}
			}
			
			$txt_booking=str_replace("'","",trim($txt_booking_no));
			$booking_data=sql_select("select id, booking_no, buyer_id from wo_non_ord_samp_booking_mst where booking_no_prefix_num='$txt_booking' and company_id=$company_name and item_category in($item_category_id_book) $year_cond");
			$buyer_id=$booking_data[0][csf('buyer_id')];
			$booking_id=$booking_data[0][csf('id')];
			$booking_no=$booking_data[0][csf('booking_no')];
			
			if($item_category_id==13)
			{
				$recv_id='';
				if($db_type==0)
				{
					$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
				}
				else
				{
					$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
				}
				
				if($recv_id=="") $recv_id=0;
				$sql_recv="select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and a.transaction_type in(1,4) and a.prod_id=$txt_product_id and r.receive_basis!=9 
					union all
					select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.receive_basis=9 and r.entry_form in($entryFormR) and r.company_id=$company_name and r.booking_id in($recv_id) and a.item_category=$item_category_id and a.transaction_type in(1) and a.prod_id=$txt_product_id order by trans_type";
				
				$sql_iss="select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and r.issue_purpose in(3,8,26,29,30,31) and r.issue_basis=1 and a.transaction_type in(2) and a.prod_id=$txt_product_id 
					union all
					select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a, inv_receive_master d where r.id=a.mst_id and r.received_id=d.id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and a.item_category=$item_category_id and a.transaction_type in(3) and a.prod_id=$txt_product_id order by trans_type";
				
				$sql_trans="select r.transfer_system_id, r.transfer_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_item_transfer_mst r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.company_id=$company_name and a.item_category=$item_category_id and r.transfer_criteria in(7) and a.transaction_type in(6) and a.prod_id=$txt_product_id
					union all
					select r.transfer_system_id, r.transfer_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_item_transfer_mst r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.company_id=$company_name and a.item_category=$item_category_id and r.transfer_criteria in(6) and a.transaction_type in(5) and a.prod_id=$txt_product_id order by trans_type";
			}
			else if($item_category_id==14)
			{
				$recv_id='';
				if($db_type==0)
				{
					$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
				}
				else
				{
					$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
				}
				
				if($recv_id=="") $recv_id=0;
				$sql_recv="select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and a.transaction_type in(1,4) and a.prod_id=$txt_product_id and r.receive_basis!=9 
					union all
					select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.receive_basis=9 and r.entry_form in($entryFormR) and r.company_id=$company_name and r.booking_id in($recv_id) and a.item_category=$item_category_id and a.transaction_type in(1) and a.prod_id=$txt_product_id order by trans_type
				";
				
				$sql_iss="select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and r.issue_purpose in(3,8,26,29,30,31) and r.issue_basis=1 and a.transaction_type in(2) and a.prod_id=$txt_product_id 
					union all
					select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a, inv_receive_master d where r.id=a.mst_id and r.received_id=d.id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and a.item_category=$item_category_id and a.transaction_type in(3) and a.prod_id=$txt_product_id order by trans_type";
			}
			else if($item_category_id==2)
			{
				$batch_id=''; $recv_id='';
				if($db_type==0)
				{
					$batch_id=return_field_value("group_concat(distinct(id)) as id","pro_batch_create_mst","booking_without_order=1 and booking_no_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(0)","id");
				}
				else
				{
					$batch_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","pro_batch_create_mst","booking_without_order=1 and booking_no_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(0)","id");
				}
				if($batch_id=="") $batch_id=0;
				
				$sql_recv="select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and a.pi_wo_batch_no in($batch_id) and a.item_category=$item_category_id and a.transaction_type in(1) and a.prod_id=$txt_product_id
						union all
						select r.recv_number, r.receive_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and a.batch_id_from_fissuertn in($batch_id) and a.item_category=$item_category_id and a.transaction_type in(4) and a.prod_id=$txt_product_id order by trans_type";
				
				$sql_iss="select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and a.pi_wo_batch_no in($batch_id) and a.item_category=$item_category_id and r.issue_purpose in(3,8,26,29,30,31) and a.transaction_type in(2,3) and a.prod_id=$txt_product_id
						union all
						select r.issue_number, r.issue_date, a.rack, a.self, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and a.batch_id_from_fissuertn in($batch_id) and a.item_category=$item_category_id and a.transaction_type in(3) and a.prod_id=$txt_product_id order by  trans_type";
			}
			else if($item_category_id==4)
			{
				$sql_recv="select r.recv_number, r.receive_date, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and r.receive_basis=2 and r.booking_without_order=1 and a.transaction_type in(1) and a.prod_id=$txt_product_id 
					union all
					select r.recv_number, r.receive_date, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_receive_master r, inv_transaction a, inv_issue_master d where r.id=a.mst_id and r.issue_id=d.id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormR) and r.company_id=$company_name and d.booking_no='$booking_no' and d.issue_basis=2 and a.item_category=$item_category_id and a.transaction_type in(4) and a.prod_id=$txt_product_id order by trans_type
				";
				
				$sql_iss="select r.issue_number, r.issue_date, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a where r.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and r.booking_no='$booking_no' and a.item_category=$item_category_id and r.issue_basis=2 and a.transaction_type in(2) and a.prod_id=$txt_product_id 
					union all
					select r.issue_number, r.issue_date, a.transaction_type as trans_type, a.cons_quantity as qnty from inv_issue_master r, inv_transaction a, inv_receive_master d where r.id=a.mst_id and r.received_id=d.id and a.status_active=1 and a.is_deleted=0 and r.item_category=$item_category_id and r.entry_form in($entryFormI) and r.company_id=$company_name and d.receive_basis=2 and d.booking_no='$booking_no' and d.booking_without_order=1 and a.item_category=$item_category_id and a.transaction_type in(3) and a.prod_id=$txt_product_id order by trans_type";
			}
		}
		
		$buyer_name=return_field_value("buyer_name","lib_buyer","id='$buyer_id'");//echo $sql_recv;die;
		$resultRecv=sql_select($sql_recv);
		$resultIss=sql_select($sql_iss);
		$resultTrans=sql_select($sql_trans);
	}
	
	$sql="select
			sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as rcv_qty,
			sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) as iss_qty, 
			sum(case when a.transaction_type=3 then a.cons_quantity else 0 end) as rcv_ret_qty,
			sum(case when a.transaction_type=4 then a.cons_quantity else 0 end) as iss_ret_qty
			from inv_transaction a where a.transaction_type in (1,2,3,4) and a.item_category=$item_category_id and a.status_active=1 and a.is_deleted=0 and a.prod_id=$txt_product_id";
	$dataArray=sql_select($sql);
	$productData=sql_select("select product_name_details, current_stock, supplier_id from product_details_master where id=$txt_product_id");
	
	$sql_transfer="select
					sum(case when a.transaction_type=6 then a.cons_quantity else 0 end) as transfer_out_qty,
					sum(case when a.transaction_type=5 then a.cons_quantity else 0 end) as transfer_in_qty 
					from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=$item_category_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.transfer_criteria=1 and c.is_deleted=0 and a.prod_id=$txt_product_id";
	$transferData = sql_select($sql_transfer);
	
	$stock_qnty=($dataArray[0][csf('rcv_qty')]+$dataArray[0][csf('iss_ret_qty')]+$transferData[0][csf('transfer_in_qty')])-($dataArray[0][csf('iss_qty')]+$dataArray[0][csf('rcv_ret_qty')]+$transferData[0][csf('transfer_out_qty')]);
	
	if(number_format($stock_qnty,2,'','.')==number_format($productData[0][csf('current_stock')],2,'','.')) {$tdColor="";} else {$tdColor="red";} 
	
	?>
    <form name="storeItemInquiry_2" id="storeItemInquiry_2">
        <fieldset style="width:1000px;">
        	<table width="700" style="margin-bottom:10px">
            	<tr>
                	<td width="350"><b>Item Description:</b> <? echo $productData[0][csf('product_name_details')]; ?>;</td>
                    <td width="200"><b>Global Stock:</b> <font color="<? echo $tdColor; ?>"><? echo number_format($productData[0][csf('current_stock')],2); ?></font></td>
                    <td><input type="button" name="search" id="search" value="Click For Synchronize" onClick="synchronize_stock(<? echo $txt_product_id; ?>)" style="width:140px" class="formbutton" /></td>
                </tr>
            </table>
            <b>Product ID Level Transaction (Product ID : <? echo str_replace("'","",$txt_product_id); ?>)</b>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" style="margin-bottom:10px">
                <thead>
                	<? if($item_category_id==1) { echo '<th width="160">Supplier</th>'; } ?>
                    <th width="100">Receive Qty.</th>
                    <th width="100">Issue Return Qty.</th>
                    <th width="100">Transfer In (Inter Company) Qty.</th>
                    <th width="100">Issue Qty.</th>
                    <th width="110">Receive Return Qty.</th>
                    <th width="100">Transfer Out (Inter Company) Qty.</th>
                    <th>Stock Qty.</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <? if($item_category_id==1) { echo '<td>'.$supplier_arr[$productData[0][csf('supplier_id')]].'</td>'; } ?>
                    <td align="right" style="padding-right:3px"><? echo number_format($dataArray[0][csf('rcv_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($dataArray[0][csf('iss_ret_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($transferData[0][csf('transfer_in_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($dataArray[0][csf('iss_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($dataArray[0][csf('rcv_ret_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($transferData[0][csf('transfer_out_qty')],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($stock_qnty,2); ?></td>
                </tr>
            </table>
            <? 
			if($item_category_id==2 || $item_category_id==3 || $item_category_id==13 || $item_category_id==14 || $item_category_id==4)
			{
				$i=1;
				if(str_replace("'","",trim($txt_order_no))!="")
				{
				?>
					<b>Order Level Transaction (Order No : <? echo $po_data[0][csf('po_number')];?>; Job No: <? echo $po_data[0][csf('job_no')]; ?>; Buyer: <? echo $buyer_name; ?>; Internal Ref: <? echo $grouping; ?>; File No: <? echo $file_no; ?>)</b>
				<?		
				}
				else
				{
				?>
					<b>Without Order Level Transaction (Booking No : <? echo $txt_booking; ?>; Buyer: <? echo $buyer_name; ?>)</b>
				<?
				}
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" style="margin-bottom:10px">
					<thead>
						<th width="30">SL</th>
						<th width="120">Transaction Ref</th>
						<th width="80">Date</th>
						<th width="100">Receive Qty.</th>
						<th width="100">Issue Return Qty.</th>
						<th width="100">Transfer In Qty.</th>
						<th width="100">Issue Qty.</th>
						<th width="110">Receive Return Qty.</th>
						<th width="100">Transfer Out Qty.</th>
						<th>Stock Qty.</th>
					</thead>
					<?
					$stockQtyPo=0;
					foreach($resultRecv as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$stockQtyPo+=$row[csf('qnty')];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('recv_number')]; ?>&nbsp;</td>
							<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==1) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==4) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? echo number_format($stockQtyPo,2); ?></td>
						</tr>
					<?
						$i++;
					}
					
					foreach($resultIss as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$stockQtyPo-=$row[csf('qnty')];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('issue_number')]; ?>&nbsp;</td>
							<td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==2) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==3) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? echo number_format($stockQtyPo,2); ?></td>
						</tr>
					<?
						$i++;
					}
					
					foreach($resultTrans as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if($row[csf('trans_type')]==5)
						{
							$stockQtyPo+=$row[csf('qnty')];
						}
						else
						{
							$stockQtyPo-=$row[csf('qnty')];
						}
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</td>
							<td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==5) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==6) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px"><? echo number_format($stockQtyPo,2); ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
            <?
			}
			if($item_category_id==2 || $item_category_id==13)
			{
			?>
                <b>Rack and Shelf Level Transaction</b>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" style="margin-bottom:10px">
                    <thead>
                        <th width="30">SL</th>
                        <th width="115">Transaction Ref</th>
                        <th width="75">Date</th>
                        <th width="65">Rack</th>
                        <th width="60">Shelf</th>
                        <th width="80">Receive Qty.</th>
                        <th width="95">Issue Return Qty.</th>
                        <th width="90">Transfer In Qty.</th>
                        <th width="80">Issue Qty.</th>
                        <th width="110">Receive Return Qty.</th>
                        <th width="95">Transfer Out Qty.</th>
                        <th>Stock Qty.</th>
                    </thead>
                    <?
                    $stockQtyRo=0; $i=1;
                    foreach($resultRecv as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $stockQtyRo+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2<? echo $i;?>','<? echo $bgcolor;?>')" id="tr2<? echo $i;?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?>&nbsp;</td>
                            <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td><? echo $row[csf('rack')]; ?>&nbsp;</td>
                            <td><? echo $row[csf('self')]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==1) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==4) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? echo number_format($stockQtyRo,2); ?></td>
                        </tr>
                    <?
                        $i++;
                    }
                    
                    foreach($resultIss as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $stockQtyRo-=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2<? echo $i;?>','<? echo $bgcolor;?>')" id="tr2<? echo $i;?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?>&nbsp;</td>
                            <td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td><? echo $row[csf('rack')]; ?>&nbsp;</td>
                            <td><? echo $row[csf('self')]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==2) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==3) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? echo number_format($stockQtyRo,2); ?></td>
                        </tr>
                    <?
                        $i++;
                    }
                    
                    foreach($resultTrans as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                        if($row[csf('trans_type')]==5)
                        {
                            $stockQtyRo+=$row[csf('qnty')];
                        }
                        else
                        {
                            $stockQtyRo-=$row[csf('qnty')];
                        }
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2<? echo $i;?>','<? echo $bgcolor;?>')" id="tr2<? echo $i;?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</td>
                            <td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                            <td><? echo $row[csf('rack')]; ?>&nbsp;</td>
                            <td><? echo $row[csf('self')]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==5) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==6) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px"><? echo number_format($stockQtyRo,2); ?></td>
                        </tr>
                    <?
                        $i++;
                    }
                    ?>
                </table> 
            <?
			}
			if(str_replace("'","",trim($txt_progarm_no))!="" && str_replace("'","",trim($txt_progarm_no))!="" && $item_category_id==13)
			{ 
			?>
            	<b>Program Level Transaction (Program No : <? echo str_replace("'","",trim($txt_progarm_no)); str_replace("'","",trim($txt_progarm_no))?>; Order No : <? echo $po_data[0][csf('po_number')];?>; Job No: <? echo $po_data[0][csf('job_no')]; ?>; Buyer: <? echo $buyer_name; ?>) </b>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" style="margin-bottom:10px">
                    <thead>
                        <th width="30">SL</th>
                        <th width="120">Transaction Ref</th>
                        <th width="80">Date</th>
                        <th width="100">Receive Qty.</th>
                        <th width="100">Issue Return Qty.</th>
                        <th width="100">Transfer In Qty.</th>
                        <th width="100">Issue Qty.</th>
                        <th width="110">Receive Return Qty.</th>
                        <th width="100">Transfer Out Qty.</th>
                        <th>Stock Qty.</th>
                    </thead>
					<?
                    $stockQtyPro=0; $i=1;
                    foreach($resultProg as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $stockQtyPro+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3<? echo $i;?>','<? echo $bgcolor;?>')" id="tr3<? echo $i;?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?>&nbsp;</td>
                            <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==1) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==4) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? echo number_format($stockQtyPro,2); ?></td>
                        </tr>
                    <?
                        $i++;
                    }
					foreach($resultIssProg as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $stockQtyPro-=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?>&nbsp;</td>
                            <td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==2) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==3) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
                            <td align="right" style="padding-right:3px">&nbsp;</td>
                            <td align="right" style="padding-right:3px"><? echo number_format($stockQtyPro,2); ?></td>
                        </tr>
                    <?
                        $i++;
                    }
					
					foreach($resultTransProg as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if($row[csf('trans_type')]==5)
						{
							$stockQtyPro+=$row[csf('qnty')];
						}
						else
						{
							$stockQtyPro-=$row[csf('qnty')];
						}
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('transfer_system_id')]; ?>&nbsp;</td>
							<td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==5) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px">&nbsp;</td>
							<td align="right" style="padding-right:3px"><? if($row[csf('trans_type')]==6) echo number_format($row[csf('qnty')],2); else echo "&nbsp;"; ?></td>
							<td align="right" style="padding-right:3px"><? echo number_format($stockQtyPro,2); ?></td>
						</tr>
					<?
						$i++;
					}
                    ?>
                </table>
            <?
			}
			?>          
        </fieldset>
    </form>         
<?
	exit();	
}

if ($action=="synchronize_stock")
{
	extract($_REQUEST);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$row_prod=sql_select("select avg_rate_per_unit, allocated_qnty from product_details_master where id=$prod_id");
	$curr_stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end)-sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as stock_qty","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id","stock_qty");
	$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')]; 
	$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
	$available_qnty=$curr_stock_qnty-$row_prod[0][csf('allocated_qnty')];
	
	$field_array_prod_update="current_stock*stock_value*available_qnty";
	$data_array_prod_update=$curr_stock_qnty."*".$stock_value."*".$available_qnty;
	
	$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,1);
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Data Synchronize is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Data Synchronize is not completed successfully**$data_array_prod_update";
		}
	}
	disconnect($con);
	die;

}

?>
