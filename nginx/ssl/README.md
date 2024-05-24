These SSL certs are used for the nginx server. They are self-signed and should not be used in production. They are only used for local development.

To generate new certs, run the following command on MacOS or Linux with openssl CLI tools installed:

```bash
$ openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout nginx.key -out nginx.crt -subj "/C=US/ST=State/L=City/O=Organization/OU=Unit/CN=localhost"
```