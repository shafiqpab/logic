<?php
	
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if ($action=="load_drop_down_party")
{
    $data=explode("_",$data);

    if($data[1]==1 && $data[0]!=0)
    {

        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down('requires/yd_delivery_entry_v2_controller', this.value,'load_drop_down_party_location', 'party_location_td' );");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
    }   
    exit();  
}


if ($action=="load_drop_down_location")
{
    
    echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action=="load_drop_down_party_location")
{
    echo create_drop_down("cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if($action == "job_search_popup_job")
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);

	?>
	<script>
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}

		var selected_id = new Array(); var jobNoArr = new Array;
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data() 
		{
			$("#tbl_data_list tbody tr").each(function() 
			{
				var valTP=$(this).attr("id");
				//if(valTP==1) return;
				//alert(valTP);
				//$("#"+valTP).click();

				if (typeof valTP != "undefined")
				{
					var val = valTP.split("_");
					var id = val[1];
					var job_no =  $('#txt_buyer_id'+id).val()*1;

					if(jobNoArr.length==0)
					{
						jobNoArr.push( job_no );
					}
					else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
					{
						alert("Buyer Mixed Not Allowed");
						return true;
					}
					toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

					if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
					{
						selected_id.push( $('#txt_individual_id_' +id).val() );
					}
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
						}
						selected_id.splice( i, 1 );
						jobNoArr.splice( i, 1 );
					}

					var id = '';
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						id += selected_id[i] + ',';
					}
					id = id.substr( 0, id.length - 1 );
					$('#txt_selected_id').val( id );
					document.getElementById('hidden_party_id').value=job_no;
				}
			});
		}

		function js_set_value(str)
		{
			splitArr = str.split('***'); 
			var delevery_id=splitArr[0]*1;
			var receive_dtls_id=splitArr[1]*1;
			var job_no=splitArr[2]*1;

			$("#tbl_data_list tbody tr").each(function() 
			{
				var valTP=$(this).attr("id");

				if (typeof valTP != "undefined")
				{
					var val = valTP.split("_");
					var id = val[1];
					var delevery_id1 =  $('#txt_id'+id).val()*1;
					if(delevery_id==delevery_id1)
					{
		
					if(jobNoArr.length==0)
					{
						jobNoArr.push( job_no );
					}
					else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
					{
						alert("Buyer Mixed Not Allowed");
						return true;
					}
					toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

					if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
					{
						selected_id.push( $('#txt_individual_id_' +id).val() );
					}
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
						}
						selected_id.splice( i, 1 );
						jobNoArr.splice( i, 1 );
					}

					var id = '';
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						id += selected_id[i] + ',';
					}
					id = id.substr( 0, id.length - 1 );
					$('#txt_selected_id').val( id );
					document.getElementById('hidden_party_id').value=job_no;
				}

				}
			});
		}
			
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
					<thead>
		                <tr>
		                    <th colspan="11"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
		                </tr>
		                <tr>
		                    <th width="120" class="must_entry_caption" >Company Name</th>
		                    <th width="80" class="must_entry_caption" >Within Group</th>
		                    <th width="120">Party Name</th>
		                    <th width="80">Receive Id</th>
		                    <th width="80">Search By</th>
		                    <th width="80" id="search_by_td">YD Job No</th>
		                    <th width="70">Prod. Type</th>
		                    <th width="70" class="must_entry_caption" >Order Type</th>
		                    <th width="70">Y/D Type</th>
		                    <th width="160">Receive Date Range</th>
		                    <th>
		                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $cbo_company_name, "load_drop_down( 'yd_delivery_entry_v2_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",1); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $cbo_within_group, "load_drop_down( 'yd_delivery_entry_v2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",1); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php 

		                            if($cbo_within_group==1 && $cbo_company_name!=0)
								    {

								        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $cbo_party_name, "",1);
								    }
								    elseif($cbo_within_group==2 && $cbo_company_name!=0)
								    {
								        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$cbo_party_name, "",1 );
								    }
								    else
								    {
								    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
								    }

	                            ?>
	                        </td>
	                        <td> 
	                            <input type="text" name="txt_receive_no" id="txt_receive_no" class="text_boxes" placeholder="Write Receive Id" />
	                        </td>
	                        <td>
	                        	<?
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
									echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
	                        </td>
	                        <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
	                        	<? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$selected,'',0 );?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_receive_no').value, 'create_job_search_list_view', 'search_div', 'yd_delivery_entry_v2_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:70px;" />
                            </td>
                		</tr>
                		<tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" style="width:70px">
                                <input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" />
                            </td>
                        </tr>
                	</tbody>
		        </table>
			</form>
		</div>
		<div id="search_div" align="center">
			
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?php
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

	$search_type 			=trim(str_replace("'","",$data[0]));
	$cbo_company_name  		=trim(str_replace("'","",$data[1]));
	$cbo_within_group 		=trim(str_replace("'","",$data[2]));
	$cbo_party_name 		=trim(str_replace("'","",$data[3]));
	$search_by 				=trim(str_replace("'","",$data[4]));
	$search_str 			=trim(str_replace("'","",$data[5]));
	$cbo_pro_type 			=trim(str_replace("'","",$data[6]));
	$cbo_order_type 		=trim(str_replace("'","",$data[7]));
	$cbo_yd_type 			=trim(str_replace("'","",$data[8]));
	$txt_date_from 			=trim(str_replace("'","",$data[9]));
	$txt_date_to 			=trim(str_replace("'","",$data[10]));
	$cbo_year_selection 	=trim(str_replace("'","",$data[11]));
	$txt_receive_no 		=trim(str_replace("'","",$data[12]));
	
	
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1

	if($cbo_company_name==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Company Name first!!!</p>";
		die;
	}

	if($cbo_within_group==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Within Group first!!!</p>";
		die;
	}

	if($cbo_order_type==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Order Type first!!!</p>";
		die;
	}

	$condition = "";



 if($yarn_dyeing_process==1 && $service_process_id==1)
 	{
		if($cbo_company_name!=0)
		{
			$condition .= " and a.company_id=$cbo_company_name";
		}
		
		if($txt_receive_no!=0) //job_no_prefix, job_no_prefix_num, yd_job,
		{
			$condition .= " and a.job_no_prefix_num=$txt_receive_no";
		}
		
		if($cbo_within_group!=0)
		{
			$condition .= " and a.within_group=$cbo_within_group";
		}
		
		if($cbo_party_name!=0)
		{
			$condition .= " and a.party_id=$cbo_party_name";
		}
		
		if($cbo_pro_type!=0)
		{
			$condition .= " and a.pro_type=$cbo_pro_type";
		}
		
		if($cbo_order_type!=0)
		{
			$condition .= " and a.order_type=$cbo_order_type";
		}
		
		if($cbo_yd_type!=0)
		{
			$condition .= " and a.yd_type=$cbo_yd_type";
		}
		
		$date_con = '';
		if($db_type==0)
		{ 
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
		}
		else
		{
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
		}
		
		
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.yd_job='$search_str'";
				else if($search_by==2) $condition="and a.order_no='$search_str'";
				else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
				else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
				
			 
			}
			
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.yd_job like '$search_str%'";
				else if($search_by==2) $condition="and a.order_no like '$search_str%'";
				else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
			}
			
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.yd_job like '%$search_str'";
				else if($search_by==2) $condition="and a.order_no like '%$search_str'";
				else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
			}
			
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.yd_job like '%$search_str%'";
				else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
				else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
			}
			
		}
	}
	if($yarn_dyeing_process==13 && $service_process_id==3)
	{
	   if($cbo_company_name!=0)
	   {
		   $condition .= " and a.company_id=$cbo_company_name";
	   }
	   
	   if($txt_receive_no!=0) //job_no_prefix, job_no_prefix_num, yd_job,
	   {
		   $condition .= " and a.SYS_NUMBER_PREFIX_NUM=$txt_receive_no";
	   }
	   
	   if($cbo_within_group!=0)
	   {
		   $condition .= " and a.within_group=$cbo_within_group";
	   }
	   
	   if($cbo_party_name!=0)
	   {
		   $condition .= " and a.party_id=$cbo_party_name";
	   }
	   
	   if($cbo_pro_type!=0)
	   {
		   $condition .= " and c.pro_type=$cbo_pro_type";
	   }
	   
	   if($cbo_order_type!=0)
	   {
		   $condition .= " and c.order_type=$cbo_order_type";
	   }
	   
	   if($cbo_yd_type!=0)
	   {
		   $condition .= " and c.yd_type=$cbo_yd_type";
	   }
	   
	   $date_con = '';
	   if($db_type==0)
	   { 
		   if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.INSPECTION_DATE between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
	   }
	   else
	   {
		   if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.INSPECTION_DATE between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
	   }
	   
	   
	   if($search_type==1)
	   {
		   if($search_str!="")
		   {
			   if($search_by==1) $condition="and c.yd_job='$search_str'";
			   else if($search_by==2) $condition="and c.order_no='$search_str'";
			   else if ($search_by==3) $condition=" and d.style_ref = '$search_str' ";
			   else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
			   
			
		   }
		   
	   }
	   else if($search_type==2)
	   {
		   if($search_str!="")
		   {
			   if($search_by==1) $condition="and c.yd_job like '$search_str%'";
			   else if($search_by==2) $condition="and c.order_no like '$search_str%'";
			   else if ($search_by==3) $condition=" and d.style_ref like  '$search_str%' ";
			   else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
		   }
		   
	   }
	   else if($search_type==3)
	   {
		   if($search_str!="")
		   {
			   if($search_by==1) $condition="and c.yd_job like '%$search_str'";
			   else if($search_by==2) $condition="and c.order_no like '%$search_str'";
			   else if ($search_by==3) $condition=" and d.style_ref like  '%$search_str' ";
			   else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
		   }
		   
	   }
	   else if($search_type==4 || $search_type==0)
	   {
		   if($search_str!="")
		   {
			   if($search_by==1) $condition="and c.yd_job like '%$search_str%'";
			   else if($search_by==2) $condition="and c.order_no like '%$search_str%'";
			   else if ($search_by==3) $condition=" and d.style_ref like  '%$search_str%' ";
			   else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
		   }
		   
	   }
   	}
	else
	{  
				
		if($cbo_company_name!=0)
		{
			$condition .= " and a.company_id=$cbo_company_name";
		}
	
		if($txt_receive_no!=0)
		{
			$condition .= " and a.receive_no_prefix_num=$txt_receive_no";
		}
	
		if($cbo_within_group!=0)
		{
			$condition .= " and a.within_group=$cbo_within_group";
		}
	
		if($cbo_party_name!=0)
		{
			$condition .= " and a.party_id=$cbo_party_name";
		}
	
		if($cbo_pro_type!=0)
		{
			$condition .= " and a.pro_type=$cbo_pro_type";
		}
	
		if($cbo_order_type!=0)
		{
			$condition .= " and a.order_type=$cbo_order_type";
		}
	
		if($cbo_yd_type!=0)
		{
			$condition .= " and a.yd_type=$cbo_yd_type";
		}
	 
		$date_con = '';
		if($db_type==0)
		{ 
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
		}
		else
		{
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
		}
	
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.job_no='$search_str'";
				else if($search_by==2) $condition="and a.order_no='$search_str'";
				else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
				else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
			}
			
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.job_no like '$search_str%'";
				else if($search_by==2) $condition="and a.order_no like '$search_str%'";
				else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
			}
			
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.job_no like '%$search_str'";
				else if($search_by==2) $condition="and a.order_no like '%$search_str'";
				else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
			}
			
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition="and a.job_no like '%$search_str%'";
				else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
				else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
				else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
			}
			
		}   
				
	}


	
	
	
	//$variable_status=return_field_value("item_show_in_detail","variable_setting_yarn_dyeing","company_name='$data[0]' and variable_list =2 and is_deleted = 0 and status_active = 1");
	
	//echo "select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id"; die;
	
	
			if($db_type==0)
			{ 
 				$ins_year_cond="year(a.insert_date)";
			}
			else
			{
 				$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
			}
			
 		if($yarn_dyeing_process==1 && $service_process_id==1)
		{
			   $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no from yd_ord_mst a,yd_ord_dtls b where a.id=b.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $condition  $date_con group by  a.id, a.yd_job, a.job_no_prefix_num,a.within_group, a.insert_date,a.party_id, a.location_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.id order by a.id DESC";
		}
		else if($yarn_dyeing_process==13 && $service_process_id==3)
		{
			$sql= "select a.SYS_NUMBER as yd_receive, a.id, b.id as receive_dtls_id, d.style_ref, a.party_id, c.pro_type, a.within_group, c.yd_job as job_no, d.order_no, d.order_id, c.order_type, a.INSPECTION_DATE as receive_date, d.count_type, b.SALES_ORDER_ID as sales_order_no 
			from yd_inspection_mst a, yd_inspection_dtls b, yd_ord_mst c ,yd_ord_dtls d
			where a.id=b.mst_id and d.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.SALES_ORDER_ID=d.SALES_ORDER_ID and b.job_dtls_id=d.id and a.entry_form=443 $condition $date_con 
			group by a.SYS_NUMBER, a.id, b.id, d.style_ref, a.party_id, c.pro_type, a.within_group, c.yd_job, d.order_no, d.order_id, c.order_type, a.INSPECTION_DATE, d.count_type, b.SALES_ORDER_ID order by a.id desc";
		}
		else
		{     
 
	$sql= "select a.yd_receive, a.id, b.id as receive_dtls_id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b, yd_ord_mst c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.yd_job=a.job_no and a.entry_form=571 $condition $date_con group by a.yd_receive, a.id, b.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.id desc";
		}
// echo $sql;
	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="995" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Receive No</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">Count Type</th>
            <th >Receive Date</th>
        </thead>
	</table>
	<div style="width:996px; max-height:370px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="995" >
			<tbody>
				<?php
					$i=1;
					$count_type_arr = array(1 => "Single",2 => "Double");
					foreach($result as $data)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($data[csf('within_group')]==1)
						{
							$party_name = $comp_arr[$data[csf('party_id')]];

						}
						else
						{
							$party_name = $party_arr[$data[csf('party_id')]];
						}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $data[csf('id')].'***'.$data[csf('receive_dtls_id')].'***'.$data[csf('party_id')]; ?>")' style="cursor:pointer" id="search_<? echo $data[csf('receive_dtls_id')];?>">
					<td align="center" width="30"><? echo $i; ?></td>
		            <td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('yd_receive')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" >
		            	<? echo $data[csf('receive_date')]; ?>
		            	<input type="hidden" name="txt_individual_id" id="txt_individual_id_<?php echo $data[csf('receive_dtls_id')]; ?>" value="<? echo $data[csf('receive_dtls_id')]; ?>"/>
		            	<input type="hidden" name="txt_id" id="txt_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('id')]; ?>"/>
		            	<input type="hidden" name="txt_buyer_id" id="txt_buyer_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('party_id')]; ?>"/>
		            </td>
				</tr>
				<?php
					$i++;
					}
				?>
	        </tbody>
		</table>
		<br>
		<table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
		    <tr>
		        <td align="center" height="30" valign="middle">
			        <div style="width:100%">
			        <div style="width:50%; float:left;" align="left">
			        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
			        </div>
			        <div style="width:100%; float:left" align="center">
			        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
			        </div>
			        </div>
		        </td>
		    </tr>
		</table>
	</div>
	<?php

	exit();
}

if($action=="load_php_yd_job_data_to_form")
{
	$sql = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type from yd_ord_mst a where a.yd_job='$data' and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1";

	$data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {
    	//echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";

    	//echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
    	//echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
    	//echo "$('#cbo_location_name').attr('disabled','disabled');\n";

    	//echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";

    	//echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
    	////echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
    	//echo "$('#cbo_party_name').attr('disabled','disabled');\n";

    	//echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";
    	//echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
    	//echo "$('#cbo_party_location').attr('disabled','disabled');\n";

    	echo "document.getElementById('txt_wo_no').value = '".$data[csf('order_no')]."';\n";
    	echo "document.getElementById('cbo_pro_type').value = '".$data[csf('pro_type')]."';\n";
    	echo "document.getElementById('cbo_order_type').value = '".$data[csf('order_type')]."';\n";

    	// $update_id = "'".$data[csf('id')]."'";

    	//echo "show_list_view(".$update_id.",'job_details_list_view','receive_details','requires/yd_delivery_entry_v2_controller','');\n";
    }
}

if($action=="load_php_yd_delivery_data_to_form")
{

	$data=explode('_', $data);

	$sql = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.receive_date, a.remarks from yd_store_receive_mst a where a.yd_receive='$data[1]' and a.status_active=1 and a.is_deleted=0 and a.entry_form=640";

	$data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {
    	echo "document.getElementById('hdn_update_id').value = '".$data[csf('id')]."';\n";
    	echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";
    	echo "document.getElementById('txt_delivery_date').value = '".change_date_format($data[csf('receive_date')])."';\n";

    	echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
    	echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
    	echo "$('#cbo_location_name').attr('disabled','disabled');\n";

    	echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";
    	echo "$('#cbo_within_group').attr('disabled','disabled');\n";

    	echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
    	echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
    	echo "$('#cbo_party_name').attr('disabled','disabled');\n";

    	echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_party_location', 'party_location_td' );\n";
    	echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
    	echo "$('#cbo_party_location').attr('disabled','disabled');\n";

    	echo "document.getElementById('txt_remarks').value = '".$data[csf('remarks')]."';\n";
    }
}

if($action=="receive_dtls_list_view")
{
	$data=explode('_', $data);
	
	$job_no = $data[0];
	$receive_no = $data[1];
	$cbo_company_name = $data[2];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 
	
	
	 	if($yarn_dyeing_process==1 && $service_process_id==1)
		{
 			 $pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$receive_no' and a.entry_form=640";

			$pre_del_data = sql_select($pre_del_sql);

			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')]; 
 			}
		}
		if($yarn_dyeing_process==13 && $service_process_id==3)
		{
 			 $pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$receive_no' and a.entry_form=640";

			$pre_del_data = sql_select($pre_del_sql);

			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')]; 
 			}
		}
		else
		{ 
 			   $pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and a.entry_form=640";

			$pre_del_data = sql_select($pre_del_sql);

			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')]; 
 			}
	
		}
	
	
	 
		
	
	
	//die;
	
	
	    if($yarn_dyeing_process==1 && $service_process_id==1)
		{
 			   $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process,b.lot,b.count_id, b.yarn_type_id, b.yarn_composition_id,b.uom,b.no_bag,b.process_loss, b.cone_per_bag, b.no_cone,b.order_quantity,b.order_quantity as receive_qty,b.adj_type, b.total_order_quantity,  
   item_color_id, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.buyer_buyer,b.yd_color_id from yd_ord_mst a,yd_ord_dtls b where a.id=b.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and a.yd_job='$receive_no'";
			   $result = sql_select($sql);
		}
		else if($yarn_dyeing_process==13 && $service_process_id==3)
		{
 			   $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process,b.lot,b.count_id, b.yarn_type_id, b.yarn_composition_id,b.uom,b.no_bag,b.process_loss, b.cone_per_bag, b.no_cone,b.order_quantity,b.order_quantity as receive_qty,b.adj_type, b.total_order_quantity, item_color_id, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.buyer_buyer,b.yd_color_id from yd_ord_mst a,yd_ord_dtls b , yd_inspection_mst c, yd_inspection_dtls d where a.id=b.mst_id and  d.mst_id = c.id AND d.SALES_ORDER_ID = b.SALES_ORDER_ID AND c.entry_form=443 AND a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.SYS_NUMBER='$receive_no'";
//    echo $sql; 
			   $result = sql_select($sql);
		}
		else
		{ 

			$sql = "select a.id, a.order_type, b.id as receive_dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty 
			from yd_store_receive_mst a, yd_store_receive_dtls b 
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$receive_no' and a.entry_form=571";
			$result = sql_select($sql);
			$sql1 = "select b.dtls_id, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and a.entry_form=640 group by b.dtls_id";
			$receive_result = sql_select($sql1);
	
		}

	$receive_array = array();

	foreach($receive_result as $data)
	{
		$receive_array[$data[csf('dtls_id')]]+= $data[csf('receive_qty')];
	}

	$readonly = '';
	if($receiveBasis==21){
		$readonly = "readonly";
	}

	$tblRow = 1;
	foreach($result as $data)
	{


		$prev_receive_qty = $pre_del_arr[$data[csf('receive_dtls_id')]]['receive_qty'];
 
		if($data[csf('order_type')]==1)
		{
			$current_delivery =number_format($data[csf('total_order_quantity')],2,'.','')-$prev_receive_qty;
			$order_qty = $data[csf('total_order_quantity')];
		}
		elseif($data[csf('order_type')]==2)
		{
			$current_delivery =number_format($data[csf('order_quantity')],2,'.','')-$prev_receive_qty;
			$order_qty = $data[csf('order_quantity')];
		}
		//$receive_qty = $receive_array[$data[csf('receive_dtls_id')]];
		
		$balance = $data[csf('receive_qty')]-$prev_receive_qty;
	?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>">
            </td>
            <td width="60">
            	<input style="width:60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
            	<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="60">
            	<input style="width:60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtlot[]" id="txtlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            	<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
            	<?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
            	<?
                   if ($within_group==2) 
                   {
                    	
                    	$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                	echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

            	<? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
            	<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
            	<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
            	<? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
            	<input style="width:40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
            </td>
            <td width="50">
            	<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

            	<? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtProcessLoss[]" id="txtProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenProcessLoss[]" id="txtHiddenProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            </td>
            <td width="80">
            	<input  readonly class="text_boxes" type="hidden" name="txtadjTypeId[]" id="txtadjTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('adj_type')];?>">
            	<?
                	echo create_drop_down( "txtadjType_".$tblRow, 80, $adj_type_arr,'', 1, '--- Select---',$data[csf('adj_type')], "",1,'','','','','','',"txtadjType[]");
                ?>
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalqty[]" id="txtTotalqty_<?php echo $tblRow;?>" value="<?php echo number_format( $order_qty,2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalqty[]" id="txtHiddenTotalqty_<?php echo $tblRow;?>" value="<?php echo number_format( $order_qty,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalReceiveqty[]" id="txtTotalReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('receive_qty')],2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalReceiveqty[]" id="txtHiddenTotalReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('receive_qty')],2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtPreviousReceiveqty[]" id="txtPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($prev_receive_qty,2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenPreviousReceiveqty[]" id="txtHiddenPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($receive_qty,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtbalanceqty[]" id="txtbalanceqty_<?php echo $tblRow;?>" value="<?php echo number_format($balance,2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenbalanceqty[]" id="txtHiddenbalanceqty_<?php echo $tblRow;?>" value="<?php echo number_format($balance,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric" type="text" onKeyup="calculate_receive_qty(this.id,this.value);" name="txtReceiveQty[]" id="txtReceiveQty_<?php echo $tblRow;?>"  placeholder="<?php echo number_format($current_delivery,2,'.',''); ?>" value="<?php echo number_format($current_delivery,2,'.','');  ?>">
            	<input class="text_boxes_numeric" type="hidden" name="hiddenOrderQty[]" id="hiddenOrderQty_<?php echo $tblRow;?>" value="<?php echo $order_qty;?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenDeliveryId[]" id="txtHiddenDeliveryId_<?php echo $tblRow;?>" value="">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_dtls_id')];?>">
            </td>
        </tr>
    <?php
    	$tblRow++;
	}

	exit();

}

if($action=="delivery_update_dtls_list_view")
{
	$data=explode('_', $data);
	
	$job_no = $data[0];
	$delivery_no = $data[1];
	$receive_no = $data[2];
	$cbo_company_name = $data[3];
	$update_id = $data[4];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 
	
	
	  if($yarn_dyeing_process==1 && $service_process_id==1)
		{
 			   $pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$receive_no' and b.mst_id!=$update_id and a.entry_form=640";
 			$pre_del_data = sql_select($pre_del_sql);
 			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')]; 
 			}
		}
		else
		{ 
 			   $pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.mst_id!=$update_id  and a.entry_form=640";
 			$pre_del_data = sql_select($pre_del_sql);
 			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')]; 
 			}
	
		}
	
	
	//die;
 	
	    if(($yarn_dyeing_process==1 && $service_process_id==1) || ($yarn_dyeing_process==13 && $service_process_id==3))
		{
 			  $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process,b.lot,b.count_id, b.yarn_type_id, b.yarn_composition_id,b.uom,b.no_bag,b.process_loss, b.cone_per_bag, b.no_cone,b.order_quantity,b.order_quantity as receive_qty,b.adj_type, b.total_order_quantity,  
   item_color_id, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.buyer_buyer,b.yd_color_id, a.yd_job  as store_receive,c.yd_receive from yd_ord_mst a,yd_ord_dtls b,yd_store_receive_mst c, yd_store_receive_dtls d  where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and c.yd_receive='$delivery_no' and c.entry_form=640  and d.dtls_id=b.id";  
   
   
   $current_delivery_sql = "select b.id as receive_dtls_id, b.gray_lot, b.no_bag, b.cone_per_bag, b.receive_qty, b.dtls_id from yd_store_receive_mst a, yd_store_receive_dtls b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$delivery_no' and b.job_no='$job_no' and a.entry_form=640";
			   
		}
		else
		{ 
	

	$sql = "select a.id, a.order_type, b.id as receive_dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$receive_no' and a.entry_form=571";
	
	$sql1 = "select b.dtls_id, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and a.entry_form=640 group by b.dtls_id";
	
	$current_delivery_sql = "select b.id as receive_dtls_id, b.gray_lot, b.no_bag, b.cone_per_bag, b.receive_qty, b.dtls_id from yd_store_receive_mst a, yd_store_receive_dtls b, yd_store_receive_mst c, yd_store_receive_dtls d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$delivery_no' and b.job_no='$job_no' and a.entry_form=640 and c.id=d.mst_id and b.dtls_id=d.id and c.entry_form=571 and c.yd_receive='$receive_no'";
		}

 
	$current_delivery_result = sql_select($current_delivery_sql);

	$current_delivery_arr = array();

	foreach($current_delivery_result as $data)
	{
		$current_delivery_arr[$data[csf('dtls_id')]]['receive_qty']+= $data[csf('receive_qty')];
		$current_delivery_arr[$data[csf('dtls_id')]]['receive_dtls_id']= $data[csf('receive_dtls_id')];
		$current_delivery_arr[$data[csf('dtls_id')]]['gray_lot']= $data[csf('gray_lot')];
		$current_delivery_arr[$data[csf('dtls_id')]]['no_bag']+= $data[csf('no_bag')];
		$current_delivery_arr[$data[csf('dtls_id')]]['cone_per_bag']+= $data[csf('cone_per_bag')];
	}
	$result = sql_select($sql);

	$receive_result = sql_select($sql1);

	$receive_array = array();

	foreach($receive_result as $data)
	{
		$receive_array[$data[csf('dtls_id')]]+= $data[csf('receive_qty')];
	}

	$readonly = '';
	if($receiveBasis==21){
		$readonly = "readonly";
	}

	$tblRow = 1;
	foreach($result as $data)
	{

		$prev_receive_qty = $pre_del_arr[$data[csf('receive_dtls_id')]]['receive_qty'];
 		if($data[csf('order_type')]==1)
		{
			$current_delivery =number_format($data[csf('total_order_quantity')],2,'.','')-$prev_receive_qty;
			$order_qty = $data[csf('total_order_quantity')];
		}
		elseif($data[csf('order_type')]==2)
		{
			$current_delivery =number_format($data[csf('order_quantity')],2,'.','')-$prev_receive_qty;
			$order_qty = $data[csf('order_quantity')];
		}
		 

		$delivery_qty = $current_delivery_arr[$data[csf('receive_dtls_id')]]['receive_qty'];
		$lot = $current_delivery_arr[$data[csf('receive_dtls_id')]]['gray_lot'];
		$no_bag = $current_delivery_arr[$data[csf('receive_dtls_id')]]['no_bag'];
		$cone_per_bag = $current_delivery_arr[$data[csf('receive_dtls_id')]]['cone_per_bag'];
		$receive_dtls_id = $current_delivery_arr[$data[csf('receive_dtls_id')]]['receive_dtls_id'];

		$balance = $data[csf('receive_qty')]-$prev_receive_qty;

		//$previous_delivery = $receive_array[$data[csf('receive_dtls_id')]];
	?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>">
            </td>
            <td width="60">
            	<input style="width:60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
            	<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="60">
            	<input style="width:60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtlot[]" id="txtlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $lot;?>">
            	<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $lot;?>">
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
            	<?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
            	<?
                   if ($within_group==2) 
                   {
                    	
                    	$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                	echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

            	<? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
            	<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
            	<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
            	<? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
            	<input style="width:40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $no_bag;?>">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $cone_per_bag;?>">
            </td>
            <td width="50">
            	<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

            	<? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtProcessLoss[]" id="txtProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenProcessLoss[]" id="txtHiddenProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            </td>
            <td width="80">
            	<input  readonly class="text_boxes" type="hidden" name="txtadjTypeId[]" id="txtadjTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('adj_type')];?>">
            	<?
                	echo create_drop_down( "txtadjType_".$tblRow, 80, $adj_type_arr,'', 1, '--- Select---',$data[csf('adj_type')], "",1,'','','','','','',"txtadjType[]");
                ?>
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalqty[]" id="txtTotalqty_<?php echo $tblRow;?>" value="<?php echo number_format( $order_qty,2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalqty[]" id="txtHiddenTotalqty_<?php echo $tblRow;?>" value="<?php echo number_format( $order_qty,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalReceiveqty[]" id="txtTotalReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('receive_qty')],2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalReceiveqty[]" id="txtHiddenTotalReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('receive_qty')],2,'.','');;?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtPreviousReceiveqty[]" id="txtPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format($prev_receive_qty,2,'.',''); ?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenPreviousReceiveqty[]" id="txtHiddenPreviousReceiveqty_<?php echo $tblRow;?>" value="<?php echo number_format( $prev_receive_qty,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtbalanceqty[]" id="txtbalanceqty_<?php echo $tblRow;?>" value="<?php echo number_format($balance,2,'.','');?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenbalanceqty[]" id="txtHiddenbalanceqty_<?php echo $tblRow;?>" value="<?php echo number_format($balance,2,'.','');?>">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric" type="text" onKeyup="calculate_receive_qty(this.id,this.value);" name="txtReceiveQty[]" id="txtReceiveQty_<?php echo $tblRow;?>" placeholder="<?php echo number_format($current_delivery,2,'.','');  ?>" value="<?php echo number_format($delivery_qty,2,'.','');  ?>">
            	<input class="text_boxes_numeric" type="hidden" name="hiddenOrderQty[]" id="hiddenOrderQty_<?php echo $tblRow;?>" value="<?php echo $order_qty;?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenDeliveryId[]" id="txtHiddenDeliveryId_<?php echo $tblRow;?>" value="<?php echo $receive_dtls_id; ?>">
            	<input class="text_boxes_numeric" type="hidden" name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_dtls_id')];?>">
            </td>
        </tr>
    <?php
    	$tblRow++;
	}

	exit();

}


if($action=="dtls_list_view")
{
	$data=explode('_', $data);
	
	$party_id = $data[0];
	$receive_ids = $data[1];
	$cbo_company_name = $data[2];

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	
	
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 
	
	//die;
	
	
	  if($yarn_dyeing_process==1 && $service_process_id==1)
		{
			  $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no,a.yd_job as yd_receive, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no from yd_ord_mst a,yd_ord_dtls b where a.id=b.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and a.party_id='$party_id' and b.id in($receive_ids)  group by  a.id, a.yd_job, a.job_no_prefix_num,a.within_group, a.insert_date,a.party_id, a.location_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.id order by a.id DESC";
		}
		else if($yarn_dyeing_process==13 && $service_process_id==3)
		{
			// "SELECT a.SYS_NUMBER          AS yd_receive, a.id, b.id                  AS receive_dtls_id, d.style_ref, a.party_id, c.pro_type, a.within_group, c.yd_job              AS job_no, d.order_no, d.order_id, c.order_type, a.INSPECTION_DATE     AS receive_date, d.count_type, b.SALES_ORDER_ID      AS sales_order_no 
			// FROM yd_inspection_mst a, yd_inspection_dtls b, yd_ord_mst        c, yd_ord_dtls       d 
			// WHERE     a.id = b.mst_id AND d.mst_id = c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.SALES_ORDER_ID = d.SALES_ORDER_ID AND b.job_dtls_id = d.id AND a.entry_form = 443 AND a.company_id = 3 AND a.within_group = 1 AND a.party_id = 3 AND c.order_type = 2 
			// GROUP BY a.SYS_NUMBER, a.id, b.id, d.style_ref, a.party_id, c.pro_type, a.within_group, c.yd_job, d.order_no, d.order_id, c.order_type, a.INSPECTION_DATE, d.count_type, b.SALES_ORDER_ID ORDER BY a.id DESC";
			
			$sql= "select a.id,  c.yd_job as job_no,a.SYS_NUMBER as yd_receive,  c.party_id,  c.order_no, c.order_type, c.yd_type,c.pro_type 
			from yd_inspection_mst a, yd_inspection_dtls b, yd_ord_mst  c, yd_ord_dtls d 
			where a.id=b.mst_id AND d.mst_id = c.id AND b.SALES_ORDER_ID = d.SALES_ORDER_ID AND b.job_dtls_id = d.id and a.entry_form=443  and c.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.party_id='$party_id' and b.id in($receive_ids)  group by  a.id, c.yd_job, a.SYS_NUMBER,c.party_id,  c.order_no ,c.order_type, c.yd_type,c.pro_type order by a.id DESC";
		}
		else
		{     

	$sql = "select a.yd_receive, a.party_id, a.job_no, a.order_type, a.pro_type, a.order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_id='$party_id' and b.id in($receive_ids) and a.entry_form=571 group by a.yd_receive, a.party_id, a.job_no, a.order_type, a.pro_type, a.order_no";
		}
// echo $sql;
	$result = sql_select($sql);
	?>

	<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="35">Sl</th>
			<th width="150">Party Name</th>
			<th width="150">Receive No</th>
			<th width="150">Job No</th>
			<th width="100">WO No</th>
			<th width="100">Prod. Type</th>
			<th width="100">Order Type</th>
		</thead>
		<tbody id="tbl_list_view">
	<?php

	$tblRow = 1;
	foreach($result as $data)
	{
		if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		if($data[csf('within_group')]==1)
						{
			$party_name = $comp_arr[$data[csf('party_id')]];

		}
		else
		{
			$party_name = $party_arr[$data[csf('party_id')]];
		}
	?>
        <tr style="cursor:pointer" id="dtls_row_<?php echo $tblRow;?>" bgcolor="<? echo $bgcolor; ?>" onClick="load_receive_data('<?php echo $data[csf('job_no')];?>','<?php echo $data[csf('yd_receive')];?>',<?php echo $tblRow;?>)">
        	<td width="35" align="center"><?php echo $tblRow;?></td>
            <td align="center" width="150">
            	<?php echo $party_name;?>
            </td>
            <td width="150" align="center">
            	<?php echo $data[csf('yd_receive')];?>
            </td>
            <td width="150" align="center">
            	<?php echo $data[csf('job_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $data[csf('order_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_pro_type_arr[$data[csf('pro_type')]];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_order_type_arr[$data[csf('order_type')]];?>
            </td>
        </tr>
    <?php
    	$tblRow++;
	}
	?>
	</tbody>
	</table>
	<?php
	exit();

}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $user_id=$_SESSION['logic_erp']['user_id'];

    if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

	    $txt_job_no   				= str_replace("'",'',$txt_job_no);
	    $txt_delivery_date 			= str_replace("'",'',$txt_delivery_date);
	    $cbo_company_name    		= str_replace("'",'',$cbo_company_name);
	    $cbo_location_name    		= str_replace("'",'',$cbo_location_name);
	    $cbo_within_group    		= str_replace("'",'',$cbo_within_group);
	    $cbo_party_name    			= str_replace("'",'',$cbo_party_name);
	    $cbo_party_location   		= str_replace("'",'',$cbo_party_location);
	    $txt_wo_no   				= str_replace("'",'',$txt_wo_no);
	    $cbo_pro_type 				= str_replace("'",'',$cbo_pro_type);
	    $cbo_order_type    			= str_replace("'",'',$cbo_order_type);
	    $hdn_update_id    			= str_replace("'",'',$hdn_update_id);
	   	$txt_delivery_no    		= str_replace("'",'',$txt_delivery_no);

	    if($db_type==0){
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
        }else{
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
        }

        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
	    else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

	    $pre_del_arr =array();

	    if(str_replace("'","",$hdn_update_id)=="")
		{

			$id=return_next_id("id","yd_store_receive_mst",1);

			$txt_delivery_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDD', date("Y",time()), 5, "select receive_no_prefix,receive_no_prefix_num from yd_store_receive_mst where entry_form=640 and company_id=$cbo_company_name $insert_date_con and status_active=1 and is_deleted=0 order by id desc ", "receive_no_prefix", "receive_no_prefix_num" ));

	    	$field_array="id, entry_form, yd_receive, receive_no_prefix, receive_no_prefix_num, receive_date, company_id, location_id, within_group, party_id,party_location,remarks,inserted_by, insert_date";

	    	$data_array="(".$id.", 640, '".$txt_delivery_no[0]."', '".$txt_delivery_no[1]."', '".$txt_delivery_no[2]."', '".$txt_delivery_date."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

	    	$txt_delivery_no=$txt_delivery_no[0];
		}
		else
		{
			$id = $hdn_update_id;
			$txt_delivery_no=$txt_delivery_no;

			$pre_del_sql = "select b.id, a.yd_receive, b.dtls_id, b.receive_qty, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$txt_delivery_no' and a.id=$id and a.entry_form=640";

			$pre_del_data = sql_select($pre_del_sql);

			foreach($pre_del_data as $data)
			{
				$pre_del_arr[$data[csf('yd_receive')]][$data[csf('job_no')]][$data[csf('dtls_id')]]['receive_qty'] += $data[csf('receive_qty')];

				$pre_del_arr[$data[csf('yd_receive')]][$data[csf('job_no')]][$data[csf('dtls_id')]]['id'] = $data[csf('id')];
			}
		}

		$id1=return_next_id( "id", "yd_store_receive_dtls",1);

	    $field_array2="id, mst_id, receive_no_mst, style_ref, sales_order_no, sales_order_id, buyer_buyer, lot, gray_lot, count_type, count_id, yarn_type_id, yarn_composition_id, yd_color_id, no_bag, cone_per_bag, uom, order_quantity, process_loss, adj_type, total_order_quantity, receive_qty, dtls_id, job_no, order_no, pro_type, order_type, inserted_by, insert_date";

	    $field_array3="style_ref*sales_order_no*sales_order_id*buyer_buyer*lot*gray_lot*count_type*count_id* yarn_type_id*yarn_composition_id*yd_color_id*no_bag*cone_per_bag*uom*order_quantity*process_loss*adj_type* total_order_quantity*receive_qty*dtls_id*updated_by*update_date";

	    $data_array2=""; $add_commaa=0;
	    for($i=1; $i<=$total_row; $i++)
	    {
	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtProcessLoss         = "txtProcessLoss_".$i;          
	        $txtadjTypeId           = "txtadjTypeId_".$i;
	        $txtHiddenTotalqty      = "txtHiddenTotalqty_".$i;
	        $previousReceiveqty     = "previousReceiveqty_".$i;
	        $txtReceiveQty          = "txtReceiveQty_".$i;
	        $txtHiddenReceiveId     = "txtHiddenReceiveId_".$i;
	        $hiddenOrderQty     	= "hiddenOrderQty_".$i;
	        $txtHiddenDeliveryId    = "txtHiddenDeliveryId_".$i;
	        $txtHiddenDtlsId     	= "txtHiddenDtlsId_".$i;
	        $txtTotalReceiveqty    	= "txtTotalReceiveqty_".$i;

	        $hidden_dtls_id = str_replace("'","",$$txtHiddenDtlsId);


	        if($pre_del_arr!='')
	        {
	        	if($pre_del_arr[$txt_delivery_no][$txt_job_no][$hidden_dtls_id]['receive_qty']!='')
	        	{
	        		$cu_receive_qty = str_replace("'","",$$txtReceiveQty);
	        		$dtlsUpdateId = $pre_del_arr[$txt_delivery_no][$txt_job_no][$hidden_dtls_id]['id'];
	        		$receive_qty = $pre_del_arr[$txt_delivery_no][$txt_job_no][$hidden_dtls_id]['receive_qty']+$cu_receive_qty;

	        		$data_array3[$dtlsUpdateId]=explode("*",("".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$buyerBuyer."*".$$txtlot."*".$$txtGrayLot."*".$$txtcountTypeId."*".$$txtcountId."*".$$cboYarnTypeId."*".$$txtydCompositionId."*".$$txtYarnColorId."*".$$txtnoBag."*".$$txtConeBag."*".$$cboUomId."*".$$txtOrderqty."*".$$txtProcessLoss."*".$$txtadjTypeId."*".$$txtHiddenTotalqty."*".$receive_qty."*".$$txtHiddenDtlsId."*".$user_id."*'".$pc_date_time."'"));

                	$hdn_dtls_id_arr[]=str_replace("'",'',$dtlsUpdateId);
	        	}
	        	else
	        	{
	        		if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

			        $data_array2 .="(".$id1.",".$id.",'".$txt_delivery_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtProcessLoss.",".$$txtadjTypeId.",".$$txtHiddenTotalqty.",".$$txtReceiveQty.",".$$txtHiddenDtlsId.",'".$txt_job_no."','".$txt_wo_no."',".$cbo_pro_type.",".$cbo_order_type.",'".$user_id."','".$pc_date_time."')";

		           	$id1=$id1+1; $add_commaa++;
	        	}
	        }
	        else
	        {
		        if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

		        $data_array2 .="(".$id1.",".$id.",'".$txt_delivery_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtProcessLoss.",".$$txtadjTypeId.",".$$txtHiddenTotalqty.",".$$txtReceiveQty.",".$$txtHiddenDtlsId.",'".$txt_job_no."','".$txt_wo_no."',".$cbo_pro_type.",".$cbo_order_type.",'".$user_id."','".$pc_date_time."')";

	           	$id1=$id1+1; $add_commaa++;
           }
	    }

	    $flag=true;

	    if($data_array!='')
	    {
	    	//echo "10**INSERT INTO yd_store_receive_mst (".$field_array.") VALUES ".$data_array; die;
		    $rID=sql_insert("yd_store_receive_mst",$field_array,$data_array,1);

		    if($rID==1) $flag=1; else $flag=0;
	    }

	    if($data_array3!="" && $flag==1)
        {
			//echo "10**".bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array3,$data_array3,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($flag==1 && $data_array2!=''){

        	//echo "10**INSERT INTO yd_store_receive_dtls (".$field_array2.") VALUES ".$data_array2; die;
            $rID3=sql_insert("yd_store_receive_dtls",$field_array2,$data_array2,1);
            if($rID3==1) $flag=1; else $flag=0;
        }

        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "0**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){

		        oci_commit($con);
		        echo "0**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
		    }
		}
        
        disconnect($con);
        die;
    }
    elseif ($operation==1) // Update Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $txt_job_no   				= str_replace("'",'',$txt_job_no);
	    $txt_delivery_date 			= str_replace("'",'',$txt_delivery_date);
	    $cbo_company_name    		= str_replace("'",'',$cbo_company_name);
	    $cbo_location_name    		= str_replace("'",'',$cbo_location_name);
	    $cbo_within_group    		= str_replace("'",'',$cbo_within_group);
	    $cbo_party_name    			= str_replace("'",'',$cbo_party_name);
	    $cbo_party_location   		= str_replace("'",'',$cbo_party_location);
	    $txt_wo_no   				= str_replace("'",'',$txt_wo_no);
	    $cbo_pro_type 				= str_replace("'",'',$cbo_pro_type);
	    $cbo_order_type    			= str_replace("'",'',$cbo_order_type);
	    $hdn_update_id    			= str_replace("'",'',$hdn_update_id);
	    $txt_delivery_no    		= str_replace("'",'',$txt_delivery_no);

	    if($db_type==0){
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
        }else{
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
        }

        $field_array="job_no*receive_date*company_id*location_id*within_group*party_id*party_location*order_no*pro_type*order_type*remarks*updated_by*update_date";

        $data_array="'".$txt_job_no."'*'".$txt_delivery_date."'*'".$cbo_company_name."'*'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$txt_wo_no."'*'".$cbo_order_type."'*'".$cbo_pro_type."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array2="style_ref*sales_order_no*sales_order_id*buyer_buyer*lot*gray_lot*count_type*count_id* yarn_type_id*yarn_composition_id*yd_color_id*no_bag*cone_per_bag*uom*order_quantity*process_loss*adj_type* total_order_quantity*receive_qty*dtls_id*updated_by*update_date";

        $field_array3="id, mst_id, receive_no_mst, style_ref, sales_order_no, sales_order_id, buyer_buyer, lot, gray_lot, count_type, count_id, yarn_type_id, yarn_composition_id, yd_color_id, no_bag, cone_per_bag, uom, order_quantity, process_loss, adj_type, total_order_quantity, receive_qty, dtls_id, job_no, order_no, pro_type, order_type, inserted_by, insert_date";

        $data_array2=array(); $add_commaa=0; $receive_qty=0;$detailsOrderqty =0; $data_array3 ='';

        $id1=return_next_id( "id", "yd_store_receive_dtls",1);

        for($i=1; $i<=$total_row; $i++)
	    {

	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtProcessLoss         = "txtProcessLoss_".$i;          
	        $txtadjTypeId           = "txtadjTypeId_".$i;
	        $txtHiddenTotalqty      = "txtHiddenTotalqty_".$i;
	        $previousReceiveqty     = "previousReceiveqty_".$i;
	        $txtReceiveQty          = "txtReceiveQty_".$i;
	        $txtHiddenReceiveId     = "txtHiddenReceiveId_".$i;
	        $hiddenOrderQty     	= "hiddenOrderQty_".$i;
	        $txtHiddenDeliveryId    = "txtHiddenDeliveryId_".$i;
	        $txtHiddenDtlsId     	= "txtHiddenDtlsId_".$i;
	        $txtTotalReceiveqty    	= "txtTotalReceiveqty_".$i;

	        $dtlsUpdateId =str_replace("'",'',$$txtHiddenDeliveryId);

	        if(str_replace("'",'',$$txtHiddenDeliveryId)!="")
            {
            	$data_array2[$dtlsUpdateId]=explode("*",("".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$buyerBuyer."*".$$txtlot."*".$$txtGrayLot."*".$$txtcountTypeId."*".$$txtcountId."*".$$cboYarnTypeId."*".$$txtydCompositionId."*".$$txtYarnColorId."*".$$txtnoBag."*".$$txtConeBag."*".$$cboUomId."*".$$txtOrderqty."*".$$txtProcessLoss."*".$$txtadjTypeId."*".$$txtHiddenTotalqty."*".$$txtReceiveQty."*".$$txtHiddenDtlsId."*".$user_id."*'".$pc_date_time."'"));

                $hdn_dtls_id_arr[]=str_replace("'",'',$dtlsUpdateId);
            }
            else
        	{
        		if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

		        $data_array3 .="(".$id1.",".$hdn_update_id.",'".$txt_delivery_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtProcessLoss.",".$$txtadjTypeId.",".$$txtHiddenTotalqty.",".$$txtReceiveQty.",".$$txtHiddenDtlsId.",'".$txt_job_no."','".$txt_wo_no."',".$cbo_pro_type.",".$cbo_order_type.",'".$user_id."','".$pc_date_time."')";

	           	$id1=$id1+1; $add_commaa++;
        	}
	    }

	    $flag=true;
        $rID=sql_update("yd_store_receive_mst",$field_array,$data_array,"id",$hdn_update_id,0);
        if($rID) $flag=1; else $flag=0;

        if($data_array2!="" && $flag==1)
        {
			//echo "10**".bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "yd_store_receive_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($flag==1 && $data_array3!=''){

        	//echo "10**INSERT INTO yd_store_receive_dtls (".$field_array2.") VALUES ".$data_array2; die;
            $rID3=sql_insert("yd_store_receive_dtls",$field_array3,$data_array3,1);
            if($rID3==1) $flag=1; else $flag=0;
        }

        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "1**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){
		        oci_commit($con);
		        echo "1**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$hdn_update_id)."**".str_replace("'",'',$txt_job_no);
		    }
		}
        
        disconnect($con);
        die;

    }

    elseif ($operation==2) // Update Start Here
    {
    	$con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }


        $update_id    = str_replace("'",'',$hdn_update_id);
        $txt_job_no   				= str_replace("'",'',$txt_job_no);;
	   	$txt_delivery_no    			= str_replace("'",'',$txt_delivery_no);

        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $sql = "select c.yd_receive as store_receive from yd_store_receive_mst a, yd_store_receive_dtls b, yd_store_receive_mst c, yd_store_receive_dtls d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$update_id and a.entry_form=640 and c.entry_form=571 and c.id=d.mst_id and b.dtls_id=d.id group by c.yd_receive";
    	
    	$delivery_sql = sql_select($sql);

    	$total_details = count($delivery_sql);

    	if (count($delivery_sql)==1) {
    		$rID=sql_update("yd_store_receive_mst",$field_array,$data_array,"id",$update_id,0);
    	}
    	else
    	{
    		$rID = 1;
    	}

        if($rID) $flag=1; else $flag=0; 

        for($i=1; $i<=$total_row; $i++)
	    {

	        $txtHiddenDeliveryId    = "txtHiddenDeliveryId_".$i;
	        $dtlsUpdateId =str_replace("'",'',$$txtHiddenDeliveryId);

	        if($flag==1)
	        {
	            $rID1=sql_update("yd_store_receive_dtls",$field_array,$data_array,"id",$dtlsUpdateId,1);
	            if($rID1) $flag=1; else $flag=0; 
	        }
	    }
         
        
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".$total_details;
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
                echo "2**".str_replace("'",'',$txt_delivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".$total_details;
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

if($action=="delivery_dtls_list_view")
{
	$data=explode('_', $data);
	
	$update_id = $data[0];
	$cbo_company_name = $data[2];

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 
	
	//die;
 	
	    if($yarn_dyeing_process==1 && $service_process_id==1)
		{
 			  $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process,b.lot,b.count_id, b.yarn_type_id, b.yarn_composition_id,b.uom,b.no_bag,b.process_loss, b.cone_per_bag, b.no_cone,b.order_quantity,b.order_quantity as receive_qty,b.adj_type, b.total_order_quantity,  
   item_color_id, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.buyer_buyer,b.yd_color_id, a.yd_job  as store_receive,c.yd_receive from yd_ord_mst a,yd_ord_dtls b,yd_store_receive_mst c, yd_store_receive_dtls d  where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and c.id=$update_id and c.entry_form=640  and d.dtls_id=b.id";  ;
			   
		}
		if($yarn_dyeing_process==13 && $service_process_id==3)
		{
 			  $sql= "select a.id, b.id as receive_dtls_id, a.yd_job as job_no, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type,a.yd_process,b.lot,b.count_id, b.yarn_type_id, b.yarn_composition_id,b.uom,b.no_bag,b.process_loss, b.cone_per_bag, b.no_cone,b.order_quantity,b.order_quantity as receive_qty,b.adj_type, b.total_order_quantity,  item_color_id, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no,b.buyer_buyer,b.yd_color_id, a.yd_job  as store_receive,c.yd_receive from yd_ord_mst a,yd_ord_dtls b,yd_store_receive_mst c, yd_store_receive_dtls d  where a.id=b.mst_id and c.id=d.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and c.id=$update_id and c.entry_form=640  and d.dtls_id=b.id";  
   			//echo $sql;
			   
		}
		else
		{ 
	

	$sql = "select a.id, a.yd_receive, a.party_id, b.job_no, b.order_type, b.pro_type, b.order_no, c.yd_receive as store_receive from yd_store_receive_mst a, yd_store_receive_dtls b, yd_store_receive_mst c, yd_store_receive_dtls d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$update_id and a.entry_form=640 and c.entry_form=571 and c.id=d.mst_id and b.dtls_id=d.id group by a.id, a.yd_receive, a.party_id, b.job_no, b.order_type, b.pro_type, b.order_no, c.yd_receive order by b.job_no";
	
		}

	$result = sql_select($sql);

	$tblRow = 1;
	foreach($result as $data)
	{
		if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		if($data[csf('within_group')]==1)
						{
			$party_name = $comp_arr[$data[csf('party_id')]];

		}
		else
		{
			$party_name = $party_arr[$data[csf('party_id')]];
		}
	?>
        <tr style="cursor:pointer" id="row_<?php echo $tblRow;?>" bgcolor="<? echo $bgcolor; ?>" onClick="load_delivery_data('<?php echo $data[csf('job_no')];?>','<?php echo $data[csf('yd_receive')];?>',<?php echo $data[csf('pro_type')];?>,<?php echo $data[csf('order_type')];?>,'<?php echo $data[csf('order_no')];?>','<?php echo $data[csf('store_receive')];?>')">
        	<td width="35" align="center"><?php echo $tblRow;?></td>
            <td align="center" width="150">
            	<?php echo $party_name;?>
            </td>
            <td id="td_<?php echo $tblRow;?>" width="150" align="center">
            	<?php echo $data[csf('yd_receive')];?>
            	<p id="receiveId_<?php echo $tblRow;?>" style="display: none;"><?php echo $data[csf('id')];?></p>
            </td>
            <td width="150" align="center">
            	<?php echo $data[csf('job_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $data[csf('order_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_pro_type_arr[$data[csf('pro_type')]];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_order_type_arr[$data[csf('order_type')]];?>
            </td>
            <td width="100" align="center">
            	<?php echo $data[csf('store_receive')];?>
            </td>
        </tr>
    <?php
    	$tblRow++;
	}
	exit();

}

if($action == "job_search_popup_delivery")
{
	echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	extract($_REQUEST);

	?>
	<script>
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}

		function js_set_value(id,job_no,delivery_no)
		{ 
			$("#hidden_mst_id").val(id);
			$("#hidden_job_no").val(job_no);
			$("#hidden_delivery_no").val(delivery_no);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
					<thead>
		                <tr>
		                    <th colspan="11"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
		                </tr>
		                <tr>
		                    <th width="120" class="must_entry_caption" >Company Name</th>
		                    <th width="80" class="must_entry_caption" >Within Group</th>
		                    <th width="120">Party Name</th>
		                    <th width="120">Delivery No</th>
		                    <th width="80">Search By</th>
		                    <th width="80" id="search_by_td">YD Job No</th>
		                    <th width="70">Prod. Type</th>
		                    <th width="70">Order Type</th>
		                    <th width="70">Y/D Type</th>
		                    <th width="160">Delivery Date Range</th>
		                    <th>
		                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
                		<tr class="general">
                			<td>
		                        <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'yd_delivery_entry_v2_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );"); ?>
		                    </td>
		                    <td> 
	                            <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'yd_delivery_entry_v2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
	                        </td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1); ?>
	                        </td>
	                        <td > 
	                            <input name="txt_delivery_no" id="txt_delivery_no" class="text_boxes" style="width:80px" placeholder="Write Receive No">
	                        </td>
	                        <td>
	                        	<?
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
									echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
	                        </td>
	                        <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
	                        	<? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$selected,'',0 );?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
	                        	<? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
	                        </td>
	                        <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_delivery_no').value, 'create_receive_search_list_view', 'search_div', 'yd_delivery_entry_v2_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:70px;" />
                            </td>
                		</tr>
                		<tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                                <input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" style="width:70px">
                                <input type="hidden" name="hidden_delivery_no" id="hidden_delivery_no" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                	</tbody>
		        </table>
			</form>
		</div>
		<div id="search_div" align="center">
			
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?php
}

if($action=="create_receive_search_list_view")
{	
	$data=explode('_',$data);

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

	$search_type 			=trim(str_replace("'","",$data[0]));
	$cbo_company_name  		=trim(str_replace("'","",$data[1]));
	$cbo_within_group 		=trim(str_replace("'","",$data[2]));
	$cbo_party_name 		=trim(str_replace("'","",$data[3]));
	$search_by 				=trim(str_replace("'","",$data[4]));
	$search_str 			=trim(str_replace("'","",$data[5]));
	$cbo_pro_type 			=trim(str_replace("'","",$data[6]));
	$cbo_order_type 		=trim(str_replace("'","",$data[7]));
	$cbo_yd_type 			=trim(str_replace("'","",$data[8]));
	$txt_date_from 			=trim(str_replace("'","",$data[9]));
	$txt_date_to 			=trim(str_replace("'","",$data[10]));
	$cbo_year_selection 	=trim(str_replace("'","",$data[11]));
	$txt_receive_no 		=trim(str_replace("'","",$data[12]));

	if($cbo_company_name==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Company Name first!!!</p>";
		die;
	}

	if($cbo_within_group==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Within Group first!!!</p>";
		die;
	}

	$condition = "";

	if($cbo_company_name!=0)
	{
		$condition .= " and a.company_id=$cbo_company_name";
	}

	if($txt_receive_no!=0)
	{
		$condition .= " and a.receive_no_prefix_num=$txt_receive_no";
	}

	if($cbo_within_group!=0)
	{
		$condition .= " and a.within_group=$cbo_within_group";
	}

	if($cbo_party_name!=0)
	{
		$condition .= " and a.party_id=$cbo_party_name";
	}

	if($cbo_pro_type!=0)
	{
		$condition .= " and b.pro_type=$cbo_pro_type";
	}

	if($cbo_order_type!=0)
	{
		$condition .= " and b.order_type=$cbo_order_type";
	}

	if($cbo_yd_type!=0)
	{
		$condition .= " and a.yd_type=$cbo_yd_type";
	}


	$date_con = '';
	if($db_type==0)
    { 
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and b.job_no='$search_str'";
			else if($search_by==2) $condition="and b.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and b.job_no like '$search_str%'";
			else if($search_by==2) $condition="and b.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and b.job_no like '%$search_str'";
			else if($search_by==2) $condition="and b.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and b.job_no like '%$search_str%'";
			else if($search_by==2) $condition="and b.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }


	$sql = "select a.yd_receive, a.id, b.style_ref, a.party_id, b.pro_type, a.within_group, b.job_no, b.order_no, a.order_id, b.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=640 $condition $date_con group by a.yd_receive, a.id, b.style_ref, a.party_id, b.pro_type, a.within_group, b.job_no, b.order_no, a.order_id, b.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.id desc";

	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="995" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Delivery No</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">Count Type</th>
            <th width="100">Delivery Date</th>
        </thead>
	</table>
	<div style="width:996px; max-height:370px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="995" >
			<tbody>
				<?php
					$i=1;
					$count_type_arr = array(1 => "Single",2 => "Double");
					foreach($result as $data)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($data[csf('within_group')]==1)
						{
							$party_name = $comp_arr[$data[csf('party_id')]];

						}
						else
						{
							$party_name = $party_arr[$data[csf('party_id')]];
						}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value(<? echo $data[csf('id')]; ?>,"<? echo $data[csf('job_no')]; ?>","<? echo $data[csf('yd_receive')]; ?>")' style="cursor:pointer">
					<td align="center" width="30"><? echo $i; ?></td>
		            <td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('yd_receive')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('receive_date')]; ?></td>
				</tr>
				<?php
					$i++;
					}
				?>
	        </tbody>
		</table>
	</div>
	<?php

	exit();
}


if ($action=="embl_delivery_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$template_id = $data[4];
	$com_id 	 = $data[0];
 
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$plot_arr = return_library_array("select id, plot_no from lib_company", 'id', 'plot_no');
	$city_arr = return_library_array("select id, city from lib_company", 'id', 'city');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr=return_library_array( "select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$count_type_arr = array(1 => "Single",2 => "Double");

	$sql_mst = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type, a.receive_date, a.yd_receive, a.inserted_by, a.remarks from yd_store_receive_mst a where a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and a.entry_form=640";

	$dataArray = sql_select($sql_mst);

	$delivery_no = $dataArray[0][csf('yd_receive')];
	$company_name = $company_library[$data[0]];

/*	$sql = "select b.order_no, b.style_ref, b.sales_order_no, b.buyer_buyer, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.uom, b.job_no, sum(b.order_quantity) as order_quantity, sum(b.total_order_quantity) as total_order_quantity, sum(b.receive_qty) as receive_qty, c.id as store_receive, b.dtls_id, b.process_loss, b.order_type from yd_store_receive_mst a, yd_store_receive_dtls b, yd_store_receive_mst c, yd_store_receive_dtls d where a.id=b.mst_id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$delivery_no' and a.entry_form=640 and c.entry_form=571 and b.dtls_id=d.id group by b.order_no, b.style_ref, b.sales_order_no, b.buyer_buyer, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.uom, b.job_no, c.id, b.dtls_id, b.process_loss, b.order_type order by c.id, b.job_no, b.dtls_id";*/


	 $sql = "select b.order_no, b.style_ref, b.sales_order_no, b.buyer_buyer, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.job_no, sum(b.order_quantity) as order_quantity, sum(b.total_order_quantity) as total_order_quantity, sum(b.receive_qty) as receive_qty, b.id as store_receive, b.dtls_id, b.process_loss, b.order_type from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$delivery_no' and a.entry_form=640   group by b.order_no, b.style_ref, b.sales_order_no, b.buyer_buyer, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.job_no, b.id, b.dtls_id, b.process_loss, b.order_type order by b.id, b.job_no, b.dtls_id";

	$sql_res=sql_select($sql);

	$delivery_type = $sql_res[0][csf('order_type')];

	$within_group = $dataArray[0][csf('within_group')];
	$party_id = $dataArray[0][csf('party_id')];
	for($j=1;$j<=3;$j++){
	?>
	<style>
		div{
			font-size:18px;
		}
		.breakAfter {
        page-break-after: always;
    	}
	</style>
    <div style="width:1220px;">
    	<table width="1220" cellpadding="0" cellspacing="0" >
            <tr>
                <td style="display:none;" width="70" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center" style="padding-left:100px;">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_name; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:14px"><strong>Address : </strong><? echo $plot_arr[$dataArray[0][csf('company_id')]].", ".$city_arr[$dataArray[0][csf('company_id')]]; ?></td>
                        </tr>
                        <tr style="display:none;" class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                    </table>
                </td>
				<td width="100" align="right">
					<strong>
					<? 
					if($j==1){
						echo "1st Copy";
					}else if($j==2){
						echo "2nd Copy";
					}else{
						echo "3rd Copy";
					}
					?>
					</strong>
				</td>
            </tr>
        </table>
        <hr>
        <table width="1220" align="center" cellpadding="0" cellspacing="0">
        	<tr>
                <td align="center" style="font-size:18px"><strong>Delivery Challan</strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:18px"><strong>Delivery Type: <?php echo $w_order_type_arr[$delivery_type]; ?></strong></td>
            </tr>
        </table>
        <br>
        <table width="800" cellpadding="5" cellspacing="0" border="1" rules="all" >  
            <tr>
            	<td width="150"><strong>Date:</strong></td>
                <td colspan="3"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>To,(Customer Name):	</strong></td>
            	<td colspan="3">
                	<?php
                		
                		if($dataArray[0][csf('within_group')]==1){
                			
                			$party_name=$company_library[$dataArray[0][csf('party_id')]];
                		}
                		else{

                			$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
                		}
                		echo $party_name;	
                	?>	
                </td>
            </tr>
            <tr>
            	<td><strong>Address: </strong></td>
            	<td colspan="3"><?php
		            	if( $dataArray[0][csf('within_group')]==1)
						{
							
							$party_address=show_company($dataArray[0][csf('party_id')],'','');
						}
						else
						{
							$party_id=$dataArray[0][csf('party_id')];
							$nameArray=sql_select( "SELECT address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_id"); 
							foreach ($nameArray as $result)
							{ 
								if($result!="") $party_address=$result[csf('address_1')];
							}
						}
            			echo $party_address;
            		?>
            			
            	</td>
            </tr>
            <tr>
            	<td><strong>Delivery No: </strong></td>
            	<td colspan="3"><?php echo $dataArray[0][csf('yd_receive')]; ?></td>
            </tr>
			<tr>
				<td><strong>Driver Name:</strong></td>
				<td width="250">&nbsp;</td>
				<td width="150"><strong> Vahicle No:</strong></td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td colspan="3"><?php echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
        </table>
    </div>
    <br>
    <div style="width:100%;">
        <table align="left" cellspacing="0" width="1220" border="1" rules="all" class="rpt_table" style="font-size:13px">
        	<thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                <th width="30">SL</th>
                <th width="100">Buyer Style</th>
                <th width="100">Buyer Job</th>
                <th width="100">WO</th>
                <th width="90">Cust:Buyer</th>
                <th width="300">Particular</th>
                <th width="80">Color</th>
                <th width="80">Batch</th>
                <th width="80">Order UOM</th>
                <th width="70">Order Qty.</th>
                <th width="100">Dlv Grey Qty.</th>
                <th width="100">Dlv Finish Qty.</th>
                <th width="60">No of Bag</th>
                <th width="60">No of Cone</th>
                <th width="150">Internal Job No</th>
            </thead>
            <tbody>
            	<?php

            		$sql1="select a.yd_job, sum(b.order_quantity) as order_quantity from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.company_id=$com_id[0] and a.within_group=$within_group and a.status_active=1 and a.is_deleted=0 and a.entry_form=374 group by a.yd_job";

            		$job_res=sql_select($sql1);

            		$job_qty_arr = array();
            		foreach ($job_res as $data)
            		{

            			$job_qty_arr[$data[csf('yd_job')]] = $data[csf('order_quantity')];
            		}

            		$order_job_arr = array();
            		foreach ($sql_res as $data)
            		{

            			$order_job_arr[$data[csf('job_no')]]['job_no'] = $data[csf('job_no')];
            			$order_job_arr[$data[csf('job_no')]]['order_quantity'] = $job_qty_arr[$data[csf('job_no')]];
            			$order_job_arr[$data[csf('job_no')]]['count'] += 1;
            		}

            		$i=1; $grand_tot_qty=0; $grand_tot_total_order_quantity_qty=0; $grand_tot_del_qty=0; $grand_tot_no_bag_qty=0; 

            		foreach ($sql_res as $row) 
					{
						$Particular = $composition[$row[csf("yarn_composition_id")]]." ". $yarn_type[$row[csf("yarn_type_id")]].", ". $count_arr[$row[csf("count_id")]];
						?>
						<tr>
							<td align="center" width="30"><? echo $i; ?></td>
			                <td align="center" width="100"><?php echo $row[csf("style_ref")]; ?></td>
			                <td align="center" width="100"><?php echo $row[csf("sales_order_no")]; ?></td>
			                <td align="center" width="100"><?php echo $row[csf("order_no")]; ?></td>
			                <td align="center" width="90"><?php echo $row[csf("buyer_buyer")]; ?></td>
			                <td align="center" width="300"><?php echo $Particular; ?></td>
			                <td align="center" width="80"><?php echo $color_arr[$row[csf("yd_color_id")]]; ?></td>
			                <td align="center" width="80"><?php echo $row[csf("lot")]; ?></td>
			                <td align="center" width="80"><?php echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
			                <td align="right" width="80"><?php echo number_format($row[csf("order_quantity")],2,'.',''); ?></td>
			                
			                <td align="right" width="100">
			                	<?php 
			                		$total_order_quantity_qty = (($row[csf("receive_qty")]*$row[csf("process_loss")])/100)+$row[csf("receive_qty")];

			                		echo number_format($total_order_quantity_qty,2,'.','');
			                	?>
			                </td>
			                <td align="right" width="100"><?php echo number_format($row[csf("receive_qty")],2,'.',''); ?></td>
			                <td align="right" width="60"><?php echo $row[csf("no_bag")]; ?></td>
			                <td align="right" width="60"><?php echo $row[csf("cone_per_bag")]; ?></td>
			                <?php
			                	if($order_job_arr[$row[csf('job_no')]]['job_no']==$row[csf('job_no')])
			                	{
			                ?>
			                	<td align="right" rowspan="<?php echo $order_job_arr[$row[csf('job_no')]]['count'];?>" width="120"><?php echo $order_job_arr[$row[csf('job_no')]]['job_no']; ?></td>
			                <?php
			                		$order_job_arr[$row[csf('job_no')]]['job_no']='';
			                	}
			                ?>
			            </tr>
						<?php
						$i++;
						$grand_tot_qty+=$row[csf("order_quantity")];
						$grand_tot_total_order_quantity_qty += $total_order_quantity_qty;
						$grand_tot_del_qty += $row[csf("receive_qty")];
						$grand_tot_no_bag_qty += $row[csf("no_bag")];
						$grand_tot_no_cone_qty += $row[csf("cone_per_bag")];
					}
            	?>
            	<tr>
            		<td colspan="9" align="right"><strong>Total:</strong></td>
	            	<td align="right"><strong><?php echo number_format($grand_tot_qty,2,'.','');?></strong></td>
	            	<td align="right"><strong><?php echo number_format($grand_tot_total_order_quantity_qty,2,'.','');?></strong></td>
	            	<td align="right"><strong><?php echo number_format($grand_tot_del_qty,2,'.','');?></strong></td>
	            	<td align="right"><strong><?php echo $grand_tot_no_bag_qty;?></strong></td>
	            	<td align="right"><strong><?php echo $grand_tot_no_cone_qty;?></strong></td>
	            	<td align="right"><strong></strong></td>
            	</tr>
            </tbody>
        </table>
        <div style="padding-top:10px; width: 1220px;">
        	&nbsp;
        </div>
        <table align="left" cellspacing="0" width="1220" border="1" rules="all" class="rpt_table" style="font-size:12px">
        	<tr>
        		<td valign="top" style="height:100px; font-size: 14px;">
				Special Instruction: 
        		</td>
        	</tr>
        </table>
			<? echo signature_table(300, $com_id, "1220px",$template_id,0,$dataArray[0][csf('inserted_by')]); ?>
    </div>
   	<?php
	echo '<p class="breakAfter"></p>';
	}
}
