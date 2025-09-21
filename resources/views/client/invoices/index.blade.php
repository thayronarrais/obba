@extends('wowdash.layout.layout')
@php
    $title='Faturas';
    $subTitle = 'Basic Table';
    $script='<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')

    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card border-0 overflow-hidden">
                <div class="card-header">
                    <h6 class="card-title mb-0 text-lg">Listagem de Faturas</h6>
                </div>
                <div class="card-body">

                    <!-- Flex container para alinhar os grupos de filtros -->
                    <form method="GET" action="{{ route('invoice.index') }}" class="flex flex-wrap items-end gap-4 mb-4">

                        <!-- Grupo de Filtro: Tipo -->
                        <div class="filter-group flex flex-col">
                            <label for="tipo-select" class="text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select id="tipo-select" name="tipo" class="h-10 w-48 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos os tipos</option>
                                <option value="1" @if(request('tipo') == '1') selected @endif>Despesas</option>
                                <option value="2" @if(request('tipo') == '2') selected @endif>Vendas</option>
                            </select>
                        </div>

                        <!-- Grupo de Filtro: Categoria -->
                        <div class="filter-group flex flex-col">
                            <label for="categoria-select" class="text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select id="categoria-select" name="categoria" class="h-10 w-48 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas as categorias</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if(request('categoria') == $category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Grupo de Filtro: NIF -->
                        <div class="filter-group flex flex-col">
                            <label for="nif-input" class="text-sm font-medium text-gray-700 mb-1">NIF</label>
                            <input id="nif-input" name="nif" value="{{ request('nif') }}" class="h-10 w-32 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Procurar NIF" maxlength="9" pattern="[0-9]{1,9}">
                        </div>

                        <!-- Grupo de Filtro: Data (De) -->
                        <div class="filter-group flex flex-col">
                            <label for="date-from" class="text-sm font-medium text-gray-700 mb-1">De:</label>
                            <input id="date-from" name="date_from" value="{{ request('date_from') }}" class="h-10 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" type="date">
                        </div>

                        <!-- Grupo de Filtro: Data (Até) -->
                        <div class="filter-group flex flex-col">
                            <label for="date-to" class="text-sm font-medium text-gray-700 mb-1">Até:</label>
                            <input id="date-to" name="date_to" value="{{ request('date_to') }}" class="h-10 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" type="date">
                        </div>

                        <div class="filter-group flex flex-col">
                            <button type="submit" class="h-10 px-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">Filtrar</button>
                        </div>
                        <div class="filter-group flex flex-col">
                            <a href="{{ route('invoice.index') }}" class="h-10 px-4 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 inline-flex items-center">Limpar</a>
                        </div>
                    </form>


                    <table id="selection-table" class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                        <thead>
                            <tr>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" id="serial" type="checkbox">
                                        <label class="ms-2 form-check-label" for="serial">
                                            S.L
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Tipo
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Categoria
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark.text-white">
                                    <div class="flex items-center gap-2">
                                        ATCUD
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        NIF
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Data
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        IVA
                                    </div>
                                </th>
                                 <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Total
                                    </div>
                                </th>
                                 <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Criado Por
                                    </div>
                                </th>
                                 <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Criado Em
                                    </div>
                                </th>
                                 <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Ações
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="ms-2 form-check-label">
                                            {{ $invoice->id }}
                                        </label>
                                    </div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-primary-600">{{ $invoice->type->label() }}</a></td>
                                <td>
                                    {{ $invoice->categoryData?->name }}
                                </td>
                                <td>{{ $invoice->atcud }}</td>
                                <td>{{ $invoice->nif }}</td>
                                <td>{{ $invoice->date->format('d/m/Y') }} </td>
                                <td>{{ $invoice->total_iva }}</td>
                                <td>{{ $invoice->total }}</td>
                                <td>{{ $invoice->creator->name }}</td>
                                <td>{{ $invoice->created_at->format('d/m/Y') }} </td>
                                <td>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                       
                    </table>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection