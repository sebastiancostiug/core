<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  Config
 *
 * @author      Sebastian Costiug, sebastian@overbyte.dev
 * @copyright   2019-2023 Sebastian Costiug <https://www.overbyte.dev>
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    config
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.10.14
 */

return [
    'default' => [
        'layout' => [
            'main' => [
                'css' => [
                    'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Righteous&display=swap',
                    '/css/global.css',
                    '/css/main.css',
                    ],
                'js' => [
                    '/js/main.js',
                ],
            ],
            'blank' => [
                'css' => [],
                'js' => [],
            ],
        ],
        'views_path' => views_path('default'),
    ]
];
