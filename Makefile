.PHONY: build test

# build the Go port; bin/class-leak-go is the committed launcher for it
build:
	cd blink && go build -o ../bin/.class-leak-go.bin ./cmd/class-leak

test:
	cd blink && go test ./...
