@extends('app')

@section('title', 'Edit Invoice')

@section('content')

<div class="page-layout invoice-page-layout">
    <form method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf
        @method('PUT')

        @include('invoices.partials.form', [
            'title' => 'Edit Invoice',
            'subtitle' => 'Update invoice details, products, payment, and stock movement',
            'submitLabel' => 'Update Invoice',
        ])
    </form>
</div>

@endsection

