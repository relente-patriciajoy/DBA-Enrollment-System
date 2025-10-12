<?php include_once '../../config/database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/content.css">
</head>
<body>
    <div class="wrapper d-flex">
        <?php include_once '../../templates/sidebar.php'; ?>
        <div class="main">
            <div class="container-fluid">
                <h1 class="mt-4 mb-4">Student Management</h1>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="search-bar">
                            <form action="" method="GET">
                                <input type="text" name="search" class="form-control" placeholder="Search students...">
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="add_student.php" class="btn btn-primary">Add Student</a>
                        <a href="export.php" class="btn btn-success">Export</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php include 'tblstudent.php'; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>