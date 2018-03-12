<?php

// Constants for the application are defined here.
return [
// Paypal constants
    /* Sandbox Details 
    'clientId' => 'AcZieQWF-vBUnWZJAP4SIhLHNe3iJLVRTsL_aHHIaKkzYATCyDCGd7hAv858PMEL8jKFmv4wjxx1VYUL',
    'secret' => 'EDyiQiJZImFxOyRbSBZHkTFrmUqdrg22ri_xff4FZn8CxCDYyC47kLRETuSJU4UWnkJdETCIIlYkiGVX', */
    /* Paypal Live Details Client */
    'clientId' => 'AUsejlM1qyWzgZsKSILCBsR-KGonT7Z97-_iNY0jDmfr4geUZ9qUqFncBAHqUv8o3vksuppjYVNN5Dq1',
    'secret' => 'EGy3ICRVf1a2dSZi5IRgnPbvEEYey_BDHkKCBER7nj1llzoWf_yN7cZdbEMZ6rvuZ1aAcNnywlFsXGGL',

    /* Paypal Sandbox Details Client 
    'clientId' => 'Acre42ufDZqlwgnYFdybVMeowuhsQE5dnzGMt7bEVaQWqYVEdGW89aU9Rk2LEqe1mao6UsA2JyWqdT8f',
    'secret' => 'EGkDUTti1Tk6JFP7H65HpfrrEgo8gDBeg9eYW9gEPEQ_UQMqdKo78k_F5uA7rfUuV3MOi8rtaoQXaloT',*/

        
    /* Developer account credentials "manish@visions.net.in"
    'clientId' => 'AatXdTSunOYwfWP1kF-r_r3tkJ009YxluTqIOCD6KQkQ1lWmqMUxpm6oQTAD9OB3lcGL6mqTzxyTs09o',
    'secret' => 'EMMPF9KEpHobyfg1uw8Hf75aLakeCc7-QbuA1FbzG5Fdql8AAUNcqw1zpFhBeZlSgO3PMvi7GW7JZjil',*/
    
    'pgPaypal' => 'PAYPAL',
    'approved' => 'approved',
    'phase1Amt' => '9.95',
    'phase2Amt' => '49.95',
    /*'phase1Amt' => '0.05',
    'phase2Amt' => '0.05',*/
    'currency' => 'USD',
    'countryCode' => 'US',
    'creditCard' => 'credit_card',
	'currencySymbol' => '$',
    
    // other constants
    'constants' => [
        'lookup_type' => [
            'address' => 'Address_type',
            'additionl_homeowner_questions' => 'Additional_homeowner_questions',
        ],
        'lookup' => [
            'address' => [
                'search_address' => 'search_address',
                'billing_address' => 'billing_address',
				'phase2_billing_address' => 'phase2_billing_address',
				'default_address' => 'default_address',
            ]
        ]
    ],
	
	'smsSid' => 'ACdd751cd7a33591d77394ec7bb2171c4f',
	'smsToken' => 'cbe7e658073e1baaec7b8c498e6f29e0',
	'smsFromNumber' => '+13852444046',
	'adminUser' => '1',
	'member' => '0',

    'caseRangeFrom' => '250000',
    'caseRangeTo' => '500000',
    'getSixPercent' => '0.94',
    'getThreePercent' => '0.97',
    'getTwoPercent' => '0.98',
    'getTwentyPercent' => '0.80',
    'getTenPercent' => '0.90',
    'notification_cron_check'=>'20',

    'excludeLivingAreaAboveAmount' => '2000000',
    'recentSaleMonthsBefore', '18',
    'recentSaleMonthsAfter', '12',
    'minimumTaxSavings', '200',
    'livingAreaVarianceFilterPercent', '20',
    'tier1OutlierPercent', '1',
    'tier1Outlier2Percent', '20',
    'minCompsForAppeal', '3',
    'maxCompsForAppeal', '5',
    'in_out_default_message','We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.',

    'livingAreaVarianceFilterPercentForGreater' => '40',

	
];

