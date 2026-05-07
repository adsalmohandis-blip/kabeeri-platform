# KABEERI V16 Release Candidate

V16 is ready when the customer can move from public entry to authenticated workspace and first active app.

## Go Gates

- V16 customer routes are registered.
- `/start` renders customer paths and starter themes.
- `/register` creates a user and profile.
- `/login` authenticates customer web users.
- `/customer/onboarding` provisions Organization, optional Company, Site/App, selected Theme, theme settings, and starter content.
- `/customer/dashboard` renders authenticated workspace data.
- `/customer/apps/{username}` is scoped to the owning customer and resolves the app/site username from the stored site slug.
- Profile capabilities can be updated.
- V16 docs and tests exist.
- V16 task tracker is synced.

## No-Go Gates

- Customer auth is mixed with Filament admin auth.
- `/customer` public V13 page is broken.
- Theme install executes unreviewed package code.
- A customer can view another customer's site.
- Tracker says done before tests pass.

## Manual QA

Open these pages manually:

- `/start`
- `/register`
- `/login`
- `/customer/onboarding`
- `/customer/dashboard`
- `/customer/apps/{username}` after provisioning
- `/admin/login` to confirm admin remains separate

## Next Version Candidates

- V17: deeper app builder editor and theme preview.
- V18: customer billing, team invitations, and module add-ons.
- V19: Kabeeri Builder marketplace and implementation partner workflow.
