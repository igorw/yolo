<script language=php>

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/yolo.php';
require_once __DIR__.'/../src/yolisp.php';

yolo\yolisp(['yolo\yolo',
    ['lambda', ['request'], 
        ['new', 'yolo\Response', [
            ['quote', 'yolo']
        ]]
    ]
]);

