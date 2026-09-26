@extends('layouts.admin.app')

@section('title', translate('messages.third_party_apis'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/api.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.third_party_apis')}}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.third-party-links')
        </div>
        <!-- End Page Header -->
        <div class="card">
        @php($map_api_key=\App\Models\BusinessSetting::where(['key'=>'map_api_key'])->first())
        @php($map_api_key=$map_api_key?$map_api_key->value:null)

        @php($map_api_key_server=\App\Models\BusinessSetting::where(['key'=>'map_api_key_server'])->first())
        @php($map_api_key_server=$map_api_key_server?$map_api_key_server->value:null)

        @php($success_topup_status=\App\Models\BusinessSetting::where(['key'=>'success_topup_status'])->first())
        @php($success_topup_status=$success_topup_status?$success_topup_status->value:0)
        @php($success_topup_go_live=\App\Models\BusinessSetting::where(['key'=>'success_topup_go_live'])->first())
        @php($success_topup_go_live=$success_topup_go_live?$success_topup_go_live->value:0)
        @php($success_topup_key=\App\Models\BusinessSetting::where(['key'=>'success_topup_key'])->first())
        @php($success_topup_key=$success_topup_key?$success_topup_key->value:null)
        @php($success_topup_secret=\App\Models\BusinessSetting::where(['key'=>'success_topup_secret'])->first())
        @php($success_topup_secret=$success_topup_secret?$success_topup_secret->value:null)
            <div class="card-header card-header-shadow border-0 align-items-center">
                <h5 class="card-title align-items-center text--title">
                    {{translate('Google Map API Setup')}}
                </h5>
                <div class="blinkings active lg-top">
                    <i class="tio-info-outined"></i>
                    <div class="business-notes">
                        <h6><img src="{{asset('/assets/admin/img/notes.png')}}" alt=""> {{translate('Note')}}</h6>
                        <div>
                            {{translate('Without configuring this section map functionality will not work properly. Thus the whole
                                 system will not work as it planned')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert--primary d-flex" role="alert">
                    <div class="alert--icon">
                        <i class="tio-info"></i>
                    </div>
                    <div>
                        {{translate('messages.map_api_hint_map_api_hint_2')}}
                    </div>
                </div>
                <div class="py-1"></div>
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.third-party.config-update'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.client')}})</label>
                                <input type="text" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.client')}})" class="form-control" name="map_api_key"
                                    value="{{env('APP_MODE')!='demo'?$map_api_key??'':''}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.server')}})</label>
                                <input type="text" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.server')}})" class="form-control" name="map_api_key_server"
                                    value="{{env('APP_MODE')!='demo'?$map_api_key_server??'':''}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header card-header-shadow border-0 align-items-center">
                <h5 class="card-title align-items-center text--title">
                    {{translate('Success TopUp API Setup')}}
                </h5>
                <div class="blinkings active lg-top">
                    <i class="tio-info-outined"></i>
                    <div class="business-notes">
                        <h6><img src="{{asset('/assets/admin/img/notes.png')}}" alt=""> {{translate('Note')}}</h6>
                        <div>
                            {{translate('These credentials are used by the customer app to perform mobile recharge, data pack and bill payment. Keep the secret safe.')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.third-party.config-update'):'javascript:'}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="success_topup_form" value="1">
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control">
                                    <span class="pr-2">{{ translate('Enable Success TopUp') }}</span>
                                    <input type="checkbox" name="success_topup_status" id="success_topup_status" value="1" class="toggle-switch-input" {{ $success_topup_status == 1 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control">
                                    <span class="pr-2">
                                        {{ translate('Go Live') }}
                                        <small class="d-block text-muted">{{ translate('Disabled uses the Success TopUp sandbox endpoint. Enabled sends real transactions.') }}</small>
                                    </span>
                                    <input type="checkbox" name="success_topup_go_live" id="success_topup_go_live" value="1" class="toggle-switch-input" {{ $success_topup_go_live == 1 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('Success TopUp API Key')}}</label>
                                <input type="text" placeholder="{{translate('Success TopUp API Key')}}" class="form-control" name="success_topup_key" value="{{env('APP_MODE')!='demo'?$success_topup_key??'':''}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('Success TopUp API Secret')}}</label>
                                <input type="text" placeholder="{{translate('Success TopUp API Secret')}}" class="form-control" name="success_topup_secret" value="{{env('APP_MODE')!='demo'?$success_topup_secret??'':''}}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
