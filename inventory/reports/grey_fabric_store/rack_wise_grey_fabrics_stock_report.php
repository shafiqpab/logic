<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Rack Wise Grey Fabrics Stock Report

Functionality	:
JS Functions	:
Created by		:	Zaman
Creation date 	: 	16-02-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters_2 =
	{
		col_operation: {
			id: ["value_tot_booking_qty","value_tot_recv_qty","value_tot_iss_ret_qty","value_tot_trans_in_qty","value_grand_tot_recv_qty","value_tot_iss_qty","value_tot_rec_ret_qty","value_tot_trans_out_qty","value_grand_tot_iss_qty","value_grand_stock_qty"],
			col: [8,14,15,16,17,18,19,20,21,22],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_job
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		
		var title='Job No Search';
		var page_link='requires/rack_wise_grey_fabrics_stock_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_booking
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/rack_wise_grey_fabrics_stock_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_booking_no').val(booking_no);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_item
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_item()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/rack_wise_grey_fabrics_stock_report_controller.php?action=item_description_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo); 
		}
	}

	/*
	|--------------------------------------------------------------------------
	| openmypage_store
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_store()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=store_popup&data='+data,'Store Popup', 'width=450px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("store_id");
			var theemailv=this.contentDoc.getElementById("store_name");
			$('#txt_store_id').val(theemail.value);	
			$('#txt_store').val(theemailv.value);	
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_floor
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_floor()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_store_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=floor_popup&data='+data,'Floor Popup', 'width=550px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("floor_id");
			var theemailv=this.contentDoc.getElementById("floor_name");
			$('#txt_floor_id').val(theemail.value);	
			$('#txt_floor').val(theemailv.value);	
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_room
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_room()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_floor_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=room_popup&data='+data,'Floor Popup', 'width=550px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("room_id");
			var theemailv=this.contentDoc.getElementById("room_name");
			$('#txt_room_id').val(theemail.value);	
			$('#txt_room').val(theemailv.value);	
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_rack
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_rack()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_floor_id').value+"_"+document.getElementById('txt_room_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=rack_popup&data='+data,'Floor Popup', 'width=610px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("rack_id");
			var theemailv=this.contentDoc.getElementById("rack_name");
			$('#txt_rack_id').val(theemail.value);	
			$('#txt_rack').val(theemailv.value);	
		}
	}

	/*
	|--------------------------------------------------------------------------
	| openmypage_shelf
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_shelf()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_floor_id').value+"_"+document.getElementById('txt_room_id').value+"_"+document.getElementById('txt_rack_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=shelf_popup&data='+data,'Shelf Popup', 'width=610px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("shelf_id");
			var theemailv=this.contentDoc.getElementById("shelf_name");
			$('#txt_shelf_id').val(theemail.value);	
			$('#txt_shelf').val(theemailv.value);	
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| generate_report
	|--------------------------------------------------------------------------
	| rpt_type = 1 = Summary
	| rpt_type = 2 = Order Wise
	| rpt_type = 3 = Rack Wise
	| rpt_type = 4 = Date Wise
	|
	*/
	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		if(rpt_type == 3 || rpt_type == 4)
		{
			if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				return;
			}
		}
		else if(rpt_type == 1)
		{
			$('#txt_date_to').val($('#txt_date_from').val());
		}
				
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_booking_no*txt_booking_id*cbo_search_by*txt_search_comm*txt_product*txt_product_id*txt_product_no*txt_store_id*txt_floor_id*txt_room_id*txt_rack_id*txt_shelf_id*cbo_value_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		freeze_window(3);
		http.open("POST","requires/rack_wise_grey_fabrics_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}
	
	/*
	|--------------------------------------------------------------------------
	| generate_report_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			if(reponse[2] == 1)
			{
				/*
				var tableFiltersSummary = 
				{
					col_6: "none",
					col_operation: {
						id: ["value_totalStockQty"],
						col: [4,5,6],
						operation: ["sum"],
						write_method: ["innerHTML"]
					}
				}
				*/
				//setFilterGrid("table_body",-1,tableFiltersSummary);
				//alert(reponse[3]);
				hs_chart("["+reponse[3]+"]", "["+reponse[4]+"]");
			}
			else if(reponse[2] == 2)
			{
				var tableFiltersOrderWise = 
				{
					//col_36: "none",
					col_operation: {
						id: ["value_rcvQty","value_issueReturnQty","value_transferInQty","value_totalRcvQty","value_totalRollRcvQty","value_issueQty","value_rcvReturnQty","value_transferOutQty","value_totalIssueQty","value_totalRollIssueQty","value_totalStockQty","value_totalRollBalanceQty"],
						col: [25,26,27,28,29,30,31,32,33,34,35,36],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}

				setFilterGrid("table_body",-1,tableFiltersOrderWise);
			}
			else if(reponse[2] == 3)
			{
				var tableFiltersRackWise = 
				{
					//col_36: "none",
					col_operation: {
						id: ["value_totalStockQty","value_totalRollBalanceQty"],
						col: [19,20],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
					}
				}

				setFilterGrid("table_body",-1,tableFiltersRackWise);
			}
			else if(reponse[2] == 5)
			{
				var tableFilterShelfWise = 
				{
					//col_36: "none",
					col_operation: {
						id: ["value_totalStockQty","value_totalRollBalanceQty"],
						col: [18,19],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
					}
				}

				setFilterGrid("table_body",-1,tableFilterShelfWise);
			}
			release_freezing();
		}
	}

	function new_window(type)
	{
		if(type==2 || type==3 || type==5)
		{
			$(".flt").css("display","none");
		}
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if(type==2 || type==3 || type==5)
		{
			$(".flt").css("display","block");
		}
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="280px";
	}
	
	function change_caption(type)
	{
		$('#txt_search_comm').val('');
		if(type==1)
		{
			$('#td_search').html('Enter Style');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Order');
		}
	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_company_id').value;
	    if(company_id !='') {
			var data="action=load_drop_down_buyer&choosenCompany="+company_id+'&type='+1+'&type2='+4;
			http.open("POST","requires/rack_wise_grey_fabrics_stock_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function(){
			if(http.readyState == 4) 
			{
			  var response = trim(http.responseText).split("**");
			  $('#buyer_td').html(response[0]);
			}			 
	      };
	    }         
	}
	
	/*
	function hs_chart(stockQty, rack)
	{
		$('#container').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: 'Rack Wise Quantity'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories:eval(rack),
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {
				valueSuffix: '',
				backgroundColor: 'rgba(219,219,216,0.8)',
				borderWidth: 0
			},
			
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			credits: {
				enabled: false
			},
			series: [{
				name: 'Stock Qty',
				data: eval(stockQty)
			}]
		});
	}
	*/
	
	function hs_chart(stockQty, rack)
	{
		Highcharts.theme = {
		   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
			  "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
		   chart: {
			  backgroundColor: null, //null
			  style: {
				 fontFamily: "Dosis, sans-serif"
			  }
		   },
		   title: {
			  style: {
				 fontSize: '16px',
				 fontWeight: 'bold',
				 textTransform: 'uppercase'
			  }
		   },
		   tooltip: {
			  borderWidth: 0,
			  backgroundColor: 'rgba(219,219,216,0.8)',
			  shadow: false
		   },
		   legend: {
			  itemStyle: {
				 fontWeight: 'bold',
				 fontSize: '13px'
			  }
		   },
		   xAxis: {
			  gridLineWidth: 1,
			  
			  labels: {
				 style: {
					fontSize: '12px'
				 }
			  }
		   },
		   yAxis: {
			  minorTickInterval: 'auto',
			  
			  title: {
				 style: {
					textTransform: 'uppercase'
				 }
			  },
			  labels: {
				 style: {
					fontSize: '12px'
				 }
			  }
		   },
		   plotOptions: {
			  candlestick: {
				 lineColor: '#404048'
			  }
		   },
		   background2: '#FF0000'
		   
		};

		// Apply the theme
		Highcharts.setOptions(Highcharts.theme);
			var msg="Total Values"
			var uom=" USD";
		
		
		$('#container').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Graphp Titile'
					},
					xAxis: {
						categories: eval(rack)
					},
					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: msg
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b> ' +
								 ': ' + this.y + uom +'<br/>' ;
						}
					},
					plotOptions: {
						column: {
							stacking: false //'normal'
						}
					},
					series: eval(stockQty)
				});
	}

	function openmypage_stock(companyId,po_id, product_ids, storeId,floorId,roomId,rackId,selfId)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=companyId+"_"+po_id+"_"+product_ids+"_"+storeId+"_"+floorId+"_"+roomId+"_"+rackId+"_"+selfId;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/rack_wise_grey_fabrics_stock_report_controller.php?action=stock_popup&data='+data,'Roll Stock Popup', 'width=700px,height=420px,center=1,resize=0','../../');
	}
</script>
<script src="../../../ext_resource/hschart/hschart.js"></script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<?php echo load_freeze_divs ("../../../",$permission); ?>
		<form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
			<h3 style="width:1220px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1180px;">
					<table class="rpt_table" width="1180" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Buyer</th>
								<th>Year</th>
								<th>Job</th>
								<th>Booking</th>
								<th>Search By</th>
								<th id="td_search">Enter Order</th>
								<th>Fabric Description</th>
								<th>Store</th>
								<th>Floor</th>
								<th>Room</th>
								<th>Rack</th>
								<th>Shelf</th>
                                <th>Value</th>
								<th class="must_entry_caption">Date From</th>
								<th class="must_entry_caption">Date To</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td id="td_company">
								<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", "", "-- Select Company --", $selected, "" );
								?>
							</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td>
								<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_job_id" name="txt_job_id"/>
							</td>
							<td>
								<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
							</td>
							<td>
								<? 
									$search_by_arr=array(1=>"Style No",2=>"Order No");
									echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"", 0,"-Select-", 2, "change_caption(this.value);","","" );
	                            ?>
							</td>
							<td>
	                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:80px" placeholder="Write"/>
	                        </td>
	                        <td align="center">
		                        <input style="width:140px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
		                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   
		                        <input type="hidden" name="txt_product_no" id="txt_product_no"/>             
		                    </td>
		                    <td>
                            	<input style="width:100px;" name="txt_store" id="txt_store" class="text_boxes" onDblClick="openmypage_store()" placeholder="Browse store" readonly />
                                <input type="hidden" name="txt_store_id" id="txt_store_id"/>
                            </td>
							<td>
                            	<input style="width:100px;" name="txt_floor" id="txt_floor" class="text_boxes" onDblClick="openmypage_floor()" placeholder="Browse Floor" readonly />
                                <input type="hidden" name="txt_floor_id" id="txt_floor_id"/>
                            </td>
							<td>
                            	<input style="width:100px;" name="txt_room" id="txt_room" class="text_boxes" onDblClick="openmypage_room()" placeholder="Browse Room" readonly />
                                <input type="hidden" name="txt_room_id" id="txt_room_id"/>
                            </td>
                            <td>
                            	<input style="width:100px;" name="txt_rack" id="txt_rack" class="text_boxes" onDblClick="openmypage_rack()" placeholder="Browse Rack" readonly />
                                <input type="hidden" name="txt_rack_id" id="txt_rack_id"/>
                            </td>
                            <td>
                            	<input style="width:100px;" name="txt_shelf" id="txt_shelf" class="text_boxes" onDblClick="openmypage_shelf()" placeholder="Browse shelf" readonly />
                                <input type="hidden" name="txt_shelf_id" id="txt_shelf_id"/>
                            </td>
                            <td>
								<?   
                                $valueTypeArr=array(1=>'Value With 0',2=>'Value Without 0');
                                echo create_drop_down( "cbo_value_type", 100, $valueTypeArr,"",0,"",1,"","","");
                                ?>
                            </td>                            
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="12" align="center"><? echo load_month_buttons(1); ?></td>
							<td align="right"><input type="button" name="search" id="search" value="Order Wise" onClick="generate_report(2)" style="width:80px" class="formbutton" /></td>
							<td align="right"><input type="button" name="search" id="search" value="Rack Wise" onClick="generate_report(3)" style="width:80px" class="formbutton" /></td>
							<td align="right"><input type="button" name="search" id="search" value="Shelf Wise" onClick="generate_report(5)" style="width:80px" class="formbutton" /></td>
							<td align="right"><input type="button" name="search" id="search" value="Date Wise" onClick="generate_report(4)" style="width:80px" class="formbutton" /></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_company_id','0','0','0');
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];	
</script>
</html>
