<?php

namespace App\Enums;

// Adicionar todo o tipo de despesas tributáveis
enum InvoiceCategory: int
{
	case NONE = 0;
	case SUPPLIER = 1;

		// 1. Operational Expenses
	case RENT = 2;
	case UTILITIES = 3; // Water, Electricity, Gas
	case COMMUNICATIONS = 4;
	case GENERAL_MAINTENANCE = 5;
	case VEHICLE_MAINTENANCE = 6;

		// 2. Administrative Expenses
	case OFFICE_SUPPLIES = 7;
	case SOFTWARE_LICENSES = 8;
	case CONSULTING_ACCOUNTING = 9;
	case COMPANY_INSURANCE = 10;

		// 3. Marketing and Advertising
	case ADVERTISING = 11;
	case CONTENT_PRODUCTION = 12;
	case EVENTS_TRADE_SHOWS = 13;
	case DESIGN_BRANDING = 14;

		// 4. Travel and Representation
	case TRAVEL_LODGING = 15;
	case CLIENT_MEALS = 16;

		// 5. Equipment and Fixed Assets
	case EQUIPMENT = 17;
	case DEPRECIATION = 18;

		// 6. Financial Expenses
	case BANK_INTEREST = 19;
	case COMMISSIONS = 20;
	case FEES = 21;

		// 7. Taxes and Levies
	case NON_DEDUCTIBLE_VAT = 22;
	case LOCAL_TAXES = 23; // (Derrama / Contributions)
	case FINES_PENALTIES = 24;

		// 8. Personnel Expenses
	case SALARIES = 25;
	case SOCIAL_SECURITY = 26;
	case SUBSIDIES = 27; // (Holidays/Christmas)
	case TRAINING = 28;

		// 9. Vehicles and Kilometers (Km Sheet)
	case PERSONAL_VEHICLE_KMS = 29;
	case VEHICLE_EXPENSES_UP_TO_20K = 30;
	case VEHICLES_20K_35K = 31;
	case ELECTRIC_VEHICLES = 32;

	public function label(): string
	{
		return match ($this) {
			self::NONE => 'Sem categoria',
			self::SUPPLIER => 'Fornecedor',

			// 1. Operational Expenses
			self::RENT => 'Renda',
			self::UTILITIES => 'Água, Luz, Gás',
			self::COMMUNICATIONS => 'Comunicações',
			self::GENERAL_MAINTENANCE => 'Manutenção Geral',
			self::VEHICLE_MAINTENANCE => 'Manutenção de Viaturas',

			// 2. Administrative Expenses
			self::OFFICE_SUPPLIES => 'Material de Escritório',
			self::SOFTWARE_LICENSES => 'Software e Licenças',
			self::CONSULTING_ACCOUNTING => 'Consultoria / Contabilidade',
			self::COMPANY_INSURANCE => 'Seguros (Empresa)',

			// 3. Marketing and Advertising
			self::ADVERTISING => 'Anúncios',
			self::CONTENT_PRODUCTION => 'Produção de Conteúdo',
			self::EVENTS_TRADE_SHOWS => 'Eventos e Feiras',
			self::DESIGN_BRANDING => 'Design e Branding',

			// 4. Travel and Representation
			self::TRAVEL_LODGING => 'Viagens e Estadias',
			self::CLIENT_MEALS => 'Refeições com Clientes',

			// 5. Equipment and Fixed Assets
			self::EQUIPMENT => 'Equipamento',
			self::DEPRECIATION => 'Depreciações',

			// 6. Financial Expenses
			self::BANK_INTEREST => 'Juros Bancários',
			self::COMMISSIONS => 'Comissões',
			self::FEES => 'Taxas',

			// 7. Taxes and Levies
			self::NON_DEDUCTIBLE_VAT => 'IVA não Dedutível',
			self::LOCAL_TAXES => 'Derrama / Contribuições',
			self::FINES_PENALTIES => 'Multas e Coimas',

			// 8. Personnel Expenses
			self::SALARIES => 'Salários',
			self::SOCIAL_SECURITY => 'Segurança Social',
			self::SUBSIDIES => 'Subsídios (férias/Natal)',
			self::TRAINING => 'Formação',

			// 9. Vehicles and Kilometers (Km Sheet)
			self::PERSONAL_VEHICLE_KMS => 'Kms com Viatura Própria (0,36€/km)',
			self::VEHICLE_EXPENSES_UP_TO_20K => 'Despesas com Viaturas até 20.000€',
			self::VEHICLES_20K_35K => 'Viaturas >20k-35k / >35k',
			self::ELECTRIC_VEHICLES => 'Viaturas Elétricas',
		};
	}
}
