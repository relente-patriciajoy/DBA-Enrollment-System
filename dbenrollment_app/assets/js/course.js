$(document).ready(function () {
    // ADD Course (form submit)
    $('#courseAddForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'add_course.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Course added successfully!');
                    $('#courseAddModal').modal('hide');

                    // Get form values
                    const courseCode = $('input[name="course_code"]').val();
                    const courseTitle = $('input[name="course_title"]').val();
                    const units = $('input[name="units"]').val();
                    const lectureHours = $('input[name="lecture_hours"]').val();
                    const labHours = $('input[name="lab_hours"]').val();
                    const deptId = $('select[name="dept_id"]').val();
                    const newCourseId = response.course_id;

                    // Create new row HTML
                    const newRow = `
            <tr class="new-course-highlight">
              <td>${newCourseId}</td>
              <td>${courseCode}</td>
              <td>${courseTitle}</td>
              <td>${units}</td>
              <td>${lectureHours}</td>
              <td>${labHours}</td>
              <td>${deptId}</td>
              <td class='text-center'>
                <button class='btn btn-warning btn-sm edit-course' data-id='${newCourseId}'>Edit</button>
                <button class='btn btn-danger btn-sm delete-course' data-id='${newCourseId}'>Delete</button>
              </td>
            </tr>
          `;

                    // Add new row at the top of the table
                    $('.student-table tbody').prepend(newRow);

                    // Add highlight effect
                    setTimeout(function () {
                        $('.new-course-highlight').removeClass('new-course-highlight');
                    }, 2000);

                    // Clear form
                    $('#courseAddForm')[0].reset();
                } else {
                    alert(response.error || 'Failed to add course');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error adding course - Check console');
            }
        });
    });

    // EDIT Course (delegated click handler)
    $(document).on('click', '.edit-course', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        console.log('Edit button clicked, ID:', id);

        if (!id) {
            alert('No course ID provided');
            return;
        }

        editCourse(id);
    });

    // UPDATE Course (form submit)
    $('#courseEditForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        console.log('Update form data:', formData);

        $.ajax({
            url: 'update_course.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Course updated successfully!');
                    $('#courseEditModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.error || 'Failed to update course');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error updating course - Check console');
            }
        });
    });

    // DELETE Course
    $(document).on('click', '.delete-course', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        console.log('Delete button clicked, ID:', id);

        if (!id) {
            alert('No course ID provided');
            return;
        }

        if (!confirm('Are you sure you want to delete this course?')) return;

        $.ajax({
            url: 'delete.php',
            method: 'POST',
            data: { course_id: id },
            dataType: 'json'
        }).done(function (resp) {
            console.log('Delete response:', resp);
            if (resp.success) {
                alert('Course deleted successfully!');
                $(`.delete-course[data-id='${id}']`).closest('tr').fadeOut();
            } else {
                alert(resp.error || 'Failed to delete course');
                console.error('delete.php resp', resp);
            }
        }).fail(function (xhr, status, err) {
            alert('Server error deleting course — see console.');
            console.error('delete_ajax fail', status, err, xhr.responseText);
        });
    });
});

// Function to load course data for editing
function editCourse(id) {
    console.log('editCourse called with ID:', id);

    $.ajax({
        url: 'get_course.php',
        type: 'GET',
        data: { course_id: id },
        dataType: 'json',
        success: function (response) {
            console.log('Server response:', response);

            if (response.success) {
                var course = response.data;
                $('#edit_course_id').val(course.course_id);
                $('#edit_course_code').val(course.course_code);
                $('#edit_course_title').val(course.course_title);
                $('#edit_units').val(course.units);
                $('#edit_lecture_hours').val(course.lecture_hours);
                $('#edit_lab_hours').val(course.lab_hours);
                $('#edit_dept_id').val(course.dept_id);
                $('#courseEditModal').modal('show');
            } else {
                alert(response.error || 'Failed to load course data');
                console.error('Server error:', response);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error Details:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            alert('Error loading course data - Check console');
        }
    });
}