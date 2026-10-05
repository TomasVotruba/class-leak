.PHONY: build test

# build the Go port; bin/class-leak-go is the committed launcher for it
build:
	cd go && go build -o ../bin/.class-leak-go.bin ./cmd/class-leak

test:
	cd go && go test ./...
