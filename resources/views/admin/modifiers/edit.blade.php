@extends('layouts.admin')

@section('heading', 'Edit modifier group')

@section('content')
    <form method="POST" action="{{ route('admin.modifiers.update', $group) }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.modifiers.partials.form', ['group' => $group])
        <button type="submit" class="btn-primary">Update group</button>
    </form>
@endsection
