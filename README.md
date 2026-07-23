# Zenserp Integration

This project was created as a small technical exercise demonstrating an integration with the Zenserp search API.

Given Netwatch Global's focus on transforming public information into actionable intelligence, I chose to build a simple internal search interface backed by Zenserp.

## Approach

The frontend does not communicate with Zenserp directly. Requests are handled by the Laravel application, which acts as a boundary between the user interface and the external provider.

This keeps the API credentials server-side and prevents the UI from becoming coupled to Zenserp’s request and response formats. Changes to the upstream integration can therefore be handled without requiring corresponding changes throughout the frontend.

The implementation also includes basic rate limiting to control usage of the search endpoint.

## Further Development

With more time, I would move the provider-specific implementation behind a general search service contract. This would allow Zenserp to be replaced or selected through configuration without affecting the rest of the application.

Potential extensions would include:

- persisting searches for auditing and reporting
- recording provider response metadata and failures
- configurable regular-expression alerts for matching search results
- caching repeated searches

## Running the Project

Copy the environment file and configure the Zenserp API credentials:

```bash
cp .env.example .env
```

Install dependencies:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
```

Run the project (exposes artisan to port 5000 & vite to 5173 on loopback)

```bash
composer run dev
```