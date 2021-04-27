# mjml-api-sp
MJML API Service Provider

### Installation

```shell script
composer install
npm install
```

### Test

```shell script
curl -X POST -H "Content-Type: application/json" \
    -d '{"mjml":"<mjml><mj-body><mj-section><mj-column><mj-text>Hello World!</mj-text></mj-column></mj-section></mj-body></mjml>"}' \
    https://mjml-api-sp-url/v1/render
```
