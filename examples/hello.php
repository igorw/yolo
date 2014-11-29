<script language=php>

require_once __DIR__.'/../vendor/autoload.php';

use function yolo\y;

yolo\yolisp(y('yolo\yolo',
    y('lambda', y('request'), 
        y('new', YoLo\resPONsE::clASS, y(
            y('quote', 'yolo')
        ))
    )
));

%>
