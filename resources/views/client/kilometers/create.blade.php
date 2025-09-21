@extends('wowdash.layout.layout')

@php
    $title = 'Registar Quilometragem';
    $subTitle = 'Registar Quilometragem';
@endphp

@section('content')
    <div class="grid grid-cols-12 gap-5">
        <div class="md:col-span-12 col-span-12">
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="text-lg font-semibold mb-0">Registar Quilometragem</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kilometer.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-12 gap-4">

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Empresa *</label>
                                <select name="companyId" class="form-control" required>
                                    <option value="">Selecionar empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" @if(old('companyId') == $company->id) selected @endif>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('companyId')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Nome do Condutor *</label>
                                <input type="text" name="name" class="form-control" placeholder="Ex: João Silva" value="{{ old('name') }}" required maxlength="128">
                                @error('name')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Matrícula do Veículo *</label>
                                <input type="text" name="licensePlate" class="form-control" placeholder="Ex: 12-AB-34" value="{{ old('licensePlate') }}" required maxlength="12" pattern="[A-Z0-9]{2}-[A-Z0-9]{2}-[A-Z0-9]{2}" style="text-transform: uppercase;">
                                <small class="text-gray-500">Formato português: XX-XX-XX</small>
                                @error('licensePlate')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Data da Viagem *</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                                @error('date')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Origem *</label>
                                <input type="text" name="origin" class="form-control" placeholder="Ex: Lisboa" value="{{ old('origin') }}" required maxlength="256">
                                @error('origin')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Destino *</label>
                                <input type="text" name="destination" class="form-control" placeholder="Ex: Porto" value="{{ old('destination') }}" required maxlength="256">
                                @error('destination')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Quilómetros *</label>
                                <input type="number" name="kilometers" class="form-control" placeholder="Ex: 315" value="{{ old('kilometers') }}" required min="1" max="999999999">
                                <small class="text-gray-500">Distância total da viagem (ida e volta se aplicável)</small>
                                @error('kilometers')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <!-- Espaço vazio para alinhamento -->
                            </div>

                            <div class="col-span-12">
                                <label class="form-label">Motivo da Viagem *</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Ex: Reunião com cliente, deslocação a obra, entrega de documentos..." required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                        <!-- Resumo dos Cálculos -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h6 class="text-md font-semibold mb-3">Cálculo do Ajuste de Custos</h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Quilómetros:</span>
                                    <div class="font-semibold" id="display-km">0 km</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Taxa por Km:</span>
                                    <div class="font-semibold">0,36 €</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Custo Estimado:</span>
                                    <div class="font-bold text-lg text-green-600" id="display-cost">0,00 €</div>
                                </div>
                            </div>
                            <small class="text-gray-500 mt-2 block">* Valor baseado na taxa de 0,36€ por quilómetro para veículos próprios conforme legislação portuguesa</small>
                        </div>

                        <div class="col-span-12 mt-6">
                            <div class="flex gap-3">
                                <button type="submit" class="btn btn-primary-600">Registar Quilometragem</button>
                                <a href="{{ route('kilometer.index') }}" class="btn btn-secondary-600">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para calcular e atualizar o resumo
        function updateSummary() {
            const kilometers = parseInt(document.querySelector('input[name="kilometers"]').value) || 0;
            const costPerKm = 0.36;
            const totalCost = kilometers * costPerKm;

            document.getElementById('display-km').textContent = formatNumber(kilometers) + ' km';
            document.getElementById('display-cost').textContent = formatCurrency(totalCost);
        }

        // Função para formatar valores em euros
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-PT', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 2
            }).format(value);
        }

        // Função para formatar números
        function formatNumber(value) {
            return new Intl.NumberFormat('pt-PT').format(value);
        }

        // Função para validar e formatar matrícula portuguesa
        function formatLicensePlate(input) {
            let value = input.value.replace(/[^A-Z0-9]/g, '').toUpperCase();

            if (value.length > 6) {
                value = value.substring(0, 6);
            }

            if (value.length > 4) {
                value = value.substring(0, 2) + '-' + value.substring(2, 4) + '-' + value.substring(4);
            } else if (value.length > 2) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }

            input.value = value;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar cálculos quando os quilómetros mudarem
            const kmInput = document.querySelector('input[name="kilometers"]');
            if (kmInput) {
                kmInput.addEventListener('input', updateSummary);
                kmInput.addEventListener('change', updateSummary);
            }

            // Formatação automática da matrícula
            const licensePlateInput = document.querySelector('input[name="licensePlate"]');
            if (licensePlateInput) {
                licensePlateInput.addEventListener('input', function() {
                    formatLicensePlate(this);
                });
            }

            // Atualizar resumo inicial
            updateSummary();
        });
    </script>
@endsection