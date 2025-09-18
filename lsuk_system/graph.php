<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
</script>
<div id="chart_div" style="width:680px;"></div>
<script>


function drawChart() {
    
    var chartDataRaw = [
	<?php include'db.php';//$query="SELECT dated FROM invoice where dated >= date_sub(now(), interval 12 month) 
$i=0;
$query_outer="SELECT distinct(EXTRACT( YEAR_MONTH FROM `dated` )) as distYrMonth FROM invoice
			where dated >= date_sub(now(), interval 12 month) 
			
			";						
			$result_outer = mysqli_query($con,$query_outer);
			while($row_outer = mysqli_fetch_array($result_outer)){$distYrMonth=$row_outer['distYrMonth'];



		 	$query="SELECT count(month(dated)) as totalRec FROM invoice
			where dated >= date_sub(now(), interval 12 month) and EXTRACT( YEAR_MONTH FROM `dated` )=$distYrMonth
			
			";						
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){//echo "".$mon=$row['mon'];
			$totalRec=$row['totalRec']; ?>
	{
        "month": "<?php echo $distYrMonth; ?>",
            "<?php echo 'articles'; ?>": <?php echo $totalRec; ?>
    }, 
	<?php $i++;}} ?>
	 ];

    var dataTable = new google.visualization.DataTable();

    dataTable.addColumn('date', 'Month');
    dataTable.addColumn('number', 'Registered Cases');

    var i = 1;

    //chartDataRaw is array of objects, requested from server. looped through jquery each to fill dataTable
    $.each(chartDataRaw, function () {

        var year = this.month.substring(0, 4);
        var month = this.month.substring(4);

        var dataItem = [new Date(year, month), this.articles];

        dataTable.addRow(dataItem);

    });

    var options = {
        title: 'Company Coverage',
        hAxis: {
            title: 'Last 12 Months',
            titleTextStyle: {
                color: 'red'
            },
            format: 'MMM, yyyy',
            fontSize: '8px'
        },
        vAxis: {
            textPosition: 'none'
        },
        trendlines: {
            0: {
                color: 'black',
                lineWidth: 3,
                opacity: 0.4
            }
        },
        legend: 'none'
    };

    var monthYearFormatter = new google.visualization.DateFormat({
        pattern: "MMM, yyyy"
    });
    monthYearFormatter.format(dataTable, 0); //change date format to render on chart


    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(dataTable, options);
}

drawChart();
</script>