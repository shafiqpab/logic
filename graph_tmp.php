<? 
session_start(); 
 echo load_html_head_contents("Graph", "", "", $popup, $unicode, $multi_select, 1);
 //include('includes/common.php');
 function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
<style>
.stack_company
{
	visibility:visible;
}

</style>

<script>
	function change_color(v_id,e_color)
	{
		var clss;
		$('td').click(function() {
			var myCol = $(this).index();
			clss='res'+myCol;
		
		});
		
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			$('.'+clss).removeAttr('bgColor');
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			$('.'+clss).attr('bgColor','#33CC00');
		}
	}
	
	//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )
	
</script>

<div style="margin-top:5px">

<!-- <a href="##" onClick='hs_homegraph(1)'>Value Wise</a>&nbsp;&nbsp;<a href="##" onClick='hs_homegraph(2)'>Quantity Wise</a>&nbsp;&nbsp;<a href="##" onClick='hs_homegraph_stack(1)'>Stack Value Chart</a>&nbsp;&nbsp; --><a href="index.php?g=5" >Home Graph</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<a href="index.php?g=2">Today Hourly Prod.</a>&nbsp;&nbsp;<a href="index.php?g=3">Trend Monthly</a>&nbsp;&nbsp;<a href="index.php?g=4">Trend Daily</a></div>

<table width="1050" cellpadding="0" cellspacing="0">
    	<tr>
        	<td height="30" valign="middle" align="center" colspan="2">
            	<font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span> <? // echo "$start_yr"."-"."$end_yr"; ?></strong></font>
            </td>
            <td colspan="2" rowspan="2" valign="top" align="center"> 
           <!-- <br />
            	<a href="##" onClick='show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )'>Value Wise</a>&nbsp;&nbsp;<a href="##" onClick='show_graph( "settings_qnty", "data_qnty", "column", "chartdiv", "", "", 1, 400, 750 )'>Quantity Wise</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>
                
                 &nbsp;&nbsp;<a href="##" onClick="set_stack_graph(0)">Stack Chart</a><br>
            	 <a href="##" onClick="set_intimates_qnty_graph()">Product Category WIse Quantity</a><br><a href="##" onClick="set_intimates_value_graph()">Product Category WIse Value</a><br> --><br><br> 
                 <div class="stack_company" id="stack_company" style="display:none">
                 <? 
					//echo create_drop_down( "cbo_company_name", 172, "select company_name, id from lib_company where core_business=1 and status_active=1 and is_deleted=0 order by company_name asc","id,company_name", 1, "All Company", $selected, "set_stack_graph( this.value )" );
				?>
                </div> 
                <div style="margin-left:5px; margin-top:5px">
                	<!--<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360">
                    	<thead>
                        	<th width="55">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <tbody>
                        
                        </tbody>
                        
                    </table> -->
                </div>
                
                <!--  <a href="##" onClick="generate_site_map()">Full Site Map</a>  -->
            </td>
        </tr>
        
        <tr>
        	<td width="8" bgcolor=" ">
        	<td align="center" height="400" width="750">
        		<div id="chartdiv" style="width:750px; height:400px; background-color:#FFFFFF">
                	<img src="home.jpg" width="750" height="400" />
                </div>
            </td>
             
        </tr>
        <tr>
        	<td height="8" colspan="2" bgcolor=" "></td>
            <td width="8" bgcolor=""></td> <!--#00CCFF-->
            <td></td>
        </tr>
        <tr>
        	<td colspan="2">
            	<table width="100%">
                	<tr>
                    	<td width="150"></td>
                        <td  align="right" valign="top">Copyright</td>
                        <td align="right" valign="top" width="310"> <img src="images/logic/logic_bottom_logo.png" height="65" width="300" /> 
                        </td>
                    </tr>
                </table>
            </td>
        	 <td colspan="7" ></td>
        </tr>
	</table>
     
 