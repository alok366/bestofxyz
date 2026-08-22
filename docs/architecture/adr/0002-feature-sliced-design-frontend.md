# ADR 0002: Adoption of Feature-Sliced Design (FSD) for React Frontend

- **Status**: Accepted
- **Date**: 2026-08-22
- **Deciders**: Frontend Team

---

## Context and Problem Statement

As single-page React applications scale across multiple entry bundles (User App, Admin Dashboard, Login Portal), organizing components by technical role (e.g. `components/`, `containers/`, `stores/`) leads to tight coupling, cyclic dependencies, and unclear ownership of business logic.

## Decision

We adopt **Feature-Sliced Design (FSD)** methodology for the entire React codebase under `resources/js/`:

1. **Layers**:
   - `app/`: Bundle composition root, providers, routing.
   - `pages/`: Page-level screens mapped to routes.
   - `widgets/`: Standalone compound UI assemblies.
   - `features/`: User actions and interaction units (e.g. Upvoting, Searching).
   - `entities/`: Domain entities and data models (User, Resource, Category).
   - `shared/`: Generic reusable UI primitives, HTTP client, and utilities.

2. **Public API Contract**: Each slice exposes its public API exclusively via its root `index.js`. Internal subdirectories (`ui/`, `model/`, `api/`) are private to that slice.

3. **Strict Top-Down Dependency**: Layers may only import from layers below them. Cross-slice imports within the same layer are strictly disallowed.

## Consequences

### Positive
- **Predictable Scalability**: Adding new features or pages does not risk breaking unrelated slices.
- **Colocation**: State (Redux slices), API queries, and UI components for a business domain live together.
- **Reusability**: Slices can be easily shared between different app bundles (`User`, `Admin`) without code duplication.

### Negative / Trade-offs
- Requires discipline to adhere to layer import rules and avoid shortcuts.
