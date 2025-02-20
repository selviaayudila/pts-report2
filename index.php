<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="dist/img/logo.png" type="image/png">
  <title>Sanwa | Report</title>

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

  <style>
  .dropdown-menu {
    border-radius: 10px;
    min-width: 220px;
  }
  .dropdown-item {
    padding: 10px 15px;
    transition: background 0.3s ease;
  }
  .dropdown-item:hover {
    background:rgb(255, 255, 255);
  }
</style>

</head>
<body>

<?php include('nav.php'); ?>
<?php include('sidebar.php'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Report</a></li>
            <li class="breadcrumb-item active">Report View</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <!-- Alert petunjuk -->
      <div class="alert alert-info m-3" role="alert">
      Make sure you have done <strong>Download Data</strong> before <strong>Generate Report</strong>.
      </div>
      <div class="card-header d-flex justify-content-left align-items-center">
        <!-- Tombol Download Data -->
        <button id="downloadDataBtn" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addDatabaseModal">
          <i class="fas fa-download"></i> Download Data
        </button>
        <!-- Tombol Generate Report dengan dropdown, nonaktif secara default -->
<div class="btn-group">
  <button id="generateReportBtn" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
    <i class="fas fa-folder-open"></i> Generate Report
  </button>
          <div class="dropdown-menu">
            <button class="dropdown-item" data-toggle="modal" data-target="#selectMonthYearModal">
              <i class="fas fa-tasks"></i> Report by JobOrderID
            </button>
            <button class="dropdown-item" data-toggle="modal" data-target="#selectMonth2YearModal">
              <i class="fas fa-cogs"></i> Report by MachineID
            </button>
            <button class="dropdown-item" data-toggle="modal" data-target="#machineStatusModal">
              <i class="fas fa-chart-line"></i> Machine Status Report
            </button>
          </div>
        </div>
      </div>
        <div class="card-body">
          <table id="dataTable" class="table table-bordered table-striped">
            <thead>
              <tr>
              <th>PostingDate</th>
              <th>CustName</th>
              <th>PartNo</th>
              <th>PartName</th>
              <th>MoldNo</th>
              <th>JSNo</th>
              <th>OprShift</th>
              <th>AOutput</th>
              <th>ACavity</th>
              <th>QCV</th>
              <th>QCT</th>
              <th>ACT</th>
              <th>ShiftHours</th>
              <th>NumOpr</th>
              <th>ActualMC</th>
              <th>MCTonnage</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Silver Streak</th>
              <th>Short Molding</th>
              <th>Dented</th>
              <th>Sink Mark</th>
              <th>Burn Mark</th>
              <th>Bubble</th>
              <th>Black Dot</th>
              <th>Scratches</th>
              <th>Flow Mark</th>
              <th>Dim Out</th>
              <th>Discolouration</th>
              <th>Shiny</th>
              <th>White Mark</th>
              <th>Flashes</th>
              <th>Drag Mark</th>
              <th>Oily Mark</th>
              <th>Over Cut</th>
              <th>Pin Mark</th>
              <th>Wrinkle</th>
              <th>Weld Line</th>
              <th>Pin Broken</th>
              <th>Damage</th>
              <th>Metal Chip</th>
              <th>Crack</th>
              <th>Part Drop to Floor</th>
              <th>Total Reject</th>
              <th>NPO</th>
              <th>TPM</th>
              <th>MOC</th>
              <th>T</th>
              <th>MSSD</th>
              <th>DMC</th>
              <th>NMP</th>
              <th>PA</th>
              <th>QP</th>
              <th>MB</th>
              <th>PM</th>
              <th>MOR1</th>
              <th>MOR2</th>
              <th>Total Downtime</th>
              <th>JOS</th>
              <th>EOS</th>

                
              </tr>
            </thead>
            <tbody>
              <!-- Data akan dimuat di sini -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Pilih Bulan dan Tahun -->
<div class="modal fade" id="addDatabaseModal" tabindex="-1" role="dialog" aria-labelledby="addDatabaseModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDatabaseModalLabel">Database Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Pesan Info -->
        <div class="alert alert-warning" role="alert">
          <i class="fas fa-info-circle"></i> Before downloading the PTS report, first download the data from the database, because this data will be used to generate report.
        </div>

        <form id="filterForm" method="post">
          <div class="form-group">
            <label for="selectMonth">Month</label>
            <select class="form-control" id="selectMonth" name="month" required>
              <option value="">Select Month</option>
              <?php
              for ($m = 1; $m <= 12; $m++) {
                  $monthName = date('F', mktime(0, 0, 0, $m, 1));
                  $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT); // Format dengan 2 digit
                  echo "<option value=\"$monthValue\">$monthName</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="selectYear">Year</label>
            <select class="form-control" id="selectReportYear" name="year" required>
            <option value="">Select Year</option>
            <?php
            $currentYear = date("Y");
            $startYear = 2024; // Ganti dengan tahun terawal yang memiliki data

            for ($y = $currentYear; $y >= $startYear; $y--) {
                echo "<option value=\"$y\">$y</option>";
            }
            ?>
        </select>

          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="filterButton">Export</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Pilih Bulan dan Tahun untuk Laporan -->
<div class="modal fade" id="selectMonthYearModal" tabindex="-1" role="dialog" aria-labelledby="selectMonthYearModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectMonthYearModalLabel">Monthly Report by JobOrderID</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Pesan Info -->
        <div class="alert alert-warning" role="alert">
          <i class="fas fa-info-circle"></i> After downloading the database data, please select the month and year to generate a report based on JobOrderID.
        </div>

        <form id="selectMonthYearForm" method="GET" action="job2.php">
          <div class="form-group">
            <label for="selectReportMonth">Month</label>
            <select class="form-control" id="selectReportMonth" name="month" required>
              <option value="">Select Month</option>
              <?php
              for ($m = 1; $m <= 12; $m++) {
                  $monthName = date('F', mktime(0, 0, 0, $m, 1));
                  $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT); // Format dengan 2 digit
                  echo "<option value=\"$monthValue\">$monthName</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="selectReportYear">Year</label>
            <select class="form-control" id="selectReportYear" name="year" required>
              <option value="">Select Year</option>
              <?php
              $currentYear = date("Y");
              $startYear = 2024; // Ganti dengan tahun terawal yang memiliki data

              for ($y = $currentYear; $y >= $startYear; $y--) {
                  echo "<option value=\"$y\">$y</option>";
              }
              ?>
          </select>

          </div>
        </form>
      </div> <!-- Tutup modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="filterReportButton">Display</button>
        
      </div>
    </div>
  </div>
</div>

<!-- Modal Pilih Bulan dan Tahun untuk Laporan -->
<div class="modal fade" id="selectMonth2YearModal" tabindex="-1" role="dialog" aria-labelledby="selectMonth2YearModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectMonth2YearModalLabel">Monthly Report by MachineID</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Pesan Info -->
        <div class="alert alert-warning" role="alert">
          <i class="fas fa-info-circle"></i> After downloading the database data, please select the month and year below if you want to view the report based on MachineID.
        </div>

        <form id="selectMonth2YearForm" method="GET" action="job4.php">

          <div class="form-group">
            <label for="selectReportMonth">Month</label>
            <select class="form-control" id="selectReportMonth" name="month" required>
              <option value="">Select Month</option>
              <?php
              for ($m = 1; $m <= 12; $m++) {
                  $monthName = date('F', mktime(0, 0, 0, $m, 1));
                  $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT); // Format dengan 2 digit
                  echo "<option value=\"$monthValue\">$monthName</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="selectReportYear">Year</label>
            <select class="form-control" id="selectReportYear" name="year" required>
            <option value="">Select Year</option>
            <?php
            $currentYear = date("Y");
            $startYear = 2024; // Ganti dengan tahun terawal yang memiliki data

            for ($y = $currentYear; $y >= $startYear; $y--) {
                echo "<option value=\"$y\">$y</option>";
            }
            ?>
        </select>

          </div>
        </form>
      </div> <!-- Tutup modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="filterReportButton2">Display</button>
        
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="machineStatusModal" tabindex="-1" aria-labelledby="machineStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="machineStatusModalLabel">Machine Status Report | Downtime Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="machineStatusForm">
          <!-- Dropdown Pilihan Waktu -->
          <div class="form-group">
            <label for="selectPeriod">Select Period</label>
            <select class="form-control" id="selectPeriod" name="period">
              <option value="today">Today</option>
              <option value="yesterday">Yesterday</option>
              <option value="this_week">This Week</option>
              <option value="last_week">Last Week</option>
              <option value="this_month">This Month</option>
              <option value="last_month">Last Month</option>
              <option value="custom">Custom</option>
            </select>
          </div>

          <!-- Input Custom Date (Hidden by Default) -->
          <div id="customDateSection" class="d-none">
            <div class="form-group">
              <label for="dateFrom">From:</label>
              <input type="date" class="form-control" id="dateFrom" name="from_date">
            </div>
            <div class="form-group">
              <label for="toDate">To:</label>
              <input type="date" class="form-control" id="toDate" name="to_date">
            </div>
          </div>

          <!-- Dropdown Pilihan Kategori Mesin -->
          <div class="form-group">
            <label for="machineCategory">Select Line</label>
            <select class="form-control" id="machineCategory" name="machine_category">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="E">E</option>
              <option value="F">F</option>
              <option value="G">G</option>
            </select>
          </div>

          <!-- Daftar Checkbox Mesin dengan Scrollbar -->
          <div class="form-group">
            <label>Select Machine</label>
            <div id="machineList" class="border p-2 rounded" style="height: 150px; overflow-y: auto;"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submitBtn">Submit</button>
      </div>
    </div>
  </div>
</div>


<!-- Footer -->
<?php include 'footer.php'; ?>

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

<script>
  $(document).ready(function () {
    $("#dataTable").DataTable({
      "responsive": false,
      "scrollX": true,
      "autoWidth": false,
      "lengthChange": true,
      "columnDefs": [
        { "width": "100px", "targets": "_all" }
      ]
    }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');

    $('#dataTable').css({
      'table-layout': 'fixed',
      'word-wrap': 'break-word'
    });

    $('#filterButton').click(function () {
      const form = $('#filterForm');
      if (form[0].checkValidity()) {
        // Serialize form data
        const formData = form.serializeArray();
        
        // Get month and year values from the form
        const month = formData.find(item => item.name === 'month').value;
        const year = formData.find(item => item.name === 'year').value;

        // Show a loading message or spinner
        const loadingMessage = $('<p>Loading, please wait...</p>').appendTo('.modal-body');

        // Use AJAX to send the form data
        $.post('process.php', $.param(formData), function (response) {
          // Remove loading message
          loadingMessage.remove();
          // Handle the response
          alert(`Database data (${month} ${year}) successfully exported!`);
          location.reload(); // Reload to reflect changes if needed
        }).fail(function (xhr, status, error) {
          // Remove loading message
          loadingMessage.remove();
          alert('An error occurred: ' + xhr.status + ' ' + error);
        });
      } else {
        alert('Please select both month and year!');
      }
    });
  });

  document.getElementById("filterReportButton").addEventListener("click", function() {
    document.getElementById("selectMonthYearForm").submit();
    $('#selectMonthYearModal').modal('hide'); // Tutup modal setelah tombol ditekan
});

  document.getElementById("filterReportButton2").addEventListener("click", function() {
    document.getElementById("selectMonth2YearForm").submit();
    $('#selectMonth2YearModal').modal('hide'); // Tutup modal setelah tombol ditekan
});

</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const selectPeriod = document.getElementById("selectPeriod");
    const customDateSection = document.getElementById("customDateSection");
    const machineCategory = document.getElementById("machineCategory");
    const machineList = document.getElementById("machineList");
    const submitBtn = document.getElementById("submitBtn");

    const machineData = {
        A: [...Array(15).keys()].map(i => `A${i + 1}`).filter(machine => machine !== "A4"),
        B: [...Array(17).keys()].map(i => `B${i + 1}`).filter(machine => !["B14", "B15"].includes(machine)),
        C: [...Array(19).keys()].map(i => `C${i + 1}`),
        D: [...Array(7).keys()].map(i => `D${i + 3}`), // Dimulai dari D3
        E: [...Array(16).keys()].map(i => `E${i + 1}`).filter(machine => !["E4", "E14"].includes(machine)),
        F: [...Array(6).keys()].map(i => `F${i + 1}`).filter(machine => machine !== "F4"),
        G: [...Array(12).keys()].map(i => `G${i + 1}`).filter(machine => machine !== "G4")
    };


    selectPeriod.addEventListener("change", function () {
        customDateSection.classList.toggle("d-none", selectPeriod.value !== "custom");
    });

    function updateMachineList() {
        machineList.innerHTML = "";
        const selectedCategory = machineCategory.value;

        if (machineData[selectedCategory]) {
            machineData[selectedCategory].forEach(machine => {
                const div = document.createElement("div");
                div.classList.add("form-check");
                div.innerHTML = `
                    <input type="checkbox" class="form-check-input" id="${machine}" name="machines[]" value="${machine}">
                    <label class="form-check-label" for="${machine}">${machine}</label>
                `;
                machineList.appendChild(div);
            });
        }
    }

    machineCategory.addEventListener("change", updateMachineList);
    updateMachineList();

    submitBtn.addEventListener("click", async function () {
    const period = selectPeriod.value;
    const dateFrom = document.getElementById("dateFrom").value;
    const toDate = document.getElementById("toDate").value;
    const selectedMachines = Array.from(document.querySelectorAll("#machineList input:checked"))
        .map(machine => machine.value);

    if (selectedMachines.length === 0) {
        alert("Please select at least one machine.");
        return;
    }

    if (period === "custom" && (!dateFrom || !toDate)) {
        alert("Please select both start and end dates for custom period.");
        return;
    }

    const requestData = {
        timePeriod: period,
        machines: selectedMachines
    };

    if (period === "custom") {
        requestData.dateFrom = dateFrom;
        requestData.dateTo = toDate;
    }

    try {
        const response = await fetch("api.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(errorResponse.error || "Unknown error");
        }

        const chartData = await response.json();
        console.log("Success:", chartData);

        alert("Report submitted successfully!");
        $("#machineStatusModal").modal("hide");

        // Hapus data lama dari localStorage sebelum menyimpan yang baru
        localStorage.removeItem("chartData");
        localStorage.setItem("chartData", JSON.stringify(chartData));

        // Redirect ke chart.php tanpa membawa data di URL
        window.location.href = "chart.php";

    } catch (error) {
        console.error("Error:", error);
        alert("Failed to submit report: " + error.message);
    }
});

});
</script>

<!-- Script untuk mengaktifkan tombol Generate Report setelah Download Data dilakukan -->
<script>
  $(document).ready(function () {
    // Cek apakah sebelumnya tombol sudah diaktifkan dalam sesi ini
    if (sessionStorage.getItem('reportReady') === 'true') {
      $('#generateReportBtn').prop('disabled', false);
    }
  });

  // Ketika modal Download Data (#addDatabaseModal) ditutup, aktifkan tombol
  $('#addDatabaseModal').on('hidden.bs.modal', function () {
    $('#generateReportBtn').prop('disabled', false);
    sessionStorage.setItem('reportReady', 'true'); // Simpan status tombol dalam sessionStorage
  });

  // Optional: Tampilkan peringatan jika tombol masih nonaktif
  $('#generateReportBtn').on('click', function(e) {
    if ($(this).prop('disabled')) {
      e.preventDefault();
      alert('Please download the data first before generating the report.');
    }
  });
</script>
</body>
</html>

