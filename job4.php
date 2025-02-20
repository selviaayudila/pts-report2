<?php include('job3.php'); ?>

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
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">
</head>
<body>

<?php include('nav.php'); ?>
<?php include('sidebar.php'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Monthly Report by MachineID</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Report</a></li>
            <li class="breadcrumb-item active">Report Machine</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  
  <section class="content">
    <div class="card">
      <div class="card-body">
        <table id="machineTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Actual Machine</th>
                    <th>Total Output</th>
                    <?php foreach (array_keys($rejectTypes) as $rejectType) { echo "<th>{$rejectType}</th>"; } ?>
                    <th>Total Reject</th> <!-- Kolom Total Reject -->
                    <?php foreach (array_keys($downtimeTypes) as $downtimeType) { echo "<th>{$downtimeType}</th>"; } ?>
                    <th>Total Downtime</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($machineResults as $machineID => $data): ?>
<tr>
    <td><?= $machineID ?></td>
    <td><?= number_format($data['totalOutput'], 0, ',', '.') ?></td> <!-- Format angka -->
    
    <?php foreach ($rejectTypes as $rejectType => $_) { ?>
        <td><?= $data['rejects'][$rejectType] ?? 0 ?></td>
    <?php } ?>

    <td>
        <?php 
            $totalReject = array_sum($data['rejects'] ?? []); 
            echo $totalReject; 
        ?>
    </td> <!-- Menampilkan total reject -->

    <?php foreach ($downtimeTypes as $downtimeType => $_) { ?>
        <td><?= $data['downtimes'][$downtimeType] ?? 0 ?></td>
    <?php } ?>

    <td><?= $data['totalDowntime'] ?? 0 ?></td>
</tr>
<?php endforeach; ?>

            </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>

<script>
  $(document).ready(function() {
  function getMonthYearFromURL() {
    let urlParams = new URLSearchParams(window.location.search);
    let month = urlParams.get('month'); // Ambil bulan dari parameter
    let year = urlParams.get('year'); // Ambil tahun dari parameter

    let monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    if (!month || !year) {
      Swal.fire({
        title: 'Missing Parameters!',
        text: 'Month and Year are required in the URL. Example: ?month=09&year=2024',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return "Report";
    }

    let monthIndex = parseInt(month, 10) - 1; // Konversi ke index array (0-based)
    if (monthIndex < 0 || monthIndex >= 12 || isNaN(monthIndex) || isNaN(year)) {
      Swal.fire({
        title: 'Invalid Parameters!',
        text: 'Please enter a valid Month (1-12) and Year.',
        icon: 'error',
        confirmButtonText: 'OK'
      });
      return "Report";
    }

    return monthNames[monthIndex] + "_" + year;
  }

  let reportFilename = getMonthYearFromURL();

  $('#machineTable').DataTable({
    scrollX: true,
    autoWidth: false,
    lengthChange: true,
    searching: true,
    paging: true,
    info: true,
    buttons: [
      {
        extend: "excelHtml5",
        text: "Excel",
        filename: function () { return 'Machine_Report_' + reportFilename; },
        exportOptions: {
          columns: ':visible',
          format: {
            body: function(data, row, column, node) {
              return column === 7 ? data.replace(/\./g, '').replace(/,/g, '') : data;
            }
          }
        },
        action: function (e, dt, button, config) {
          $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
          Swal.fire({
            title: 'Download Started!',
            text: 'Your Excel file is being downloaded. Check your Download folder.',
            icon: 'success',
            confirmButtonText: 'OK'
          });
        }
      },
      {
        extend: "csvHtml5",
        text: "CSV",
        filename: function () { return 'Machine_Report_' + reportFilename; },
        exportOptions: {
          columns: ':visible',
          format: {
            body: function(data, row, column, node) {
              return column === 7 ? data.replace(/\./g, '').replace(/,/g, '') : data;
            }
          }
        },
        action: function (e, dt, button, config) {
          $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
          Swal.fire({
            title: 'Download Started!',
            text: 'Your CSV file is being downloaded. Check your Download folder.',
            icon: 'success',
            confirmButtonText: 'OK'
          });
        }
      },
      {
        extend: "copy",
        text: "Copy",
        action: function (e, dt, button, config) {
          $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
          Swal.fire({
            title: 'Data Copied!',
            text: 'Data has been successfully copied to your clipboard.',
            icon: 'success',
            confirmButtonText: 'OK'
          });
        }
      },
      {
        extend: "print",
        text: "Print",
        action: function (e, dt, button, config) {
          $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
          Swal.fire({
            title: 'Print Opened!',
            text: 'If it doesn’t appear, check your pop-up blocker.',
            icon: 'info',
            confirmButtonText: 'OK'
          });
        }
      }
    ]
  }).buttons().container().appendTo('#machineTable_wrapper .col-md-6:eq(0)');
});

</script>
</body>
</html>
