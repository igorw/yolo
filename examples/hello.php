<script language=php>

require_once __DIR__.'/../vendor/autoload.php';

use function yolo\y;

yolo\yolisp(y('yolo\yolo',
    y('lambda', y('request'), 
        y('new', 'yolo\Response', y(
            y('quote', 'yolo')
        ))
    )
));

%>
