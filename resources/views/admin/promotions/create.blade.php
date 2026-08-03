@extends('layouts.admin')

@section('heading', 'Add promotion')

@section('content')
    <form method="POST" action="{{ route('admin.promotions.store') }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('admin.promotions.partials.form')
        <button type="submit" class="btn-primary">Save promotion</button>
    </form>
@endsection
