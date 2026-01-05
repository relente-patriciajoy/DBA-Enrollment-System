$(document).ready(function () {
  // ADD Department (form submit)
  $('#departmentAddForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: 'add_department.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Department added successfully!');
          $('#departmentAddModal').modal('hide');

          // Get form values
          const deptCode = $('input[name="dept_code"]').val();
          const deptName = $('input[name="dept_name"]').val();
          const newDeptId = response.dept_id;

          // Create new row HTML
          const newRow = `
            <tr class="new-department-highlight">
              <td>${newDeptId}</td>
              <td>${deptCode}</td>
              <td>${deptName}</td>
              <td class='text-center'>
                <button class='btn btn-warning btn-sm edit-department' data-id='${newDeptId}'>Edit</button>
                <button class='btn btn-danger btn-sm delete-department' data-id='${newDeptId}'>Delete</button>
              </td>
            </tr>
          `;

          // Add new row at the top of the table
          $('.student-table tbody').prepend(newRow);

          // Add highlight effect
          setTimeout(function () {
            $('.new-department-highlight').removeClass('new-department-highlight');
          }, 2000);

          // Clear form
          $('#departmentAddForm')[0].reset();
        } else {
          alert(response.error || 'Failed to add department');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error adding department - Check console');
      }
    });
  });

  // EDIT Department (delegated click handler)
  $(document).on('click', '.edit-department', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    console.log('Edit button clicked, ID:', id);

    if (!id) {
      alert('No department ID provided');
      return;
    }

    editDepartment(id);
  });

  // UPDATE Department (form submit)
  $('#departmentEditForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    console.log('Update form data:', formData);

    $.ajax({
      url: 'update_department.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert('Department updated successfully!');
          $('#departmentEditModal').modal('hide');
          location.reload();
        } else {
          alert(response.error || 'Failed to update department');
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Response:', xhr.responseText);
        alert('Error updating department - Check console');
      }
    });
  });

  // DELETE Department (delegated click handler)
  $(document).on('click', '.delete-department', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    console.log('Delete button clicked, ID:', id);

    if (!id) {
      alert('No department ID provided');
      return;
    }

    if (!confirm('Are you sure you want to delete this department?')) return;

    $.ajax({
      url: 'delete.php',
      method: 'POST',
      data: { dept_id: id },
      dataType: 'json'
    }).done(function (resp) {
      console.log('Delete response:', resp);
      if (resp.success) {
        alert('Department deleted successfully!');
        $(`.delete-department[data-id='${id}']`).closest('tr').fadeOut();
      } else {
        alert(resp.error || 'Failed to delete department');
        console.error('delete.php resp', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error deleting department — see console.');
      console.error('delete_ajax fail', status, err, xhr.responseText);
    });
  });
});

// Function to load department data for editing
function editDepartment(id) {
  console.log('editDepartment called with ID:', id);

  $.ajax({
    url: 'get_department.php',
    type: 'GET',
    data: { dept_id: id },
    dataType: 'json',
    success: function (response) {
      console.log('Server response:', response);

      if (response.success) {
        var department = response.data;
        $('#edit_dept_id').val(department.dept_id);
        $('#edit_dept_code').val(department.dept_code);
        $('#edit_dept_name').val(department.dept_name);
        $('#departmentEditModal').modal('show');
      } else {
        alert(response.error || 'Failed to load department data');
        console.error('Server error:', response);
      }
    },
    error: function (xhr, status, error) {
      console.error('AJAX Error Details:');
      console.error('Status:', status);
      console.error('Error:', error);
      console.error('Response:', xhr.responseText);
      console.error('Status Code:', xhr.status);
      alert('Error loading department data - Check console');
    }
  });
}