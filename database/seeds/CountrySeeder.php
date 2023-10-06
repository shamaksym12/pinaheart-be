<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Country;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->truncate();
        $countries = [
            [
              'id' => 1,
              'code' => 'AF',
              'name' => 'Afghanistan',
            ],
            [
              'id' => 2,
              'code' => 'AX',
              'name' => 'Aland Islands',
            ],
            [
              'id' => 3,
              'code' => 'AL',
              'name' => 'Albania',
            ],
            [
              'id' => 4,
              'code' => 'DZ',
              'name' => 'Algeria',
            ],
            [
              'id' => 5,
              'code' => 'AS',
              'name' => 'American Samoa',
            ],
            [
              'id' => 6,
              'code' => 'AD',
              'name' => 'Andorra',
            ],
            [
              'id' => 7,
              'code' => 'AO',
              'name' => 'Angola',
            ],
            [
              'id' => 8,
              'code' => 'AI',
              'name' => 'Anguilla',
            ],
            [
              'id' => 9,
              'code' => 'AQ',
              'name' => 'Antarctica',
            ],
            [
              'id' => 10,
              'code' => 'AG',
              'name' => 'Antigua and Barbuda',
            ],
            [
              'id' => 11,
              'code' => 'AR',
              'name' => 'Argentina',
            ],
            [
              'id' => 12,
              'code' => 'AM',
              'name' => 'Armenia',
            ],
            [
              'id' => 13,
              'code' => 'AW',
              'name' => 'Aruba',
            ],
            [
              'id' => 14,
              'code' => 'AU',
              'name' => 'Australia',
            ],
            [
              'id' => 15,
              'code' => 'AT',
              'name' => 'Austria',
            ],
            [
              'id' => 16,
              'code' => 'AZ',
              'name' => 'Azerbaijan',
            ],
            [
              'id' => 17,
              'code' => 'BS',
              'name' => 'Bahamas',
            ],
            [
              'id' => 18,
              'code' => 'BH',
              'name' => 'Bahrain',
            ],
            [
              'id' => 19,
              'code' => 'BD',
              'name' => 'Bangladesh',
            ],
            [
              'id' => 20,
              'code' => 'BB',
              'name' => 'Barbados',
            ],
            [
              'id' => 21,
              'code' => 'BY',
              'name' => 'Belarus',
            ],
            [
              'id' => 22,
              'code' => 'BE',
              'name' => 'Belgium',
            ],
            [
              'id' => 23,
              'code' => 'BZ',
              'name' => 'Belize',
            ],
            [
              'id' => 24,
              'code' => 'BJ',
              'name' => 'Benin',
            ],
            [
              'id' => 25,
              'code' => 'BM',
              'name' => 'Bermuda',
            ],
            [
              'id' => 26,
              'code' => 'BT',
              'name' => 'Bhutan',
            ],
            [
              'id' => 27,
              'code' => 'BO',
              'name' => 'Bolivia, Plurinational State of',
            ],
            [
              'id' => 28,
              'code' => 'BQ',
              'name' => 'Bonaire, Sint Eustatius and Saba',
            ],
            [
              'id' => 29,
              'code' => 'BA',
              'name' => 'Bosnia and Herzegovina',
            ],
            [
              'id' => 30,
              'code' => 'BW',
              'name' => 'Botswana',
            ],
            [
              'id' => 31,
              'code' => 'BR',
              'name' => 'Brazil',
            ],
            [
              'id' => 32,
              'code' => 'IO',
              'name' => 'British Indian Ocean Territory',
            ],
            [
              'id' => 33,
              'code' => 'BN',
              'name' => 'Brunei Darussalam',
            ],
            [
              'id' => 34,
              'code' => 'BG',
              'name' => 'Bulgaria',
            ],
            [
              'id' => 35,
              'code' => 'BF',
              'name' => 'Burkina Faso',
            ],
            [
              'id' => 36,
              'code' => 'BI',
              'name' => 'Burundi',
            ],
            [
              'id' => 37,
              'code' => 'CV',
              'name' => 'Cabo Verde',
            ],
            [
              'id' => 38,
              'code' => 'KH',
              'name' => 'Cambodia',
            ],
            [
              'id' => 39,
              'code' => 'CM',
              'name' => 'Cameroon',
            ],
            [
              'id' => 40,
              'code' => 'CA',
              'name' => 'Canada',
            ],
            [
              'id' => 41,
              'code' => 'KY',
              'name' => 'Cayman Islands',
            ],
            [
              'id' => 42,
              'code' => 'CF',
              'name' => 'Central African Republic',
            ],
            [
              'id' => 43,
              'code' => 'TD',
              'name' => 'Chad',
            ],
            [
              'id' => 44,
              'code' => 'CL',
              'name' => 'Chile',
            ],
            [
              'id' => 45,
              'code' => 'CN',
              'name' => 'China',
            ],
            [
              'id' => 46,
              'code' => 'CO',
              'name' => 'Colombia',
            ],
            [
              'id' => 47,
              'code' => 'KM',
              'name' => 'Comoros',
            ],
            [
              'id' => 48,
              'code' => 'CG',
              'name' => 'Congo',
            ],
            [
              'id' => 49,
              'code' => 'CD',
              'name' => 'Congo, The Democratic Republic of The',
            ],
            [
              'id' => 50,
              'code' => 'CK',
              'name' => 'Cook Islands',
            ],
            [
              'id' => 51,
              'code' => 'CR',
              'name' => 'Costa Rica',
            ],
            [
              'id' => 52,
              'code' => 'CI',
              'name' => 'Cote D\'ivoire',
            ],
            [
              'id' => 53,
              'code' => 'HR',
              'name' => 'Croatia',
            ],
            [
              'id' => 54,
              'code' => 'CU',
              'name' => 'Cuba',
            ],
            [
              'id' => 55,
              'code' => 'CW',
              'name' => 'Curacao',
            ],
            [
              'id' => 56,
              'code' => 'CY',
              'name' => 'Cyprus',
            ],
            [
              'id' => 57,
              'code' => 'CZ',
              'name' => 'Czech Republic',
            ],
            [
              'id' => 58,
              'code' => 'DK',
              'name' => 'Denmark',
            ],
            [
              'id' => 59,
              'code' => 'DJ',
              'name' => 'Djibouti',
            ],
            [
              'id' => 60,
              'code' => 'DM',
              'name' => 'Dominica',
            ],
            [
              'id' => 61,
              'code' => 'DO',
              'name' => 'Dominican Republic',
            ],
            [
              'id' => 62,
              'code' => 'EC',
              'name' => 'Ecuador',
            ],
            [
              'id' => 63,
              'code' => 'EG',
              'name' => 'Egypt',
            ],
            [
              'id' => 64,
              'code' => 'SV',
              'name' => 'El Salvador',
            ],
            [
              'id' => 65,
              'code' => 'GQ',
              'name' => 'Equatorial Guinea',
            ],
            [
              'id' => 66,
              'code' => 'ER',
              'name' => 'Eritrea',
            ],
            [
              'id' => 67,
              'code' => 'EE',
              'name' => 'Estonia',
            ],
            [
              'id' => 68,
              'code' => 'SZ',
              'name' => 'Eswatini',
            ],
            [
              'id' => 69,
              'code' => 'ET',
              'name' => 'Ethiopia',
            ],
            [
              'id' => 70,
              'code' => 'FK',
              'name' => 'Falkland Islands (Malvinas)',
            ],
            [
              'id' => 71,
              'code' => 'FO',
              'name' => 'Faroe Islands',
            ],
            [
              'id' => 72,
              'code' => 'FJ',
              'name' => 'Fiji',
            ],
            [
              'id' => 73,
              'code' => 'FI',
              'name' => 'Finland',
            ],
            [
              'id' => 74,
              'code' => 'FR',
              'name' => 'France',
            ],
            [
              'id' => 75,
              'code' => 'GF',
              'name' => 'French Guiana',
            ],
            [
              'id' => 76,
              'code' => 'PF',
              'name' => 'French Polynesia',
            ],
            [
              'id' => 77,
              'code' => 'GA',
              'name' => 'Gabon',
            ],
            [
              'id' => 78,
              'code' => 'GM',
              'name' => 'Gambia',
            ],
            [
              'id' => 79,
              'code' => 'GE',
              'name' => 'Georgia',
            ],
            [
              'id' => 80,
              'code' => 'DE',
              'name' => 'Germany',
            ],
            [
              'id' => 81,
              'code' => 'GH',
              'name' => 'Ghana',
            ],
            [
              'id' => 82,
              'code' => 'GI',
              'name' => 'Gibraltar',
            ],
            [
              'id' => 83,
              'code' => 'GR',
              'name' => 'Greece',
            ],
            [
              'id' => 84,
              'code' => 'GL',
              'name' => 'Greenland',
            ],
            [
              'id' => 85,
              'code' => 'GD',
              'name' => 'Grenada',
            ],
            [
              'id' => 86,
              'code' => 'GP',
              'name' => 'Guadeloupe',
            ],
            [
              'id' => 87,
              'code' => 'GU',
              'name' => 'Guam',
            ],
            [
              'id' => 88,
              'code' => 'GT',
              'name' => 'Guatemala',
            ],
            [
              'id' => 89,
              'code' => 'GG',
              'name' => 'Guernsey',
            ],
            [
              'id' => 90,
              'code' => 'GN',
              'name' => 'Guinea',
            ],
            [
              'id' => 91,
              'code' => 'GW',
              'name' => 'Guinea-Bissau',
            ],
            [
              'id' => 92,
              'code' => 'GY',
              'name' => 'Guyana',
            ],
            [
              'id' => 93,
              'code' => 'HT',
              'name' => 'Haiti',
            ],
            [
              'id' => 94,
              'code' => 'VA',
              'name' => 'Holy See',
            ],
            [
              'id' => 95,
              'code' => 'HN',
              'name' => 'Honduras',
            ],
            [
              'id' => 96,
              'code' => 'HK',
              'name' => 'Hong Kong',
            ],
            [
              'id' => 97,
              'code' => 'HU',
              'name' => 'Hungary',
            ],
            [
              'id' => 98,
              'code' => 'IS',
              'name' => 'Iceland',
            ],
            [
              'id' => 99,
              'code' => 'IN',
              'name' => 'India',
            ],
            [
              'id' => 100,
              'code' => 'ID',
              'name' => 'Indonesia',
            ],
            [
              'id' => 101,
              'code' => 'IR',
              'name' => 'Iran, Islamic Republic of',
            ],
            [
              'id' => 102,
              'code' => 'IQ',
              'name' => 'Iraq',
            ],
            [
              'id' => 103,
              'code' => 'IE',
              'name' => 'Ireland',
            ],
            [
              'id' => 104,
              'code' => 'IM',
              'name' => 'Isle of Man',
            ],
            [
              'id' => 105,
              'code' => 'IL',
              'name' => 'Israel',
            ],
            [
              'id' => 106,
              'code' => 'IT',
              'name' => 'Italy',
            ],
            [
              'id' => 107,
              'code' => 'JM',
              'name' => 'Jamaica',
            ],
            [
              'id' => 108,
              'code' => 'JP',
              'name' => 'Japan',
            ],
            [
              'id' => 109,
              'code' => 'JE',
              'name' => 'Jersey',
            ],
            [
              'id' => 110,
              'code' => 'JO',
              'name' => 'Jordan',
            ],
            [
              'id' => 111,
              'code' => 'KZ',
              'name' => 'Kazakhstan',
            ],
            [
              'id' => 112,
              'code' => 'KE',
              'name' => 'Kenya',
            ],
            [
              'id' => 113,
              'code' => 'KI',
              'name' => 'Kiribati',
            ],
            [
              'id' => 114,
              'code' => 'KP',
              'name' => 'Korea, Democratic People\'s Republic of',
            ],
            [
              'id' => 115,
              'code' => 'KR',
              'name' => 'Korea, Republic of',
            ],
            [
              'id' => 116,
              'code' => 'KW',
              'name' => 'Kuwait',
            ],
            [
              'id' => 117,
              'code' => 'KG',
              'name' => 'Kyrgyzstan',
            ],
            [
              'id' => 118,
              'code' => 'LA',
              'name' => 'Lao People\'s Democratic Republic',
            ],
            [
              'id' => 119,
              'code' => 'LV',
              'name' => 'Latvia',
            ],
            [
              'id' => 120,
              'code' => 'LB',
              'name' => 'Lebanon',
            ],
            [
              'id' => 121,
              'code' => 'LS',
              'name' => 'Lesotho',
            ],
            [
              'id' => 122,
              'code' => 'LR',
              'name' => 'Liberia',
            ],
            [
              'id' => 123,
              'code' => 'LY',
              'name' => 'Libya',
            ],
            [
              'id' => 124,
              'code' => 'LI',
              'name' => 'Liechtenstein',
            ],
            [
              'id' => 125,
              'code' => 'LT',
              'name' => 'Lithuania',
            ],
            [
              'id' => 126,
              'code' => 'LU',
              'name' => 'Luxembourg',
            ],
            [
              'id' => 127,
              'code' => 'MO',
              'name' => 'Macao',
            ],
            [
              'id' => 128,
              'code' => 'MG',
              'name' => 'Madagascar',
            ],
            [
              'id' => 129,
              'code' => 'MW',
              'name' => 'Malawi',
            ],
            [
              'id' => 130,
              'code' => 'MY',
              'name' => 'Malaysia',
            ],
            [
              'id' => 131,
              'code' => 'MV',
              'name' => 'Maldives',
            ],
            [
              'id' => 132,
              'code' => 'ML',
              'name' => 'Mali',
            ],
            [
              'id' => 133,
              'code' => 'MT',
              'name' => 'Malta',
            ],
            [
              'id' => 134,
              'code' => 'MH',
              'name' => 'Marshall Islands',
            ],
            [
              'id' => 135,
              'code' => 'MQ',
              'name' => 'Martinique',
            ],
            [
              'id' => 136,
              'code' => 'MR',
              'name' => 'Mauritania',
            ],
            [
              'id' => 137,
              'code' => 'MU',
              'name' => 'Mauritius',
            ],
            [
              'id' => 138,
              'code' => 'YT',
              'name' => 'Mayotte',
            ],
            [
              'id' => 139,
              'code' => 'MX',
              'name' => 'Mexico',
            ],
            [
              'id' => 140,
              'code' => 'FM',
              'name' => 'Micronesia, Federated States of',
            ],
            [
              'id' => 141,
              'code' => 'MD',
              'name' => 'Moldova, Republic of',
            ],
            [
              'id' => 142,
              'code' => 'MC',
              'name' => 'Monaco',
            ],
            [
              'id' => 143,
              'code' => 'MN',
              'name' => 'Mongolia',
            ],
            [
              'id' => 144,
              'code' => 'ME',
              'name' => 'Montenegro',
            ],
            [
              'id' => 145,
              'code' => 'MS',
              'name' => 'Montserrat',
            ],
            [
              'id' => 146,
              'code' => 'MA',
              'name' => 'Morocco',
            ],
            [
              'id' => 147,
              'code' => 'MZ',
              'name' => 'Mozambique',
            ],
            [
              'id' => 148,
              'code' => 'MM',
              'name' => 'Myanmar',
            ],
            [
              'id' => 149,
              'code' => 'NA',
              'name' => 'Namibia',
            ],
            [
              'id' => 150,
              'code' => 'NR',
              'name' => 'Nauru',
            ],
            [
              'id' => 151,
              'code' => 'NP',
              'name' => 'Nepal',
            ],
            [
              'id' => 152,
              'code' => 'NL',
              'name' => 'Netherlands',
            ],
            [
              'id' => 153,
              'code' => 'NC',
              'name' => 'New Caledonia',
            ],
            [
              'id' => 154,
              'code' => 'NZ',
              'name' => 'New Zealand',
            ],
            [
              'id' => 155,
              'code' => 'NI',
              'name' => 'Nicaragua',
            ],
            [
              'id' => 156,
              'code' => 'NE',
              'name' => 'Niger',
            ],
            [
              'id' => 157,
              'code' => 'NG',
              'name' => 'Nigeria',
            ],
            [
              'id' => 158,
              'code' => 'NF',
              'name' => 'Norfolk Island',
            ],
            [
              'id' => 159,
              'code' => 'MK',
              'name' => 'North Macedonia',
            ],
            [
              'id' => 160,
              'code' => 'MP',
              'name' => 'Northern Mariana Islands',
            ],
            [
              'id' => 161,
              'code' => 'NO',
              'name' => 'Norway',
            ],
            [
              'id' => 162,
              'code' => 'OM',
              'name' => 'Oman',
            ],
            [
              'id' => 163,
              'code' => 'PK',
              'name' => 'Pakistan',
            ],
            [
              'id' => 164,
              'code' => 'PW',
              'name' => 'Palau',
            ],
            [
              'id' => 165,
              'code' => 'PS',
              'name' => 'Palestine, State of',
            ],
            [
              'id' => 166,
              'code' => 'PA',
              'name' => 'Panama',
            ],
            [
              'id' => 167,
              'code' => 'PG',
              'name' => 'Papua New Guinea',
            ],
            [
              'id' => 168,
              'code' => 'PY',
              'name' => 'Paraguay',
            ],
            [
              'id' => 169,
              'code' => 'PE',
              'name' => 'Peru',
            ],
            [
              'id' => 170,
              'code' => 'PH',
              'name' => 'Philippines',
            ],
            [
              'id' => 171,
              'code' => 'PL',
              'name' => 'Poland',
            ],
            [
              'id' => 172,
              'code' => 'PT',
              'name' => 'Portugal',
            ],
            [
              'id' => 173,
              'code' => 'PR',
              'name' => 'Puerto Rico',
            ],
            [
              'id' => 174,
              'code' => 'QA',
              'name' => 'Qatar',
            ],
            [
              'id' => 175,
              'code' => 'RE',
              'name' => 'Reunion',
            ],
            [
              'id' => 176,
              'code' => 'RO',
              'name' => 'Romania',
            ],
            [
              'id' => 177,
              'code' => 'RU',
              'name' => 'Russian Federation',
            ],
            [
              'id' => 178,
              'code' => 'RW',
              'name' => 'Rwanda',
            ],
            [
              'id' => 179,
              'code' => 'BL',
              'name' => 'Saint Barthelemy',
            ],
            [
              'id' => 180,
              'code' => 'KN',
              'name' => 'Saint Kitts and Nevis',
            ],
            [
              'id' => 181,
              'code' => 'LC',
              'name' => 'Saint Lucia',
            ],
            [
              'id' => 182,
              'code' => 'MF',
              'name' => 'Saint Martin (French Part)',
            ],
            [
              'id' => 183,
              'code' => 'PM',
              'name' => 'Saint Pierre and Miquelon',
            ],
            [
              'id' => 184,
              'code' => 'VC',
              'name' => 'Saint Vincent and The Grenadines',
            ],
            [
              'id' => 185,
              'code' => 'WS',
              'name' => 'Samoa',
            ],
            [
              'id' => 186,
              'code' => 'SM',
              'name' => 'San Marino',
            ],
            [
              'id' => 187,
              'code' => 'ST',
              'name' => 'Sao Tome and Principe',
            ],
            [
              'id' => 188,
              'code' => 'SA',
              'name' => 'Saudi Arabia',
            ],
            [
              'id' => 189,
              'code' => 'SN',
              'name' => 'Senegal',
            ],
            [
              'id' => 190,
              'code' => 'RS',
              'name' => 'Serbia',
            ],
            [
              'id' => 191,
              'code' => 'SC',
              'name' => 'Seychelles',
            ],
            [
              'id' => 192,
              'code' => 'SL',
              'name' => 'Sierra Leone',
            ],
            [
              'id' => 193,
              'code' => 'SG',
              'name' => 'Singapore',
            ],
            [
              'id' => 194,
              'code' => 'SX',
              'name' => 'Sint Maarten (Dutch Part)',
            ],
            [
              'id' => 195,
              'code' => 'SK',
              'name' => 'Slovakia',
            ],
            [
              'id' => 196,
              'code' => 'SI',
              'name' => 'Slovenia',
            ],
            [
              'id' => 197,
              'code' => 'SB',
              'name' => 'Solomon Islands',
            ],
            [
              'id' => 198,
              'code' => 'SO',
              'name' => 'Somalia',
            ],
            [
              'id' => 199,
              'code' => 'ZA',
              'name' => 'South Africa',
            ],
            [
              'id' => 200,
              'code' => 'SS',
              'name' => 'South Sudan',
            ],
            [
              'id' => 201,
              'code' => 'ES',
              'name' => 'Spain',
            ],
            [
              'id' => 202,
              'code' => 'LK',
              'name' => 'Sri Lanka',
            ],
            [
              'id' => 203,
              'code' => 'SD',
              'name' => 'Sudan',
            ],
            [
              'id' => 204,
              'code' => 'SR',
              'name' => 'Suriname',
            ],
            [
              'id' => 205,
              'code' => 'SE',
              'name' => 'Sweden',
            ],
            [
              'id' => 206,
              'code' => 'CH',
              'name' => 'Switzerland',
            ],
            [
              'id' => 207,
              'code' => 'SY',
              'name' => 'Syrian Arab Republic',
            ],
            [
              'id' => 208,
              'code' => 'TW',
              'name' => 'Taiwan, Province of China',
            ],
            [
              'id' => 209,
              'code' => 'TJ',
              'name' => 'Tajikistan',
            ],
            [
              'id' => 210,
              'code' => 'TZ',
              'name' => 'Tanzania, United Republic of',
            ],
            [
              'id' => 211,
              'code' => 'TH',
              'name' => 'Thailand',
            ],
            [
              'id' => 212,
              'code' => 'TL',
              'name' => 'Timor-Leste',
            ],
            [
              'id' => 213,
              'code' => 'TG',
              'name' => 'Togo',
            ],
            [
              'id' => 214,
              'code' => 'TK',
              'name' => 'Tokelau',
            ],
            [
              'id' => 215,
              'code' => 'TO',
              'name' => 'Tonga',
            ],
            [
              'id' => 216,
              'code' => 'TT',
              'name' => 'Trinidad and Tobago',
            ],
            [
              'id' => 217,
              'code' => 'TN',
              'name' => 'Tunisia',
            ],
            [
              'id' => 218,
              'code' => 'TR',
              'name' => 'Turkey',
            ],
            [
              'id' => 219,
              'code' => 'TM',
              'name' => 'Turkmenistan',
            ],
            [
              'id' => 220,
              'code' => 'TC',
              'name' => 'Turks and Caicos Islands',
            ],
            [
              'id' => 221,
              'code' => 'TV',
              'name' => 'Tuvalu',
            ],
            [
              'id' => 222,
              'code' => 'UG',
              'name' => 'Uganda',
            ],
            [
              'id' => 223,
              'code' => 'UA',
              'name' => 'Ukraine',
            ],
            [
              'id' => 224,
              'code' => 'AE',
              'name' => 'United Arab Emirates',
            ],
            [
              'id' => 225,
              'code' => 'GB',
              'name' => 'United Kingdom',
            ],
            [
              'id' => 226,
              'code' => 'US',
              'name' => 'United States',
            ],
            [
              'id' => 227,
              'code' => 'UM',
              'name' => 'United States Minor Outlying Islands',
            ],
            [
              'id' => 228,
              'code' => 'UY',
              'name' => 'Uruguay',
            ],
            [
              'id' => 229,
              'code' => 'UZ',
              'name' => 'Uzbekistan',
            ],
            [
              'id' => 230,
              'code' => 'VU',
              'name' => 'Vanuatu',
            ],
            [
              'id' => 231,
              'code' => 'VE',
              'name' => 'Venezuela, Bolivarian Republic of',
            ],
            [
              'id' => 232,
              'code' => 'VN',
              'name' => 'Viet Nam',
            ],
            [
              'id' => 233,
              'code' => 'VG',
              'name' => 'Virgin Islands, British',
            ],
            [
              'id' => 234,
              'code' => 'VI',
              'name' => 'Virgin Islands, U.S.',
            ],
            [
              'id' => 235,
              'code' => 'WF',
              'name' => 'Wallis and Futuna',
            ],
            [
              'id' => 236,
              'code' => 'YE',
              'name' => 'Yemen',
            ],
            [
              'id' => 237,
              'code' => 'ZM',
              'name' => 'Zambia',
            ],
            [
              'id' => 238,
              'code' => 'ZW',
              'name' => 'Zimbabwe',
            ],
        ];
        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
