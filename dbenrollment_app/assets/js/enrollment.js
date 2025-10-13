$(document).ready(function () {
  // ADD Enrollment (form submit)
  $('#enrollmentAddForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: 'add_enrollment.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Enrollment added successfully!');
          $('#enrollmentAddModal').modal('hide');

          // Get form values
          const studentId = $('select[name="student_id"]').val();
          const sectionId = $('input[name="section_id"]').val();
          const dateEnrolled = $('input[name="date_enrolled"]').val();
          const status = $('select[name="status"]').val();
          const letterGrade = $('select[name="letter_grade"]').val();
          const newEnrollmentId = response.enrollment_id;

          // Create new row HTML
          const newRow = `
            <tr class="new-enrollment-highlight">
              <td>${newEnrollmentId}</td>
              <td>${studentId}</td>
              <td>${sectionId}</td>
              <td>${dateEnrolled}</td>
              <td>${status}</td>
              <td>${letterGrade}</td>
              <td class='text-center'>
                <button class='btn btn-warning btn-sm edit-enrollment' data-id='${newEnrollmentId}'>Edit</button> 
                <button class='btn btn-danger btn-sm delete-enrollment' data-id='${newEnrollmentId}'>Delete</button>
              </td>
            </tr>
          `;

          // Add new row at the top of the table
          $('.student-table tbody').prepend(newRow);

          // Add highlight effect
          setTimeout(function () {
            $('.new-enrollment-highlight').removeClass('new-enrollment-highlight');
          }, 2000);

          // Clear form
          $('#enrollmentAddForm')[0].reset();
        } else {
          alert(response.error || 'Failed to add enrollment');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error adding enrollment - Check console');
      }
    });
  });

  // EDIT Enrollment (delegated click handler)
  $(document).on('click', '.edit-enrollment', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    console.log('Edit button clicked, ID:', id);

    if (!id) {
      alert('No enrollment ID provided');
      return;
    }

    editEnrollment(id);
  });

  // UPDATE Enrollment (form submit)
  $('#enrollmentEditForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    console.log('Update form data:', formData);

    $.ajax({
      url: 'update_enrollment.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Enrollment updated successfully!');
          $('#enrollmentEditModal').modal('hide');
          location.reload();
        } else {
          alert(response.error || 'Failed to update enrollment');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error updating enrollment - Check console');
      }
    });
  });

  // DELETE Enrollment (delegated click handler)
  $(document).on('click', '.delete-enrollment', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    console.log('Delete button clicked, ID:', id);

    if (!id) {
      alert('No enrollment ID provided');
      return;
    }

    if (!confirm('Are you sure you want to delete this enrollment?')) return;

    $.ajax({
      url: 'delete_ajax.php',
      method: 'POST',
      data: { enrollment_id: id },
      dataType: 'json'
    }).done(function (resp) {
      console.log('Delete response:', resp);
      if (resp.success) {
        alert('Enrollment deleted successfully!');
        $(`.delete-enrollment[data-id='${id}']`).closest('tr').fadeOut();
      } else {
        alert(resp.error || 'Failed to delete enrollment');
        console.error('delete_ajax.php resp', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error deleting enrollment — see console.');
      console.error('delete_ajax fail', status, err, xhr.responseText);
    });
  });
});

// Function to load enrollment data for editing
function editEnrollment(id) {
  console.log('editEnrollment called with ID:', id);

  $.ajax({
    url: 'get_enrollment.php',
    type: 'GET',
    data: { enrollment_id: id },
    dataType: 'json',
    success: function (response) {
      console.log('Server response:', response);

      if (response.success) {
        var enrollment = response.data;
        $('#edit_enrollment_id').val(enrollment.enrollment_id);
        $('#edit_student_id').val(enrollment.student_id);
        $('#edit_section_id').val(enrollment.section_id);
        $('#edit_date_enrolled').val(enrollment.date_enrolled);
        $('#edit_status').val(enrollment.status);
        $('#edit_letter_grade').val(enrollment.letter_grade);
        $('#enrollmentEditModal').modal('show');
      } else {
        alert(response.error || 'Failed to load enrollment data');
        console.error('Server error:', response);
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error Details:');
      console.error('Status:', status);
      console.error('Error:', error);
      console.error('Response:', xhr.responseText);
      console.error('Status Code:', xhr.status);
      alert('Error loading enrollment data - Check console');
    }
  });
}