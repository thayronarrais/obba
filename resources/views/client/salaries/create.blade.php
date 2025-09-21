@extends('wowdash.layout.layout')

@php
    $title = 'Registar Salário';
    $subTitle = 'Registar Salário';
@endphp

@section('content')
    <div class="grid grid-cols-12 gap-5">
        <div class="md:col-span-12 col-span-12">
            <div class="card border-0">
                <div class="card-header">
                    <h5 class="text-lg font-semibold mb-0">Registar Salário</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('salary.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-12 gap-4">

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Empresa *</label>
                                <select name="company_id" class="form-control" required>
                                    <option value="">Selecionar empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" @if(old('company_id') == $company->id) selected @endif>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Funcionário *</label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">Selecionar funcionário</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @if(old('employee_id') == $employee->id) selected @endif>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Data do Salário *</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                                @error('date')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Salário Bruto Mensal *</label>
                                <input type="number" step="0.01" min="0" max="999999.99" name="gross_salary_month" class="form-control" placeholder="Ex: 1200.00" value="{{ old('gross_salary_month') }}" required>
                                @error('gross_salary_month')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Subsídio de Alimentação Mensal</label>
                                <input type="number" step="0.01" min="0" max="999999.99" name="food_allowance_month" class="form-control" placeholder="Ex: 120.00" value="{{ old('food_allowance_month') }}">
                                @error('food_allowance_month')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Subsídios Adicionais</label>
                                <input type="number" step="0.01" min="0" max="999999.99" name="additional_subsidies" class="form-control" placeholder="Ex: 50.00" value="{{ old('additional_subsidies') }}">
                                @error('additional_subsidies')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Segurança Social (Taxa Patronal)</label>
                                <input type="number" step="0.01" min="0" max="999999.99" name="social_security" class="form-control" placeholder="Ex: 275.50" value="{{ old('social_security') }}">
                                <small class="text-gray-500">Contribuição da empresa para a Segurança Social (≈23.75%)</small>
                                @error('social_security')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="lg:col-span-6 md:col-span-6 col-span-12">
                                <label class="form-label">Seguro Obrigatório</label>
                                <input type="number" step="0.01" min="0" max="999999.99" name="mandatory_ensurance" class="form-control" placeholder="Ex: 15.00" value="{{ old('mandatory_ensurance') }}">
                                <small class="text-gray-500">Seguro de acidentes de trabalho obrigatório</small>
                                @error('mandatory_ensurance')
                                    <small class="text-red-500">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                        <!-- Resumo dos Cálculos -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <h6 class="text-md font-semibold mb-3">Resumo dos Custos</h6>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Salário Bruto:</span>
                                    <div class="font-semibold" id="display-gross">0,00 €</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Subsídios:</span>
                                    <div class="font-semibold" id="display-benefits">0,00 €</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Encargos Sociais:</span>
                                    <div class="font-semibold" id="display-charges">0,00 €</div>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Custo Total:</span>
                                    <div class="font-bold text-lg text-blue-600" id="display-total">0,00 €</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 mt-6">
                            <div class="flex gap-3">
                                <button type="submit" class="btn btn-primary-600">Registar Salário</button>
                                <a href="{{ route('salary.index') }}" class="btn btn-secondary-600">Cancelar</a>
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
            const gross = parseFloat(document.querySelector('input[name="gross_salary_month"]').value) || 0;
            const food = parseFloat(document.querySelector('input[name="food_allowance_month"]').value) || 0;
            const additional = parseFloat(document.querySelector('input[name="additional_subsidies"]').value) || 0;
            const socialSecurity = parseFloat(document.querySelector('input[name="social_security"]').value) || 0;
            const insurance = parseFloat(document.querySelector('input[name="mandatory_ensurance"]').value) || 0;

            const totalBenefits = food + additional;
            const totalCharges = socialSecurity + insurance;
            const totalCost = gross + totalBenefits + totalCharges;

            document.getElementById('display-gross').textContent = formatCurrency(gross);
            document.getElementById('display-benefits').textContent = formatCurrency(totalBenefits);
            document.getElementById('display-charges').textContent = formatCurrency(totalCharges);
            document.getElementById('display-total').textContent = formatCurrency(totalCost);
        }

        // Função para formatar valores em euros
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-PT', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 2
            }).format(value);
        }

        // Event listeners para atualizar cálculos em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = [
                'input[name="gross_salary_month"]',
                'input[name="food_allowance_month"]',
                'input[name="additional_subsidies"]',
                'input[name="social_security"]',
                'input[name="mandatory_ensurance"]'
            ];

            inputs.forEach(selector => {
                const input = document.querySelector(selector);
                if (input) {
                    input.addEventListener('input', updateSummary);
                    input.addEventListener('change', updateSummary);
                }
            });

            // Cálculo automático da Segurança Social (se estiver vazio)
            document.querySelector('input[name="gross_salary_month"]').addEventListener('input', function() {
                const ssInput = document.querySelector('input[name="social_security"]');
                if (!ssInput.value || ssInput.value == 0) {
                    const grossSalary = parseFloat(this.value) || 0;
                    const socialSecurityRate = 0.2375; // 23.75%
                    ssInput.value = (grossSalary * socialSecurityRate).toFixed(2);
                }
                updateSummary();
            });

            // Atualizar resumo inicial
            updateSummary();
        });
    </script>
@endsection