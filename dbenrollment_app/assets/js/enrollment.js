$(document).ready(function () {
  // Load available courses when student is selected
  $('#student_select').on('change', function () {
    const studentId = $(this).val();

    console.log('Student selected:', studentId);

    if (!studentId) {
      $('#available_courses_section').hide();
      return;
    }

    // Show loading state
    $('#available_courses_list').html('<p class="text-center"><i class="spinner-border spinner-border-sm"></i> Loading available courses...</p>');
    $('#available_courses_section').show();

    $.ajax({
      url: 'get_available_courses.php',
      type: 'GET',
      data: { student_id: studentId },
      dataType: 'json',
      success: function (response) {
        console.log('Response:', response);

        if (response.success) {
          if (response.courses.length === 0) {
            $('#available_courses_list').html(`
              <div class="alert alert-info">
                <strong>No available courses found.</strong><br>
                This student (${response.student.year_level}) has either:<br>
                - Already enrolled in all courses for their year level<br>
                - No courses are available for their year level
              </div>
            `);
            $('#section_select').html('<option value="">No courses available</option>').prop('disabled', true);
          } else {
            // Display student info
            let infoHtml = `<div class="alert alert-primary mb-3">
              <strong>Student Year Level:</strong> ${response.student.year_level}<br>
              <strong>Showing courses for:</strong> ${response.student.year_level}
            </div>`;

            // Display available courses
            let coursesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered table-hover"><thead class="table-dark"><tr><th>Course Code</th><th>Course Title</th><th>Units</th><th>Section</th><th>Schedule</th><th>Prerequisites</th><th>Select</th></tr></thead><tbody>';

            let sectionsHtml = '<option value="">Select a section</option>';
            let hasEnrollableCourses = false;

            response.courses.forEach(function (course) {
              const schedule = `${course.day_pattern} ${course.start_time}-${course.end_time}`;
              const prereqInfo = course.prerequisites ? `<small class="text-muted">${course.prerequisites}</small>` : '<small class="text-success">None</small>';

              let rowClass = '';
              let selectButton = '';

              if (course.can_enroll) {
                hasEnrollableCourses = true;
                selectButton = `<input type="radio" name="selected_course" value="${course.section_id}" data-section="${course.section_id}">`;
                sectionsHtml += `<option value="${course.section_id}">${course.course_code} - ${course.section_code} (${schedule})</option>`;
              } else {
                rowClass = 'table-secondary';
                selectButton = `<span class="text-danger" title="${course.prereq_message}">✗</span>`;
              }

              coursesHtml += `
                <tr class="${rowClass}">
                  <td><strong>${course.course_code}</strong></td>
                  <td>${course.course_title}</td>
                  <td class="text-center">${course.units}</td>
                  <td>${course.section_code}</td>
                  <td><small>${schedule}</small></td>
                  <td>${prereqInfo}${!course.can_enroll ? '<br><span class="badge bg-danger">Prerequisites not met</span>' : ''}</td>
                  <td class="text-center">${selectButton}</td>
                </tr>
              `;
            });

            coursesHtml += '</tbody></table></div>';

            if (!hasEnrollableCourses) {
              coursesHtml += '<div class="alert alert-warning">All displayed courses have unmet prerequisites. Student cannot enroll in any courses at this time.</div>';
            }

            $('#available_courses_list').html(infoHtml + coursesHtml);
            $('#section_select').html(sectionsHtml).prop('disabled', !hasEnrollableCourses);

            // When radio button is clicked, select the section in dropdown
            $('input[name="selected_course"]').on('change', function () {
              const sectionId = $(this).val();
              $('#section_select').val(sectionId);
            });
          }
        } else {
          $('#available_courses_list').html('<div class="alert alert-danger">' + response.error + '</div>');
        }
      },
      error: function (xhr, status, error) {
        console.error('Error loading courses:', error);
        console.error('Response:', xhr.responseText);
        $('#available_courses_list').html('<div class="alert alert-danger">Error loading courses. Check console.</div>');
      }
    });
  });

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
          const sectionId = $('#section_select').val();
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
          $('#available_courses_section').hide();
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