<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="dist/img/logo.png" type="image/png">
  <title>Sanwa | Report</title>

<script src="plugins/chart.js/d3.v7.min.js"></script>
    <!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>


<link href="plugins/chart.js/tailwind.min.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <?php include('nav.php'); ?>
<?php include('sidebar.php'); ?>
    
    <style>
        .chart-container {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .chart-scroll-container {
            position: relative;
            overflow: auto;
            border: 1px solid #ddd;
        }

        /* Custom scrollbar styling */
        .chart-scroll-container::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        .chart-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }

        .chart-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }

        .chart-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .machine-bar {
            fill: #90EE90;
            opacity: 0.7;
        }

        .downtime-bar {
            fill:rgb(255, 141, 96);
            opacity: 0.9;
        }

        .tooltip {
            position: absolute;
            padding: 8px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            pointer-events: none;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .axis-label {
            font-size: 14px;
            font-weight: bold;
        }

        .grid-line {
            stroke: #ddd;
            stroke-width: 1;
            stroke-dasharray: 4, 4;
        }

        </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">



<body class="bg-gray-100 min-h-screen p-6">
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Machine Status</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Report</a></li>
            <li class="breadcrumb-item active">Downtime Report</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="card">
      
        <!-- Tombol Pilih Bulan dan Tahun -->
        <div class="mb-4">  
        </div>

        <div class="chart-container">
        <h1 class="text-2xl font-bold mb-6" style="color:rgb(248, 145, 89);">Machine Downtime Chart</h1>
            <div id="chart-wrapper">
                <div class="chart-scroll-container" id="chart"></div>
          
          </div>
        </div>
      </div>
    </section>
  </div>

    <!-- Footer -->
<?php include 'footer.php'; ?>

</div>

    <script>
  document.addEventListener("DOMContentLoaded", function () {
    // Ambil data dari localStorage
    const storedData = localStorage.getItem("chartData");
    if (!storedData) {
        console.error("Tidak ada data ditemukan di localStorage");
        return;
    }
    
    const data = JSON.parse(storedData);
    console.log("Data diterima dari localStorage:", data);

    function createChart() {
        d3.select("#chart").selectAll("*").remove();
        
        const containerWidth = document.getElementById('chart-wrapper').offsetWidth;
        const containerHeight = window.innerHeight * 0.6;
        
        const minHeightPerMachine = 80;
        const minHeightNeeded = data.machines.length * minHeightPerMachine + 100;
        
        const margin = { top: 40, right: 40, bottom: 60, left: 80 };
        
        const startDate = new Date(data.chartDuration.from);
        const endDate = new Date(data.chartDuration.to);
        const dayCount = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        const dayWidth = 100;
        const width = dayCount * dayWidth;
        const finalWidth = Math.max(width, containerWidth - margin.left - margin.right - 20);
        const height = Math.max(300, minHeightNeeded);
        
        const scrollContainer = document.querySelector('.chart-scroll-container');
        scrollContainer.style.maxHeight = `${Math.min(containerHeight, Math.max(height + margin.top + margin.bottom, window.innerHeight * 0.3))}px`;
        
        const svg = d3.select("#chart")
            .append("svg")
            .attr("width", finalWidth + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", `translate(${margin.left},${margin.top})`);
        
        const timeScale = d3.scaleTime()
            .domain([startDate, endDate])
            .range([0, finalWidth]);

        const machineScale = d3.scaleBand()
            .domain(data.machines.map(d => d.machine_id))
            .range([0, height])
            .padding(0.3);

        const days = timeScale.ticks(d3.timeDay);
        svg.selectAll("line.grid-line")
            .data(days)
            .enter()
            .append("line")
            .attr("class", "grid-line")
            .attr("x1", d => timeScale(d))
            .attr("x2", d => timeScale(d))
            .attr("y1", 0)
            .attr("y2", height);

        const customTimeFormat = d3.timeFormat("%-d-%b");

        svg.append("g")
            .attr("transform", `translate(0,${height})`)
            .call(d3.axisBottom(timeScale).ticks(d3.timeDay.every(1)).tickFormat(customTimeFormat))
            .selectAll("text")
            .style("text-anchor", "middle")
            .attr("dx", "0")
            .attr("dy", "1em")
            .style("font-size", "12px");

        svg.append("g")
            .call(d3.axisLeft(machineScale))
            .selectAll("text")
            .style("font-size", "12px")
            .style("font-weight", "bold");

              // Add axis labels
              svg.append("text")
                .attr("class", "axis-label")
                .attr("text-anchor", "middle")
                .attr("x", finalWidth / 2)
                .attr("y", height + margin.bottom - 5)
                .text("Date");

            svg.append("text")
                .attr("class", "axis-label")
                .attr("text-anchor", "middle")
                .attr("transform", "rotate(-90)")
                .attr("y", -margin.left + 30)
                .attr("x", -height / 2)
                .text("Machines");

            // Create tooltip
            const tooltip = d3.select("body")
                .append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);

        svg.selectAll(".machine-bar")
            .data(data.machines)
            .enter()
            .append("rect")
            .attr("class", "machine-bar")
            .attr("x", 0)
            .attr("y", d => machineScale(d.machine_id))
            .attr("width", finalWidth)
            .attr("height", machineScale.bandwidth());

        // Draw downtime bars
            data.machines.forEach(machine => {
                // Gambar downtime bar
                svg.selectAll(".downtime-bar-" + machine.machine_id)
                    .data(machine.downtimes)
                    .enter()
                    .append("rect")
                    .attr("class", "downtime-bar")
                    .attr("x", d => timeScale(new Date(d.start)))
                    .attr("y", machineScale(machine.machine_id))
                    .attr("width", d => {
                        const startTime = new Date(d.start);
                        const endTime = new Date(d.end);
                        return Math.max(timeScale(endTime) - timeScale(startTime), 1);
                    })
                    .attr("height", machineScale.bandwidth())
                    .on("mouseover", function(event, d) {
                        const startDate = new Date(d.start);
                        const endDate = new Date(d.end);
                        const duration = (endDate - startDate) / (1000 * 60 * 60);

                        tooltip.transition()
                            .duration(200)
                            .style("opacity", 0.9);
                        tooltip.html(
                                `<strong>Reason:</strong> ${d.reason}<br>` +
                                `<strong>Start:</strong> ${startDate.toLocaleString()}<br>` +
                                `<strong>End:</strong> ${endDate.toLocaleString()}<br>` +
                                `<strong>Duration:</strong> ${duration.toFixed(1)} hours`
                            )
                            .style("left", (event.pageX + 10) + "px")
                            .style("top", (event.pageY - 28) + "px");
                    })
                    .on("mouseout", function() {
                        tooltip.transition()
                            .duration(500)
                            .style("opacity", 0);
                    });
            });

            svg.selectAll(".downtime-label-" + machine.machine_id)
                .data(machine.downtimes)
                .enter()
                .append("text")
                .attr("class", "downtime-label")
                // Tempatkan di tengah-tengah secara horizontal: posisi x = x(start) + setengah lebar bar
                .attr("x", d => {
                    const start = new Date(d.start);
                    const end = new Date(d.end);
                    return timeScale(start) + (timeScale(end) - timeScale(start)) / 2;
                })
                // Tempatkan di tengah-tengah secara vertikal: posisi y = y(machine) + setengah tinggi bar
                .attr("y", d => machineScale(machine.machine_id) + machineScale.bandwidth() / 2)
                .attr("dy", ".35em") // Untuk penyesuaian vertikal teks
                .attr("text-anchor", "middle")
                // Format tanggal, misalnya "5-Feb"
                .text(d => {
                    const start = new Date(d.start);
                    return d3.timeFormat("%-d-%b")(start);
                })
                .style("fill", "#000") // Warna teks (sesuaikan jika perlu)
                .style("pointer-events", "none"); // Pastikan teks tidak mengganggu interaksi mouse


            // Add legend
            const legend = svg.append("g")
                .attr("font-family", "sans-serif")
                .attr("font-size", 10)
                .attr("text-anchor", "start")
                .selectAll("g")
                .data([{
                        label: "Normal Operation",
                        color: "#90EE90"
                    },
                    {
                        label: "Downtime",
                        color: "#FFA07A"
                    }
                ])
                .enter().append("g")
                .attr("transform", (d, i) => `translate(${finalWidth - 120},${i * 20 - 30})`);

            legend.append("rect")
                .attr("x", 0)
                .attr("width", 19)
                .attr("height", 19)
                .attr("fill", d => d.color)
                .attr("opacity", 0.7);

            legend.append("text")
                .attr("x", 24)
                .attr("y", 9.5)
                .attr("dy", "0.32em")
                .text(d => d.label);
        }


        // Initial chart creation
        createChart();


    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            createChart();
        }, 250);
    });
});

    </script>
</body>
</html>