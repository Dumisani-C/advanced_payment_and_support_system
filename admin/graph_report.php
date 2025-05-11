<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
        <?php include('db_connect.php')?>
        <?php include('../code/graph_reports.php')?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <h4 class="header-title">Fill all fields</h4> -->
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="">Date From</label>
                                    <input type="date" id="start" name="start" required class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Date To</label>
                                    <input type="date" id="end" name="end" required class="form-control">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <button type="submit" name="view" class="ladda-button btn btn-primary" data-style="expand-right">View Report</button>
                                </div>
                            </div>
                        </form>
                        <!--End Patient Form-->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
            <br><br><br>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <!-- <canvas id="myChart"></canvas> -->
                            <canvas id="barGraph"></canvas>
                            <hr>
                            <div id="buttonContainer"></div>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
            <hr>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <!-- <canvas id="myChart"></canvas> -->
                            <canvas id="pieChart"></canvas>
                            <hr>
                            <div id="buttonDownloadPie"></div>
                        </div>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
    </div>
</div>
<script>
    // Parse the JSON data from PHP
    var jsonData = <?php echo isset($jsonData) ? $jsonData : "[]" ?>;

    // Prepare arrays for labels and data
    var labels = [];
    var values = [];
    jsonData.forEach(function(item) {
        labels.push(item.apartment_no);
        values.push(item.total_rent);
    });

    // Create the chart using Chart.js
    var ctx = document.getElementById('barGraph').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rental payments from <?php echo $startDate; ?> to <?php echo $endDate; ?>',
                data: values,
                backgroundColor: [
                    "#5969ff",
                    "#ff407b",
                    "#25d5f2",
                    "#ffc750",
                    "#2ec551",
                    "#7040fa",
                    "#ff004e"
                    ],
                // backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<script>
    // Parse the JSON data from PHP
    var jsonData = <?php echo isset($jsonData) ? $jsonData : "[]" ?>;

    // Prepare arrays for labels and data
    var labels = [];
    var values = [];
    jsonData.forEach(function(item) {
        labels.push(item.apartment_no);
        values.push(item.total_rent);
    });

    // Create the chart using Chart.js
    var ctx = document.getElementById('pieChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rental payments from <?php echo $startDate; ?> to <?php echo $endDate; ?>',
                data: values,
                backgroundColor: [
                    "#5969ff",
                    "#ff407b",
                    "#25d5f2",
                    "#ffc750",
                    "#2ec551",
                    "#7040fa",
                    "#ff004e"
                    ],
                // backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<script>
    // Function to print the chart
    function printChart() {
        var canvas = document.getElementById('barGraph');
        var win = window.open();
        win.document.write("<br><img src='" + canvas.toDataURL() + "'/>");
        win.print();
        win.location.reload();
    }

    // Function to download the chart as an image
    function downloadChart() {
        var canvas = document.getElementById('barGraph');
        var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
        var link = document.createElement('a');
        link.download = 'chart.png';
        link.href = image;
        link.click();
    }

    var downloadButton = document.createElement('button');
    downloadButton.textContent = 'Download Graph';
    downloadButton.onclick = downloadChart;

    // Append buttons to a container element
    var buttonContainer = document.getElementById('buttonContainer');
    buttonContainer.appendChild(downloadButton);
</script>
<script>
    // Function to print the chart
    function printChart() {
        var canvas = document.getElementById('pieChart');
        var win = window.open();
        win.document.write("<br><img src='" + canvas.toDataURL() + "'/>");
        win.print();
        win.location.reload();
    }

    // Function to download the chart as an image
    function downloadChart() {
        var canvas = document.getElementById('pieChart');
        var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
        var link = document.createElement('a');
        link.download = 'chart.png';
        link.href = image;
        link.click();
    }

    var downloadButton = document.createElement('button');
    downloadButton.textContent = 'Download Chart';
    downloadButton.onclick = downloadChart;

    // Append buttons to a container element
    var buttonContainer = document.getElementById('buttonDownloadPie');
    buttonContainer.appendChild(downloadButton);
</script>
</body>
</html>