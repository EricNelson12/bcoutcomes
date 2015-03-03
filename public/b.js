!(function (d3) {

$("bcontent").empty();
var margin = {top: 20, right: 45, bottom: 30, left: 60},
width = 1000 - margin.left - margin.right,
height = 300 - margin.top - margin.bottom;


var parseDate = d3.time.format("%d-%b-%y").parse;

var x = d3.scale.linear()
.range([0, width - 300]);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .interpolate("step-after")
    .x(function(d) { return x(d.year); })
    .y(function(d) { return y(d.close); });
var line2 = d3.svg.line()
    .x(function(d) { return x(d.year); })
    .y(function(d) { return y(d.open); });

var svg = d3.select("bcontent").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv("data2.tsv", function(error, data) {
  data.forEach(function(d) {
    d.year = +d.year;
    d.close = +d.close;
    d.open = +d.open; 
  });

 
  x.domain(d3.extent(data, function(d) { return d.year; }));
  //y.domain(d3.extent(data, function(d) { return d.close; }));
  y.domain([0, d3.max(data, function(d) {
    return Math.max(d.close); })]); 


  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Survival (%)");

  svg.append("path")
      .datum(data)
      .attr("class", "line")
      .attr("d", line);
  svg.append("path")      // Add the valueline2 path.
      .attr("class", "line")
      .style("stroke","#ff7f0e")
      .attr("d", line2(data));

 var stat = svg.selectAll(".stat")
    .data(stats)
  .enter().append("g")
    .attr("class", "stat");

  stat.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d.values); })
      .style("stroke", function(d) {return color(d.name); });

  stat.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.year) + "," + y(d.value.value) + ")"; })
      .attr("x", 3)
      .attr("dy", "0.35em")
      .text(function(d) { return d.name; });
});
})(d3);