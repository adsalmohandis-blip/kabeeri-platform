# KABEERI V7 Implementation Notes

Started on 2026-05-06.

## Scope Direction

V7 adds the mobile app foundation. It provides app configuration, theme profiles, mobile API manifests, device registry, push token registry, public mobile APIs, and mobile auth APIs.

## Implemented Foundations

- Mobile app configuration table and default seeded app.
- Mobile theme profiles.
- Mobile API manifest with public/auth/device/push endpoint declarations.
- Mobile device registry.
- Push token registry that stores token hashes only.
- Mobile auth token registry that stores token hashes only.
- Public JSON APIs under `/api/mobile/config`, `/api/mobile/manifest`, and `/api/mobile/theme`.
- Auth/device JSON APIs under `/api/mobile/auth/register`, `/api/mobile/auth/login`, `/api/mobile/devices`, and `/api/mobile/push-tokens`.

## Safety Boundaries

- Push tokens and auth tokens are hashed before storage.
- Mobile auth tokens are returned once at issuance time.
- V7 does not add tenant columns to `users`.
- V7 does not introduce native app binaries; it provides backend foundations only.
