@extends('layouts.admin.app')

@section('title',translate('messages.investment_settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->

        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{ translate('messages.Investment') }} {{ translate('messages.investment_settings') }}
                                <span class="badge badge-soft-dark ml-2" id="itemCount">{{ count($settings) }}</span>
                            </h5>
                            <!-- Unfold -->
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.investment.settings') }}" method="POST">
                            @csrf
                            <div class="row">
                                @forelse($settings as $key => $value)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">{{translate("messages.$key")}}</label>
                                            <input type="text" class="form-control" value="{{ $value }}" id="{{ $key }}" name="{{ $key }}">
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                            <button type="submit" class="btn btn-primary">{{ translate('messages.Save') }}</button>
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
