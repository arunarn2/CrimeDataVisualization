<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <meta charset="UTF-8">
	<br>
    <link rel="stylesheet" type="text/css" href="line.css">
		<h3 id = "bottomMenu" align = "center">
			<div id="navcontainer">
			<ul id="navlist">
				<li id="active"><li><a href="index.php">Crime Data in US</a></li>
				<li><a href="CrimeByState.html">Percentages By Crime Type</a></li>
				<li><a href="CrimeByWeapon.html">Crime By Weapon</a></li>
				<li><a href="CrimeByRace.html">Crime By Race</a></li>
				<li><a href="MostDangerousCities.html">Most Dangerous Cites</a></li>
				<li><a href="VictimPerpAge.html">Victim Perpetrator Age</a></li>
				<li><a href="CrimeSolved.html">Crime Solved By Agency</a></li>
			</ul>
			</div>
		</h3>
    
	<script src="line.js" charset="utf-8"></script>
	<br>
</head>
<style>
body {

	background-color: #ffffff;
	max-width: 900px;
    min-width: 304px;
    margin: 0 auto;
}
.centered {
    text-align: center;
}

#navlist li
{
	display: inline;
	list-style-type: none;
	border-radius: 5px;
	color:#fff;
	background-color: #bdbdbd;
	height = 80px;
}

#h2 {
  font-weight: bold;
  color: #fff;
  font-size: 30px;
  text-align: center;
}

.states {
  fill: none;
  stroke: #fff;
  stroke-linejoin: round;
}

.states-choropleth {
  fill: #ccc;
}

.legend {
	position:absolute;
}

#metrics {
	display: inline;
   	position:absolute;
	right:80px;
	top:150px;
	font-size: 11px;
	display: inline-block;
    float: left;
    height: 50px;
    width: 100px;
    margin-right: 5px;
    text-align: center;
    line-height: 50px;
    text-decoration: none;
	border-radius: 5px;
}

#metrics h4 {
    font-size: 13px;
    font-family: sans-serif;
    text-align: center;
    margin-bottom: 0;
}

#metrics ul {
list-style-type: none;
    font-size: 12px;
    font-family:  sans-serif;
    margin-left: 10;
    padding: 0;
	font: 12px sans-serif;
	border: 1px;
	border-radius: 5px;
	
}

#metrics ul li {
	background-color: #9ecae1;
	padding: 3px;
	margin: 2px;
	border-radius: 5px;
	-webkit-box-shadow: 5px 5px 3px #666666;
	-moz-box-shadow: 5px 5px 3px #666666;
	box-shadow: 5px 5px 3px #666666; /* offset-x, offset-y, blur-radius, color */
}
#metrics ul li:hover {
  background: -webkit-linear-gradient(top, #3cb0fd, #3498db);
  background: -moz-linear-gradient(top, #3cb0fd, #3498db);
  background: -o-linear-gradient(top, #3cb0fd, #3498db);
  background: linear-gradient(to bottom, #3cb0fd, #3498db);
}

#metrics ul li:active {
  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;
}

div.years_buttons {
    position: fixed;
    top: 5px;
    left: 50px;
}
div.years_buttons div {
    background-color: #9ecae1;
    padding: 3px;
    margin: 7px;
}


#tooltip-container {
	position: absolute;
	background-color: #fff;
	color: #000;
	padding: 10px;

	background: white;
	border: 0px;
	border-radius: 5px;
	display: none;
}

.tooltip_key {
  font-weight: bold;
}

.tooltip_value {
  margin-left: 20px;
  float: right;
}

.option-select {
  margin-top: 20px;
  margin-left: 300px;
  fill: #64b646;
  width: auto;
  padding: 5px;
  font-weight: bold;
}

.slider {
  margin: 200px 0 10px 20px;
  width: 1500px;
}
</style>
<body>
<div id = "main" class = "centered">
	<div id="tooltip-container"></div>
	<div id="button" align= "right">
	  <a class="btn" href="about.html">About the Visualization</a>
	</div>
	<div id="canvas-svg"  class = "centered">
		<div id="metrics"  align = "left">
				<ul class="metrics_button" >
				<p>Click to see the counts</p>
				<li data-metric="TotalCountOfIncidents" >Total Count Of Incident Reports</li>
				<li data-metric="PropertyCrimes" >Property Crimes</li>
				<li data-metric="ViolentCrimes" >Violent Crimes</li>
				<li data-metric="Robbery">Robbery</li>
				<li data-metric="Murder" class="differenceLoc">Murder</li>
				<li data-metric="Burglary" class="differenceLoc">Burglary</li>
				<li data-metric="Larceny" class="differenceLoc">Larceny</li>
				<li data-metric="Rape" class="differenceLoc">Rape</li>
				<li data-metric="AggravatedAssault" class="differenceLoc">Aggravated Assault</li>
				<li data-metric="MotorVehicleTheft" class="differenceLoc">Motor Vehicle Theft</li>
				</ul>
		</div>
		<div id = "map" class = "centered">
		</div>
	</div>
	</main>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://unpkg.com/topojson-client@3"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/topojson/1.1.0/topojson.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<h4 align="justify">SOURCE: <a href="https://ucr.fbi.gov/"> FBI’s Crime Data</a> reporting has extensive data from 1994-2014 available for criminal offenses by organized by state and city.
	<li>Can this crime data be used to increase crime prevention? </li>
	<li>Does the data on victims and the perpetrators reveal any race being affected more? </li>
	<li>What factors have contributed to the increase in crime in some metros compared to others where they have significantly decreased? </li>
</h4>

<script>
var xScale, yScale, tooltipsvg, line, xAxis, yAxis;
var dateFormat = d3.timeFormat("%Y");
var tooltip = d3.select("body").append("div").attr("class", "tooltip").style("display", "none");
var lineData=[]
var current = "TotalCountOfIncidents"; // default view
var totalcounts,property,violent,robbery,murder,burglary,larceny,rape,aggravatedassault,motorvehicletheft;
// use a d3 map to make a lookup table for the string in the chart title
var chartLabels = d3.map();
chartLabels.set("TotalCountOfIncidents", "Total Count Of Incident Reports");
chartLabels.set("PropertyCrimes", "Property Crimes");
chartLabels.set("ViolentCrimes", "Violent Crimes");
chartLabels.set("Robbery", "Robbery");
chartLabels.set("Murder" ,"Murder");
chartLabels.set("Burglary", "Burglary");
chartLabels.set("Larceny", "Larceny");
chartLabels.set("Rape", "Rape");
chartLabels.set("AggravatedAssault", "Aggravated Assault");
chartLabels.set("MotorVehicleTheft", "Motor Vehicle Theft");
var data1 = [3, 6, 2, 7, 5, 2, 0, 3, 8, 9, 2, 5, 9, 3, 6, 3, 6, 2, 7, 5, 2, 1, 3, 8, 9, 2, 5, 9, 2, 7];
var lowColor = '#deebf7';
var highColor = '#08306b';
var w = 100, h = 240;
var selectedYear = 1994;
var headline = "Crime Data in the US ";

function setupTooltipChart() {

   var m = [80, 80, 80, 80]; // margins
		var w = 1000 - m[1] - m[3]; // width
		var h = 400 - m[0] - m[2]; // height
		
		// create a simple data array that we'll plot with a line (this array represents only the Y values, X will just be the index location)
		var data = [3, 6, 2, 7, 5, 2, 0, 3, 8, 9, 2, 5, 9, 3, 6, 3, 6, 2, 7, 5, 2, 1, 3, 8, 9, 2, 5, 9, 2, 7];
		// X scale will fit all values from data[] within pixels 0-w
		var x = d3.scaleLinear().domain([0, data.length]).range([0, w]);
		// Y scale will fit values from 0-10 within pixels h-0 (Note the inverted domain for the y-scale: bigger is up!)
		var y = d3.scaleLinear().domain([0, 10]).range([h, 0]);
			// automatically determining max range can work something like this
			// var y = d3.scale.linear().domain([0, d3.max(data)]).range([h, 0]);
		// create a line function that can convert data[] into x and y points
		var line = d3.line()
			// assign the X function to plot our line as we wish
			.x(function(d,i) { 
				// verbose logging to show what's actually being done
				console.log('Plotting X value for data point: ' + d + ' using index: ' + i + ' to be at: ' + x(i) + ' using our xScale.');
				// return the X coordinate where we want to plot this datapoint
				return x(i); 
			})
			.y(function(d) { 
				// verbose logging to show what's actually being done
				console.log('Plotting Y value for data point: ' + d + ' to be at: ' + y(d) + " using our yScale.");
				// return the Y coordinate where we want to plot this datapoint
				return y(d); 
			})
			// Add an SVG element with the desired dimensions and margin.
			tooltipsvg = tooltip.append("svg:svg")
			      .attr("width", w + m[1] + m[3])
			      .attr("height", h + m[0] + m[2])
			    .append("svg:g")
			      .attr("transform", "translate(" + m[3] + "," + m[0] + ")");
			// create yAxis
		 xAxis = d3.axisBottom().scale(x).ticks(5);
			// Add the x-axis.
			tooltipsvg.append("svg:g")
			      .attr("class", "x axis")
			      .attr("transform", "translate(0," + h + ")")
			      .call(xAxis);
			// create left yAxis
			 yAxisLeft = d3.axisLeft().scale(y).ticks(4);
			// Add the y-axis to the left
			tooltipsvg.append("svg:g")
			      .attr("class", "y axis")
			      .attr("transform", "translate(-25,0)")
			      .call(yAxisLeft);
			
  			// Add the line by appending an svg:path element with the data line we created above
			// do this AFTER the axes above so that the line is above the tick-lines
  			tooltipsvg.append("svg:path").attr("d", line(data));

}


d3.select("#main")
		.insert("h4", ":first-child")
		.text("Slide to see the changes through the years (1994- 2014)")
		.insert("p", ":first-child").append("input")
				.attr("type", "range")
				.attr("min", "1994")
				.attr("max", "2014")
				.attr("step","1")
				.attr("value", selectedYear)
				.attr("id", "year")
				.attr("text","Year");

d3.select("body")
		.insert("h1", ":first-child")
		.text(headline + selectedYear + " - " +current)
		.attr("text-anchor", "middle");

var key = d3.select("#metrics")
		.append("svg")
		.attr("width", w)
		.attr("height", h)
		.attr("class", "legend");

var legend = key.append("defs")
		.append("svg:linearGradient")
		.attr("id", "gradient")
		.attr("x1", "100%")
		.attr("y1", "0%")
		.attr("x2", "100%")
		.attr("y2", "100%")
		.attr("spreadMethod", "pad")
		.attr("labelFormat",(d3.format("s")));
var yAxis = d3.axisRight();

var config = {"color1":lowColor,"color2":highColor,"stateDataColumn":"State",
				"defaultValue":"1994","state":"State"};
	var WIDTH = 960, HEIGHT = 600;
	var COLOR_COUNTS = 50;
	var SCALE = 0.9;

	function Interpolate(start, end, steps, count) {
	  var s = start,
		  e = end,
		  final = s + (((e - s) / steps) * count);
	  return Math.floor(final);
	}

	function Color(_r, _g, _b) {
	  var r, g, b;
	  var setColors = function(_r, _g, _b) {
		  r = _r;
		  g = _g;
		  b = _b;
	  };

      setColors(_r, _g, _b);
      this.getColors = function() {
          var colors = {
              r: r,
              g: g,
              b: b
          };
          return colors;
      };
  }

  function hexToRgb(hex) {
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16)
      } : null;
  }

  function valueFormat(d) {
    if (d > 1000000000) {
      return Math.round(d / 1000000000 * 10) / 10 + "B";
    } else if (d > 1000000) {
      return Math.round(d / 1000000 * 10) / 10 + "M";
    } else if (d > 1000) {
      return Math.round(d / 1000 * 10) / 10 + "K";
    } else {
      return d;
    }
  }

  var COLOR_FIRST = config.color1, COLOR_LAST = config.color2;
  var rgb = hexToRgb(COLOR_FIRST);
  var COLOR_START = new Color(rgb.r, rgb.g, rgb.b);

  rgb = hexToRgb(COLOR_LAST);
  var COLOR_END = new Color(rgb.r, rgb.g, rgb.b);

  var width = WIDTH,
      height = HEIGHT;


  var startColors = COLOR_START.getColors(),
      endColors = COLOR_END.getColors();

  var colors = [];

  for (var i = 0; i < COLOR_COUNTS; i++) {
    var r = Interpolate(startColors.r, endColors.r, COLOR_COUNTS, i);
    var g = Interpolate(startColors.g, endColors.g, COLOR_COUNTS, i);
    var b = Interpolate(startColors.b, endColors.b, COLOR_COUNTS, i);
    colors.push(new Color(r, g, b));
  }

  var quantize = d3.scaleQuantize()
      .domain([0, 1.0])
      .range(d3.range(COLOR_COUNTS).map(function(i) { return i }));

  var path = d3.geoPath();

  var svg = d3.select("#map").append("svg")
      .attr("width", width)
      .attr("height", height);


d3.queue()
	.defer(d3.csv, "property.csv")
	.defer(d3.csv, "violent.csv")
	.defer(d3.csv, "robbery.csv")
	.defer(d3.csv, "murder.csv")
	.defer(d3.csv, "burglary.csv")
	.defer(d3.csv, "larceny.csv")
	.defer(d3.csv, "rape.csv")
	.defer(d3.csv, "aggravatedassault.csv")
	.defer(d3.csv, "motorvehicletheft.csv")
	.defer(d3.csv, "TotalCounts.csv")
	.awaitAll(loadButtons);

function loadButtons(error, results) {
	property = results[0];
	violent = results[1];
	robbery = results[2];
	murder = results[3];
	burglary = results[4];
	larceny = results[5];
	rape = results[6];
	aggravatedassault = results[7];
	motorvehicletheft = results[8];
	totalcounts = results[9];
}

	d3.csv("TotalCounts.csv", function(err, data) {

	
  d3.tsv("us-state-names.tsv", function(error, names) {
  d3.json("https://unpkg.com/us-atlas@1/us/10m.json", function(error, us) {

    var name_id_map = {};
    var id_name_map = {};

    for (var i = 0; i < names.length; i++) {
      name_id_map[names[i].name] = names[i].id;
      id_name_map[names[i].id] = names[i].name;
    }

    var dataMap = {};
    data.forEach(function(d) {
      if (!dataMap[d[config.state]]) {
        dataMap[d[config.state]] = {};
      }

      for (var i = 0; i < Object.keys(data[0]).length; i++) {
        if ((Object.keys(data[0])[i] !== config.state)  ){
			dataMap[d[config.state]][Object.keys(data[0])[i]] = +d[Object.keys(data[0])[i]];
        }
      }

    });

    function drawMap(dataColumn) {
		var xScale, yScale, tooltipsvg, line, xAxis, yAxis;
		var valueById = d3.map();


      data.forEach(function(d) {
        var id = name_id_map[d[config.state]];
        valueById.set(id, +d[dataColumn]);
      });

      quantize.domain([
		d3.min(data, function(d){ return +d[dataColumn] }),
        d3.max(data, function(d){ return +d[dataColumn] })]);

      d3.select("svg.legend").remove();
		key = d3.select("#metrics")
		.append("svg")
		.attr("width", w)
		.attr("height", h)
		.attr("class", "legend");

		legend = key.append("defs")
		.append("svg:linearGradient")
		.attr("id", "gradient")
		.attr("x1", "100%")
		.attr("y1", "0%")
		.attr("x2", "100%")
		.attr("y2", "100%")
		.attr("spreadMethod", "pad")
		.attr("labelFormat",(d3.format("s")));
		yAxis = d3.axisRight();
		
	for (var i = 0; i < Object.keys(data[0]).length-1; i++) {            
		lineData.push({
			year:Object.keys(data[0])[i],
			value:Object.values(data[0])[i]
		});
	}
	
	 ///////////////////////////////////////////////////////////////////
	  
	    var m = [10, 20, 10, 20]; // margins
		var wid = 150 - m[1] - m[3]; // width
		var hei = 100 - m[0] - m[2]; // height
		
		// create a simple data1 array that we'll plot with a line (this array represents only the Y values, X will just be the index location)
		
		// X scale will fit all values from data1[] within pixels 0-w
		var x = d3.scaleLinear().domain([0, data1.length]).range([0, wid]);
		// Y scale will fit values from 0-10 within pixels h-0 (Note the inverted domain for the y-scale: bigger is up!)
		var y = d3.scaleLinear().domain([0, 10]).range([hei, 0]);
			// automatically determining max range can work something like this
			// var y = d3.scale.linear().domain([0, d3.max(data1)]).range([hei, 0]);
		// create a line function that can convert data1[] into x and y points
		var line = d3.line()
			// assign the X function to plot our line as we wish
			.x(function(d,i) { 
				// verbose logging to show what's actually being done
				console.log('Plotting X value for data1 point: ' + d + ' using index: ' + i + ' to be at: ' + x(i) + ' using our xScale.');
				// return the X coordinate where we want to plot this data1point
				return x(i); 
			})
			.y(function(d) { 
				// verbose logging to show what's actually being done
				console.log('Plotting Y value for data1 point: ' + d + ' to be at: ' + y(d) + " using our yScale.");
				// return the Y coordinate where we want to plot this data1point
				return y(d); 
			})
			// Add an SVG element with the desired dimensions and margin.
		tooltipsvg = tooltip.append("svg:svg")
			      .attr("width", wid + m[1] + m[3])
			      .attr("height", hei + m[0] + m[2])
			    .append("svg:g")
			      .attr("transform", "translate(" + m[3] + "," + m[0] + ")");
			// create yAxis
		xAxis = d3.axisBottom().scale(x).ticks(5);
			// Add the x-axis.
			tooltipsvg.append("svg:g")
			      .attr("class", "x axis")
			      .attr("transform", "translate(0," + hei + ")")
			      .call(xAxis);
		// create left yAxis
		 yAxisLeft = d3.axisLeft().scale(y).ticks(4);
		// Add the y-axis to the left
		tooltipsvg.append("svg:g")
			      .attr("class", "y axis")
			      .attr("transform", "translate(-25,0)")
			      .call(yAxisLeft);
			
		// Add the line by appending an svg:path element with the data1 line we created above
		// do this AFTER the axes above so that the line is above the tick-lines
		tooltipsvg.append("svg:path").attr("d", line(data1));
		//////////////////////////////////////////////////////////////////////////////
			  
	 
		
	//setupTooltipChart();
	  svg.append("g")
          .attr("class", "states-choropleth")
        .selectAll("path")
          .data(topojson.feature(us, us.objects.states).features)
        .enter().append("path")
          .attr("transform", "scale(" + SCALE + ")")
          .style("fill", function(d) {
            if (valueById.get(d.id)) {
              var i = quantize(valueById.get(d.id));
              var color = colors[i].getColors();
              return "rgb(" + color.r + "," + color.g +"," + color.b + ")";
            } else {
              return "";
            }
          })
          .attr("d", path)
          .on("mouseover", function(d) {
              var html = "";
              html += "<div class=\"tooltip_kv\">";
              html += "<span class=\"tooltip_key\">";
              html += id_name_map[d.id];
              html += "</span>";
              html += "</div>";

              for (var i = 0; i < Object.keys(data[0]).length-1; i++) {
                html += "<div class=\"tooltip_kv\">";
                html += "<span class='tooltip_key'>";
                html += Object.keys(data[0])[i];
                html += "</span>";
                html += "<span class=\"tooltip_value\">";
                html += valueFormat(dataMap[id_name_map[d.id]][Object.keys(data[0])[i]]);
                html += "";
                html += "</span>";
                html += "</div>";
				lineData.push({
					year:Object.keys(data[0])[i],
					value:dataMap[id_name_map[d.id]][Object.keys(data[0])[i]]
				});
              }

              /*$("#tooltip-container").html(html);
              $(this).attr("fill-opacity", "0.7");
              $("#tooltip-container").show();

              var coordinates = d3.mouse(this);

              var map_width = $('.states-choropleth')[0].getBoundingClientRect().width;

              if (d3.event.layerX < map_width / 2) {
                d3.select("#tooltip-container")
                  .style("top", (d3.event.layerY + 15) + "px")
                  .style("left", (d3.event.layerX + 15) + "px");
              } else {
                var tooltip_width = $("#tooltip-container").width();
                d3.select("#tooltip-container")
                  .style("top", (d3.event.layerY + 15) + "px")
                  .style("left", (d3.event.layerX - tooltip_width - 30) + "px");
              }*/
			  d3.select(this).attr("id", "selected");
  d3.select(this).attr("fill-opacity", "0.7");
  tooltip.style("opacity", 1);

			    tooltip.select("p").text("State"); // adds title text to tooltip



    //yScale.domain(d3.extent(lineData, function(d) { return d.value; }));

    var linechart = tooltipsvg.selectAll("path.line")
          .data( [data1] );

      linechart
          .enter()
          .append("path")
          .attr("class", "line")
          .attr("d", line);

      linechart.transition().attr("d", line);

      linechart.exit().remove();

      tooltipsvg.selectAll(".x.axis").transition().call(xAxis);
      tooltipsvg.selectAll(".y.axis").transition().call(yAxis);
	  var map_width = $('.states-choropleth')[0].getBoundingClientRect().width;
			  if (d3.event.layerX < map_width / 2) {
                tooltip
                  .style("top", (d3.event.layerY + 15) + "px")
                  .style("left", (d3.event.layerX + 15) + "px");
              } else {
                var tooltip_width = $("#tooltip-container").width();
                tooltip
                  .style("top", (d3.event.layerY + 15) + "px")
                  .style("left", (d3.event.layerX - tooltip_width - 30) + "px");
              }
          })
          .on("mouseout", function() {
                  $(this).attr("fill-opacity", "1.0");
                  $("#tooltip-container").hide();
              });

		svg.append("path")
			  .datum(topojson.mesh(us, us.objects.states, function(a, b) { return a !== b; }))
			  .attr("class", "states")
			  .attr("transform", "scale(" + SCALE + ")")
			  .attr("d", path);
		
		selectedYear=d3.select("#year").node().value;
		d3.select("h1").text(headline + " ("+selectedYear +"-"+current +")" ).attr("text-anchor", "middle");
		d3.select("#year").attr("value",selectedYear);

		 var dataArray = [];
	for (var d = 0; d < data.length; d++) {
		dataArray.push(parseFloat(data[d][selectedYear]))
	}
	var minVal = d3.min(dataArray)
	var maxVal = d3.max(dataArray)
	var ramp = d3.scaleLinear().domain([minVal,maxVal]).range([lowColor,highColor])

	legend.append("stop")
		.attr("offset", "0%")
		.attr("stop-color", highColor)
		.attr("stop-opacity", 1);

	legend.append("stop")
		.attr("offset", "100%")
		.attr("stop-color", lowColor)
		.attr("stop-opacity", 1);

	key.append("rect")
		.attr("width", w - 80)
		.attr("height", h)
		.style("fill", "url(#gradient)")
		.attr("transform", "translate(0,10)");

	var y = d3.scaleLinear()
		.range([h, 0])
		.domain([minVal, maxVal]);

	var yAxis = d3.axisRight(y).tickFormat(function(d){return d/1000000 + " Mil"});

	key.append("g")
		.attr("class", "yaxis")
		.attr("transform", "translate(18,10)")
		.call(yAxis);

	function updateFill() {
	svg.selectAll(".countries path")
		.attr("fill", function(d) { return "#ccc"; });
	}

    }

    drawMap(selectedYear);
	var colorScale = d3.scaleLinear().range([lowColor, highColor]);
		d3.selectAll("#metrics li")
			.on("click", function() {
				var item = d3.select(this);
				d3.selectAll(".metrics_button li").classed("selected", false);
				current = item.attr("data-metric");
				colorScale.domain(d3.extent(data, function(d) { return +d[current];}));

        var buttonlabel = chartLabels.get(current);
        item.classed("selected", true);
        item.classed(item.attr("data-metric"), true);
		switch(buttonlabel) {
			case "Total Count Of Incident Reports":
				config = {"color1":"#ffeda0","color2":"#800026","stateDataColumn":"State",
					"defaultValue":"1994","state":"State"};
				data = totalcounts;
				break;
			case "Property Crimes":
				config = {"color1":"#ffeda0","color2":"#800026","stateDataColumn":"State",
					"defaultValue":"1994","state":"State"};
				data = property;
				break;
			case "Violent Crimes":
				data = violent;
				break;
			case "Robbery":
				data=robbery;
				break;
			case "Murder":
				data = murder;
				break;
			case "Burglary":
				data = burglary;
				break;
			case "Larceny":
				data=larceny;
				break;
			case "Rape":
				data = rape;
				break;
			case "Aggravated Assault":
				data = aggravatedassault;
				break;
			case "Motor Vehicle Theft":
				data=motorvehicletheft;
				break;
			default:
				data = TotalCounts;
		}
		data.forEach(function(d) {
		  if (!dataMap[d[config.state]]) {
			dataMap[d[config.state]] = {};
		  }

		  for (var i = 0; i < Object.keys(data[0]).length; i++) {
			if ((Object.keys(data[0])[i] !== config.state)  ){
				dataMap[d[config.state]][Object.keys(data[0])[i]] = +d[Object.keys(data[0])[i]];
			}
		  }


		});
		selectedYear=1994;
		d3.select("#year").attr("value", selectedYear);
		drawMap(selectedYear);
    });

	// was the slider used?
	d3.select("#year").on("input", function() {
		selectedYear=this.value
		drawMap(selectedYear);
	});


  });
  });
});

</script>

</body>
</html>