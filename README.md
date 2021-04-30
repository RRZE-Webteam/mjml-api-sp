# MJML API SP

MJML API Service Provider

## Beschreibung

Die MJML-API bietet responsive E-Mails als Service.

### Authentifizierung

Keine Authentifizierung erforderlich.

### PATH

POST /render

### Request Body

Das MJML-Markup zum Konvertieren in responsives HTML (JSON).

```shell script
{
  "mjml": "string"
}
```

### Responses

**200 OK**

Gibt ein JSON-Objekt zurück, das den gerenderten HTML-Code zusammen mit dem verwendeten MJML-Markup enthält.

```shell script
{
  "error": "string",
  "html": "string",
  "mjml": "string"
}
```

**400 Bad Request**

Das Senden von ungültigem JSON oder falschen Parametern gibt eine 400 Bad Request-Antwort zurück.

**500 Internal Server Error**

Wenn ein unbekannter Fehler auftritt, gibt die API eine Antwort auf 500 interne Fehler zurück.

## Installation

```shell script
composer install
npm install
```

## Einstellungsdatei

```shell script
./config.php
```

## Test

```shell script
curl -X POST -H "Content-Type: application/json" \
    -d '{"mjml":"<mjml><mj-body><mj-section><mj-column><mj-text>Hello World!</mj-text></mj-column></mj-section></mj-body></mjml>"}' \
    https://mjml-api-sp-url/v1/render
```
