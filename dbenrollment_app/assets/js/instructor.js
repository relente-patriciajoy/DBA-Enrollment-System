$(document).ready(function () {
  $('#instructorAddForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'add.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          // Hide modal and reset form
          $('#instructorAddModal').modal('hide');
          $('#instructorAddForm')[0].reset();

          alert('Instructor added successfully!');

          // Create a new row dynamically
          let newRow = `
                    <tr class="new-row">
                        <td>${response.last_name}</td>
                        <td>${response.first_name}</td>
                        <td>${response.email}</td>
                        <td>${response.dept_id}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning" onclick="editInstructor(${response.instructor_id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteInstructor(${response.instructor_id})">Delete</button>
                        </td>
                    </tr>
                `;

          // Add row to TOP of table body
          $('table tbody').prepend(newRow);
        } else {
          alert(response.error || 'Failed to add instructor');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('Error adding instructor. Please try again.');
      }
    });
  });

  $('#instructorEditForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'update.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#instructorEditModal').modal('hide');
          alert('Instructor updated successfully!');
          location.reload();
        } else {
          alert(response.error || 'Failed to update instructor');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('Error updating instructor. Please try again.');
      }
    });
  });
});

function editInstructor(id) {
  $.ajax({
    url: 'get_instructor.php',
    type: 'GET',
    data: { instructor_id: id },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        var instructor = response.data;
        $('#edit_instructor_id').val(instructor.instructor_id);
        $('#edit_last_name').val(instructor.last_name);
        $('#edit_first_name').val(instructor.first_name);
        $('#edit_email').val(instructor.email);
        $('#edit_dept_id').val(instructor.dept_id);
        $('#instructorEditModal').modal('show');
      } else {
        alert(response.error || 'Failed to load instructor data');
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', error);
      alert('Error loading instructor data');
    }
  });
}

function deleteInstructor(id) {
  if (confirm('Are you sure you want to delete this instructor?')) {
    $.ajax({
      url: 'delete.php',
      type: 'POST',
      data: { id: id },
      success: function (response) {
        location.reload();
      }
    });
  }
}