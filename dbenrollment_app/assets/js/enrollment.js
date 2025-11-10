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
            <div class="alert alert-info">
              <strong>Note:</strong> You must enroll in and complete terms sequentially.
              Please wait for ${response.allowed_term.term_code} to become active.
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
              Student Year Level: ${response.student.year_level}<br><br>
              This student has either:<br>
              - Already enrolled in all courses for ${response.allowed_term.term_code}<br>
              - No courses available for their year level in this term
            </div>
          `);
        } else {
          // Display courses with progression
          displayCoursesWithProgress(response, progressHtml, studentId);
        }
      } else {
        $('#available_courses_list').html('<div class="alert alert-danger">' + response.error + '</div>');
      }
    },
    error: function (xhr, status, error) {
      console.error('Error loading courses:', error);
      console.error('Response:', xhr.responseText);
      $('#available_courses_list').html('<div class="alert alert-danger">Error loading courses.</div>');
    }
  });
});

// Function to display term progression
function displayTermProgress(termProgress, allowedTerm, studentInfo) {
  if (!termProgress || termProgress.length === 0) {
    return '';
  }

  let html = `
    <div class="card mb-3">
      <div class="card-header ${studentInfo.is_irregular ? 'bg-warning' : 'bg-primary'} text-white">
        <h6 class="mb-0">
          <i class="bi bi-list-check"></i> Term Enrollment Progress
          ${studentInfo.is_irregular ? '<span class="badge bg-danger ms-2">IRREGULAR</span>' : '<span class="badge bg-success ms-2">REGULAR</span>'}
        </h6>
      </div>
      <div class="card-body">
  `;

  if (studentInfo.is_irregular && studentInfo.irregular_reason) {
    html += `
      <div class="alert alert-warning mb-3">
        <strong>Irregular Status:</strong> ${studentInfo.irregular_reason}
      </div>
    `;
  }

  html += '<div class="row">';

  termProgress.forEach(function (term) {
    let statusIcon = '';
    let statusClass = '';
    let statusText = '';
    let badgeClass = '';
    let details = '';

    if (term.is_enrolled) {
      if (term.is_fully_graded) {
        statusIcon = '✓';
        statusClass = 'border-success';
        badgeClass = 'bg-success';
        statusText = `Completed`;
        details = `
          <small class="d-block mt-1">
            Passed: ${term.passed_courses}
            ${term.failed_courses > 0 ? ` | Failed: ${term.failed_courses}` : ''}
          </small>
        `;
      } else {
        statusIcon = '⏳';
        statusClass = 'border-warning';
        badgeClass = 'bg-warning';
        statusText = `Pending Grades`;
        details = `
          <small class="d-block mt-1 text-danger">
            ${term.pending_grades} course(s) awaiting grades
          </small>
        `;
      }
    } else if (allowedTerm && term.term_id == allowedTerm.term_id) {
      statusIcon = '→';
      statusClass = 'border-primary';
      badgeClass = 'bg-primary';
      statusText = 'Ready to Enroll';
    } else {
      statusIcon = '○';
      statusClass = 'border-secondary';
      badgeClass = 'bg-secondary';
      statusText = 'Not Yet Enrolled';
    }

    html += `
      <div class="col-md-4 mb-3">
        <div class="border ${statusClass} rounded p-3">
          <div class="d-flex justify-content-between align-items-start">
            <strong>${statusIcon} ${term.term_code}</strong>
            <span class="badge ${badgeClass}">${statusText}</span>
          </div>
          ${details}
        </div>
      </div>
    `;
  });

  html += `
        </div>
      </div>
    </div>
  `;

  return html;
}

// Function to display courses with progress
function displayCoursesWithProgress(response, progressHtml, studentId) {
  let infoHtml = `<div class="alert alert-success mb-3">
    <h6 class="mb-2"><strong>Sequential Enrollment - One Term at a Time</strong></h6>
    <div class="row">
      <div class="col-md-6">
        <strong>Student Year Level:</strong> ${response.student.year_level}<br>
        <strong>Enrolling in Term:</strong> ${response.allowed_term.term_code}
      </div>
      <div class="col-md-6">
        <strong>Active System Term:</strong> ${response.current_term.term_code}<br>
        <strong>Status:</strong> <span class="badge bg-success">Ready to Enroll</span>
      </div>
    </div>
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
            <th>TERM</th>
            <th>PREREQUISITES</th>
          </tr>
        </thead>
        <tbody>`;

  let hasEnrollableCourses = false;

  response.courses.forEach(function (course) {
    const schedule = `${course.day_pattern} ${course.start_time}-${course.end_time}`;
    const prereqInfo = course.prerequisites
      ? `<small class="text-muted">${course.prerequisites}</small>`
      : '<small class="text-success">None</small>';

    let rowClass = '';
    let selectCheckbox = '';

    if (course.can_enroll) {
      hasEnrollableCourses = true;
      selectCheckbox = `<input type="checkbox" class="form-check-input course-checkbox"
                             value="${course.section_id}"
                             data-units="${course.units}"
                             data-course-code="${course.course_code}"
                             data-section-code="${course.section_code}">`;
    } else {
      rowClass = 'table-secondary';
      selectCheckbox = `<span class="text-danger" title="${course.prereq_message}">✗</span>`;
    }

    coursesHtml += `
      <tr class="${rowClass}">
        <td class="text-center">${selectCheckbox}</td>
        <td><strong>${course.course_code}</strong></td>
        <td>${course.course_title}</td>
        <td class="text-center">${course.units}</td>
        <td>${course.section_code}</td>
        <td><small>${schedule}</small></td>
        <td><span class="badge bg-primary">${course.term_code}</span></td>
        <td>${prereqInfo}${!course.can_enroll ? '<br><span class="badge bg-danger">Prerequisites not met</span>' : ''}</td>
      </tr>
    `;
  });

  coursesHtml += `
        </tbody>
      </table>
    </div>
    <div class="row mt-3 mb-3">
      <div class="col-md-6">
        <div class="alert alert-info mb-0">
          <strong>Selected Units:</strong> <span id="total_units">0</span>
        </div>
      </div>
      <div class="col-md-6">
        <button type="button" id="enroll_selected_btn" class="btn btn-success btn-lg w-100" disabled>
          <i class="bi bi-check-circle"></i> Enroll in Selected Courses
        </button>
      </div>
    </div>
  `;

  if (!hasEnrollableCourses) {
    coursesHtml += '<div class="alert alert-warning">All courses have unmet prerequisites.</div>';
  }

  $('#available_courses_list').html(progressHtml + infoHtml + coursesHtml);

  // Handle checkbox changes - calculate total units
  $('.course-checkbox').on('change', function () {
    let total = 0;
    let count = 0;
    $('.course-checkbox:checked').each(function () {
      total += parseFloat($(this).data('units'));
      count++;
    });
    $('#total_units').text(total);
    $('#enroll_selected_btn').prop('disabled', count === 0);
  });

  // Handle enroll button click - IMPORTANT: Pass studentId here
  $('#enroll_selected_btn').off('click').on('click', function () {
    console.log('Enroll button clicked for student:', studentId);
    enrollSelectedCourses(studentId, response.allowed_term.term_id);
  });
}

// Function to enroll in multiple selected courses
function enrollSelectedCourses(studentId, termId) {
  console.log('enrollSelectedCourses called with:', studentId, termId);

  const selectedSections = [];
  $('.course-checkbox:checked').each(function () {
    selectedSections.push({
      section_id: $(this).val(),
      course_code: $(this).data('course-code'),
      section_code: $(this).data('section-code')
    });
  });

  console.log('Selected sections:', selectedSections);

  if (selectedSections.length === 0) {
    alert('Please select at least one course to enroll');
    return;
  }

  if (!confirm(`Are you sure you want to enroll in ${selectedSections.length} course(s)?`)) {
    return;
  }

  const dateEnrolled = new Date().toISOString().split('T')[0];

  // Disable button during enrollment
  $('#enroll_selected_btn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Enrolling...');

  let enrolledCount = 0;
  let failedCourses = [];
  let completed = 0;

  selectedSections.forEach(function (section, index) {
    $.ajax({
      url: 'add_enrollment.php',
      method: 'POST',
      data: {
        student_id: studentId,
        section_id: section.section_id,
        date_enrolled: dateEnrolled,
        status: 'Regular',
        letter_grade: ''
      },
      dataType: 'json',
      success: function (response) {
        completed++;
        console.log('Enrollment response for', section.course_code, ':', response);

        if (response.success) {
          enrolledCount++;
        } else {
          failedCourses.push(`${section.course_code} - ${section.section_code}: ${response.error || 'Unknown error'}`);
        }

        // Check if all requests are complete
        if (completed === selectedSections.length) {
          finishEnrollment(enrolledCount, failedCourses, selectedSections.length);
        }
      },
      error: function (xhr, status, error) {
        completed++;
        console.error('Enrollment error for', section.course_code, ':', error);
        console.error('Response:', xhr.responseText);
        failedCourses.push(`${section.course_code} - ${section.section_code}: ${error}`);

        if (completed === selectedSections.length) {
          finishEnrollment(enrolledCount, failedCourses, selectedSections.length);
        }
      }
    });
  });
}

function finishEnrollment(enrolledCount, failedCourses, totalCourses) {
  let message = `Successfully enrolled in ${enrolledCount} out of ${totalCourses} course(s)`;

  if (failedCourses.length > 0) {
    message += `\n\nFailed enrollments:\n${failedCourses.join('\n')}`;
    alert(message);
  } else {
    alert(message + '\n\nEnrollment completed successfully!');
  }

  // Close modal and reload page
  const addModal = bootstrap.Modal.getInstance(document.getElementById('enrollmentAddModal'));
  if (addModal) {
    addModal.hide();
  }

  setTimeout(function () {
    location.reload();
  }, 500);
}

// Clear form when modal is closed
$('#enrollmentAddModal').on('hidden.bs.modal', function () {
  $('#student_select').val('');
  $('#available_courses_section').hide();
  $('#available_courses_list').html('');
});