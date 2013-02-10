test:
	phpunit

web:
	php -S localhost:8080 examples/hello.php

.PHONY: web
