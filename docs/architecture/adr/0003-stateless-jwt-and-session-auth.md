# ADR 0003: Dual Authentication (Stateful Web Sessions & Stateless JWT APIs)

- **Status**: Accepted
- **Date**: 2026-08-22
- **Deciders**: Security & Architecture Team

---

## Context and Problem Statement

The platform serves both browser-based single-page applications and programmatic/mobile API consumers. Browser clients benefit from secure HTTP-only cookies and CSRF protection against XSS token theft, while API consumers and mobile clients require stateless Bearer JWT token headers.

## Decision

We implement a dual authentication pattern:

1. **Web Sessions (`start.session` middleware)**:
   - Used for first-party web UI and SPA sessions.
   - Session identifiers are stored in secure, `HttpOnly`, `SameSite=Lax` cookies.
   - Protected against cross-site request forgery via `VerifyCsrfTokenMiddleware`.

2. **Stateless JWT (`auth.jwt` middleware)**:
   - Used for REST API endpoints under `routes/api/`.
   - Uses RFC 7519 standard JSON Web Tokens issued via `/auth/token` or `/api/auth/login`.
   - Verified statelessly via `firebase/php-jwt` using a shared `JWT_SECRET` key.
   - Tokens support short expiration with refresh token rotation.

## Consequences

### Positive
- Browser clients do not store sensitive tokens in `localStorage`, reducing XSS vulnerability impact.
- API consumers can interact statelessly with high scalability and zero server session overhead.

### Negative / Trade-offs
- Middleware pipeline must distinguish between web session context and stateless token context.
