window.onload = function() {

// Get the document title
var naq_title = document.title;

// If the title contains the text "Filox Count Views" the script for the admin area is executed
if( naq_title.indexOf('Filox Count Views') >= 0 ){

// Connect with the select box in the admin area
document.getElementById("naq_id").addEventListener("change", drawChart);

  google.charts.load('current', {'packages':['annotationchart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart () {
          var naq_id = document.getElementById("naq_id").value;

        $.ajax({
        url: WPURLS.siteurl + "/wp-json/naq/v1/posts/" + naq_id,
        dataType: "json",
        success: function (jsonData) {
        var data = new google.visualization.DataTable();
        // var countposts = Object.keys(jsonData).length; // countposts = number of posts

        // console.log (jsonData[0][0]);
        // console.log (jsonData[0][2]);
        // console.log (jsonData[0][0]);

        // Create the columns for the google chart
        data.addColumn('date', 'Date');
        data.addColumn('number', 'Counter');
        data.addColumn('string', 'Title');

        var countdates = Object.keys(jsonData[0][0]).length;
        console.log (countdates);

        for ( i = 0; i < countdates; i++ ) {
        data.addRows([
          [new Date(jsonData[0][0][i]*1000), i+1, jsonData[0][1]]
        ]);
        }

            var options = {
              displayAnnotations: false,
              min:0
            };
            var chart = new google.visualization.AnnotationChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    });
}
}
 // If the title contains the text "Edit Post" the script for the edit post page is executed
else if ( naq_title.indexOf('Edit Post') >= 0 ) {

          google.charts.load('current', {'packages':['annotationchart']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart () {
          var naq_id = document.getElementById("hiddenField").value;
          // console.log (naq_id);

          $.ajax({


          url: WPURLS.siteurl + "/wp-json/naq/v1/posts/" + naq_id,
          dataType: "json",
          success: function (jsonData) {
          var data = new google.visualization.DataTable();
          // var countposts = Object.keys(jsonData).length; // countposts = number of posts
          // Create the columns for the google chart
          data.addColumn('datetime', 'Date');
          data.addColumn('number', 'Counter');
          data.addColumn('string', 'Title');

          var countdates = Object.keys(jsonData[0][0]).length;
          console.log (countdates);

          for ( i = 0; i < countdates; i++ ) {
          data.addRows([
            [new Date(jsonData[0][0][i]*1000), i+1, jsonData[0][1]]
          ]);
          }

              var options = {
                displayAnnotations: false,
                min: 0


              };
              var chart = new google.visualization.AnnotationChart(document.getElementById('chart_div'));
              chart.draw(data, options);
          }
      });
  }
  }

}
