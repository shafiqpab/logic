<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Rate Chart Entry				
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	25-06-2022
Updated by 		:	
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Dyeing Rate Chart Entry", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	// $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][540] );
	// echo "var field_level_data= ". $data_arr . ";\n";
	?>
	function fnc_dyeing_rate_chart_entry( operation )
	{		
		var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
		var data_all="";
		
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbocompanyid_'+i+'*cbofabrictype_'+i+'*cbocolorrange_'+i+'*txtcurrency_'+i+'*txtrate_'+i+'*txtformdate_'+i+'*txttodate_'+i+'*cbocurrency_'+i,'Company Name*Fabric Type*Color Range*Currency*Rate*From Date*To Date')==false)
			{
				return;
			}
			else
			{
				data_all=data_all+get_submitted_data_string('cbocompanyid_'+i+'*cbofabrictype_'+i+'*cbocolorrange_'+i+'*txtcurrency_'+i+'*txtrate_'+i+'*txtformdate_'+i+'*txttodate_'+i+'*updateid_'+i+'*cbocurrency_'+i,"../../");
			}
		}
		data_all=data_all+get_submitted_data_string('hdn_company_id*txt_dyeing_rate*update_mst_id',"../../");
		
		//alert(data_all);return;
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/dyeing_rate_chart_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_dyeing_rate_chart_entry_reponse;
	}
	
	function fnc_dyeing_rate_chart_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_dyeing_rate_chart_entry('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else
			{
				//alert(reponse[0]);
				show_msg(trim(reponse[0]));
				show_list_view(reponse[1],'search_list_view','dyeing_rate_chart_container','requires/dyeing_rate_chart_entry_controller','setFilterGrid("list_view",-1)');
				reset_form('dyeingRateChart_1','','');
				set_button_status(0, permission, 'fnc_dyeing_rate_chart_entry',1);
				release_freezing();
				reset_mst();
			}
		}
	}

	function reset_mst()
	{
		var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
		var i=''; 
		for(i=1;i<=row_num;i++)
		{
			$("#tbl_dyeing_rate_chart tbody").find("tr:not(:first)").remove();
			$('#cbocompanyid_'+i).attr('disabled',false);
			$('#cbofabrictype_'+i).attr('disabled',false);
			$('#cbocolorrange_'+i).attr('disabled',false);
			$('#txtformdate_'+i).attr('disabled',false);
			$('#txtrate_'+i).attr('disabled',false);
		} 
	}

	function add_break_down_tr(i) 
	{
		if (form_validation('cbocompanyid_'+i,'Company Name')==false)
		{
			return;
		}
		$('#cbocompanyid_'+i).attr('disabled',true);
		var prev_com = ($('#cbocompanyid_'+i).val());
		
		var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_dyeing_rate_chart tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_dyeing_rate_chart");

			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
			$('#cbocompanyid_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);opencurreny("+i+");");
			$('#txtformdate_'+i).removeAttr("onChange").attr("onChange","calculate_date("+i+");");
			$('#cbofabrictype_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			$('#cbocolorrange_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			$('#cbocurrency_'+i).removeAttr("onChange").attr("onChange","exchange_rate("+i+",this.value);");
			$('#txtformdate_'+i).removeAttr("class").attr("class","datepicker");
	
			
			$('#cbocompanyid_'+i).val(prev_com);
			$('#cbofabrictype_'+i).val("");
			$('#cbocolorrange_'+i).val("");
			$('#cbocurrency_'+i).val("");
			$('#txtcurrency_'+i).val("");
			//$('#txtrate_'+i).val("");
			$('#txtformdate_'+i).val("");
			$('#txttodate_'+i).val("");
			$('#txtformdate_'+i).attr('disabled',false);
			//$('#cbocompanyid_'+i).attr('disabled',false);
			$('#cbocompanyid_'+i).attr('disabled',true);
			$('#cbofabrictype_'+i).attr('disabled',false);
			$('#cbocolorrange_'+i).attr('disabled',false);
			$('#cbocurrency_'+i).attr('disabled',false);
			$('#txtrate_'+i).attr('disabled',false);
			
			set_all_onclick();
			 
		}
	}

	function enable_disable()
	{
		var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
		var i=''; 
		for(i=1;i<=row_num;i++)
		{
			$('#cbocompanyid_'+i).attr('disabled',true);
		} 
	}

	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		var numRow = $('table#tbl_dyeing_rate_chart tbody tr').length; 
		
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_dyeing_rate_chart tbody tr:last').remove();
		}
	}

	function show_detail_form(mst_id)
	{
		show_list_view(mst_id,'show_detail_form','form_div','requires/dyeing_rate_chart_entry_controller','');
	}

	function check_duplicate(id,td)
	{
		
		var cbocompanyid=document.getElementById('cbocompanyid_'+id).value;
		$('#hdn_company_id').val(cbocompanyid);
		var cbofabrictype=document.getElementById('cbofabrictype_'+id).value;
		var cbocolorrange=document.getElementById('cbocolorrange_'+id).value;
		var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				//cbocompanyid==document.getElementById('cbocompanyid_'+k).value && 
				if( cbofabrictype==document.getElementById('cbofabrictype_'+k).value && cbocolorrange==document.getElementById('cbocolorrange_'+k).value)
				{
					alert("Same Fabric Type and Same Color Range Duplication Not Allowed.");
					if(td==1)
					{
						$('#cbocompanyid_'+id).val('');
						$('#cbocompanyid_'+id).focus();
					}
					else
					{
						document.getElementById(td).value=0;
						document.getElementById(td).focus();
					}
				}
			}
		}
	}

		
	// function opencurreny(id)
	// {
	// 	//alert(td);
	// 	var current_date = $('#txt_current_date').val();
	// 	var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
	// 	var i=''; 
	// 	for(i=1;i<=row_num;i++)
	// 	{
	// 		var company_name = $('#cbocompanyid_'+id).val();
		
	// 		var response=return_global_ajax_value( 2+"**"+current_date+"**"+company_name, 'check_conversion_rate', '', 'requires/dyeing_rate_chart_entry_controller');
	// 		//alert(response);
	// 		if(company_name>0)
	// 		{
	// 			$('#txtcurrency_'+id).val(response);
	// 			$('#txtcurrency_'+id).attr('disabled','disabled');
	// 		}
	// 	} 
	// }

	function exchange_rate(id,val)
	{
		var cbocompanyid=document.getElementById('cbocompanyid_'+id).value;

		if(cbocompanyid==0)
		{
			alert('Please Select Ffirst Company Name....');
			$("#cbocurrency_"+id).val(0);
			return;
		}
		if(val==1)
		{
			$("#txtcurrency_"+id).val(1);
			//$('#txtcurrency_'+id).attr('disabled','disabled');
		}
		else
		{
			var current_date = $('#txt_current_date').val();
			var row_num=$('#tbl_dyeing_rate_chart tr').length-1;
			var i=''; 
			for(i=1;i<=row_num;i++)
			{
				var response=return_global_ajax_value( val+"**"+current_date+"**"+cbocompanyid, 'check_conversion_rate', '', 'requires/dyeing_rate_chart_entry_controller');
				//alert(response);
				if(cbocompanyid>0)
				{
					$('#txtcurrency_'+id).val(response);
					//$('#txtcurrency_'+id).attr('disabled','disabled');
				}
			} 
		}
	}

	function calculate_date(id)
	{	
		var thisDate=($('#txtformdate_'+id).val()).split('-');
		
		var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
		
		//alert(last);return;
		var last_date = last.getDate();
		var month = last.getMonth()+1;
		var year = last.getFullYear();
		
		if(month<10)
		{
			var months='0'+month;
		}
		else
		{
			var months=month;
		}
		
		var last_full_date=last_date+'-'+months+'-'+year;
		var first_full_date='01'+'-'+months+'-'+year;
		
		$('#txtformdate_'+id).val(first_full_date);
		$('#txttodate_'+id).val(last_full_date);
	
	}

	function open_mrrpopup()
	{
		var page_link='requires/dyeing_rate_chart_entry_controller.php?action=mrr_popup_info';
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
			var mrr_no=this.contentDoc.getElementById("hidden_mrr_no").value;
			//alert(mst_id);return;
			$("#txt_dyeing_rate").val(mrr_no);
			$("#update_mst_id").val(mst_id);
			show_detail_form(mst_id);
			enable_disable();
			set_button_status(1, permission, 'fnc_dyeing_rate_chart_entry',1,1);
		}
	}
	
</script>
</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="dyeingRateChart_1" id="dyeingRateChart_1" autocomplete="off">
            <fieldset style="width:1050px;">
                <legend>Dyeing Rate Chart </legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                	<tr>
                		<td colspan="8" align="center">
                			System ID <input type="text" id="txt_dyeing_rate" name="txt_dyeing_rate" class="text_boxes" style="width:170px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly >
							<input type="hidden" id="update_mst_id" value="" readonly/>
							<input type="hidden" id="hdn_company_id" value="" readonly/>
						     
							<input type="hidden" name="txt_current_date"  style="width:140px"  id="txt_current_date" class="datepicker" value="<? echo date("d-m-Y")?>"  readonly />
						
                		</td>
                </table>
            </fieldset>
            <fieldset style="width:1050px;">
                <legend></legend>
                <div id="form_div">
                    <table width="100%" border="0" id="tbl_dyeing_rate_chart" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                            	<th width="150" class="must_entry_caption">Production Company Name</th>
                                <th width="150">Fabric Type</th>
                                <th width="150">Color Range</th>
                                <th width="120">Currency</th>
                                <th width="50">Exchange Rate</th>
                                <th width="70">Rate</th>
                                <th width="120">From Date</th>
                                <th width="120">To Date</th>
                                <th >&nbsp;</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="dyeingcost_1" align="center">
                                <td>
									<? 
										echo create_drop_down( "cbocompanyid_1", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "check_duplicate(1,this.id);" );
										//opencurreny(1);
									?>
                                </td>
                                <td> <?=create_drop_down( "cbofabrictype_1", 150, $fabric_type_for_dyeing,"", 1, "-- Select --", '', 'check_duplicate(1,this.id)','','','','','' ); ?>
                                   
                                </td>
                                <td><?=create_drop_down( "cbocolorrange_1", 150, $color_range,"", 1, "-- Select --", '', '','','','','','' ); ?>
								</td>
								<td>
									<?
									echo create_drop_down( "cbocurrency_1", 120, $currency,"", 1, "-- Select--", 0, "exchange_rate(1,this.value)",'' );
									?>
                                </td>

                                <td><input type="text" id="txtcurrency_1"  name="txtcurrency_1" class="text_boxes_numeric" style="width:50px" value="" disabled/></td>

                                 <td><input type="text" id="txtrate_1"  name="txtrate_1"  class="text_boxes_numeric" style="width:70px" value="" /></td>
								 <td>
                                 	<input type="text" id="txtformdate_1"  name="txtformdate_1"  class="datepicker" style="width:80px" onchange="calculate_date(1)" readonly />
                                 </td>
                                 <td>
                                 	<input type="text" id="txttodate_1"  name="txttodate_1"  class="datepicker" style="width:80px" disabled/>
                                 </td>
                                <td> 
                                    <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                    <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />      
									<input type="hidden" id="updateid_1"  name="updateid_1"  class="text_boxes_numeric" style="width:70px" value="" /> 
								                     
                                </td>  
                            </tr>
                             
                        </tbody>
                    </table>
                   
                </div>
                <br/>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all" >
                   	
                    <tr>
                        <td colspan="11" align="center" class="button_container"><? echo load_submit_buttons( $permission, "fnc_dyeing_rate_chart_entry", 0,0 ,"reset_form('dyeingRateChart_1','','')",1); ?>
                        </td>
                    </tr>	
                </table>
            </fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

        <div id="dyeing_rate_chart_container">
			<?
				$lib_com_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name");
			
				//print_r($composition_arr);				
				$sql="SELECT id, mst_id, company_id, fabric_type_id, color_range_id, exchange_rate, rate,from_date, to_date, currency_id from  lib_dyeing_rate_chart_dtls where is_deleted=0 and entry_form=540 order by from_date DESC";
				
				$arr=array (0=>$lib_com_arr,1=>$fabric_type_for_dyeing, 2=>$color_range,3=>$currency);

				echo  create_list_view ( "list_view", "Production Company Name,Fabric Type,Color Range,Currency,Exchange Rate,Rate,From Date,To Date", "200,100,100,100,100,100,100,100","1000","350",0, $sql, "get_php_form_data", "mst_id", "'load_php_data_to_form'",1, "company_id,fabric_type_id,color_range_id,currency_id,0,0,0,0", $arr , "company_id,fabric_type_id,color_range_id,currency_id,exchange_rate,rate,from_date,to_date", "requires/dyeing_rate_chart_entry_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,3,3') ;
				exit();
            ?>
        </div>
    </div>
</body>

</html>
