# Alawa Resort HostPlatform Sync Handoff

## Goal

Original project goal from the start of this work:

1. When a room is booked in HostPlatform, WordPress must reflect reduced availability.
2. When a room is booked in WordPress, HostPlatform must receive that reservation.
3. Both systems must stay aligned on room availability, not drift apart.

This plugin was built to bridge:

- HostPlatform
- WordPress
- WooCommerce
- OVA BRW

Project root:

- `D:\Projects\Alawaresort`

Main plugin:

- [alawa-hostplatform-sync.php](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/alawa-hostplatform-sync.php)

Admin assets:

- [admin.js](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/assets/admin.js)
- [admin.css](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/assets/admin.css)

Local site:

- `https://alawaresort.test`

## Site Stack

The restored live WordPress copy uses:

- WooCommerce
- OVA BRW
- custom plugin `alawa-hostplatform-sync`

The decision was to build a standalone bridge plugin instead of modifying OVA BRW directly.

## HostPlatform Model We Confirmed

HostPlatform for this property is not modeled as a single stock number. It is modeled as room types with physical units underneath.

Confirmed live property:

- Property name: `ALAWA KUNDASANG`
- Property ID: `6912a0a80e1df0038cc9a5b5`
- Host ID: `6909d76f29ea8f03c1480641`

Confirmed room types:

- `QUEEN`
  - room type ID: `6912a1890e1df0038cca5fda`
  - total units: `6`
- `TWIN SINGLE`
  - room type ID: `6912a1c3a7b80b03bc660e5f`
  - total units: `2`

Confirmed unit listing IDs:

### Queen units

- `6912a2476998d903be14fe59`
- `6912a291a7b80b03bc66aede`
- `6912a3647d18d803c631c562`
- `6912a2c49ba90600e6846597`
- `6912a2f66998d903be1597c5`
- `6912a30f8b9d5b0392cc46b9`

### Twin Single units

- `6912a32d6998d903be15bef5`
- `6912a3450e1df0038ccbaa8c`

## WordPress Product Mapping

Confirmed products:

- product `3386` -> `Queen Bed Room`
- product `4367` -> `Twin Single Bed Room`
- product `5661` -> `Twin Single Bed Room test`

Live mapping currently stored:

- `3386`
  - listing type: `unit_pool`
  - unit pool: all 6 Queen units
- `4367`
  - listing type: `unit_pool`
  - unit pool: both Twin Single units
- `5661`
  - intentionally left alone

Reason for `unit_pool` mode:

- WordPress sells room categories
- HostPlatform assigns real physical units
- unit-pool mapping lets the plugin resolve the correct HostPlatform room type and availability from the grouped live data

## Credentials And Auth Model

Several auth models were investigated during this project.

### Old reference docs

The project folder included:

- `Current API&Webhook Docs`
- Postman collection `HostPlatform External API-29Aug2025.json`

From that export we found:

- an old `AccessToken` variable
- `/external/v1` endpoint patterns
- `access-token` header style

That old token returned `403` against the live system.

### Live auth model actually working

From browser session inspection and HAR review, the real live app uses:

- Base URL: `https://nebulapi-asg.hostplatform.com`
- API namespace: `/v1`
- Auth header mode: `Authorization: JWT <token>`

Current plugin settings stored in database:

- `enabled = yes`
- `base_url = https://nebulapi-asg.hostplatform.com`
- `api_namespace = /v1`
- `auth_mode = jwt`
- `property_id = 6912a0a80e1df0038cc9a5b5`
- `cron_enabled = yes`
- `cron_schedule = alawa_hps_15min`

Latest verified runtime state:

- inventory rows: `738`
- webhook rows: `3`
- retry queue rows: `0`
- latest successful sync log: `2026-04-26 01:24:08 info sync Inventory sync complete.`

Important caution:

- the live token in settings is a session JWT, not a permanent integration key
- it can expire and require replacement

## Screenshots And HostPlatform Interface Review

We reviewed multiple screenshots from:

- HostPlatform overview
- reservation list
- listing list
- webhook configuration
- multi calendar
- folder `D:\Projects\Alawaresort\HostPlatform Interfaces`

What those screenshots proved:

- property and room labels match WordPress inventory intent
- HostPlatform uses specific unit assignments on reservations
- webhook UI supports:
  - enable/disable
  - URL
  - optional header authentication
  - event selection
- Multi Calendar clearly displays the dated availability grid we needed to reverse-engineer

Recommended webhook events:

- `New Reservation`
- `Cancel Reservation`
- `Update Reservation (All)`
- `Delete Reservation`

Optional only:

- `Update Reservation (Contact Only)`

## HAR Analysis And Final Inventory Endpoint Discovery

Two HAR files were later provided:

- `C:\Users\ejaj.mahmud\Downloads\system.hostplatform.com.har`
- `C:\Users\ejaj.mahmud\Downloads\system.hostplatform-1.har`

These were the key breakthrough.

From those HAR files we identified the real dated inventory feed used by the live Multi Calendar:

- `GET /v1/property/{propertyId}/rates?startDate=...&endDate=...&otas=...`

Actual form used:

- `/v1/property/6912a0a80e1df0038cc9a5b5/rates?startDate=YYYY-MM-DD&endDate=YYYY-MM-DD&otas=agoda,agodahomes,airbnb,bookingcom,ctripcm,expedia,traveloka,tiketcom`

Important fields in the response:

- room type `_id`
- room type `name`
- `inventories`
- `roomsByDates`
- `rooms`
- `total`
- `blockOrMaintenance`
- `validReservation`

This is now the live inventory source used by the plugin.

## Plugin Features Built / Repaired During This Project

### Core integration

- custom plugin scaffolded and activated
- settings storage
- inventory cache table
- logs table
- webhook audit table
- HostPlatform API client
- WooCommerce order hooks
- cancellation hooks
- checkout/add-to-cart availability validation
- manual full sync
- scheduled sync
- room mapping UI

Database tables:

- `wp_alawa_hps_inventory`
- `wp_alawa_hps_logs`
- `wp_alawa_hps_webhooks`
- `wp_alawa_hps_retry_queue`

### Live mode repairs

- added support for live `/v1` namespace
- added `jwt` auth mode
- repaired auth mismatch from old `bearer` mode
- repaired live connection test
- repaired room type resolution from `unit_pool`
- added logic to resolve room type from pooled unit IDs
- added live dated inventory fetch from `/property/{id}/rates`

### Admin dashboard repairs

- JS admin app restored
- CSS/admin assets enqueued properly
- REST admin routes restored
- dashboard overview data shape fixed
- cards now show live counts instead of placeholders
- dashboard cards made clickable
- plugin submenu navigation changed to full page reloads for stability
- `Copy Logs` button added
- notification drawer added
- notification drawer groups webhook/log events by date
- notification drawer auto-refreshes
- notification button contrast improved for visibility in the hero area

### Operations layer added

- cron health panel added
- reservation reconciliation screen added
- retry queue screen added
- retry queue backend added
- failed reservation pushes now queue for retry instead of only logging and stopping
- cron now also processes queued retries
- reconciliation now labels rows more honestly:
  - `Synced`
  - `Retry queue`
  - `Ready to push`
  - `Waiting`
  - `Not eligible`
- notification button contrast improved for readability
- settings now have their own dedicated page slug instead of sharing the dashboard slug
- live reservation create path was corrected and given a fallback attempt path

### Admin experience cleanup

- foreign WordPress/plugin notices forcefully hidden on plugin pages
- SoftWP license notice and similar banners suppressed for these screens

## Current Verified Plugin State

The following are verified from code, database, or successful runtime behavior.

### 1. Connection is working

The admin test connection succeeds and returns `2 item(s)`.

That corresponds to the two live room groups:

- `QUEEN`
- `TWIN SINGLE`

### 2. Inventory sync is working

Most recent log evidence:

- `2026-04-25 16:54:22 info sync Inventory sync complete.`

The sync now refreshes:

- `2 product(s)`

### 3. Inventory rows exist in cache

Direct database verification:

- total cached inventory rows: `738`

Per product:

- product `3386`: `369` rows
- product `4367`: `369` rows

Date range currently cached:

- from `2026-04-23`
- through `2027-04-26`

Observed inventory ranges:

- Queen available range: `0` to `6`
- Twin Single available range: `0` to `2`

Sample cached records:

- `2026-04-24`
  - Queen: available `3`, occupied/booked `3`, total `6`
  - Twin Single: available `0`, occupied/booked `2`, total `2`
- `2026-04-26`
  - Queen: available `2`, occupied/booked `4`, total `6`
  - Twin Single: available `1`, occupied/booked `1`, total `2`

That means the plugin is not just “green”; it is storing plausible live availability data.

### 4. Cron exists

Confirmed directly in WordPress cron option:

- hook: `alawa_hps_cron_sync`
- schedule: `alawa_hps_15min`
- interval: `900` seconds

So yes, there is a plugin cron scheduled every 15 minutes.

### 5. Webhook endpoint exists

Registered endpoint:

- `/wp-json/alawa/v1/hostplatform-webhook`

Current webhook audit table count:

- `3` webhook rows stored

That proves webhook traffic or testing has at least reached the plugin.

### 6. Operations tooling exists

Verified in code:

- cron health panel
- reservation reconciliation page
- retry queue page
- retry queue table
- automatic retry processing during cron

### 7. Current order-push state is not fully proven

Direct database checks showed:

- current retry queue rows: `0`
- current stored HostPlatform reservation codes: `0`
- current `_alawa_hps_synced = yes` order count: `0`

Recent eligible Woo orders exist:

- order `5549` -> `Queen Bed Room` -> `wc-processing`
- order `5548` -> `Queen Bed Room` -> `wc-processing`

But no HostPlatform reservation codes are stored for them yet.

This means the WordPress -> HostPlatform push side is not yet fully certified.

### 8. Live reservation-create permission test result

A direct live reservation-create probe was performed using the current HostPlatform JWT session, with a clearly labeled developer test payload intended to be easy to delete later.

Important outcome:

- no reservation was created
- nothing needs to be deleted from HostPlatform from this test

What was learned:

1. `POST /external/v1/reservation` with the current live JWT returned `403`
2. the live account does respond on `POST /v1/reservation`
3. the live `/v1/reservation` route expects a different request contract than the old external API
4. once the live payload shape was corrected far enough, HostPlatform returned the decisive error:

`User has no permission to access POST Reservation!`

That means the current HostPlatform account/session can read inventory but does not have permission to create reservations through the API.

This is now the clearest blocker on the WordPress -> HostPlatform side.

## What Is Confirmed vs What Is Not

### Confirmed

- plugin loads
- dashboard loads
- settings save
- mapping saves
- live JWT connection works
- room type discovery works
- dated inventory fetch works
- inventory cache fills
- cron exists
- webhook endpoint exists
- webhook requests can be stored
- logs work
- retry queue exists
- reconciliation screen exists
- cron health panel exists
- inventory sync is actively refreshing live cache data

### Confirmed but only for the HostPlatform -> WordPress side

- live dated inventory sync works
- cron-based availability refresh works
- cache values look plausible against room capacities

### Strongly likely but not fully signed off

- HostPlatform inventory changes should flow into WordPress after cron/manual sync
- checkout validation should use live API/cache correctly
- OVA BRW stock snapshots should reflect synced availability
- retry queue should capture future push failures correctly once a live push attempt happens

### Not fully proven yet

Because HostPlatform is production-only and no safe test booking was made, these still need a controlled live test or a staging environment:

1. HostPlatform booking created by a real guest or operator
   - then confirm WordPress availability changes exactly as expected after webhook or cron

2. WordPress booking pushed into HostPlatform
   - then confirm reservation appears correctly in HostPlatform

3. cancellation/update loop
   - then confirm both sides recover inventory correctly

4. backfill/manual push behavior for existing eligible Woo orders
   - especially orders already in `processing`

This is the remaining gap between “technically working” and “fully certified in production behavior.”

## Best Possible Confirmation Without A Production Test Booking

Without creating a live production booking, the strongest evidence currently available is:

1. live authenticated API access is working
2. dated inventory is being pulled from the same source used by HostPlatform Multi Calendar
3. cached inventory values match the room capacities and booking patterns visible in HostPlatform
4. cron is present and scheduled
5. webhooks are implemented and receiving entries

That is very good evidence that the integration is substantially working.

What cannot be honestly guaranteed without one controlled live event:

- end-to-end reservation creation from WordPress into HostPlatform
- end-to-end HostPlatform webhook/update behavior on a brand new booking lifecycle event
- whether existing already-processing Woo orders need manual backfill rather than waiting for future order-status transitions
- whether the current HostPlatform user/token will ever be allowed to write reservations without permission changes

## Reconciliation Screen Interpretation

If the reconciliation screen shows rows with:

- `No reservation code yet`
- queue `No queue item`
- Woo order status `pending` or `failed`

that is not automatically a bug.

Current WordPress -> HostPlatform push hooks run when the Woo order reaches:

- `processing`
- `completed`

So orders still in:

- `pending`
- `failed`
- `cancelled`
- `refunded`

are not expected to push yet, or may never be eligible to push.

This is why the reconciliation screen was adjusted to show clearer sync states instead of a vague `pending`.

Additional interpretation from the latest review:

- rows showing `Not eligible` with Woo status `failed` are normal
- rows showing `No reservation code yet` on `failed` orders do not indicate a plugin failure
- the important rows to watch are mapped products in `processing` or `completed`
- at the time of the latest check, there were eligible `processing` orders but still no stored reservation codes
- that is the remaining reason the plugin cannot yet be called fully complete

## Webhook Role In This System

HostPlatform webhook support is useful, but only for one direction of the integration.

### What the webhook helps with

- HostPlatform -> WordPress updates
- notifying WordPress when a reservation is created, updated, cancelled, or deleted in HostPlatform
- triggering faster WordPress-side inventory refresh instead of waiting only for cron
- improving the notification drawer and webhook audit trail

### What the webhook does not solve

- WordPress -> HostPlatform reservation creation
- API permission to write reservations into HostPlatform
- missing create-reservation access on the current JWT/session

So the webhook is valuable, but it does **not** replace reservation-write permission.

It helps the plugin react to HostPlatform events.
It does not grant the plugin the right to create HostPlatform reservations.

## Current Verdict

The plugin is currently:

- strong on inventory synchronization
- strong on admin operations and observability
- not fully signed off on WordPress -> HostPlatform reservation creation

So the honest status is:

- **HostPlatform -> WordPress:** substantially working
- **WordPress -> HostPlatform:** not fully certified yet

The cleanest next engineering step is:

- add a manual `Push Eligible Orders Now` / backfill tool

That would let existing `processing` orders be tested without waiting for a brand-new order lifecycle.

Operationally, the cleanest next external step is:

- ask HostPlatform to grant reservation-create API permission to this account or issue a dedicated integration token with write access

## Suggested Safe Validation Plan

If production test creation is sensitive, use the least risky path:

1. Compare several dates manually between HostPlatform Multi Calendar and the plugin Inventory page.
2. Let cron run and verify inventory stays aligned across refreshes.
3. If possible, create one tightly controlled internal booking for a low-risk future date.
4. Confirm:
   - HostPlatform -> WordPress inventory change
   - WordPress -> HostPlatform reservation create
   - cancellation restores availability

## Notes About The Plugin UI

The plugin admin is JS-rendered, but several stability fixes were added:

- real submenu page loads instead of fragile client-only view switching
- forced hiding of unrelated admin notices
- dashboard cards clickable
- logs copy button added

If a plugin page ever goes blank again, the most likely causes are:

- browser cache with stale JS
- script load failure in wp-admin
- a JS runtime error before mount

Hard refresh is the first recovery step.

## Bottom-Line Status

Current honest status:

- the plugin is no longer just scaffolding
- it is now connected to the live HostPlatform account
- it has real dated inventory data in cache
- it has active mapping for the two production room groups
- it has a running cron
- it has an operations layer with notifications, reconciliation, retry queue, and cron health

But because no safe end-to-end live booking test has been executed, it should be described as:

- functionally implemented
- operationally promising
- not fully production-certified until one controlled booking lifecycle test is completed

## Files To Inspect Next

Main plugin:

- [alawa-hostplatform-sync.php](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/alawa-hostplatform-sync.php)

Dashboard app:

- [admin.js](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/assets/admin.js)
- [admin.css](D:/Projects/Alawaresort/wp-content/plugins/alawa-hostplatform-sync/assets/admin.css)
