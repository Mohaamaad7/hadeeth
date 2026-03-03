@extends('layouts.frontend')

@section('title', $title ?? 'الموسوعة الرقمية لصحيح الجامع')

@section('content')
    {{ $slot }}
@endsection