# Deliverables:
## Step 1 - DB Structure
ERD in docs/erd.html
## Step 2 - Delivery Date Calculator
`src/Service/DeliveryDateCalculator.php`
## Step 3 - API
Setup:
```
make init
cd frontend && npm ci && npm run dev
```
Other commands accessible either via running `make` (backend) or as npm scripts (frontend)

Unimplemented due to time constraints:
- Prod build of frontend
- Prod docker setup
- Better serialization handling on index api endpoints
- OpenAPI docs
- Frontend code polish/cleanup
- Integrate frontend scripts with Makefile
- Proper handling and tests for null delivery times for a location on ShippingMethod (should disable delivery, likely 500s)
- Nicer validation for requests, so e.g. missing multiple attributes returns multiple errors at once
- A proper local install/build process for Tailwind.
- Proper fixtures for populating the Country table.

Assumptions:
- Future instances of ShippingMethod are most likely to use the same UK/Europe/ROW split as Royal Mail - this doesn't
  seem unreasonable for a UK company, and a more complex database structure that may never be used didn't seem worth it,
  but in practice I'd ideally want more idea of what was likely in future
- Same applies for different logic with regard to how shipping dates are calculated (e.g. non-country based, weight based,
  non-business day based, different cutoff to 4pm) 
- The ERD and code include no consideration for delivery pricing - maybe not a reasonable assumption in practice
- Orders are shipped in one package
- "Assume that the first business day is the day the order is shipped" - I've taken this to mean that an order placed 
  on Monday at 1pm with 1 business day delivery, will arrive on Tuesday (rather than later that Monday) 
- I've used MariaDB, but mostly because I had a Docker config for it handy - I don't believe there's anything that would
  cause issues on MySQL or postgres, bar possibly the migrations in the latter case

## Bonus Step - Tests
Basic API and Unit tests - run with `make test`