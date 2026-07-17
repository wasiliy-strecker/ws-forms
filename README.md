# WS Forms

[![PHP syntax](https://github.com/wasiliy-strecker/ws-forms/actions/workflows/php.yml/badge.svg)](https://github.com/wasiliy-strecker/ws-forms/actions/workflows/php.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

An experimental WordPress commerce plugin that explores how a controller/repository architecture can sit on top of WordPress without hiding the platform behind a large framework.

The project combines frontend shortcodes, an administrative workspace, versioned REST endpoints, custom database tables, product management, customer accounts, order flows, and sandbox payment integrations. Its class and resource layout is intentionally influenced by TYPO3 extension conventions.

> **Portfolio status:** This is an engineering playground, not a drop-in replacement for WooCommerce and not production-ready payment software. It is published to demonstrate architecture, WordPress integration, and end-to-end request flow.

## Engineering focus

- PSR-4-style namespace autoloading without Composer runtime dependencies.
- Controllers separated from repositories, domain models, templates, and browser code.
- Dedicated backend (`be`) and frontend (`fe`) REST contexts.
- WordPress capability checks for administrative routes and REST nonces for browser requests.
- Sanitized repository writes and escaped template output.
- Custom tables created through WordPress `dbDelta` for addresses, products, media, orders, and order items.
- Sandbox workflows for Stripe, PayPal, and AI-assisted product data extraction.

## Request flow

```text
WordPress hook / shortcode / admin menu
                    |
                    v
              Init + router
                    |
          +---------+---------+
          |                   |
   REST controller       PHP template
          |
   controller helper
          |
   domain repository
          |
    WordPress API / custom tables
```

## Modules

| Module | Responsibilities |
| --- | --- |
| User | Registration, login, profiles, search, and WordPress user metadata |
| Address | Customer address CRUD backed by a custom table |
| Product | Catalog CRUD, SKU checks, media records, and optional AI extraction |
| Order | Order persistence, line items, confirmation, and payment hand-off |
| Option | Administrator-managed roles and integration configuration |

## Public entry points

Shortcodes:

- `[ws_forms]` renders the user workflow.
- `[ws_login]` renders the login workflow.
- `[ws_products]` renders product discovery and checkout.
- `[ws_order]` renders an order result.

REST routes use the namespace `ws-forms/v1` and are grouped below `/fe/...` and `/be/...`. CRUD routes exist for users, addresses, products, and orders; specialized routes cover login, email/SKU checks, product analysis, payment intents, and order display.

## Local installation

1. Create a local WordPress installation.
2. Clone this repository into `wp-content/plugins/ws-forms`.
3. Activate **Wasiliy Strecker Forms (WS Forms)** in WordPress.
4. Add one of the shortcodes above to a page.
5. Configure optional sandbox integrations from the plugin's administrator page.

Use test credentials only. Never commit payment or AI provider secrets.

## Repository map

```text
Classes/
  Controller/          REST and page actions
  ControllerHelper/    Shared controller workflows
  Domain/Model/        Domain data objects
  Domain/Repository/   WordPress and SQL persistence
Resources/
  Private/             PHP templates and partials
  Public/              Frontend/backend JavaScript and CSS
languages/             Translation catalog
index.php              Plugin metadata, autoloading, and bootstrap
```

## Quality and limitations

CI parses every PHP file on PHP 8.1 and 8.3. A real WordPress integration-test harness is outside the scope of this experiment, so behavior that depends on WordPress, external payment sandboxes, or OpenAI still requires manual testing.

Before using any part in production, add comprehensive authorization tests, webhook-based payment confirmation, secret management, database migrations, and provider failure handling.

## License

[MIT](LICENSE)
