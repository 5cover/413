CC = gcc
LFLAGS = -ljson-c
CFLAGS = -pedantic -std=c17 -Wall -Wextra -Wcast-qual -Wcast-align -Wstrict-aliasing -Wpointer-arith -Winit-self -Wshadow -Wstrict-prototypes -Wmissing-prototypes -Wredundant-decls -Wfloat-equal -Wundef -Wvla

# CLFAGS += -fanalyzer # takes time
# CLFAGS += -fsanitize=address,leak,undefined # messes with debugging

.PHONY: debug release all test clean debug

debug: CONFIG=debug all
release: CONFIG=relase all

all: bin/tct

clean:
	rm -r bin

bin/tct: tct.c
	mkdir -p bin
	gcc -o $@ $(CFLAGS) $^ $(LFLAGS)