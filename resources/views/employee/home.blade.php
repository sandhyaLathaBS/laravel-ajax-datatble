@extends('employee.layout.app_layout')
@section('content')
<style>
.error {
    color: red;
    font-size: 10px;
}
</style>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="user_table">
        <thead>
            <tr>
                <th width="7%">Sl no</th>
                <th width="7%">Select</th>
                <th width="10%">Name</th>
                <th width="14%">Contact No</th>
                <th width="15%">Hobby</th>
                <th width="15%">Category</th>
                <th width="15%">Image</th>
                <th width="15%">Action</th>
            </tr>
        </thead>
    </table>
</div>


<div id="formModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Record</h4>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="control-label col-md-4">Full Name : </label>
                        <div class="col-md-8">
                            <input type="text" required name="full_name" id="full_name" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Contact no : </label>
                        <div class="col-md-8">
                            <input type="number" required name="contactNo" id="contactNo" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Category : </label>
                        <div class="col-md-8">
                            <input type="text" required name="category" id="category" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Select Profile Image : </label>
                        <div class="col-md-8">
                            <input type="file" required name="image" id="image" class="form-control" />
                            <span id="store_image"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Hobby : </label>
                        <div class="col-md-8">
                            <select name="hobby" required class="form-control" id="hobby">
                                <option value="">Please Choose</option>
                                @if(!empty($hobbies))
                                @foreach($hobbies as $hobby)
                                <option value="{{$hobby->id}}">{{$hobby->hobby}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <br />
                    <div class="form-group" align="center">
                        <input type="hidden" name="action" id="action" />
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                        <input type="submit" name="action_button" id="action_button" class="btn btn-warning"
                            value="Add" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(document).ready(function() {

    $('#bulkDelete').click(function() {
        deleteArray = [];
        $(".checkEmployee").each(function() {
            if ($(this).is(':checked')) {
                deleteArray.push($(this).data('checkbox-val'));
            }
        });
        if (deleteArray.length > 0) {
            swal({
                title: "Delete",
                text: "Do you really want to delete?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('delete') }}",
                        data: {
                            id: deleteArray,
                            _token: "{{csrf_token()}}"
                        },
                        success: function(data) {
                            if (data) {
                                $('#user_table').DataTable().ajax.reload();
                            }
                        }
                    });
                }
            });
        } else {
            swal({
                title: "Warning",
                text: "Please select users to delete",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                closeOnCancel: false
            })
        }
    });
    $('#create_record').click(function() {
        $('#form_result').html('');
        $('.modal-title').text("Add New Record");
        $('#action_button').val("Add");
        $('#action').val("Add");
        $('#formModal').modal('show');
    });

    $('#user_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('loading') }}",
        },
        columns: [{
                data: "DT_RowIndex",
                name: "sl_no"
            },
            {
                data: 'select',
                name: 'select',
                orderable: false
            },
            {
                data: 'name',
                name: 'first_name'
            },
            {
                data: 'contactNo',
                name: 'contactNo'
            },
            {
                data: 'hobby',
                name: 'hobby'
            },
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'profile_pic',
                name: 'image',
                render: function(data, type, full, meta) {
                    return "<img src='{{ URL::to('uploads/employee_uploads/')}}/" + data +
                        "' width='100' class='img-fluid' />";
                },
                orderable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false
            }
        ]
    });

    $(function() {
        $('#sample_form').validate({
            rules: {
                full_name: {
                    required: true,
                },
                contactNo: {
                    required: true,
                    digits: true,
                    minlength: 10
                },
                category: {
                    required: true
                },
                hobby: {
                    required: true
                },
                image: {
                    required: function(element) {
                        return ($('#action').val() == 'Add');
                    },
                    extension: "jpg|jpeg|png|ico|bmp"
                }
            },
            messages: {
                full_name: {
                    required: 'Please Enter a name',
                },
                contactNo: {
                    required: 'Please Enter your Contact no',
                },
                category: {
                    required: 'Please enter a category',
                },
                hobby: {
                    required: 'Please Select a Hobby',
                },
                image: {
                    required: "Please upload file.",
                    extension: "Please upload file in these format only (jpg, jpeg, png, ico, bmp)."
                }
            },
            submitHandler: function() {
                event.preventDefault();
                if ($('#action').val() == 'Add') {
                    $.ajax({
                        url: "{{ route('save') }}",
                        method: "POST",
                        data: new FormData($('#sample_form')[0]),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function(data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors
                                    .length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' +
                                    data.success +
                                    '</div>';
                                $('#sample_form')[0].reset();
                                $('#user_table').DataTable().ajax.reload();
                            }
                            $('#form_result').html(html);
                            $('#formModal').modal('hide');
                        }
                    })
                }
                if ($('#action').val() == "Edit") {
                    $.ajax({
                        url: "{{ route('update') }}",
                        method: "POST",
                        data: new FormData($('#sample_form')[0]),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function(data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors
                                    .length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' +
                                    data.success +
                                    '</div>';
                                $('#sample_form')[0].reset();
                                $('#store_image').html('');
                                $('#user_table').DataTable().ajax.reload();
                            }
                            $('#form_result').html(html);
                            $('#formModal').modal('hide');
                        }
                    });
                }
            }

        }); //valdate end
    });

    $(document).on('click', '.edit', function() {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "{{ route('get-details') }}",
            method: "POST",
            data: {
                id: id,
                _token: "{{csrf_token()}}"
            },
            dataType: "json",
            success: function(html) {
                $('#full_name').val(html.data.name);
                $('#contactNo').val(html.data.contactNo);
                $('#category').val(html.data.category);
                $('#hobby').val(html.data.hobby_id);
                $('#store_image').html(
                    "<img src={{ URL::to('/') }}/uploads/employee_uploads/" + html.data
                    .profile_pic + " width='70' class='img-thumbnail' />");
                $('#store_image').append(
                    "<input type='hidden' id='hidden_image' name='hidden_image' value='" +
                    html.data
                    .profile_pic + "' />");
                $('#hidden_id').val(html.data.id);
                $('.modal-title').text("Edit New Record");
                $('#action_button').val("Edit");
                $('#action').val("Edit");
                $('#formModal').modal('show');
            }
        })
    });

    $(document).on('click', '.delete', function() {
        user_id = $(this).attr('id');
        swal({
            title: "Delete",
            text: "Do you really want to delete?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('delete') }}",
                    data: {
                        id: user_id,
                        _token: "{{csrf_token()}}"
                    },
                    success: function(data) {
                        if (data) {
                            $('#user_table').DataTable().ajax.reload();
                        }
                    }
                });
            }
        });
    });
});
</script>

@endpush