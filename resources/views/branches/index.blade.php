@extends('layouts.app')

@section('title', 'Branches List')

@section('content')
    <div class="container">
        <h2 class="mb-4">{{ $business->name }} - Branches</h2>
        <a href="{{ route('branches.create', $business->id) }}" class="btn btn-success mb-3">Add Branch</a>

        <table class="table table-bordered" id="branch-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Images</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <script>
        $(document).ready(function() {
            var businessId = {{ $business->id }};

            let table = $('#branch-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('branches.data', $business->id) }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'images',
                        name: 'images',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle delete action
            $(document).on('click', '.delete-branch', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                if (confirm('Are you sure you want to delete this branch?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert(response.message);
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred while deleting the branch.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
