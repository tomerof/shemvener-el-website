# Menora payment link returns 401 "invalid dispatcher secret"

**Status:** open — reported to Menora's developer on 2026-09-22, waiting on their fix.
**Reported by:** us, via their "developer" who builds on Base44.

## Symptom

Customer clicks "מעבר לתשלום באתר מנורה" on the candle-order form, gets a new tab with a
`payment_link` URL like:

```
https://base44-dispatcher-production.base44.workers.dev/ExternalPayment?order=<order_id>
```

That page returns:

```
HTTP/2 401
x-base44-cf-error: unauthorized
{"error":"unauthorized","detail":"invalid dispatcher secret"}
```

## Diagnosis (confirmed 2026-09-22)

This is **not** a bug in our code. Verified by calling Menora's own API directly with curl, the
same way `Api_Client::create_order()` in
`wp-content/plugins/menora-payment-integration/includes/class-menora-api-client.php` does:

```bash
curl -s -X POST https://menora-glow-pro.base44.app/functions/createExternalOrder \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"customer_name":"Test User","phone":"0500000000","email":"test@example.com","external_order_id":"SHEM_TEST123","amount":18,"return_url":"http://localhost:8080/"}'
```

→ Returns `200 OK` with a valid `order_id` + `payment_link`. **Order creation works fine** — our
payload and request are accepted with no changes needed.

Following that returned `payment_link` is what fails, with the 401 above. The
`x-base44-cf-error: unauthorized` response header shows the error originates inside **Base44's own
Cloudflare Workers infrastructure** — specifically their `base44-dispatcher-production` worker,
which is supposed to forward `/ExternalPayment` requests to their backend using an internal
"dispatcher secret" (a token exchanged between their own worker and their own backend service).
That secret is misconfigured, expired, or missing on their side.

We do **not** hold, send, or need any secret/API key for this integration — confirmed by
searching this repo:
- `class-menora-api-client.php`: the `createExternalOrder` call sends no auth header/token at all,
  and it still succeeds.
- `wp-config.php`: no Menora-related constants.
- `wp_options` table: no Menora-related settings stored.

So there is nothing on our end to configure, exchange, or fix. This is purely an internal
Base44/Cloudflare Workers config problem on Menora's side.

## What Menora's developer needs to do

In the Base44 dashboard, on the App/Function behind `ExternalPayment`:
1. Check the environment variable / secret used by the `base44-dispatcher-production` Worker to
   authenticate to their own backend service (the "dispatcher secret").
2. Make sure it's set, not expired, and matches what the backend expects.
3. If unclear, this is a question for Base44 support/docs directly — it's their platform's
   internal worker-to-backend auth, not custom code they wrote.

## If we end up needing to fix/debug this ourselves

We have no access to Base44's dashboard or their worker config, so there's no fix we can apply
from our side beyond what's already implemented. If we get access:

1. Ask Menora/Base44 for read access to the App's Function/Worker settings (or have them screen-
   share) — look for an env var/secret named something like `DISPATCHER_SECRET`,
   `WORKER_SECRET`, or similar on both:
   - the `base44-dispatcher-production` Worker, and
   - the backend Function/App it dispatches to (`menora-glow-pro`).
2. Confirm both sides have the same current value. Regenerate/sync it if Base44's UI allows.
3. Re-test with the curl command above (or by resubmitting the real candle-order form) — a
   successful fix means the `payment_link` URL loads Menora's payment page instead of the 401.
4. No changes needed in this repo/plugin for this specific issue — re-check this file first if the
   error resurfaces, since the root cause has already been isolated to their infrastructure.

## Relevant files (our side, confirmed working)

- `wp-content/plugins/menora-payment-integration/includes/class-menora-api-client.php` — the
  `createExternalOrder` call.
- `wp-content/plugins/menora-payment-integration/includes/class-menora-form-action.php` — builds
  the order payload from form fields and renders the payment button.
