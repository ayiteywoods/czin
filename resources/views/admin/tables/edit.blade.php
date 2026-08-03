@extends('layouts.admin')

@section('heading', 'Edit table')
@section('subheading', $table->code.' · '.$table->name)

@section('content')
    <form method="POST" action="{{ route('admin.tables.update', $table) }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.tables.partials.form', ['table' => $table])
        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="btn-primary">Save changes</button>
            <a href="{{ route('admin.tables.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
@endsection
