.PHONY: build test

# build the Go port into bin/class-leak-go
build:
	cd go && go build -o ../bin/class-leak-go ./cmd/class-leak

test:
	cd go && go test ./...
