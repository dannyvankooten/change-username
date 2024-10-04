.PHONY: all
all: assets/js/script.min.js

assets/js/script.min.js:  assets/js/script.js
	npx terser $^ --compress --mangle -o $@
