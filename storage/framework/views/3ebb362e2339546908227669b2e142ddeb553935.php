

<?php $__env->startSection('non_masking_menu_class', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
	</li>
	<li>
		<a href="<?php echo e(route('admin.senderID.index')); ?>">Sender ID</a>
	</li>
	<li class="active">Non Masking</li>
</ul>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main_content'); ?>
<div class="space-6"></div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
  
            <?php if(session()->has('message')): ?>
                <div class="alert alert-<?php echo e(session()->get('alert_type')); ?> alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo e(session()->get('message')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Add New Form -->
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Add New Non-Masking Sender ID</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <form action="<?php echo e(route('admin.senderID.nonMaskingSenderID.store')); ?>" method="post" class="form-horizontal" id="addForm">
                                <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right">Non-masking</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="nonmasking" id="nonmasking" class="form-control" value="<?php echo e(old('nonmasking')); ?>" placeholder="Enter Sender ID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right">Operator</label>
                                    <div class="col-sm-9">
                                        <select name="operator_id" id="add_operator_id" class="form-control" required>
                                            <option value="">-- Select Operator --</option>
                                            <?php $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($operator->id); ?>" <?php echo e(old('operator_id') == $operator->id ? 'selected' : ''); ?>>
                                                    <?php echo e($operator->ope_operator_name); ?> (<?php echo e($operator->ope_country_code); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-info">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>
                                        &nbsp; &nbsp;
                                        <button class="btn" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2" style="margin-top: 20px;">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Import From Excel/CSV/TXT</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <form action="<?php echo e(route('admin.senderID.nonMaskingSenderID.import')); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="importForm">
                                <?php echo csrf_field(); ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right">Select Operator <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="operator_id" id="import_operator_id" class="form-control" required>
                                            <option value="">-- Select Operator for All Sender IDs --</option>
                                            <?php $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($operator->id); ?>" <?php echo e(old('operator_id') == $operator->id ? 'selected' : ''); ?>>
                                                    <?php echo e($operator->ope_operator_name); ?> (<?php echo e($operator->ope_country_code); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <span class="help-block">This operator will be applied to all sender IDs in the file</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right">Upload File</label>
                                    <div class="col-sm-9">
                                        <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                                        <span class="help-block">Supported formats: .xlsx, .xls, .csv, .txt</span>
                                        <span class="help-block text-muted">
                                            <strong>File format:</strong> One sender ID per row or comma-separated values<br>
                                            <strong>Download sample:</strong> <a href="<?php echo e(asset('sample_sender_ids.csv')); ?>" class="btn btn-xs btn-info">Sample CSV</a>
                                        </span>
                                    </div>
                                </div>
                                <div class="clearfix form-actions">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-success">
                                            <i class="ace-icon fa fa-upload bigger-110"></i>
                                            Import
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 30px;">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Non-Masking Sender IDs List</h4>
                <div class="widget-toolbar">
                    <a href="#" onclick="exportTableToCSV()" class="btn btn-xs btn-success">
                        <i class="ace-icon fa fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <table id="senderid-list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Sender ID</th>
                                <th>Operator</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php ($serial = 1); ?>
                            <?php $__currentLoopData = $nonMaskingSenderIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nonMaskingSenderId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($serial++); ?></td>
                                    <td><?php echo e($nonMaskingSenderId->number); ?></td>
                                    <td><?php echo e($nonMaskingSenderId->operator->ope_operator_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($nonMaskingSenderId->created_at->format('d M Y, h:i A')); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="blue" href="<?php echo e(route('admin.senderID.nonMaskingSenderID.edit', $nonMaskingSenderId->id)); ?>" title="Edit">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>
                                            <a class="red" href="<?php echo e(route('admin.senderID.nonMaskingSenderID.delete', $nonMaskingSenderId->id)); ?>" onclick="return confirm('Are you sure you want to delete this sender ID?')" title="Delete">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom_script'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#senderid-list-table').DataTable({
        responsive: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 4 }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                title: 'Non-Masking Sender IDs',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'csv',
                title: 'Non-Masking_Sender_IDs',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'excel',
                title: 'Non-Masking_Sender_IDs',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                title: 'Non-Masking Sender IDs',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                title: 'Non-Masking Sender IDs',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ]
    });

    // Validation for Add Form
    $('#addForm').on('submit', function(e) {
        var nonmasking = $('#nonmasking').val().trim();
        var operatorSelected = $('#add_operator_id').val();
        
        if (nonmasking === '') {
            e.preventDefault();
            alert('Please enter a Sender ID.');
            return false;
        }
        
        if (nonmasking.length < 3) {
            e.preventDefault();
            alert('Sender ID must be at least 3 characters long.');
            return false;
        }
        
        if (!operatorSelected) {
            e.preventDefault();
            alert('Please select an operator.');
            return false;
        }
    });

    // Validation for Import Form
    $('#importForm').on('submit', function(e) {
        var operatorSelected = $('#import_operator_id').val();
        var fileInput = $('#import_file')[0].files[0];
        
        if (!operatorSelected) {
            e.preventDefault();
            alert('Please select an operator before importing.');
            return false;
        }
        
        if (!fileInput) {
            e.preventDefault();
            alert('Please select a file to import.');
            return false;
        }
        
        // Check file extension
        var fileName = fileInput.name;
        var extension = fileName.split('.').pop().toLowerCase();
        var allowedExtensions = ['xlsx', 'xls', 'csv', 'txt'];
        
        if (allowedExtensions.indexOf(extension) === -1) {
            e.preventDefault();
            alert('Please select a valid file type (.xlsx, .xls, .csv, .txt)');
            return false;
        }
        
        // Check file size (max 5MB)
        if (fileInput.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('File size must be less than 5MB.');
            return false;
        }
    });
});

// Export table to CSV function
function exportTableToCSV() {
    var csv = [];
    var rows = document.querySelectorAll('#senderid-list-table tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            // Skip the Actions column (last column)
            if (j < cols.length - 1) {
                var text = cols[j].innerText;
                row.push('"' + text.replace(/"/g, '""') + '"');
            }
        }
        
        csv.push(row.join(','));
    }
    
    var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    downloadLink.download = 'non_masking_sender_ids.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.click();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>