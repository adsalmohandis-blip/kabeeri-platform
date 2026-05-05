# KABEERI V2 Implementation Notes

Generated for Prompt 02 on 2026-05-05.

## Scope Summary

KABEERI V2 expands the stable V1 Laravel and Filament foundation without changing the tenant model. Organizations remain the tenant root, sites remain user-facing apps stored in `sites`, and users must not receive direct `organization_id`, `company_id`, `site_id`, or `role` columns.

Planned V2 scope:

- CMS expansion: menus, redirects, basic SEO metadata, sitemap, and robots support.
- Forms and Leads foundation: forms, fields, submissions, optional lead capture, and simple contact inbox status handling.
- WordPress Migration: import job tracking, XML parsing, preview summaries, safe author/taxonomy/content/media/SEO mapping, redirect suggestions, shortcode warnings, reports, and rollback foundation.
- Theme Store foundation: official theme catalog fields, filtering, app recipes, and basic demo content import.
- Package Store foundation: official package catalog, package manifest validation, plugin bundles, and safe official package installation records.
- Commerce Lite: products, categories, images, simple variants, cart, draft orders, coupons, and manual payment placeholders.
- External Source Registry basic: registry records for WordPress, WooCommerce, Shopify, custom API, CSV feed, Google Sheet, and manual sources.

## V2 Exclusions

V2 must not implement:

- Full ERP workflows or advanced inventory operations.
- Full public Mall or Universal Mall marketplace.
- Work Network, Talent Marketplace, Academy, or certification operations.
- Affiliate payouts, agency partner operations, or legal partner operations.
- Full external two-way sync, live external API connectors, unsafe sync schedulers, or secret storage in plain text.
- Paid third-party marketplace submissions, revenue share, or arbitrary package code execution.
- Advanced AI Co-builder, AI SEO Pro, or visual page-builder conversion.
- WooCommerce live API sync, order sync, payment gateway integrations, POS, subscriptions, bundles, invoices, or shipping fulfillment.

## Operating Rules

- Keep V1 tests and public/admin flows stable.
- Keep WordPress migration import-first and preview-first; imported content must not be published automatically unless explicit import settings later allow it.
- Add focused tests for every implemented feature.
- Keep changes small, modular, and reversible.
- Log risks and assumptions in this file or feature-specific docs as V2 grows.
