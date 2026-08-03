@extends('layouts.admin')

@section('heading', 'Edit promotion')

@section('content')
    <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.promotions.partials.form', ['promotion' => $promotion])
        <button type="submit" class="btn-primary">Update promotion</button>
    </form>
@endsection
