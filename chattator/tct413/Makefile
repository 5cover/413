rwildcard = $(foreach d,$(wildcard $(1:=/*)),$(call rwildcard,$d,$2) $(filter $(subst *,%,$2),$d))

SRC = $(call rwildcard,src,*.c *.h)
SRC_LIB = $(call rwildcard,lib,*.c *.h)
SRC_TEST = $(call rwildcard,test, *.c *.h)

CC = gcc
LFLAGS = -ljson-c -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
CFLAGS = -pedantic -std=c23 -isystem /work/lib -Wall -Wextra -Wcast-qual -Wcast-align -Wstrict-aliasing -Wpointer-arith -Winit-self -Wshadow -Wstrict-prototypes -Wmissing-prototypes -Wredundant-decls -Wfloat-equal -Wundef -Wvla -Wno-parentheses

# CLFAGS += -fanalyzer # takes time
# CLFAGS += -fsanitize=address,leak,undefined # messes with debugging

#todo: -lto

#i spent a lot of time doing that but it's bad practice
#let's not hardcode db configuration in the binary
#define NEWLINE
#
#
#endef # Wake up, this isn't a dream.
#ENV_FILE = raphael-test.env
#CFLAGS += -D$(subst $(NEWLINE), -D,$(file <$(ENV_FILE)))

CFLAGS_DEBUG = -g -Og
CFLAGS_RELEASE = -O2 -DNDEBUG

CONFIG ?= debug
ifeq ($(CONFIG), debug)
	CFLAGS += $(CFLAGS_DEBUG)
else
	CFLAGS += $(CFLAGS_RELEASE)
endif

.PHONY: debug release all test clean debug

debug: CONFIG=debug all
release: CONFIG=release all

all: bin/tct413

test: bin/test
	bin/test

clean:
	rm -r bin

bin/tct413: tct413.c $(SRC) $(SRC_LIB)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS)' $^

bin/test: test/test.c $(SRC) $(SRC_LIB) $(SRC_TEST)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS) -lm' $^