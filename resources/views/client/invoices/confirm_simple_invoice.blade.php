@extends('wowdash.layout.layout')

@php
    $title = 'Confirmar Fatura';
    $subTitle = 'Confirmar Fatura';
@endphp

@section('content')
    <div class="grid grid-cols-12 gap-5">
        <div class="md:col-span-12 col-span-12">
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="text-lg font-semibold mb-0">Confirmar Dados da Fatura</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('invoice.store-simple') }}" method="POST">
                        @csrf
                        @foreach($qrData as $key => $value)
                            <input type="hidden" name="qrData[{{ $key }}]" value="{{ $value }}">
                        @endforeach

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12">
                                <p><strong>NIF do Emitente:</strong> {{ $qrData['A'] ?? 'N/A' }}</p>
                                <p><strong>NIF do Adquirente:</strong> {{ $qrData['B'] ?? 'N/A' }}</p>
                                <p><strong>Data:</strong> {{ $qrData['F'] ?? 'N/A' }}</p>
                                <p><strong>ATCUD:</strong> {{ $qrData['H'] ?? 'N/A' }}</p>
                                <p><strong>Total IVA:</strong> {{ $qrData['N'] ?? '0.00' }}</p>
                                <p><strong>Total:</strong> {{ $qrData['O'] ?? '0.00' }}</p>
                            </div>

                            <div class="lg:col-span-6 col-span-12">
                                <label class="form-label">Categoria da Despesa</label>
                                <select name="invoiceCategoryId" class="form-control">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12">
                                <button type="submit" class="btn btn-primary-600">Guardar Fatura</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
