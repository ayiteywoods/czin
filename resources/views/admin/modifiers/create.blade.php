@extends('layouts.admin')

@section('heading', 'Add modifier group')

@section('content')
    <form method="POST" action="{{ route('admin.modifiers.store') }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('admin.modifiers.partials.form')
        <button type="submit" class="btn-primary">Save group</button>
    </form>
@endsection
