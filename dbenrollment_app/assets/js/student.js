$(document).ready(function () {

  // 🟢 ADD STUDENT
  $('#addStudentForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'add_ajax.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.error) {
          alert("❌ Error adding student: " + response.error);
        } else {
          alert("✅ Student added successfully!");
          $('#addModal').modal('hide');

          // Add new row to the top of the table
          let newRow = `
            <tr class="new-row">
              <td>${response.student_no}</td>
              <td>${response.last_name}</td>
              <td>${response.first_name}</td>
              <td>${response.email}</td>
              <td>${response.gender}</td>
              <td>${response.birthdate ?? ''}</td>
              <td>${response.year_level}</td>
              <td>${response.program_id}</td>
              <td>
                <a href="#" class="edit-btn" data-id="${response.student_id}">Edit</a> |
                <a href="#" class="delete-btn" data-id="${response.student_id}">Delete</a>
              </td>
            </tr>`;

          $('table tbody').prepend(newRow);

          // Highlight for new added record effect
          let addedRow = $('table tbody tr:first');
          setTimeout(() => {
            addedRow.addClass('fade-out');
          }, 500);
          setTimeout(() => {
            addedRow.removeClass('new-row fade-out');
          }, 2500);

          $('#addStudentForm')[0].reset();
        }
      },
      error: function (xhr, status, error) {
        alert("⚠️ Failed to communicate with the server: " + error);
      }
    });
  });

  // 🟡 LOAD STUDENT DATA FOR EDIT (Modal)
  $(document).on('click', '.edit-btn', function (e) {
    e.preventDefault();
    let id = $(this).data('id');

    $.ajax({
      url: 'get_student.php',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function (student) {
        if (student.error) {
          alert("❌ Error loading student: " + student.error);
          return;
        }

        $('#edit_student_id').val(student.student_id);
        $('#edit_student_no').val(student.student_no);
        $('#edit_last_name').val(student.last_name);
        $('#edit_first_name').val(student.first_name);
        $('#edit_email').val(student.email);
        $('#edit_gender').val(student.gender);
        $('#edit_birthdate').val(student.birthdate);
        $('#edit_year_level').val(student.year_level);
        $('#edit_program_id').val(student.program_id);

        $('#editModal').modal('show');
      },
      error: function (xhr, status, error) {
        alert("⚠️ Failed to load student data: " + error);
      }
    });
  });

  // 🟣 UPDATE STUDENT
  $('#editStudentForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'update_ajax.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.error) {
          alert("❌ Error updating student: " + response.error);
        } else {
          alert("✅ Student updated successfully!");
          $('#editModal').modal('hide');

          // ✅ Replace entire row instead of using .html()
          let updatedRow = `
          <tr class="new-row">
            <td>${response.student_no}</td>
            <td>${response.last_name}</td>
            <td>${response.first_name}</td>
            <td>${response.email}</td>
            <td>${response.gender}</td>
            <td>${response.birthdate ?? ''}</td>
            <td>${response.year_level}</td>
            <td>${response.program_id}</td>
            <td>
              <a href="#" class="edit-btn" data-id="${response.student_id}">Edit</a> |
              <a href="#" class="delete-btn" data-id="${response.student_id}">Delete</a>
            </td>
          </tr>
        `;

          // ✅ Replace the old row with the new one
          $(`a.edit-btn[data-id='${response.student_id}']`).closest('tr').replaceWith(updatedRow);

          // ✅ Apply highlight effect properly
          let newTableRow = $('table tbody tr.new-row').first();
          setTimeout(() => newTableRow.addClass('fade-out'), 500);
          setTimeout(() => newTableRow.removeClass('new-row fade-out'), 2500);
        }
      },
      error: function (xhr, status, error) {
        alert("⚠️ Failed to update student: " + error);
      }
    });
  });


  // 🔴 DELETE STUDENT (SOFT DELETE)
  $(document).on('click', '.delete-btn', function (e) {
    e.preventDefault();
    let id = $(this).data('id');

    if (confirm("🗑️ Are you sure you want to delete this student? This will mark them as inactive.")) {
      $.ajax({
        url: 'delete_ajax.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            alert("✅ Student has been soft-deleted successfully!");
            $(`a.delete-btn[data-id='${id}']`).closest('tr').fadeOut();
          } else {
            alert("❌ Error deleting student: " + response.error);
          }
        },
        error: function (xhr, status, error) {
          alert("⚠️ Failed to delete student: " + error);
        }
      });
    }
  });

  $('#studentAddForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'add_ajax.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#studentAddModal').modal('hide');
          $('#studentAddForm')[0].reset();
          alert('Student added successfully!');
          location.reload();
        } else {
          alert(response.error || 'Failed to add student');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.log(xhr.responseText);
        alert('Error adding student. Please try again.');
      }
    });
  });

  $('#studentEditForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'update_ajax.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#studentEditModal').modal('hide');
          alert('Student updated successfully!');
          location.reload();
        } else {
          alert(response.error || 'Failed to update student');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('Error updating student. Please try again.');
      }
    });
  });
});

function editStudent(id) {
  $.ajax({
    url: 'get_student.php',
    type: 'GET',
    data: { student_id: id },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        var student = response.data;
        $('#edit_student_id').val(student.student_id);
        $('#edit_student_no').val(student.student_no);
        $('#edit_last_name').val(student.last_name);
        $('#edit_first_name').val(student.first_name);
        $('#edit_email').val(student.email);
        $('#edit_gender').val(student.gender);
        $('#edit_birthdate').val(student.birthdate);
        $('#edit_year_level').val(student.year_level);
        $('#edit_program_id').val(student.program_id);
        $('#studentEditModal').modal('show');
      } else {
        alert(response.error || 'Failed to load student data');
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error:', error);
      alert('Error loading student data');
    }
  });
}

function deleteStudent(id) {
  if (confirm('Are you sure you want to delete this student?')) {
    $.ajax({
      url: 'delete_ajax.php',
      type: 'POST',
      data: { id: id },
      success: function (response) {
        location.reload();
      }
    });
  }
}