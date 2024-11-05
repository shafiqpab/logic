<!DOCTYPE HTML>
<html>
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
  <script type="text/javascript">
    window.onload = function () {
        Chart.types.Doughnut.extend({
    name: "DoughnutTextInside",
    showTooltip: function() {
        this.chart.ctx.save();
        Chart.types.Doughnut.prototype.showTooltip.apply(this, arguments);
        this.chart.ctx.restore();
    },
    draw: function() {
        Chart.types.Doughnut.prototype.draw.apply(this, arguments);

        var width = this.chart.width,
            height = this.chart.height;

        var fontSize = (height / 114).toFixed(2);
        this.chart.ctx.font = fontSize + "em Verdana";
        this.chart.ctx.textBaseline = "middle";

        var text = "82%",
            textX = Math.round((width - this.chart.ctx.measureText(text).width) / 2),
            textY = height / 2;

        this.chart.ctx.fillText(text, textX, textY);
    }
});

var data = [{
    value: 30,
    color: "#F7464A"
}, {
    value: 70,
    color: "#E2EAE9"
}];

var DoughnutTextInsideChart = new Chart($('#myChart')[0].getContext('2d')).DoughnutTextInside(data, {
    responsive: true
});

    }
  </script>

  <body>
   <canvas id="myChart" width=100 height=50></canvas>
  </body>
 </html>