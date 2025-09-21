<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvoiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('invoice_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        // Inserir as categorias predefinidas
        $categories = [
            ['id' => 1, 'code' => 'NONE', 'name' => 'Sem categoria', 'group' => 'Geral', 'tax_deductible' => false, 'active' => true, 'order' => 0],
            ['id' => 2, 'code' => 'SUPPLIER', 'name' => 'Fornecedor', 'group' => 'Geral', 'tax_deductible' => true, 'active' => true, 'order' => 1],
            ['id' => 3, 'code' => 'RENT', 'name' => 'Renda', 'group' => 'Despesas Operacionais', 'tax_deductible' => true, 'active' => true, 'order' => 2],
            ['id' => 4, 'code' => 'UTILITIES', 'name' => 'Água, Luz, Gás', 'group' => 'Despesas Operacionais', 'tax_deductible' => true, 'active' => true, 'order' => 3],
            ['id' => 5, 'code' => 'COMMUNICATIONS', 'name' => 'Comunicações', 'group' => 'Despesas Operacionais', 'tax_deductible' => true, 'active' => true, 'order' => 4],
            ['id' => 6, 'code' => 'GENERAL_MAINTENANCE', 'name' => 'Manutenção Geral', 'group' => 'Despesas Operacionais', 'tax_deductible' => true, 'active' => true, 'order' => 5],
            ['id' => 7, 'code' => 'VEHICLE_MAINTENANCE', 'name' => 'Manutenção de Viaturas', 'group' => 'Despesas Operacionais', 'tax_deductible' => true, 'active' => true, 'order' => 6],
            ['id' => 8, 'code' => 'OFFICE_SUPPLIES', 'name' => 'Material de Escritório', 'group' => 'Despesas Administrativas', 'tax_deductible' => true, 'active' => true, 'order' => 7],
            ['id' => 9, 'code' => 'SOFTWARE_LICENSES', 'name' => 'Software e Licenças', 'group' => 'Despesas Administrativas', 'tax_deductible' => true, 'active' => true, 'order' => 8],
            ['id' => 10, 'code' => 'CONSULTING_ACCOUNTING', 'name' => 'Consultoria / Contabilidade', 'group' => 'Despesas Administrativas', 'tax_deductible' => true, 'active' => true, 'order' => 9],
            ['id' => 11, 'code' => 'COMPANY_INSURANCE', 'name' => 'Seguros (Empresa)', 'group' => 'Despesas Administrativas', 'tax_deductible' => true, 'active' => true, 'order' => 10],
            ['id' => 12, 'code' => 'ADVERTISING', 'name' => 'Anúncios', 'group' => 'Marketing e Publicidade', 'tax_deductible' => true, 'active' => true, 'order' => 11],
            ['id' => 13, 'code' => 'CONTENT_PRODUCTION', 'name' => 'Produção de Conteúdo', 'group' => 'Marketing e Publicidade', 'tax_deductible' => true, 'active' => true, 'order' => 12],
            ['id' => 14, 'code' => 'EVENTS_TRADE_SHOWS', 'name' => 'Eventos e Feiras', 'group' => 'Marketing e Publicidade', 'tax_deductible' => true, 'active' => true, 'order' => 13],
            ['id' => 15, 'code' => 'DESIGN_BRANDING', 'name' => 'Design e Branding', 'group' => 'Marketing e Publicidade', 'tax_deductible' => true, 'active' => true, 'order' => 14],
            ['id' => 16, 'code' => 'TRAVEL_LODGING', 'name' => 'Viagens e Estadias', 'group' => 'Viagens e Representação', 'tax_deductible' => true, 'active' => true, 'order' => 15],
            ['id' => 17, 'code' => 'CLIENT_MEALS', 'name' => 'Refeições com Clientes', 'group' => 'Viagens e Representação', 'tax_deductible' => true, 'active' => true, 'order' => 16],
            ['id' => 18, 'code' => 'EQUIPMENT', 'name' => 'Equipamento', 'group' => 'Equipamentos e Ativos', 'tax_deductible' => true, 'active' => true, 'order' => 17],
            ['id' => 19, 'code' => 'DEPRECIATION', 'name' => 'Depreciações', 'group' => 'Equipamentos e Ativos', 'tax_deductible' => true, 'active' => true, 'order' => 18],
            ['id' => 20, 'code' => 'BANK_INTEREST', 'name' => 'Juros Bancários', 'group' => 'Despesas Financeiras', 'tax_deductible' => true, 'active' => true, 'order' => 19],
            ['id' => 21, 'code' => 'COMMISSIONS', 'name' => 'Comissões', 'group' => 'Despesas Financeiras', 'tax_deductible' => true, 'active' => true, 'order' => 20],
            ['id' => 22, 'code' => 'FEES', 'name' => 'Taxas', 'group' => 'Despesas Financeiras', 'tax_deductible' => true, 'active' => true, 'order' => 21],
            ['id' => 23, 'code' => 'NON_DEDUCTIBLE_VAT', 'name' => 'IVA não Dedutível', 'group' => 'Impostos e Taxas', 'tax_deductible' => false, 'active' => true, 'order' => 22],
            ['id' => 24, 'code' => 'LOCAL_TAXES', 'name' => 'Derrama / Contribuições', 'group' => 'Impostos e Taxas', 'tax_deductible' => true, 'active' => true, 'order' => 23],
            ['id' => 25, 'code' => 'FINES_PENALTIES', 'name' => 'Multas e Coimas', 'group' => 'Impostos e Taxas', 'tax_deductible' => false, 'active' => true, 'order' => 24],
            ['id' => 26, 'code' => 'SALARIES', 'name' => 'Salários', 'group' => 'Despesas com Pessoal', 'tax_deductible' => true, 'active' => true, 'order' => 25],
            ['id' => 27, 'code' => 'SOCIAL_SECURITY', 'name' => 'Segurança Social', 'group' => 'Despesas com Pessoal', 'tax_deductible' => true, 'active' => true, 'order' => 26],
            ['id' => 28, 'code' => 'SUBSIDIES', 'name' => 'Subsídios (férias/Natal)', 'group' => 'Despesas com Pessoal', 'tax_deductible' => true, 'active' => true, 'order' => 27],
            ['id' => 29, 'code' => 'TRAINING', 'name' => 'Formação', 'group' => 'Despesas com Pessoal', 'tax_deductible' => true, 'active' => true, 'order' => 28],
            ['id' => 30, 'code' => 'PERSONAL_VEHICLE_KMS', 'name' => 'Kms com Viatura Própria (0,36€/km)', 'group' => 'Viaturas e Quilómetros', 'tax_deductible' => true, 'active' => true, 'order' => 29],
            ['id' => 31, 'code' => 'VEHICLE_EXPENSES_UP_TO_20K', 'name' => 'Despesas com Viaturas até 20.000€', 'group' => 'Viaturas e Quilómetros', 'tax_deductible' => true, 'active' => true, 'order' => 30],
            ['id' => 32, 'code' => 'VEHICLES_20K_35K', 'name' => 'Viaturas >20k-35k / >35k', 'group' => 'Viaturas e Quilómetros', 'tax_deductible' => true, 'active' => true, 'order' => 31],
            ['id' => 33, 'code' => 'ELECTRIC_VEHICLES', 'name' => 'Viaturas Elétricas', 'group' => 'Viaturas e Quilómetros', 'tax_deductible' => true, 'active' => true, 'order' => 32],
        ];

        foreach ($categories as $category) {
            DB::table('invoice_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
