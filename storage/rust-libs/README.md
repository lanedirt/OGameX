This folder contains the compiled Rust modules that are called from the PHP code.

Rust libraries are compiled automatically during the Docker container startup, so no Rust compiled code is committed to the repository. If you want to compile the Rust code manually during development and testing, see the [Rust README](../../rust/README.md).