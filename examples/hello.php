<script language=php>

require_once __DIR__.'/../vendor/autoload.php';

yolo\yolisp(['yolo\yolo',
    ['lambda', ['request'], 
        ['new', 'yolo\Response', [
            ['quote', 'yolo']
        ]]
    ]
]);

%>
