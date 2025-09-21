@extends('wowdash.layout.layout')

@php
    $title = 'Inserção manual';
    $subTitle = 'Inserção manual';
@endphp

@section('content')
    <div class="grid grid-cols-12 gap-5">
        <div class="md:col-span-12 col-span-12">
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="text-lg font-semibold mb-0">Inserção manual</h5>
                </div>
                <div class="card-body" x-data="{ foreignExpense: false }">
                    <form action="{{ route('invoice.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="foreignExpense" x-bind:value="foreignExpense">
                        <div class="grid grid-cols-12 gap-4">

                            <div class="col-span-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="foreign-expense" x-model="foreignExpense">
                                    <label class="form-check-label" for="foreign-expense">Despesa de Estrangeiro?</label>
                                </div>
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Empresa</label>
                                <select name="invoiceCompanyId" class="form-control">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Tipo de Fatura</label>
                                <select name="invoiceType" class="form-control">
                                    @foreach(App\Enums\InvoiceType::options() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Categoria da Fatura</label>
                                <select name="invoiceCategoryId" class="form-control">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">NIF do emitente</label>
                                <input type="text" name="invoiceData[A]" class="form-control" placeholder="Ex: 532156498">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">NIF do adquirente</label>
                                <input type="text" name="invoiceData[B]" class="form-control" placeholder="Ex: 532156845">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">País do adquirente</label>
                                <input type="text" name="invoiceData[acquirer_country]" class="form-control" value="PT">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Tipo de documento</label>
                                <input type="text" name="invoiceData[document_type]" class="form-control" placeholder="Ex: FR">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Estado do documento</label>
                                <input type="text" name="invoiceData[document_status]" class="form-control" value="N">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Data do documento</label>
                                <input type="date" name="invoiceData[F]" class="form-control">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Identificação única do documento</label>
                                <input type="text" name="invoiceData[document_id]" class="form-control" placeholder="Ex: FR 0001111/001">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12" x-show="!foreignExpense">
                                <label class="form-label">ATCUD</label>
                                <input type="text" name="invoiceAtcud" class="form-control" placeholder="Ex: JJMSSJ-001">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Espaço fiscal</label>
                                <input type="text" name="invoiceData[fiscal_space]" class="form-control" value="PT">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Imposto do Selo (opcional)</label>
                                <input type="number" step="0.01" name="invoiceData[stamp_tax]" class="form-control" placeholder="0.00">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Total de impostos</label>
                                <input type="number" step="0.01" name="invoiceData[N]" class="form-control" placeholder="0.00">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Total do documento com impostos</label>
                                <input type="number" step="0.01" name="invoiceData[O]" class="form-control" placeholder="0.00">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Retenções na fonte (opcional)</label>
                                <input type="number" step="0.01" name="invoiceData[withholding_tax]" class="form-control" placeholder="0.00">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label">Nº do certificado</label>
                                <input type="text" name="invoiceData[certificate_number]" class="form-control" placeholder="Ex: 0001">
                            </div>

                            <div class="lg:col-span-4 md:col-span-6 col-span-12">
                                <label class="form-label btn btn-primary-600">
                                    Fotografar Fatura
                                    <input type="file" name="invoiceImage" class="hidden" accept="image/*" capture>
                                </label>
                            </div>

                            

                            <div class="col-span-12">
                                <button type="submit" class="btn btn-primary-600">Submeter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
