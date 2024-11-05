<?
extract($_REQUEST);
?>

<!DOCTYPE html>
<meta charset="utf-8">

<link href="../src/nv.d3.css" rel="stylesheet" type="text/css">

<style>

body {
  overflow-y:scroll;
}

text {
  font: 12px sans-serif;
}

svg {
  display: block;
}

#chart1 svg{
  height: 400px;
  min-width: 100px;
  min-height: 100px;
/*
  margin: 10px;
  Minimum height and width is a good idea to prevent negative SVG dimensions...
  For example width should be =< margin.left + margin.right + 1,
  of course 1 pixel for the entire chart would not be very useful, BUT should not have errors
*/
}

</style>
<body>

  <div id="chart1">
    <svg></svg>
  </div>

<script src="../lib/d3.v3.js"></script>
<script src="../nv.d3.js"></script>
<!-- including all the components so I don't have to minify every time I test in development -->
<script src="../src/tooltip.js"></script>
<script src="../src/utils.js"></script>
<script src="../src/models/axis.js"></script>
<script src="../src/models/discreteBar.js"></script>
<script src="../src/models/discreteBarChart.js"></script>
<script>








historicalBarChart =<? echo $cardata; ?>
nv.addGraph(function() {  
  var chart = nv.models.discreteBarChart()
      .x(function(d) { return d.label })
      .y(function(d) { return d.value })
      .staggerLabels(true)
      //.staggerLabels(historicalBarChart[0].values.length > 8)
      .tooltips(false)
      .showValues(true)
      .transitionDuration(250)
      ;

  d3.select('#chart1 svg')
      .datum(historicalBarChart)
      .call(chart);

  nv.utils.windowResize(chart.update);

  return chart;
});


</script>
