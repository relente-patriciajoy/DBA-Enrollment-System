$(document).ready(function () {
  // ADD Section (form submit)
  $('#sectionAddForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: 'add_section.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Section added successfully!');
          $('#sectionAddModal').modal('hide');

          // Get form values
          const sectionCode = $('input[name="section_code"]').val();
          const courseId = $('input[name="course_id"]').val();
          const termId = $('input[name="term_id"]').val();
          const instructorId = $('input[name="instructor_id"]').val();
          const dayPattern = $('input[name="day_pattern"]').val();
          const startTime = $('input[name="start_time"]').val();
          const endTime = $('input[name="end_time"]').val();
          const roomId = $('input[name="room_id"]').val();
          const maxCapacity = $('input[name="max_capacity"]').val();
          const yearLevel = $('select[name="year_level"]').val();
          const newSectionId = response.section_id;

          // Create new row HTML
          const newRow = `
            <tr class="new-section-highlight">
              <td>${newSectionId}</td>
              <td>${sectionCode}</td>
              <td>${courseId}</td>
              <td>${termId}</td>
              <td>${instructorId}</td>
              <td>${dayPattern}</td>
              <td>${startTime}</td>
              <td>${endTime}</td>
              <td>${roomId}</td>
              <td>${maxCapacity}</td>
              <td class='text-center'>
                <button class='btn btn-warning btn-sm edit-section' data-id='${newSectionId}'>Edit</button> 
                <button class='btn btn-danger btn-sm delete-section' data-id='${newSectionId}'>Delete</button>
              </td>
            </tr>
          `;

          // Add new row at the top of the table
          $('.student-table tbody').prepend(newRow);

          // Add highlight effect
          setTimeout(function () {
            $('.new-section-highlight').removeClass('new-section-highlight');
          }, 2000);

          // Clear form
          $('#sectionAddForm')[0].reset();
        } else {
          alert(response.error || 'Failed to add section');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error adding section - Check console');
      }
    });
  });

  // EDIT Section (delegated click handler)
  $(document).on('click', '.edit-section', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    console.log('Edit button clicked, ID:', id);

    if (!id) {
      alert('No section ID provided');
      return;
    }

    editSection(id);
  });

  // UPDATE Section (form submit)
  $('#sectionEditForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    console.log('Update form data:', formData);

    $.ajax({
      url: 'update_section.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Section updated successfully!');
          $('#sectionEditModal').modal('hide');
          location.reload();
        } else {
          alert(response.error || 'Failed to update section');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error updating section - Check console');
      }
    });
  });

  // DELETE Section (delegated click handler)
  $(document).on('click', '.delete-section', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    console.log('Delete button clicked, ID:', id);

    if (!id) {
      alert('No section ID provided');
      return;
    }

    if (!confirm('Are you sure you want to delete this section?')) return;

    $.ajax({
      url: 'delete_ajax.php',
      method: 'POST',
      data: { section_id: id },
      dataType: 'json'
    }).done(function (resp) {
      console.log('Delete response:', resp);
      if (resp.success) {
        alert('Section deleted successfully!');
        $(`.delete-section[data-id='${id}']`).closest('tr').fadeOut();
      } else {
        alert(resp.error || 'Failed to delete section');
        console.error('delete_ajax.php resp', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error deleting section — see console.');
      console.error('delete_ajax fail', status, err, xhr.responseText);
    });
  });
});

// Function to load section data for editing
function editSection(id) {
  console.log('editSection called with ID:', id);

  $.ajax({
    url: 'get_section.php',
    type: 'GET',
    data: { section_id: id },
    dataType: 'json',
    success: function (response) {
      console.log('Server response:', response);

      if (response.success) {
        var section = response.data;
        $('#edit_section_id').val(section.section_id);
        $('#edit_section_code').val(section.section_code);
        $('#edit_course_id').val(section.course_id);
        $('#edit_term_id').val(section.term_id);
        $('#edit_instructor_id').val(section.instructor_id);
        $('#edit_day_pattern').val(section.day_pattern);
        $('#edit_start_time').val(section.start_time);
        $('#edit_end_time').val(section.end_time);
        $('#edit_room_id').val(section.room_id);
        $('#edit_max_capacity').val(section.max_capacity);
        $('#edit_year_level').val(section.year_level);
        $('#sectionEditModal').modal('show');
      } else {
        alert(response.error || 'Failed to load section data');
        console.error('Server error:', response);
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error Details:');
      console.error('Status:', status);
      console.error('Error:', error);
      console.error('Response:', xhr.responseText);
      console.error('Status Code:', xhr.status);
      alert('Error loading section data - Check console');
    }
  });
}