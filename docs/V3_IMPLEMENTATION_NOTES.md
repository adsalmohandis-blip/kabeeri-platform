# KABEERI V3 Implementation Notes

Generated for Prompt 00 on 2026-05-06.

## Scope Summary

KABEERI V3 builds on stable V1 and V2 foundations to add comprehensive **Business Operations** (ERP-lite) features. Organizations remain the tenant root, and multi-tenancy is preserved throughout.

Planned V3 scope:

- **CRM Foundation**: Contacts, Leads, Lead sources/scoring, Sales pipelines/stages, Activities/follow-ups, Customer timeline.
- **Service Requests**: Support tickets, service request management, linking to CRM.
- **Quotations**: Quote creation, line items, approval workflow, conversion to invoices.
- **Invoicing**: Invoice generation, line items, payment status tracking, invoice numbering.
- **Payments**: Payment methods, payment recording, receipts, payment reconciliation.
- **Inventory Lite**: Inventory items, warehouses, stock movements, stock tracking.
- **Suppliers**: Supplier management, supplier contacts, purchase order foundation.
- **Financial Reporting Basic**: Invoice reports, payment reports, inventory reports.

## V3 Exclusions

V3 must not implement:

- Full ERP (accounting, tax, advanced reporting, audit trails for financials).
- Real-time sync with external accounting systems.
- Advanced AI or machine learning for lead scoring.
- Multi-currency or complex tax calculations.
- Time tracking, project management, or resource planning.
- Advanced supply chain optimization.
- Affiliate or subscription billing.
- Payment gateway integrations (PCI compliance).

## Operating Rules

- Keep V1 and V2 tests and flows stable.
- All CRM, quotation, invoice, and inventory operations must be scoped to organization.
- Use soft deletes for financial records (never hard delete invoices, quotations, payments).
- Add focused tests for every implemented feature.
- Keep operations simple and modular.
- Log risks and assumptions in this file as V3 grows.

## V3 Hard Boundaries

- No direct external API integrations (e.g., payment gateways, accounting software).
- No user-facing financial calculations in UI without backend validation.
- No automatic approval workflows—all approvals must be explicit user actions.
- No deletion of financial records once created; only void/cancel flags allowed.
