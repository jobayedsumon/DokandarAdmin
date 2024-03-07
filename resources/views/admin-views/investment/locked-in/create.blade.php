@extends('layouts.admin.app')

@section('title',translate('messages.locked_in'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->

        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card p-5">
                    <form action="{{ route('admin.investment.locked-in.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="name">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                    <input type="text" name="name" class="form-control" placeholder="{{translate('messages.name')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="amount">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.amount')}}</label>
                                    <input type="number" name="amount" class="form-control" placeholder="{{translate('messages.amount')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="yearly_interest_rate">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.yearly_interest_rate')}} (%)</label>
                                    <input type="number" name="yearly_interest_rate" class="form-control" placeholder="{{translate('messages.yearly_interest_rate')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="duration_in_months">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.duration_in_months')}}</label>
                                    <input type="number" name="duration_in_months" class="form-control" placeholder="{{translate('messages.duration_in_months')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <div class="d-flex flex-column h-100">
                                    <label class="input-label">Project Image</label>
                                    <center class="py-3 my-auto">
                                        <img class="object-cover" id="viewer" width="400px" height="200px"
                                             src="{{asset('assets/admin/img/admin.png')}}" alt="delivery-man image"/>
                                    </center>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column h-100">
                                    <label class="input-label">About Project</label>
                                    <textarea name="about" class="form-control" rows="12"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="status">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.status')}}</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1">{{translate('messages.active')}}</option>
                                        <option value="0">{{translate('messages.inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary mt-5">{{translate('messages.submit')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')

    <script>

    </script>

@endpush
