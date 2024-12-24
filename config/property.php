<?php

return [
    'market' => [
        'segment' => [
            'open' => env('MARKET_SEGMENT_OPEN', 'middle-income'),
            'economic' => env('MARKET_SEGMENT_ECONOMIC', 'economic'),
            'socialized' => env('MARKET_SEGMENT_SOCIALIZED', 'socialized'),
        ],
        'ceiling' => [
            'horizontal' => [
                'open' => env('HORIZONTAL_OPEN_MARKET_CEILING', 10000000),
                'economic' => env('HORIZONTAL_ECONOMIC_MARKET_CEILING', 2500000),
                'socialized' => env('HORIZONTAL_SOCIALIZED_MARKET_CEILING', 850000),
            ],
            'vertical' => [
                'open' => env('VERTICAL_OPEN_MARKET_CEILING', 10000000),
                'economic' => env('VERTICAL_ECONOMIC_MARKET_CEILING', 2500000),
                'socialized' => env('VERTICAL_SOCIALIZED_MARKET_CEILING', 1800000),
            ],
            'bp_957' => [
                'open' => env('BP957_OPEN_MARKET_CEILING', 6000000),
                'economic' => env('BP957_ECONOMIC_MARKET_CEILING', 2500000),
                'socialized' => env('BP957_SOCIALIZED_MARKET_CEILING', 1800000),
            ],
            'bp_220' => [
                'open' => env('BP220_OPEN_MARKET_CEILING', 10000000),
                'economic' => env('BP220_ECONOMIC_MARKET_CEILING', 2500000),
                'socialized' => env('BP220_SOCIALIZED_MARKET_CEILING', 1800000),
            ],
        ],
        'disposable-income-requirement-multiplier' => [
            'open' => env('OPEN_MARKET_DISPOSABLE_MULTIPLIER', 0.30),
            'economic' => env('ECONOMIC_MARKET_DISPOSABLE_MULTIPLIER', 0.35),
            'socialized' => env('SOCIALIZED_MARKET_DISPOSABLE_MULTIPLIER', 0.35),
        ],
        'loanable-value-multiplier' => [
            'open' => env('OPEN_MARKET_LOANABLE_MULTIPLIER', 0.90),
            'economic' => env('ECONOMIC_MARKET_LOANABLE_MULTIPLIER', 0.95),
            'socialized' => env('SOCIALIZED_MARKET_LOANABLE_MULTIPLIER', 1.00),
        ],
    ],
    'default' => [
        'processing_fee' => env('PROCESSING_FEE', 10000),
        'percent_dp' => env('PERCENT_DP', 10/100),
        'dp_term' => env('DP_TERM', 12), //months
        'percent_mf' => env('PERCENT_MF', 8.5/100),
    ],
];
