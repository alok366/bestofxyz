# ADR 0001: Custom PHP Framework Architecture on Top of Illuminate Components

- **Status**: Accepted
- **Date**: 2026-08-22
- **Deciders**: Engineering Team

---

## Context and Problem Statement

The application requires high throughput, minimal overhead, and clean architectural separation. While standard monolithic Laravel provides an extensive suite of out-of-the-box features, much of its default boilerplate is unnecessary for our dedicated API and SPA architecture, introducing unnecessary memory footprint and hidden magic.

## Decision

We chose to construct a lightweight, high-performance custom framework built explicitly on selected, battle-tested `illuminate/*` components:
- `illuminate/database` (Eloquent ORM & Query Builder)
- `illuminate/routing` (Fast route dispatcher)
- `illuminate/container` (IoC & dependency injection)
- `illuminate/events` (Event-driven system)
- `illuminate/validation` (Input validation)
- `illuminate/http` (HTTP request & response handling)

## Consequences

### Positive
- **Performance**: Dramatically smaller footprint and faster execution cycle compared to full-stack Laravel.
- **Explicit Design**: Architecture layers (`Controller -> Service -> Repository -> Model -> Transformer`) are clearly enforced without framework magic.
- **Maintainability**: Developers retain the familiarity of Eloquent, Validation, and Routing syntax without monolithic bloat.

### Negative / Trade-offs
- Third-party Laravel packages requiring auto-discovery or standard Laravel service providers cannot be blindly installed without manual container registration in `framework/Bootstrap/providers.php`.
- Custom Artisan commands must be registered explicitly in `artisan` and `framework/Console/CommandRegistry.php`.
