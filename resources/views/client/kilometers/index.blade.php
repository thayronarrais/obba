@extends('wowdash.layout.layout')
@php
    $title='Quilómetros';
    $subTitle = 'Basic Table';
    $script='<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')

    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card border-0 overflow-hidden">
                <div class="card-header">
                    <h6 class="card-title mb-0 text-lg">Listagem de Quilometragem</h6>
                </div>
                <div class="card-body">

                    <!-- Flex container para alinhar os grupos de filtros -->
                    <form method="GET" action="{{ route('kilometer.index') }}" class="flex flex-wrap items-end gap-4 mb-4">

                        <!-- Grupo de Filtro: Empresa -->
                        <div class="filter-group flex flex-col">
                            <label for="company-select" class="text-sm font-medium text-gray-700 mb-1">Empresa</label>
                            <select id="company-select" name="company_id" class="h-10 w-48 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas as empresas</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @if(request('company_id') == $company->id) selected @endif>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Grupo de Filtro: Condutor -->
                        <div class="filter-group flex flex-col">
                            <label for="driver-input" class="text-sm font-medium text-gray-700 mb-1">Condutor</label>
                            <input id="driver-input" name="driver" value="{{ request('driver') }}" class="h-10 w-48 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nome do condutor">
                        </div>

                        <!-- Grupo de Filtro: Matrícula -->
                        <div class="filter-group flex flex-col">
                            <label for="licenseplate-input" class="text-sm font-medium text-gray-700 mb-1">Matrícula</label>
                            <input id="licenseplate-input" name="licenseplate" value="{{ request('licenseplate') }}" class="h-10 w-32 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="XX-XX-XX">
                        </div>

                        <!-- Grupo de Filtro: Ano -->
                        <div class="filter-group flex flex-col">
                            <label for="year-select" class="text-sm font-medium text-gray-700 mb-1">Ano</label>
                            <select id="year-select" name="year" class="h-10 w-32 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos os anos</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" @if(request('year') == $year) selected @endif>{{ $year }}</option>
                                @endforeach
                            </select>
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

                        <!-- Grupo de Filtro: Pesquisar -->
                        <div class="filter-group flex flex-col">
                            <label for="search-input" class="text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                            <input id="search-input" name="search" value="{{ request('search') }}" class="h-10 w-48 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Origem, destino, motivo...">
                        </div>

                        <div class="filter-group flex flex-col">
                            <button type="submit" class="h-10 px-4 text-white bg-blue-600 rounded-md hover:bg-blue-700">Filtrar</button>
                        </div>
                        <div class="filter-group flex flex-col">
                            <a href="{{ route('kilometer.index') }}" class="h-10 px-4 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 inline-flex items-center">Limpar</a>
                        </div>
                    </form>

                    <!-- Estatísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Total de Registos</h3>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $totals['total_records'] }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Quilómetros</h3>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($totals['total_kilometers'], 0, ',', '.') }} km</p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Custo Estimado</h3>
                            <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ number_format($totals['total_cost'], 2, ',', '.') }} €</p>
                        </div>
                    </div>

                    <table id="selection-table" class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate">
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
                                        Condutor
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Matrícula
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Empresa
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
                                        Rota
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Quilómetros
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Custo Est.
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Motivo
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        Criado Por
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
                            @foreach ($kilometers as $kilometer)
                            <tr>
                                <td>
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="ms-2 form-check-label">
                                            {{ $kilometer->id }}
                                        </label>
                                    </div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-primary-600">{{ $kilometer->name }}</a></td>
                                <td>{{ $kilometer->formatted_license_plate }}</td>
                                <td>{{ $kilometer->company->name }}</td>
                                <td>{{ $kilometer->formatted_date }}</td>
                                <td class="text-sm">{{ $kilometer->route }}</td>
                                <td>{{ $kilometer->formatted_kilometers }}</td>
                                <td>{{ $kilometer->formatted_estimated_cost }}</td>
                                <td class="text-sm max-w-xs truncate" title="{{ $kilometer->reason }}">{{ $kilometer->reason }}</td>
                                <td>{{ $kilometer->creator->name }}</td>
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
                    {{ $kilometers->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection