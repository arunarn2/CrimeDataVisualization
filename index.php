<!DOCTYPE html>
<meta charset="utf-8">
<style>

body {
	font: 11px sans-serif;
	background-color: #ffffff;
	align: center;
	height: 50px;
    width: 960px;
}

#h2 {
  font-weight: bold;
  color: #fff;
  font-size: 30px;
  text-align: center;
}

#h3 {
 color: #67000d;
  font-size: 13px;
}

/* stylesheet for your custom graph */

.states {
  fill: none;
  stroke: #fff;
  stroke-linejoin: round;
}

.states-choropleth {
  fill: #ccc;
}
#metrics { 
   position: absolute;
    top: 300px;
    left: 720px;
	font-size: 11px;
	display: inline-block;
    float: left;
    height: 50px;
    width: 120px;
    margin-right: 5px;
    text-align: center;
    line-height: 50px;
    text-decoration: none;
	border-radius: 5px;
}

#metrics h4 { 
    font-size: 13px;
    font-family: Helvetica, sans-serif; 
    text-align: center; 
    margin-bottom: 0; 
}

#metrics ul { 
list-style-type: none;
    font-size: 12px; 
    font-family: Helvetica, sans-serif; 
    margin-left: 10; 
    padding: 0; 
	font: 12px sans-serif;        
	border: 1px;      
	border-radius: 5px;
	display: inline-block;
}

#metrics ul li {
    background-color: #6baed6;
	font-weight: bold;
    padding: 3px;
    margin: 2px;
	border-radius: 5px;
	size=auto;
}

div.years_buttons {
    position: fixed;
    top: 5px;
    left: 50px;
}  
div.years_buttons div {
    background-color: #6baed6;
    padding: 3px;
    margin: 7px;
}
#navlist li
{
    font-size: 15px; 
    font-family: Helvetica, sans-serif; 
	display: inline;
	font-weight: bold;
	list-style-type: none;
	border-radius: 5px;
	color:#fff;
	background-color: #bdbdbd;
}

.legend {
	position:absolute;
	left:770px;
	top:25px;
	font: 8px sans-serif;
	font-weight: bold;
}


#tooltip-container {
	position: absolute;
	background-color: #fff;
	color: #000;
	padding: 10px;
	font: 13px sans-serif;        
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
<div id="tooltip-container"></div>
<div id="canvas-svg"></div>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://unpkg.com/topojson-client@3"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/topojson/1.1.0/topojson.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<body>	
<br><br>
<h3>Source: <a href="https://ucr.fbi.gov/"> FBI’s Crime Data</a> reporting has extensive data from 1994-2014 available for criminal offenses by organized by state and city.</h3>
<h3>
<li>Can this crime data be used to increase crime prevention? </li>
<li>Does the data on victims and the perpetrators reveal any race being affected more? </li>
<li>What factors have contributed to the increase in crime in some metros compared to others where they have significantly decreased? </li>
</h3>

<div id="metrics">
        <ul class="metrics_button">
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


<script>
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
  
var lowColor = '#deebf7';
var highColor = '#08306b';
var w = 100, h = 240;
var init_year = 1994;
var headline = "Crime Data in the US ";

// slider
d3.select("body").insert("p", ":first-child")
				.append("input")
				.attr("type", "range")
				.attr("min", "1994")
				.attr("max", "2014")
				.attr("value", init_year)
				.attr("id", "year");
d3.select("body")
		.insert("h3", ":first-child")
		.text("See the changes through the years:");


d3.select("body")
		.insert("h1", ":first-child")
		.text(headline + init_year + " - " + current)
		.attr("text-anchor", "middle");


		
var key = d3.select("body")
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

	var config = {"color1":lowColor,"color2":highColor,"stateDataColumn":"State",
				"defaultValue":"1994","state":"State"}; 
	var WIDTH = 900, HEIGHT = 500;
	var COLOR_COUNTS = 50; 
	var SCALE = 0.80;
  
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
  
  var svg = d3.select("#canvas-svg").append("svg")
      .attr("width", width)
      .attr("height", height)
	  .attr("center", (width +height)/2);
  
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
var tooltip = d3.select("#tooltip")
 .attr("class", "tooltip")
 .style("opacity", 0);
            
var ttfh = 120;
var ttfw = 200;

var margin = {top:30, bottom:30, left:20, right:35};

var tth = ttfh - margin.top - margin.bottom;
var ttw = ttfw - margin.left - margin.right;
    
xScale = d3.scaleTime().range([0,ttw]);
yScale = d3.scaleLinear().range([tth,0]);

var parseDate = d3.timeFormat("%Y").parse;
var outputDate = d3.timeFormat("%Y");

var yAxis = d3.axisLeft()
    .scale(yScale)
    .tickFormat(d3.format("s"))
    .ticks(3)
    .tickPadding([3])
    .tickSize([0]);
    
var line = d3.line()
    .x(function(d){
        return xScale(parseDate(d.year));
    })
    .y(function(d){
        return yScale(+d.amount);
    });
            
var area = d3.area()
    .x(function(d){
        return xScale(parseDate(d.year));
    })
    .y0(tth)
    .y1(function(d){
        return yScale(+d.amount);
    });
    
var tooltipChart = tooltip
    .append("svg")
    .attr("class","lineChart")
    .attr("width",ttfw)
    .attr("height",ttfh)
    .append("g")
    .attr("transform","translate(" + margin.left + "," + margin.top + ")");
	
    
    function drawMap(dataColumn) {
		var valueById = d3.map();
		
      
      data.forEach(function(d) {
        var id = name_id_map[d[config.state]];
        valueById.set(id, +d[dataColumn]); 
      });
      
      quantize.domain([
		d3.min(data, function(d){ return +d[dataColumn] }),
        d3.max(data, function(d){ return +d[dataColumn] })]);
    
      d3.select("svg.legend").remove();
		key = d3.select("body")
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
              
			  var years = {}, yvalues ={};
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
				years[i]=Object.keys(data[0])[i];
				yvalues[i]=dataMap[id_name_map[d.id]][Object.keys(data[0])[i]];
              }

              $("#tooltip-container").html(html) + tooltipChart;
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

		 d3.select("h1").text(headline + d3.select("#year").node().value + " - " + current ).attr("text-anchor", "middle");;
		 
		 var dataArray = [];
	for (var d = 0; d < data.length; d++) {
		dataArray.push(parseFloat(data[d][init_year]))
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
    
    drawMap(init_year);
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
		init_year=1994;
		d3.select("#year").attr("value", init_year);
		drawMap(init_year);
    });
	 
	// was the slider used?
	d3.select("#year").on("input", function() {
		init_year=this.value
		drawMap(init_year);
	});
	
  
  });
  });
});

</script>

<h3>
<br><br>
<div id="navcontainer">
<ul id="navlist">
<li id="active"><li><a href="index.php">Crime Data in US</a></li>
<li><a href="CrimeByRace.html">Crime By Race</a></li>
<li><a href="CrimeByWeapon.html">Crime By Weapon</a></li>
<li><a href="MostDangerousCities.html">Most Dangerous Cites</a></li>
<li><a href="VictimPerpAge.html">Victim Perpitrator Age</a></li>
<li><a href="CrimeByState.html">Totals By Crime Type</a></li>
</ul>
</div>
</h3>