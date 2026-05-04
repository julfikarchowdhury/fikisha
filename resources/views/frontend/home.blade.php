@extends('frontend.layouts.master')
@section('title')
    {{ __('levels.home') }} | {{ @settings()->name }}
@endsection
@section('content')  
    @include('frontend.section.banner')
@endsection