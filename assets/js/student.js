function editStudent(id) {
    $.ajax({
        url: 'get_student.php',
        type: 'GET',
        data: { id: id },
        success: function (response) {
            var student = JSON.parse(response);
            $('#edit_student_id').val(student.id);
            $('#edit_student_no').val(student.student_no);
            $('#edit_last_name').val(student.last_name);
            $('#edit_first_name').val(student.first_name);
            $('#edit_email').val(student.email);
            $('#edit_gender').val(student.gender);
            $('#edit_birthdate').val(student.birthdate);
            $('#edit_year_level').val(student.year_level);
            $('#edit_program_id').val(student.program_id);
            $('#studentEditModal').modal('show');
        }
    });
}

$(document).ready(function () {
    // Remove highlight after animation
    setTimeout(function () {
        $('.new-row').removeClass('new-row');
    }, 6000);

    // Handle form submissions
    $('#studentAddForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'add_student.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                $('#studentAddModal').modal('hide');
                location.reload();
            }
        });
    });

    $('#studentEditForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'update_student.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                $('#studentEditModal').modal('hide');
                location.reload();
            }
        });
    });
});
