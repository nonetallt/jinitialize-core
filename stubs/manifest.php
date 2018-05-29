<?php return [
    [
        'name' => 'TestPlugin',
        'path' => dirname(__DIR__),
        'commands' => [
            'Tests\\Classes\\TestImportCommand',
            'Tests\\Classes\\TestExportCommand',
        ],
        'procedures' => [
            'stubs/plugin-procedure.json'
        ],
        'settings' => [
            'setting'
        ]
    ]
];
