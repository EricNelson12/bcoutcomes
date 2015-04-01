    google.load('visualization', '1.1', {packages: ['line', 'corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var survivalChart;
      var recurrenceChart;
      var button = document.getElementById('change-chart');
      //var materialDiv = document.getElementById('dcontent');
      var recurrenceDiv = document.getElementById('bcontent');
      var survivalDiv = document.getElementById('acontent');

     
/*
      var materialOptions = {
        chart: {
          title: 'Average Temperatures and Daylight in Iceland Throughout the Year'
        },
        width: 900,
        height: 500,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'Temps'},
          1: {axis: 'Daylight'}
        },
        explorer: {
      maxZoomOut:2,
      keepInBounds: true
    },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            Temps: {label: 'Temps (Celsius)'},
            Daylight: {label: 'Daylight'}
          }
        }
      };
*/
      var recurrenceOptions = {
        title: 'Kaplan-Meier Recurrence Estimator',
        width: 900,
        height: 500,
        // Gives each series an axis that matches the vAxes number below.
        series: {
          0: {targetAxisIndex: 0},
         
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'Recurrence (%)'}
          
        },
        hAxis: {
          ticks: [0,1,2,3,4,5,6,7,8,9,10
                 ]
        },
        explorer: {
          maxZoomOut:2,
          keepInBounds: true
        },
        vAxis: {
          viewWindow: {
            max: 30
          }
        }
      };

       var survivalOptions = {
        title: 'Kaplan-Meier Survival Estimator',
        width: 900,
        height: 500,
        // Gives each series an axis that matches the vAxes number below.
        series: {
          0: {targetAxisIndex: 0},
         
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'Survival (%)'}
          
        },
        hAxis: {
          ticks: [0,1,2,3,4,5,6,7,8,9,10
                 ]
        },
        explorer: {
          maxZoomOut:2,
          keepInBounds: true
        },
        vAxis: {
          viewWindow: {
            max: 100
          }
        }
      };

      //materialChart = new google.charts.Line(materialDiv);
     recurrenceChart = new google.visualization.LineChart(recurrenceDiv);
     survivalChart = new google.visualization.LineChart(survivalDiv);

      recurrenceChart.draw(data2, recurrenceOptions);
      survivalChart.draw(data1, survivalOptions);
      //materialChart.draw(data, materialOptions);

    
    }