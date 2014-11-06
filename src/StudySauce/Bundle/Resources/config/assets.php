<?php


$container->loadFromExtension('assetic', [
    'assets' => [
        'funnel' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/selectize.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.plugin.js',
                '@StudySauceBundle/Resources/public/js/jquery.timeentry.js',
                '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/contact.js',
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'dashboard' => [
            'inputs' => [
                '@funnel',
                '@StudySauceBundle/Resources/public/js/jquery.jplayer.js',
                '@StudySauceBundle/Resources/public/js/plupload/js/plupload.full.min.js',
                //'@StudySauceBundle/Resources/public/js/plupload/js/moxie.js',
                //'@StudySauceBundle/Resources/public/js/plupload/js/plupload.dev.js',
                '@StudySauceBundle/Resources/public/js/dashboard.js',
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'landing' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/landing.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/contact.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'layout' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/jquery-2.1.1.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.textfill.min.js',
                //'@StudySauceBundle/Resources/public/js/jquery.fittext.js',
                '@StudySauceBundle/Resources/public/js/jquery-ui.min.js',
                '@StudySauceBundle/Resources/public/js/bootstrap.min.js',
                '@StudySauceBundle/Resources/public/js/underscore-min.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'metrics' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/d3.v3.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.tipsy.js',
                '@StudySauceBundle/Resources/public/js/metrics.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],
        'checkin' => [
            'inputs' => [
                '@StudySauceBundle/Resources/public/js/checkin.js',
                '@StudySauceBundle/Resources/public/js/jquery.fittext.js'
            ],
            'filters' => [],
            'options' => [
                'output' => 'bundles/studysauce/js/*.js',
            ],
        ],

    ],
]);




