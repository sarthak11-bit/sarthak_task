<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Add Employees
        </button>

        <!-- Modal -->
        <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Employee Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="employeeForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="employeeCode">Employee Code:</label>
                            <input type="text" class="form-control" id="employeeCode" name="employeeCode" value="EMP-0001" readonly>
                        
                        </div>
                        <div class="form-group">
                            <label for="firstName">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="firstName" > 
                            <small class="text-danger" id="firstName_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="lastName" >
                            <small class="text-danger" id="lastName_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="joiningDate">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="joiningDate" name="joiningDate" >
                            <small class="text-danger" id="joiningDate_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="profileImage">Profile Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control-file" id="profileImage" name="profileImage" accept="image/*" >
                            <small class="text-danger" id="profileImage_error"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
        

        <h2 class="mt-5">Employee List</h2>
        <form id="employeeFilterForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3">
                    <label for="startDate">Start Date:</label>
                    <input type="date" name="startDate" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label for="endDate">End Date:</label>
                    <input type="date" name="endDate" class="form-control" id="endDate">
                </div>
                <div class="col-md-3">
                    <button type="submit" name="submit" id="filterButton" class="btn btn-primary mt-4">Filter</button>
                </div>
            </div>
        </form>
        <table id="employeeTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Employee Code</th>
                    <th>Profile Image</th>
                    <th>Full Name</th>
                    <th>Joining Date</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
           
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#employeeTable').DataTable();


            $('#employeeForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '/employee',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        
                        loadEmployees();
                    },
                    error: function(err) {
                        $('#firstName_error').text('');
                        $('#lastName_error').text('');
                        $('#joiningDate_error').text('');
                        $('#profileImage_error').text(''); 

                        if (err.status === 422) { 
                            var errors = err.responseJSON.errors;
                            $.each(errors, function (key, val) {
                                $('#' + key + '_error').text(val); 
                            });
                        }
                    }
                });
            });

            $('#employeeFilterForm').on('submit', function(e) {
                e.preventDefault(); 
                loadEmployees(); 
            });

            function loadEmployees() {

                let startDate = $('#startDate').val();
                let endDate = $('#endDate').val();

                
                $.ajax({
                    url: '/employees',
                    type: 'GET',
                    data: {
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function(data) {
                        $('#employeeTable tbody').empty();  
                        
                        data.forEach(function(employee) {
                            
                            let row = '<tr>';
                            row += '<td>' + employee.employeeCode + '</td>';
                            row += '<td><img src="/storage/' + employee.profileImage + '" width="50" height="50"></td>';
                            row += '<td>' + employee.firstName + ' ' + employee.lastName + '</td>';
                            row += '<td>' + employee.joiningDate + '</td>';
                            row += '</tr>';

                            $('#employeeTable tbody').append(row);
                        });
                    }
                });
            }

            loadEmployees();

        });
    </script>
</body>
</html>
