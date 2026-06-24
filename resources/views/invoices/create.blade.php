@extends('app')

@section('title', 'Create Invoice')

@section('content')

<div class="page-layout invoice-page-layout">
    <form method="POST" action="{{ route('invoices.store') }}">
        @csrf

        @include('invoices.partials.form', [
            'title' => 'Create Invoice',
            'subtitle' => 'Build an invoice and update inventory from one screen',
            'submitLabel' => 'Save Invoice',
        ])
    </form>
</div>

@endsection

