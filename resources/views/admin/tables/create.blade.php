@extends('layouts.admin')

@section('heading', 'Add table')
@section('subheading', 'Create a new dining table configuration')

@section('content')
    <form method="POST" action="{{ route('admin.tables.store') }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @include('admin.tables.partials.form')
        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-primary">Create table</button>
            <a href="{{ route('admin.tables.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
@endsection
