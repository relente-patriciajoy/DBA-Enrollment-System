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
        // Display term progression tracker
        let progressHtml = displayTermProgress(response.term_progress, response.allowed_term, response.student);

        // Check if student completed all terms
        if (response.message) {
          $('#available_courses_list').html(`
                        ${progressHtml}
                        <div class="alert alert-success mt-3">
                            <h5>✓ All Terms Completed</h5>
                            <p>${response.message}</p>
                        </div>
                    `);
          return;
        }

        // Check for term mismatch
        if (response.term_mismatch) {
          $('#available_courses_list').html(`
                        ${progressHtml}
                        <div class="alert alert-warning mt-3">
                            <h5><i class="bi bi-exclamation-triangle"></i> Term Sequence Required</h5>
                            <p><strong>${response.term_mismatch_message}</strong></p>
                            <hr>
                            <p class="mb-0">
                                <strong>Your Next Required Term:</strong> ${response.allowed_term.term_code}<br>
                                <strong>Currently Active Term:</strong> ${response.current_term.term_code}
                            </p>
                        </div>
                    `);
          return;
        }

        if (response.courses.length === 0) {
          $('#available_courses_list').html(`
                        ${progressHtml}
                        <div class="alert alert-info mt-3">
                            <strong>No available courses found.</strong><br>
                            Current Enrollment Term: ${response.allowed_term.term_code}<br>
                            Student Year Level: ${response.student.year_level}
                        </div>
                    `);
        } else {
          displayCoursesWithProgress(response, progressHtml, studentId);
        }
      } else {
        $('#available_courses_list').html('<div class="alert alert-danger">' + response.error + '</div>');
      }
    },
    error: function (xhr, status, error) {
      console.error('Error loading courses:', error);
      $('#available_courses_list').html('<div class="alert alert-danger">Error loading courses. Check console for details.</div>');
    }
  });
});

// Function to display term progression
function displayTermProgress(termProgress, allowedTerm, studentInfo) {
  if (!termProgress || termProgress.length === 0) return '';

  let html = `
        <div class="card mb-3">
            <div class="card-header ${studentInfo.is_irregular ? 'bg-warning' : 'bg-primary'} text-white">
                <h6 class="mb-0">
                    <i class="bi bi-list-check"></i> Term Enrollment Progress
                    ${studentInfo.is_irregular ? '<span class="badge bg-danger ms-2">IRREGULAR</span>' : '<span class="badge bg-success ms-2">REGULAR</span>'}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">`;

  termProgress.forEach(function (term) {
    let statusIcon = term.is_enrolled ? (term.is_fully_graded ? '✓' : '⏳') : '○';
    let statusClass = term.is_enrolled ? (term.is_fully_graded ? 'border-success' : 'border-warning') : 'border-secondary';
    let badgeClass = term.is_enrolled ? (term.is_fully_graded ? 'bg-success' : 'bg-warning') : 'bg-secondary';
    let statusText = term.is_enrolled ? (term.is_fully_graded ? 'Completed' : 'Pending Grades') : 'Not Yet Enrolled';

    if (!term.is_enrolled && allowedTerm && term.term_id == allowedTerm.term_id) {
      statusIcon = '→'; statusClass = 'border-primary'; badgeClass = 'bg-primary'; statusText = 'Ready to Enroll';
    }

    html += `
            <div class="col-md-4 mb-3">
                <div class="border ${statusClass} rounded p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <strong>${statusIcon} ${term.term_code}</strong>
                        <span class="badge ${badgeClass}">${statusText}</span>
                    </div>
                </div>
            </div>`;
  });

  html += `</div></div></div>`;
  return html;
}

// Function to display courses with progress
function displayCoursesWithProgress(response, progressHtml, studentId) {
  let infoHtml = `<div class="alert alert-success mb-3">
        <h6><strong>Sequential Enrollment - Term: ${response.allowed_term.term_code}</strong></h6>
    </div>`;

  let coursesHtml = `
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>SELECT</th>
                        <th>COURSE CODE</th>
                        <th>COURSE TITLE</th>
                        <th>UNITS</th>
                        <th>SECTION</th>
                        <th>SCHEDULE</th>
                    </tr>
                </thead>
                <tbody>`;

  response.courses.forEach(function (course) {
    let selectCheckbox = course.can_enroll ?
      `<input type="checkbox" class="form-check-input course-checkbox" value="${course.section_id}" data-units="${course.units}" data-course-code="${course.course_code}">` :
      `<span class="text-danger">✗</span>`;

    coursesHtml += `
            <tr class="${course.can_enroll ? '' : 'table-secondary'}">
                <td class="text-center">${selectCheckbox}</td>
                <td><strong>${course.course_code}</strong></td>
                <td>${course.course_title}</td>
                <td class="text-center">${course.units}</td>
                <td>${course.section_code}</td>
                <td><small>${course.day_pattern} ${course.start_time}-${course.end_time}</small></td>
            </tr>`;
  });

  coursesHtml += `</tbody></table></div>
        <div class="row mt-3 mb-3">
            <div class="col-md-6"><div class="alert alert-info">Selected Units: <span id="total_units">0</span></div></div>
            <div class="col-md-6"><button type="button" id="enroll_selected_btn" class="btn btn-success btn-lg w-100" disabled>Enroll Selected</button></div>
        </div>`;

  $('#available_courses_list').html(progressHtml + infoHtml + coursesHtml);

  $('.course-checkbox').on('change', function () {
    let total = 0, count = 0;
    $('.course-checkbox:checked').each(function () {
      total += parseFloat($(this).data('units'));
      count++;
    });
    $('#total_units').text(total);
    $('#enroll_selected_btn').prop('disabled', count === 0);
  });

  $('#enroll_selected_btn').on('click', function () {
    enrollSelectedCourses(studentId);
  });
}

// CONSOLIDATED ENROLLMENT FUNCTION (Fixes multiple alerts)
function enrollSelectedCourses(studentId) {
  const selectedSections = [];
  $('.course-checkbox:checked').each(function () {
    selectedSections.push({
      section_id: $(this).val(),
      course_code: $(this).data('course-code')
    });
  });

  if (!confirm(`Enroll in ${selectedSections.length} course(s)?`)) return;

  $('#enroll_selected_btn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Enrolling...');

  let enrolledCount = 0;
  let failedCourses = [];
  let processed = 0;
  const dateEnrolled = new Date().toISOString().split('T')[0];

  selectedSections.forEach(function (section) {
    $.ajax({
      url: 'add_enrollment.php',
      method: 'POST',
      data: {
        student_id: studentId,
        section_id: section.section_id,
        date_enrolled: dateEnrolled,
        status: 'Regular'
      },
      dataType: 'json',
      success: function (response) {
        if (response.success) enrolledCount++;
        else failedCourses.push(`${section.course_code}: ${response.error}`);
      },
      error: function () {
        failedCourses.push(`${section.course_code}: Server Error`);
      },
      complete: function () {
        processed++;
        // TRIGGER ONLY ON THE LAST REQUEST
        if (processed === selectedSections.length) {
          finishEnrollment(enrolledCount, failedCourses, selectedSections.length);
        }
      }
    });
  });
}

function finishEnrollment(enrolledCount, failedCourses, total) {
  let message = `Successfully enrolled in ${enrolledCount} out of ${total} courses.`;
  if (failedCourses.length > 0) {
    message += `\n\nFailed Courses:\n${failedCourses.join('\n')}`;
  }
  alert(message);
  location.reload();
}

// DELETE LISTENER
$(document).on('click', '.btn-delete-enrollment', function (e) {
  e.preventDefault();
  const id = $(this).data('enrollment-id');

  // ONLY ONE confirmation modal before sending the request
  if (!confirm('Are you sure you want to delete this enrollment record?')) return;

  $.ajax({
    url: 'delete_ajax.php',
    method: 'POST',
    data: { enrollment_id: id },
    dataType: 'json',
    success: function (response) {
      // If the server returns success: true, show ONE alert and reload
      if (response.success) {
        alert(response.message);
        location.reload();
      } else {
        alert('Error: ' + (response.error || 'Could not delete record.'));
      }
    },
    error: function (xhr, status, error) {
      // This only triggers if the PHP crashes or sends invalid JSON
      console.error('Delete Error:', xhr.responseText);
      alert('A server error occurred. Please check the console.');
    }
  });
});

// Clear form when modal is closed
$('#enrollmentAddModal').on('hidden.bs.modal', function () {
  $('#student_select').val('');
  $('#available_courses_section').hide();
  $('#available_courses_list').html('');
});

// EDIT ENROLLMENT LISTENER (Fetches data to fill the modal)
$(document).on('click', '.btn-edit-enrollment', function () {
  const enrollmentId = $(this).data('enrollment-id'); // Gets ID from the button you just shared

  $.ajax({
    url: 'get_enrollment.php',
    type: 'GET',
    data: { enrollment_id: enrollmentId },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        // This fills the empty inputs in your screenshot
        $('#edit_enrollment_id').val(response.enrollment.enrollment_id);
        $('#edit_student_name').val(response.enrollment.student_name);
        $('#edit_course_info').val(response.enrollment.course_code);
        $('#edit_date_enrolled').val(response.enrollment.date_enrolled);
        $('#edit_status').val(response.enrollment.status);
        $('#edit_letter_grade').val(response.enrollment.letter_grade);
      }
    }
  });
});

// UPDATE SUBMIT LISTENER (Sends changes to the database)
$(document).on('click', '#btn_update_enrollment', function (e) {
  e.preventDefault(); // STOPS the URL refresh/redirect

  const formData = {
    enrollment_id: $('#edit_enrollment_id').val(),
    date_enrolled: $('#edit_date_enrolled').val(),
    status: $('#edit_status').val(),
    letter_grade: $('#edit_letter_grade').val()
  };

  $.ajax({
    url: 'update_enrollment.php',
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        alert("Enrollment updated!");
        location.reload(); // Refreshes the table to show changes
      } else {
        alert("Error: " + response.error);
      }
    }
  });
});