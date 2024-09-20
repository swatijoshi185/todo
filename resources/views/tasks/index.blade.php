@extends('layouts.app')

@section('content')
    <h1 class="text-center">ToDo List</h1>
    <div class="input-group mb-3">
        <input type="text" id="task" class="form-control" placeholder="Enter Task" aria-label="Task">
        <div class="input-group-append">
            <button id="add-task" class="btn btn-primary">Add Task</button>
        </div>
    </div>
    <button id="show-all" class="btn btn-secondary mb-3">Show All Tasks</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Task</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="task-list">
            <!-- Task items will be appended here -->
        </tbody>
    </table>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        let deleteTaskId = null;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /**
             * Add the List
             */
            $('#add-task').click(function() {
                const taskName = $('#task').val();
                if (taskName) {
                    $.post('/tasks', { name: taskName }, function(task) {
                        $('#task-list').append(`
                            <tr class="task-item pending"  data-id="${task.id}">
                                <td>
                                    <input type="checkbox" class="complete-task">
                                    ${task.name}
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-task"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        `);
                        $('#task').val('');
                    }).fail(function(xhr) {
                        alert(xhr.responseJSON.message);
                    });
                }
            });

            /**
             * On click checkbox change status and disappear the row
             */
            $(document).on('click', '.complete-task', function() {
                const listItem = $(this).closest('tr');
                const taskId = listItem.data('id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PATCH',
                    success: function(task) {
                        if (task.status) {
                            listItem.addClass('completed');
                            listItem.fadeOut();
                        } else {
                            listItem.removeClass('completed');
                            listItem.addClass('pending');
                        }
                    }
                });
            });

            /**
             * Delete the task
             */
            $(document).on('click', '.delete-task', function() {
                const listItem = $(this).closest('tr');
                deleteTaskId = listItem.data('id');
                $('#deleteModal').modal('show');
            });

            /**
             * Modal confirm if you want to delete task
             */
            $('#confirm-delete').click(function() {
                $.ajax({
                    url: `/tasks/${deleteTaskId}`,
                    type: 'DELETE',
                    success: function() {
                        $(`#task-list tr[data-id="${deleteTaskId}"]`).fadeOut();
                        $('#deleteModal').modal('hide');
                    }
                });
            });

            /**
             * Show all list on click show all button
             */
            $('#show-all').click(function() {
                $.get('/tasks', function(tasks) {
                    $('#task-list').empty();
                    tasks.forEach(task => {
                        $('#task-list').append(`
                            <tr class="task-item ${task.status ? 'completed' : 'pending'}" data-id="${task.id}">
                                <td>
                                    <input type="checkbox" class="complete-task" ${task.status ? 'checked' : ''}>
                                    ${task.name}
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-task"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                });
            });
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    @endpush
