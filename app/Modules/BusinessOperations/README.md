# BusinessOperations Module (V3)

Module for managing CRM, service requests, quotations, invoicing, payments, and inventory.

## Sub-domains

- **CRM**: Contacts, Leads, Pipelines, Activities, Customer Timeline
- **Service Requests**: Support tickets, service request management
- **Quotations**: Quote creation, approval workflow, conversion to invoices
- **Invoicing**: Invoice generation, payment status, invoice numbering
- **Payments**: Payment recording, receipts, reconciliation
- **Inventory**: Items, warehouses, stock movements, stock tracking
- **Suppliers**: Supplier management, purchase orders

## Structure

- `Actions/`: Business logic actions (create invoice, convert quote, etc.)
- `Data/`: Data structures and contracts
- `Services/`: Service classes for domain operations
- `Models/`: Eloquent models (created in app/Models)
- `Factories/`: Database factories (created in database/factories)
- `Tests/`: Feature and unit tests
- `Migrations/`: Database migrations

## Design Principles

- All operations are scoped to organization
- Financial records use soft deletes (no hard delete)
- No automatic approvals; only explicit user actions
- No external integrations (e.g., payment gateways)
- Comprehensive test coverage for each feature
