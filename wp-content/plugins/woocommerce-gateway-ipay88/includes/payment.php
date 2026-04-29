<?php

/**
 * Sample array
 * '2'=> [
 *    'name'      => 'Credit Card',
 *    'id'        => '2',
 *    'image'     => 'credit-card.png',
 *    'category'  => 'credit-card',
 *  ],
 *
 * Details
 * 'ID' => [
 *    'name'      => 'PAYMENT_NAME',
 *    'id'        => 'ID',
 *    'image'     => 'XX_IMAGE_NAME__XX.png',
 *    'category'  => 'CATEGORY AS BELOW',
 * ]
 *
 * Category
 * 1. credit-card
 * 2. bank
 * 3. other
 *
 * Signature
 * 1. sha1
 * 2. sha256 ( Default )
 *
 * Upload image to path ./assets/images/
 */

class Payments
{
    const payments = [
		'2'=> [
            'name'      => 'Credit Card',
            'id'        => '2',
            'image'     => 'credit-card.png',
            'category'  => 'credit-card',
        ],
		'112'=> [
            'name'      => 'Credit Card (MBB EzyPay)',
            'id'        => '112',
            'image'     => 'mbbezypay.png',
            'category'  => 'credit-card',
        ],
		'179'=> [
            'name'      => 'Credit Card (HLB Instalment)',
            'id'        => '179',
            'image'     => 'hlbinstalment.png',
            'category'  => 'credit-card',
        ],
		'534'=> [
            'name'      => 'Credit Card (RHB Instalment)',
            'id'        => '534',
            'image'     => 'rhbinstalment.png',
            'category'  => 'credit-card',
        ],
		'115'=> [
            'name'      => 'Credit Card (AMEX EzyPay)',
            'id'        => '115',
            'image'     => 'amexezypay.png',
            'category'  => 'credit-card',
        ],
		'174'=> [
            'name'      => 'Credit Card (CIMB Instalment)',
            'id'        => '174',
            'image'     => 'cimbinstalment.png',
            'category'  => 'credit-card',
        ],
		'111'=> [
            'name'      => 'Credit Card (PBB ZIIP)',
            'id'        => '111',
            'image'     => 'pbbziip.png',
            'category'  => 'credit-card',
        ],
	    '538'=> [
            'name'      => 'TNGWalletOnline',
            'id'        => '538',
            'image'     => 'tng.png',
            'category'  => 'other',
        ],
		 '182'=> [
            'name'      => 'UnionPay_MYR',
            'id'        => '182',
            'image'     => 'unionpay.png',
            'category'  => 'credit-card',
        ],
        '6'=> [
            'name'      => 'Maybank2u',
            'id'        => '6',
            'image'     => 'maybank2u.png',
            'category'  => 'bank',
        ],
        '8'=> [
            'name'    => 'Alliance Bank (Personal)',
            'id'      => '8',
            'image'   => 'allianceonline_v1.png',
            'category'=> 'bank'
        ],
        '10'=> [
            'name'    => 'Ambank',
            'id'      => '10',
            'image'   => 'ambank.png',
            'category'=> 'bank'
        ],
        '14'=> [
            'name'    => 'RHB',
            'id'      => '14',
            'image'   => 'rhb.png',
            'category'=> 'bank'
        ],
        '15'=> [
            'name'    => 'Hong Leong Online',
            'id'      => '15',
            'image'   => 'hongleong.png',
            'category'=> 'bank'
        ],
        '16'=> [
            'name'    => 'FPX (Online Banking)',
            'id'      => '16',
            'image'   => 'fpx.png',
            'category'=> 'bank'
        ],
        '20'=> [
            'name'    => 'CIMB Clicks',
            'id'      => '20',
            'image'   => 'cimb.png',
            'category'=> 'bank'
        ],
        '31'=> [
            'name'    => 'Public Bank Online',
            'id'      => '31',
            'image'   => 'publicbank.png',
            'category'=> 'bank'
        ],
        '48'=> [
            'name'    => 'PayPal',
            'id'      => '48',
            'image'   => 'paypal.png',
            'category'=> 'other'
        ],
		'382'=> [
            'name'    => 'NETS Pay QR',
            'id'      => '382',
            'image'   => 'enets.png',
            'category'=> 'other'
        ],
        '102'=> [
            'name'    => 'Bank Rakyat',
            'id'      => '102',
            'image'   => 'bankrakyat.png',
            'category'=> 'bank'
        ],
        '103'=> [
            'name'    => 'AffinBank',
            'id'      => '103',
            'image'   => 'affinbank.png',
            'category'=> 'bank'
        ],
        '124'=> [
            'name'    => 'BSN Online',
            'id'      => '124',
            'image'   => 'bsn.png',
            'category'=> 'bank'
        ],
        '134'=> [
            'name'    => 'BankIslam Online',
            'id'      => '134',
            'image'   => 'bankislam.png',
            'category'=> 'bank'
        ],
        '152'=> [
            'name'    => 'UOB Bank Online',
            'id'      => '152',
            'image'   => 'uobbankmy.png',
            'category'=> 'bank'
        ],
        '166'=> [
            'name'    => 'Bank Muamalat',
            'id'      => '166',
            'image'   => 'bankmuamalat.png',
            'category'=> 'bank'
        ],
        '167'=> [
            'name'    => 'OCBC Online',
            'id'      => '167',
            'image'   => 'ocbcbank.png',
            'category'=> 'bank'
        ],
        '168'=> [
            'name'    => 'Standard Chartered Online',
            'id'      => '168',
            'image'   => 'standardcharteredbank.png',
            'category'=> 'bank'
        ],
        '198'=> [
            'name'    => 'HSBC Online',
            'id'      => '198',
            'image'   => 'hsbc.png',
            'category'=> 'bank'
        ],
        '199'=> [
            'name'    => 'KFH Online',
            'id'      => '199',
            'image'   => 'kfhbank.png',
            'category'=> 'bank'
        ],
	    '405'=> [
            'name'    => 'Agro Bank',
            'id'      => '405',
            'image'   => 'agro.png',
            'category'=> 'bank'
        ],
        '210'=> [
            'name'    => 'Boost Wallet',
            'id'      => '210',
            'image'   => 'boostlogo.png',
            'category'=> 'other'
        ],
		'122'=> [
            'id'       => '122',
            'name'     => 'Pay4Me (Delay Payment)',
            'image'    => 'pay4me.png',
            'category' => 'other'
        ],
		'801'=> [
            'name'      => 'ShopeePay',
            'id'        => '801',
            'image'     => 'shopeepay.png',
            'category'  => 'other',
        ],
		'542'=> [
            'name'      => 'MAE Maybank',
            'id'        => '542',
            'image'     => 'MAE.png',
            'category'  => 'other',
        ],
        '523'=> [
            'name'      => 'GrabPay',
            'id'        => '523',
            'image'     => 'grabpay.png',
            'category'  => 'other',
        ],
		'244'=> [
            'name'      => 'MCash',
            'id'        => '244',
            'image'     => 'Mcash.png',
            'category'  => 'other',
        ],
		'890'=> [
            'name'      => 'MobyPay',
            'id'        => '890',
            'image'     => 'mobypay-logo.png',
            'category'  => 'other',
        ],
		'891'=> [
            'name'      => 'Atome',
            'id'        => '891',
            'image'     => 'atome.png',
            'category'  => 'other',
        ],
		'912'=> [
            'name'      => 'SetelPay',
            'id'        => '912',
            'image'     => 'setel.png',
            'category'  => 'other',
        ],
		'391'=> [
            'name'      => 'Big Loyalty',
            'id'        => '391',
            'image'     => 'bigloyalty.png',
            'category'  => 'other',
        ]
		];

    const url         ='https://payment.ipay88.com.my/epayment/entry.asp';
    const requery_url = 'https://payment.ipay88.com.my/epayment/webservice/TxInquiryCardDetails/TxDetailsInquiry.asmx/TxDetailsInquiryCardInfo';
    const signature   = 'sha256';
}
