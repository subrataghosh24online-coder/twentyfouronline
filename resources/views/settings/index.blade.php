@extends('layouts.twentyfouronlinev1')

@section('title', __('settings.title'))

@section('content')
    <div class="container-fluid">
        <div id="app">
            <twentyfouronline-settings
                prefix="{{ url('settings') }}"
                initial-tab="{{ $active_tab }}"
                initial-section="{{ $active_section }}"
                :tabs="{{ $groups }}"
            ></twentyfouronline-settings>
        </div>
    </div>
@endsection

@push('scripts')
    @routes
    @vuei18n
@endpush




