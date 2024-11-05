
<?

include('includes/common.php');
 extract( $_REQUEST);
 if ($comp!=0) $cond=" and id='$comp'"; else $cond="";		
	
echo '<?xml version="1.0" encoding="UTF-8"?>
<settings>
  <type>column</type>
  
  <plot_area>
    <border_alpha>6</border_alpha>
    <margins>
      <left>100</left>
      <right>50</right>
      <bottom>50</bottom>
      <top>65</top>
    </margins>
  </plot_area>
  <grid>
    <category>
      <alpha>5</alpha>
    </category>
    <value>
      <alpha>5</alpha>
    </value>
  </grid>
  <axes>
    <category>
      <width>1</width>
      <alpha>15</alpha>
    </category>
    <value>
      <width>1</width>
      <alpha>15</alpha>
    </value>
  </axes>
  <values>
    <value>
      <min>0</min>
    </value>
  </values>
  <balloon>
    <alpha>80</alpha>
    <text_color>000000</text_color>
    <corner_radius>5</corner_radius>
    <border_width>3</border_width>
    <border_color>000000</border_color>
    <border_alpha>60</border_alpha>
  </balloon>
  <legend>
    <width>400</width>
    <spacing>5</spacing>
  </legend>
  <column>
    <type>stacked</type>
    <width>50</width>
    <spacing>0</spacing>
    <balloon_text>{title}: {value} USD</balloon_text>
    
    <grow_time>1</grow_time>
    <grow_effect>regular</grow_effect>
  </column>
  <depth>10</depth>
  <angle>45</angle>
  <line>
    <balloon_text>{value}</balloon_text>
    <data_labels>{value}</data_labels>
  </line><graphs>';
  //<data_labels><![CDATA[<b>{value}</b>]]></data_labels>
  
  $i=0;
 $capacity="";
 //if ($comp!=0) $cond=" id=$comp"; else $cond="";
$rs=sql_select("select company_short_name,id from lib_company where core_business=1 and status_active=1 and is_deleted=0  $cond order by id asc");
foreach($rs as $row_comp)
{
	$i++;
	echo "<graph gid='$i'><title>".$row_comp[company_short_name]." </title><color>".$row_comp[graph_color]."</color> </graph>";
}
 
 /* <graphs>
    <graph gid="0">
      <title>Europe</title>
      <color>C72C95</color>
    </graph>
    <graph gid="1">
      <title>North America</title>
      <color>D8E0BD</color>
    </graph>
    <graph gid="2">
      <title>Asia-Pacific</title>
      <color>B3DBD4</color>
    </graph>
    <graph gid="3">
      <title>Latin America</title>
      <color>69A55C</color>
    </graph>
    <graph gid="4">
      <title>Middle-East</title>
      <color>B5B8D3</color>
    </graph>
    <graph gid="5">
      <title>Africa</title>
      <color>F4E23B</color>
    </graph>
  </graphs> */
  
  
  echo ' </graphs><labels>
    <label lid="0">
      <text>  </text>
      <y>18</y>
      <align>center</align>
    </label>
  </labels>
</settings>'; //<![CDATA[<b>HNWI Population by Region, 2003 - 2005 (In Millions)</b>]]>

?>