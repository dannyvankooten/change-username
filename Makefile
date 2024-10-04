.PHONY: all
all: assets/js/script.min.js

assets/js/script.min.js:  assets/js/script.js
	terser $^ --compress --mangle -o $@
