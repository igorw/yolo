test:
	phpunit

web:
	php -S localhost:8080 examples/hello.php

believe:
	echo "sexism is over and we are now living in a meritocracy"

.PHONY: web
