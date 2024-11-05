<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);    
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $data) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 120, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}


if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $copmany, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <?
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_report_v2_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_library[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	
	//echo $sql;
	//echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	//echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order  No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					 <input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $copmany, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <?
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		
								$search_by_arr=array(1=>"Order NO",2=>"Job No.",3=>"Style Ref.");
								$dd="change_search_event(this.value, '0*0*0', '0*0*O', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'order_popup_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_report_v2_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="order_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value%')";	
	}
	else if($search_type==3 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT b. id , a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,b.po_number,$year_field as year 
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by b.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date,b.po_number
	order by b.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_library[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $data[csf('po_number')]; ?></p></td>
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

if($action=="job_wise_search")
{
	/*
		echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		$data=explode('_',$data);
		// $report_type=$data[3];
		// print_r($data);
		//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
	  {
		//alert(id);
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
	</script>
	 
	<?

		if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		
		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number"; 
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";	


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>
	            
	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>
			
		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
		disconnect($con);
		exit();
	*/
}

if ($action=="requisition_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(id )
		{
			document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
		}
    </script>
    </script>
	</head>
	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:970px;margin-left:3px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Buyer Name</th>
	                    <th width="130">Search By</th>
	                    <th width="180" align="center" id="search_by_td_up">Enter Order Number</th>
	                    <th width="230">Shipment Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" id="selected_id" name="selected_id" />
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
	                    </td>
	                    <td align="center">	
	                    	<?
								$searchby_arr=array(1=>"Requisition No");
								$dd="change_search_event(this.value, '0', '0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $searchby_arr,"",1, "--Select--", 1,$dd,0 );
							?>
	                    </td> 
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_requisition_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_report_v2_controller', 'setFilterGrid(\'tbl_po_list\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div> 
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_requisition_search_list_view")
{
 	$data=explode('_',$data);
	
	if ($data[4]=="" &&  $data[5]=="" && trim($data[2])=="") 
	{
		?>
		<div class="alert alert-danger"> Please enter value of Requisition No or Shipment Date</div>
		<?
		die;
	}
	if($data[0]==0)
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
		$buyer_id_cond=" and a.buyer_name='".$data[0]."'";
	}
	
	$search_by=$data[1];
	$txt_search_common=trim($data[2]);
	$company_id=$data[3];
	
	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$shipment_date = "and a.ESTIMATED_SHIPDATE between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and a.ESTIMATED_SHIPDATE between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$shipment_date ="";
	
	$sql_cond="";
	if($txt_search_common!="")
	{
		if($search_by==1)
			$sql_cond = " and a.REQUISITION_NUMBER_PREFIX_NUM like '%".trim($txt_search_common)."'";	
 	}
	
	$type=$data[6];
	
	$sql = "SELECT a.id, a.buyer_name,a.requisition_number,a.style_ref_no,a.ESTIMATED_SHIPDATE,b.color_id,b.gmts_item_id,b.sample_prod_qty  from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b where a.id = b.sample_mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0  $sql_cond $shipment_date $buyer_id_cond group by a.id,a.buyer_name,a.requisition_number,a.style_ref_no,	a.ESTIMATED_SHIPDATE,b.color_id,b.gmts_item_id,	b.sample_prod_qty order by a.id desc";	
	// echo $sql;die;
	$result = sql_select($sql);
	
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
 	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="70">Ship. Date</th>
            <th width="100">Req. No</th>
            <th width="100">Booking No</th>
            <th width="100">Buyer</th>
            <th width="120">Style</th>
            <th width="130">Color</th>
            <th width="130">Item</th>
            <th>Req. Qty</th>
        </thead>
	</table>
	<div style="width:960px; max-height:220px;overflow-y:auto;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_po_list" align="left">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$data=$row[csf("id")]."_".$row[csf("requisition_number")]."_".$row[csf("buyer_name")]."_".$row[csf("style_ref_no")]."_".$row[csf("color_id")]."_".$row[csf("gmts_item_id")]."_".$row[csf("sample_prod_qty")]."_".$buyer_arr[$row[csf("buyer_name")]]."_".$garments_item[$row[csf("gmts_item_id")]]."_".$row[csf("ESTIMATED_SHIPDATE")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data; ?>');" > 
					<td width="40"><?php echo $i; ?></td>
					<td width="70" align="center"><?php echo $row[csf("ESTIMATED_SHIPDATE")]; ?></td>		 
					<td width="100"><p><?php echo $row[csf("requisition_number")]; ?></p></td>
					<td width="100"><p><?php //echo $row[csf("job_no")]; ?></p></td>
					<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
					<td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
					<td width="130"><p><?php echo $color_arr[$row[csf("color_id")]];?></p></td>	
					<td width="130"><p><?php echo $garments_item[$row[csf("gmts_item_id")]];?></p></td>	
					<td align="right"><?php echo $row[csf("sample_prod_qty")];?></td>
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


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	// ============================= getting form value =============================
	$company_id 		= str_replace("'", "", $cbo_company_name);
	$location_id 		= str_replace("'", "", $cbo_location_name);
	$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
	$year 				= str_replace("'", "", $cbo_year);
	$job_id 			= str_replace("'", "", $txt_job_no);
	$hidden_job_id 		= str_replace("'", "", $hidden_job_id);
	$order_id 			= str_replace("'", "", $txt_order_no);
	$hidden_order_id 	= str_replace("'", "", $hidden_order_id);
	$requisition_no 	= str_replace("'", "", $txt_requisition_no);
	$hidden_requisition_id = str_replace("'", "", $hidden_requisition_id);
	$transfer_criteria 	= str_replace("'", "", $cbo_transfer_criteria);
	$date_category 		= str_replace("'", "", $cbo_date_category);
	$date_from 			= str_replace("'", "", $txt_date_from);
	$date_to 			= str_replace("'", "", $txt_date_to);
	//echo $hidden_order_id."kkk";

	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
	$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
	// $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');

	$sql_cond 		= "";
	$sql_cond .= ($company_id!=0) ? " and d.company_id=$company_id": "";
	$sql_cond .= ($location_id==0) ? " and d.location=$location_id": "";
	// $sql_cond .= ($buyer_id!=0) ? " and a.buyer_name=$buyer_id": "";
	// $sql_cond .= ($year!=0) ? " and to_char(a.insert_date,'YYYY')=$year": "";

	if($date_category!="0")
	{
		if($date_from!="" && $date_to!="")
		{
			switch ($date_category) 
			{
			  	case 1:
			    	$sql_cond .= " and f.delivery_date between '$date_from' and '$date_to'";
			    	break;
			  	case 2:
			    	$sql_cond .= " and b.pub_shipment_date between '$date_from' and '$date_to'";
			    	break;
			  	case 3:
			    	$sql_cond .= " and d.insert_date between '$date_from' and '$date_to 11:59:59 PM'";
			    	break;
			  	default:
			    	$sql_cond .= " and f.delivery_date between '$date_from' and '$date_to'";
			}
		}
	}				
	$sql_cond .= ($transfer_criteria!=0) ? " and f.transfer_criteria=$transfer_criteria": "";
		//$sql_cond .= ($hidden_requisition_id!=0) ? " and d.po_break_down_id=$hidden_requisition_id": "";
	
	// echo $sql_cond;

	if($buyer_id!=0 && $hidden_job_id=="" && $hidden_order_id=="")
	{
		$po_id_arr = return_library_array("SELECT b.id,b.id from  wo_po_details_master a, wo_po_break_down  b where a.buyer_name in($buyer_id) and a.id=b.job_id and a.status_active=1 and b.status_active=1 ", "id", "id");
		// echo "SELECT a.id,a.id from  wo_po_details_master a, wo_po_break_down  b where a.buyer_name in($buyer_id) and a.status_active=1 and b.status_active=1";

		$order_id_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");

		$delv_id_arr = return_library_array("SELECT delivery_mst_id,delivery_mst_id from  pro_garments_production_mst where trans_type in(5,6) and production_type=10 $order_id_cond", "delivery_mst_id", "delivery_mst_id");
		// print_r($delv_id_arr);
		$delv_id_cond = where_con_using_array($delv_id_arr,0,"f.id");
	}
	//echo  $hidden_order_id."yyy";die;
	if($hidden_job_id!="" && $hidden_order_id=="")
	{
		$po_id_arr = return_library_array("SELECT id,id from  wo_po_break_down where job_id in($hidden_job_id) and status_active=1", "id", "id");

		$order_id_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");

		$delv_id_arr = return_library_array("SELECT delivery_mst_id,delivery_mst_id from  pro_garments_production_mst where trans_type in(5,6) and production_type=10 $order_id_cond", "delivery_mst_id", "delivery_mst_id");
		// print_r($delv_id_arr);
		$delv_id_cond = where_con_using_array($delv_id_arr,0,"f.id");
	}

	//echo $hidden_order_id."hhh";
	if($hidden_order_id!="")
	{
		$delv_id_arr = return_library_array("SELECT delivery_mst_id,delivery_mst_id from  pro_garments_production_mst where po_break_down_id in($hidden_order_id) and trans_type in(5,6) and production_type=10", "delivery_mst_id", "delivery_mst_id");
	
		$delv_id_cond = where_con_using_array($delv_id_arr,0,"f.id");
	}

	if($hidden_requisition_id!="")
	{
		$delv_id_arr = return_library_array("SELECT delivery_mst_id,delivery_mst_id from  pro_garments_production_mst where po_break_down_id in($hidden_requisition_id) and trans_type in(5,6) and production_type=10", "delivery_mst_id", "delivery_mst_id");
	
		$delv_id_cond = where_con_using_array($delv_id_arr,0,"f.id");
	}


	
	// ======================================== MAIN QUERY FOR LAY =========================================
	$sql="SELECT f.SYS_NUMBER,f.delivery_date as TR_DATE, f.TRANSFER_CRITERIA,a.COMPANY_NAME,a.BUYER_NAME, c.color_number_id as COLOR_ID,b.id as PO_ID,b.PO_NUMBER,a.STYLE_REF_NO,a.JOB_NO, d.item_number_id as ITEM_ID,e.PRODUCTION_QNTY,d.TRANS_TYPE,d.COUNTRY_ID,d.REMARKS,e.color_size_break_down_id as col_size_id
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f
	where a.id=b.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and d.delivery_mst_id=f.id and  f.TRANSFER_CRITERIA is not null and  f.TRANSFER_CRITERIA!=0  and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_type=10 and d.trans_type in(5,6) $sql_cond $delv_id_cond  order by f.delivery_date"; //and b.is_confirmed=1
	//echo $sql;die();
	$sqlres = sql_select($sql);
	
	
	if(count($sqlres)==0)
	{
		?>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
			  <strong>Data not found!</strong> Please try again.
			</div>
		</div>
		<?
		die();
	} 

	
	$po_id_array = array();
	$req_id_array = array();
	foreach ($sqlres as $val) 
	{
		
		if($val['TRANSFER_CRITERIA']==1)
		{
			$po_id_array[$val['COL_SIZE_ID']] = $val['COL_SIZE_ID'];
		}
		else if($val['TRANSFER_CRITERIA']==2 && $val['TRANS_TYPE']==6)
		{
			$po_id_array[$val['COL_SIZE_ID']] = $val['COL_SIZE_ID'];
		}
		else
		{
			$req_id_array[$val['COL_SIZE_ID']] = $val['COL_SIZE_ID'];
		}
	}
	// print_r($req_id_array);
	if(count($po_id_array)>0)
	{
		$po_id_cond = where_con_using_array($po_id_array,0,"po_break_down_id");

		$sql = "SELECT id,color_number_id,counry_id from wo_po_color_size_breakdown where status_active=1 $po_id_cond";
		$res = sql_select($sql);
		$order_info_array = array();
		foreach ($res as $v) 
		{
			$order_info_array[$v['ID']]['color_id'] = $v['COLOR_NUMBER_ID'];
			$order_info_array[$v['ID']]['counry_id'] = $v['COUNRY_ID'];
		}
	}

	if(count($req_id_array)>0)
	{
		$req_id_cond = where_con_using_array($req_id_array,0,"a.ID");

		$sql = "SELECT a.id,a.gmts_item_id,a.sample_color,b.requisition_number,0 as country_id,b.STYLE_REF_NO,b.buyer_name from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_MST b where a.sample_mst_id=b.id and a.status_active=1 $req_id_cond";
		//echo $sql;die;
		$res = sql_select($sql);
		$sample_info_array = array();
		foreach ($res as $v) 
		{
			$sample_info_array[$v['ID']]['color_id'] = $v['SAMPLE_COLOR'];
			$sample_info_array[$v['ID']]['item_id'] = $v['GMTS_ITEM_ID'];
			$sample_info_array[$v['ID']]['country_id'] = $v['COUNTRY_ID'];
			$sample_info_array[$v['ID']]['req_no'] = $v['REQUISITION_NUMBER'];
			$sample_info_array[$v['ID']]['style'] = $v['STYLE_REF_NO'];
			$sample_info_array[$v['ID']]['buyer_name'] = $v['BUYER_NAME'];
		}
	}
 // echo"<pre>";print_r($sample_info_array);die;

	$data_array = array();
	foreach ($sqlres as $val) 
	{
		if($val['TRANSFER_CRITERIA']==1)
		{
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['company_name'] = $val['COMPANY_NAME'];
		
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['po_number'] = $val['PO_NUMBER'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['style'] = $val['STYLE_REF_NO'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['job_no'] = $val['JOB_NO'];
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['country_id'] = $val['COUNTRY_ID'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['remarks'] = $val['REMARKS'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];
		}
		else if($val['TRANSFER_CRITERIA']==2 && $val['TRANS_TYPE']==6)
		{
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['company_name'] = $val['COMPANY_NAME'];
		
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['po_number'] = $val['PO_NUMBER'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['style'] = $val['STYLE_REF_NO'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['job_no'] = $val['JOB_NO'];
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['country_id'] = $val['COUNTRY_ID'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['remarks'] = $val['REMARKS'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$val['BUYER_NAME']][$val['ITEM_ID']][$val['COLOR_ID']][$val['TRANS_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];
		}
		else
		{
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['company_name'] = $val['COMPANY_NAME'];
		
			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['po_number'] = $sample_info_array[$val['COL_SIZE_ID']]['req_no'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['style'] = $sample_info_array[$val['COL_SIZE_ID']]['style'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['job_no'] = $val['JOB_NO'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['buyer_name'] = $val['BUYER_NAME'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['remarks'] = $val['REMARKS'];

			$data_array[$val['TRANSFER_CRITERIA']][$val['TR_DATE']][$val['SYS_NUMBER']][$sample_info_array[$val['COL_SIZE_ID']]['buyer_name']][ $sample_info_array[$val['COL_SIZE_ID']]['item_id']][$sample_info_array[$val['COL_SIZE_ID']]['color_id']][$val['TRANS_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];
		}		
	}
	//echo "<pre>";print_r($data_array);echo "</pre>";
	unset($sqlres);

	// ============================ order info ====================================
	// wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,pro_gmts_delivery_mst f

	// ============================ req info ====================================
	ob_start();	
	?>
    <!-- ===================================== DETAILS PART START ===================================== -->
	<?
	
	?>
	
						
    <fieldset>
    	<div style="margin:0 auto;">		
	        <table width="1610" cellpadding="0" cellspacing="0"> 
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 18px;">Finish Garments Order to Order Transfer Report V2.</td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 18px;"><? echo $company_library[$company_id]; ?></td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 15px;"><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) )." to ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></td> 
	            </tr>
	        </table>
	    </div>
		<?
		$tot_transfr = 0;
		ksort($data_array);
		foreach ($data_array as $trans_key => $trans_val) 
		{	
			$trans_from_txt = "";
			$trans_to_txt = "";
			if($trans_key==1)
			{
				$trans_from_txt = "From Order";
				$trans_to_txt = "To Order";
			}
			elseif($trans_key==2)
			{
				$trans_from_txt = "From Order";
				$trans_to_txt = "To Sample";
			}
			else
			{
				$trans_from_txt = "From Sample";
				$trans_to_txt = "To Sample";
			}
			?>
						
			
			<div style="margin-bottom: 10px;">
			<div><b> Transfer Criteria :<?=$fin_gmts_transfer_criteria_array[$trans_key];?></b></div>
				<table width="1610" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
					<thead>
						<tr>
							<th rowspan="2" width="30" ><p>SL</p></th>
							<th rowspan="2" width="80"><p>Transfer Date</p></th>
							<th rowspan="2" width="100"><p>Transfer ID</p></th>
							<th rowspan="2" width="100"><p>Buyer</p></th>
							<?
							  if($trans_key==1 || $trans_key==2)
							  {  ?>
								<th colspan="5"><p><?=$trans_from_txt;?></p></th>
							    <?
							  }else if($trans_key==3)
							  {  ?>
							 	<th colspan="4"><p><?=$trans_from_txt;?></p></th>
							    <?
							  }
							?>
							
							<th colspan="5"><p><?=$trans_to_txt;?></p></th>
							<th rowspan="2" width="100"><p>Item Name</p></th>
							<th rowspan="2" width="100"><p>Transfer Qnty</p></th>
							<th rowspan="2" width="100"><p>Remarks</p></th>
						</tr>	
						<tr>
							
							<?
							  if($trans_key==1 || $trans_key==2)
							  {  ?>
							 	<th width="100">Order No</th>
							    <?
							  }else if($trans_key==3)
							  {  ?>
							 	<th width="100">Req. No</th>
							    <?
							  }
							?>
							<th width="100">Job No</th>
							<th width="100">Style Ref. No</th>
							<th width="100">Color</th>

							<?
							  if($trans_key==1  ||  $trans_key==2)
							  {  ?>
								 <th width="100">Country</th>

							    <?
							  }
							?>
							<?
							  if($trans_key==1)
							  {  ?>
							 	<th width="100">Order No</th>
							    <?
							  }else if($trans_key==2 || $trans_key==3)
							  {  ?>
							 	<th width="100">Req. No</th>
							    <?
							  }
							?>
							
							<?
							  if($trans_key==1)
							  {  ?>
							 	 <th width="100">Job No</th>
							    <?
							  }
							?>
							<th width="100">Style Ref. No</th>
							<th width="100">Color</th>
							<?
							  if($trans_key==1)
							  {  ?>
								 <th width="100">Country</th>

							    <?
							  }
							?>
						</tr>				   
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; width:1630px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1610" rules="all" id="table_body_" align="left">
						<tbody>
							<?
							$i=1;							
							foreach ($trans_val as $date_key => $date_val) 
							{
								foreach ($date_val as $sys_key => $sys_val) 
								{
									foreach ($sys_val as $buyer_key => $buyer_data) 
									{
										foreach ($buyer_data as $itm_key => $itm_data) 
										{
											foreach ($itm_data as $color_key => $row) 
											{
												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
													<td width="30"><p><?=$i;?></p></td>
													<td width="80" align="center"><p><?= change_date_format($date_key);?></p></td>
													<td width="100" align="center"><p><?=$sys_key;?></p></td>
													<td width="100"><p><?=$buyer_library[$buyer_key];?></p></td>
													

													<td width="100"><p><?=$row[6]['po_number'];?></p></td>
													<td width="100"><p><?=$row[6]['job_no'];?></p></td> 
													<td width="100"><p><?=$row[6]['style'];?></p></td>
													<td width="100"><p><?=$color_arr[$color_key];?></p></td>

														<?
														if($trans_key==1  ||  $trans_key==2)
														{  ?>
														<td width="100"><p><?=$country_library[$row[6]['country_id']];?></p></td>
												

															<?
														}
														?>
													
													<td width="100"><p><?=$row[5]['po_number'];?></p></td>
													<?if($trans_key==1)
													{ ?>
													<td width="100"><p><?=$row[5]['job_no'];?></p></td>
													<?
													}
													?>														
													<td width="100"><p><?=$row[5]['style'];?></p></td>
													<td width="100"><p><?=$color_arr[$color_key];?></p></td>
													<?if($trans_key==1)
													{ ?>
													<td width="100"><p><?=$country_library[$row[5]['country_id']];?></p></td>
													<?
													}
													?>																				
													<td width="100"><p><?=$garments_item[$itm_key];?></p></td>
													<td width="100" align="right"><p><?=$row[5]['qty'];?></p></td>
													<td width="100"><p><?=$row[5]['remarks'];?></p></td>
												</tr>
												<?
												$i++;
												$tot_transfr += $row[5]['qty'];
											}
											
										}
									}
								}
							}
							
							?>
						</tbody>										
					</table>										  
				</div>	
				<table width="1610" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
					<tfoot>
						<tr>
							<!-- <th width="30"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th> -->
							<th colspan="8"><p>Grand Total</p></th>
							<th width="100"><p><?=$tot_transfr;?></p></th>
							<th width="100"></th>
							
						</tr>
					</tfoot>
				</table>
			</div>	
			<?
		}
		?>
	 </fieldset> 
    <?
    unset($data_array);
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();	 

}
?>