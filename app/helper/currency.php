<?php
namespace HubloyMembership\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Currency helper
 *
 * @since 1.0.0
 */
class Currency {

	/**
	 * List currencies
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function list_currencies() {
		static $currencies;
		if ( ! isset( $currencies ) ) {
			$currencies = array_unique(
				apply_filters(
					'hubloy_membership_currencies',
					array(
						'AED' => __( 'United Arab Emirates dirham', 'memberships-by-hubloy' ),
						'AFN' => __( 'Afghan afghani', 'memberships-by-hubloy' ),
						'ALL' => __( 'Albanian lek', 'memberships-by-hubloy' ),
						'AMD' => __( 'Armenian dram', 'memberships-by-hubloy' ),
						'ANG' => __( 'Netherlands Antillean guilder', 'memberships-by-hubloy' ),
						'AOA' => __( 'Angolan kwanza', 'memberships-by-hubloy' ),
						'ARS' => __( 'Argentine peso', 'memberships-by-hubloy' ),
						'AUD' => __( 'Australian dollar', 'memberships-by-hubloy' ),
						'AWG' => __( 'Aruban florin', 'memberships-by-hubloy' ),
						'AZN' => __( 'Azerbaijani manat', 'memberships-by-hubloy' ),
						'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'memberships-by-hubloy' ),
						'BBD' => __( 'Barbadian dollar', 'memberships-by-hubloy' ),
						'BDT' => __( 'Bangladeshi taka', 'memberships-by-hubloy' ),
						'BGN' => __( 'Bulgarian lev', 'memberships-by-hubloy' ),
						'BHD' => __( 'Bahraini dinar', 'memberships-by-hubloy' ),
						'BIF' => __( 'Burundian franc', 'memberships-by-hubloy' ),
						'BMD' => __( 'Bermudian dollar', 'memberships-by-hubloy' ),
						'BND' => __( 'Brunei dollar', 'memberships-by-hubloy' ),
						'BOB' => __( 'Bolivian boliviano', 'memberships-by-hubloy' ),
						'BRL' => __( 'Brazilian real', 'memberships-by-hubloy' ),
						'BSD' => __( 'Bahamian dollar', 'memberships-by-hubloy' ),
						'BTC' => __( 'Bitcoin', 'memberships-by-hubloy' ),
						'BTN' => __( 'Bhutanese ngultrum', 'memberships-by-hubloy' ),
						'BWP' => __( 'Botswana pula', 'memberships-by-hubloy' ),
						'BYR' => __( 'Belarusian ruble (old)', 'memberships-by-hubloy' ),
						'BYN' => __( 'Belarusian ruble', 'memberships-by-hubloy' ),
						'BZD' => __( 'Belize dollar', 'memberships-by-hubloy' ),
						'CAD' => __( 'Canadian dollar', 'memberships-by-hubloy' ),
						'CDF' => __( 'Congolese franc', 'memberships-by-hubloy' ),
						'CHF' => __( 'Swiss franc', 'memberships-by-hubloy' ),
						'CLP' => __( 'Chilean peso', 'memberships-by-hubloy' ),
						'CNY' => __( 'Chinese yuan', 'memberships-by-hubloy' ),
						'COP' => __( 'Colombian peso', 'memberships-by-hubloy' ),
						'CRC' => __( 'Costa Rican col&oacute;n', 'memberships-by-hubloy' ),
						'CUC' => __( 'Cuban convertible peso', 'memberships-by-hubloy' ),
						'CUP' => __( 'Cuban peso', 'memberships-by-hubloy' ),
						'CVE' => __( 'Cape Verdean escudo', 'memberships-by-hubloy' ),
						'CZK' => __( 'Czech koruna', 'memberships-by-hubloy' ),
						'DJF' => __( 'Djiboutian franc', 'memberships-by-hubloy' ),
						'DKK' => __( 'Danish krone', 'memberships-by-hubloy' ),
						'DOP' => __( 'Dominican peso', 'memberships-by-hubloy' ),
						'DZD' => __( 'Algerian dinar', 'memberships-by-hubloy' ),
						'EGP' => __( 'Egyptian pound', 'memberships-by-hubloy' ),
						'ERN' => __( 'Eritrean nakfa', 'memberships-by-hubloy' ),
						'ETB' => __( 'Ethiopian birr', 'memberships-by-hubloy' ),
						'EUR' => __( 'Euro', 'memberships-by-hubloy' ),
						'FJD' => __( 'Fijian dollar', 'memberships-by-hubloy' ),
						'FKP' => __( 'Falkland Islands pound', 'memberships-by-hubloy' ),
						'GBP' => __( 'Pound sterling', 'memberships-by-hubloy' ),
						'GEL' => __( 'Georgian lari', 'memberships-by-hubloy' ),
						'GGP' => __( 'Guernsey pound', 'memberships-by-hubloy' ),
						'GHS' => __( 'Ghana cedi', 'memberships-by-hubloy' ),
						'GIP' => __( 'Gibraltar pound', 'memberships-by-hubloy' ),
						'GMD' => __( 'Gambian dalasi', 'memberships-by-hubloy' ),
						'GNF' => __( 'Guinean franc', 'memberships-by-hubloy' ),
						'GTQ' => __( 'Guatemalan quetzal', 'memberships-by-hubloy' ),
						'GYD' => __( 'Guyanese dollar', 'memberships-by-hubloy' ),
						'HKD' => __( 'Hong Kong dollar', 'memberships-by-hubloy' ),
						'HNL' => __( 'Honduran lempira', 'memberships-by-hubloy' ),
						'HRK' => __( 'Croatian kuna', 'memberships-by-hubloy' ),
						'HTG' => __( 'Haitian gourde', 'memberships-by-hubloy' ),
						'HUF' => __( 'Hungarian forint', 'memberships-by-hubloy' ),
						'IDR' => __( 'Indonesian rupiah', 'memberships-by-hubloy' ),
						'ILS' => __( 'Israeli new shekel', 'memberships-by-hubloy' ),
						'IMP' => __( 'Manx pound', 'memberships-by-hubloy' ),
						'INR' => __( 'Indian rupee', 'memberships-by-hubloy' ),
						'IQD' => __( 'Iraqi dinar', 'memberships-by-hubloy' ),
						'IRR' => __( 'Iranian rial', 'memberships-by-hubloy' ),
						'IRT' => __( 'Iranian toman', 'memberships-by-hubloy' ),
						'ISK' => __( 'Icelandic kr&oacute;na', 'memberships-by-hubloy' ),
						'JEP' => __( 'Jersey pound', 'memberships-by-hubloy' ),
						'JMD' => __( 'Jamaican dollar', 'memberships-by-hubloy' ),
						'JOD' => __( 'Jordanian dinar', 'memberships-by-hubloy' ),
						'JPY' => __( 'Japanese yen', 'memberships-by-hubloy' ),
						'KES' => __( 'Kenyan shilling', 'memberships-by-hubloy' ),
						'KGS' => __( 'Kyrgyzstani som', 'memberships-by-hubloy' ),
						'KHR' => __( 'Cambodian riel', 'memberships-by-hubloy' ),
						'KMF' => __( 'Comorian franc', 'memberships-by-hubloy' ),
						'KPW' => __( 'North Korean won', 'memberships-by-hubloy' ),
						'KRW' => __( 'South Korean won', 'memberships-by-hubloy' ),
						'KWD' => __( 'Kuwaiti dinar', 'memberships-by-hubloy' ),
						'KYD' => __( 'Cayman Islands dollar', 'memberships-by-hubloy' ),
						'KZT' => __( 'Kazakhstani tenge', 'memberships-by-hubloy' ),
						'LAK' => __( 'Lao kip', 'memberships-by-hubloy' ),
						'LBP' => __( 'Lebanese pound', 'memberships-by-hubloy' ),
						'LKR' => __( 'Sri Lankan rupee', 'memberships-by-hubloy' ),
						'LRD' => __( 'Liberian dollar', 'memberships-by-hubloy' ),
						'LSL' => __( 'Lesotho loti', 'memberships-by-hubloy' ),
						'LYD' => __( 'Libyan dinar', 'memberships-by-hubloy' ),
						'MAD' => __( 'Moroccan dirham', 'memberships-by-hubloy' ),
						'MDL' => __( 'Moldovan leu', 'memberships-by-hubloy' ),
						'MGA' => __( 'Malagasy ariary', 'memberships-by-hubloy' ),
						'MKD' => __( 'Macedonian denar', 'memberships-by-hubloy' ),
						'MMK' => __( 'Burmese kyat', 'memberships-by-hubloy' ),
						'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'memberships-by-hubloy' ),
						'MOP' => __( 'Macanese pataca', 'memberships-by-hubloy' ),
						'MRU' => __( 'Mauritanian ouguiya', 'memberships-by-hubloy' ),
						'MUR' => __( 'Mauritian rupee', 'memberships-by-hubloy' ),
						'MVR' => __( 'Maldivian rufiyaa', 'memberships-by-hubloy' ),
						'MWK' => __( 'Malawian kwacha', 'memberships-by-hubloy' ),
						'MXN' => __( 'Mexican peso', 'memberships-by-hubloy' ),
						'MYR' => __( 'Malaysian ringgit', 'memberships-by-hubloy' ),
						'MZN' => __( 'Mozambican metical', 'memberships-by-hubloy' ),
						'NAD' => __( 'Namibian dollar', 'memberships-by-hubloy' ),
						'NGN' => __( 'Nigerian naira', 'memberships-by-hubloy' ),
						'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'memberships-by-hubloy' ),
						'NOK' => __( 'Norwegian krone', 'memberships-by-hubloy' ),
						'NPR' => __( 'Nepalese rupee', 'memberships-by-hubloy' ),
						'NZD' => __( 'New Zealand dollar', 'memberships-by-hubloy' ),
						'OMR' => __( 'Omani rial', 'memberships-by-hubloy' ),
						'PAB' => __( 'Panamanian balboa', 'memberships-by-hubloy' ),
						'PEN' => __( 'Sol', 'memberships-by-hubloy' ),
						'PGK' => __( 'Papua New Guinean kina', 'memberships-by-hubloy' ),
						'PHP' => __( 'Philippine peso', 'memberships-by-hubloy' ),
						'PKR' => __( 'Pakistani rupee', 'memberships-by-hubloy' ),
						'PLN' => __( 'Polish z&#x142;oty', 'memberships-by-hubloy' ),
						'PRB' => __( 'Transnistrian ruble', 'memberships-by-hubloy' ),
						'PYG' => __( 'Paraguayan guaran&iacute;', 'memberships-by-hubloy' ),
						'QAR' => __( 'Qatari riyal', 'memberships-by-hubloy' ),
						'RON' => __( 'Romanian leu', 'memberships-by-hubloy' ),
						'RSD' => __( 'Serbian dinar', 'memberships-by-hubloy' ),
						'RUB' => __( 'Russian ruble', 'memberships-by-hubloy' ),
						'RWF' => __( 'Rwandan franc', 'memberships-by-hubloy' ),
						'SAR' => __( 'Saudi riyal', 'memberships-by-hubloy' ),
						'SBD' => __( 'Solomon Islands dollar', 'memberships-by-hubloy' ),
						'SCR' => __( 'Seychellois rupee', 'memberships-by-hubloy' ),
						'SDG' => __( 'Sudanese pound', 'memberships-by-hubloy' ),
						'SEK' => __( 'Swedish krona', 'memberships-by-hubloy' ),
						'SGD' => __( 'Singapore dollar', 'memberships-by-hubloy' ),
						'SHP' => __( 'Saint Helena pound', 'memberships-by-hubloy' ),
						'SLL' => __( 'Sierra Leonean leone', 'memberships-by-hubloy' ),
						'SOS' => __( 'Somali shilling', 'memberships-by-hubloy' ),
						'SRD' => __( 'Surinamese dollar', 'memberships-by-hubloy' ),
						'SSP' => __( 'South Sudanese pound', 'memberships-by-hubloy' ),
						'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'memberships-by-hubloy' ),
						'SYP' => __( 'Syrian pound', 'memberships-by-hubloy' ),
						'SZL' => __( 'Swazi lilangeni', 'memberships-by-hubloy' ),
						'THB' => __( 'Thai baht', 'memberships-by-hubloy' ),
						'TJS' => __( 'Tajikistani somoni', 'memberships-by-hubloy' ),
						'TMT' => __( 'Turkmenistan manat', 'memberships-by-hubloy' ),
						'TND' => __( 'Tunisian dinar', 'memberships-by-hubloy' ),
						'TOP' => __( 'Tongan pa&#x2bb;anga', 'memberships-by-hubloy' ),
						'TRY' => __( 'Turkish lira', 'memberships-by-hubloy' ),
						'TTD' => __( 'Trinidad and Tobago dollar', 'memberships-by-hubloy' ),
						'TWD' => __( 'New Taiwan dollar', 'memberships-by-hubloy' ),
						'TZS' => __( 'Tanzanian shilling', 'memberships-by-hubloy' ),
						'UAH' => __( 'Ukrainian hryvnia', 'memberships-by-hubloy' ),
						'UGX' => __( 'Ugandan shilling', 'memberships-by-hubloy' ),
						'USD' => __( 'United States (US) dollar', 'memberships-by-hubloy' ),
						'UYU' => __( 'Uruguayan peso', 'memberships-by-hubloy' ),
						'UZS' => __( 'Uzbekistani som', 'memberships-by-hubloy' ),
						'VEF' => __( 'Venezuelan bol&iacute;var', 'memberships-by-hubloy' ),
						'VES' => __( 'Bol&iacute;var soberano', 'memberships-by-hubloy' ),
						'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'memberships-by-hubloy' ),
						'VUV' => __( 'Vanuatu vatu', 'memberships-by-hubloy' ),
						'WST' => __( 'Samoan t&#x101;l&#x101;', 'memberships-by-hubloy' ),
						'XAF' => __( 'Central African CFA franc', 'memberships-by-hubloy' ),
						'XCD' => __( 'East Caribbean dollar', 'memberships-by-hubloy' ),
						'XOF' => __( 'West African CFA franc', 'memberships-by-hubloy' ),
						'XPF' => __( 'CFP franc', 'memberships-by-hubloy' ),
						'YER' => __( 'Yemeni rial', 'memberships-by-hubloy' ),
						'ZAR' => __( 'South African rand', 'memberships-by-hubloy' ),
						'ZMW' => __( 'Zambian kwacha', 'memberships-by-hubloy' ),
					)
				)
			);
		}

		return $currencies;
	}


	public static function get_currency_symbol( $currency = 'USD' ) {
		$symbols         = apply_filters(
			'hubloy_membership_currency_symbols',
			array(
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'DKK',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x639;.&#x62f;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => 'KZT',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => '&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#x434;&#x438;&#x43d;.',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'L',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			)
		);
		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

		return apply_filters( 'hubloy_membership_currency_symbol', $currency_symbol, $currency );
	}

	/**
	 * Get Membership currency
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_membership_currency() {
		$settings = new \HubloyMembership\Model\Settings();
		$general  = $settings->get_general_settings();
		$currency = $general['currency'];
		$code     = self::get_currency_symbol( $currency );
		return apply_filters( 'hubloy_membership_get_membership_currency', $code, $currency );
	}


	/**
	 * Formats a number to display a valid price.
	 *
	 * @param  numeric $price
	 *
	 * @since  1.0.0
	 *
	 * @return numeric
	 */
	public static function format_price( $price ) {
		$formatted = number_format_i18n( $price, 2 );

		return apply_filters(
			'hubloy_membership_format_price',
			$formatted,
			$price
		);
	}


	/**
	 * Round to the nearest number.
	 * 
	 * @param mixed $val The value to round.
	 * @param int   $precision The optional number of decimal digits to round to.
	 * @param int   $mode A constant to specify the mode in which rounding occurs.
	 * 
	 * @since 1.1.0
	 *
	 * @return float The value rounded to the given precision as a float, or the supplied default value.
	 */
	public static function round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ) {
		if ( ! is_numeric( $val ) ) {
			$val = floatval( $val );
		}
		return round( $val, $precision, $mode );
	}
}

