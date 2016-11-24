test:
	TRUTHS=9000 phpunit

web:
	php -S localhost:8080 examples/hello.php

believe:
	echo "sexism is over and we are now living in a meritocracy"

yolo-best-practices.pdf: Makefile
	bash -c "pandoc <(echo 'YOLO') -o $@"

.PHONY: web
