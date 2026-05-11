# Test Plan: Toevoeg Leden (Add Members) Function
**Date**: May 8, 2026  
**Feature**: Add New Member Form  
**Status**: Debug & Testing Required

---

## 1. CRITICAL ISSUES TO DEBUG

### Issue #1: Field Name Mismatch
- **Problem**: Form sends lowercase field names (`name`, `email`), but database expects capitalized names (`Naam`, `Email`)
- **Test Method**: 
  1. Open browser DevTools → Network tab
  2. Fill form and submit
  3. Check the POST request payload - what field names are being sent?
  4. Query database directly: `SELECT * FROM leden WHERE lid_id = '<last_id>';`
  5. Verify if `Naam` and `Email` columns have data

### Issue #2: Missing Validation
- **Problem**: Form might not be validating required fields before submission
- **Test Method**:
  1. Try submitting empty form
  2. Check if validation errors display in modal
  3. Check browser console for JavaScript errors
  4. Verify StoreLidRequest is being used in PostController

### Issue #3: Missing Member ID Generation
- **Problem**: New members might not get a `lid_id` assigned
- **Test Method**:
  1. Add a member via form
  2. Query database: `SELECT lid_id FROM leden ORDER BY lid_sinds DESC LIMIT 1;`
  3. Check if the record has a valid UUID or is NULL

### Issue #4: Default Payment Status
- **Problem**: Members might be created without a `betaalstatus`
- **Test Method**:
  1. Add a member
  2. Query: `SELECT betaalstatus FROM leden WHERE lid_id = '<member_id>';`
  3. Verify value is "Niet Betaald" not NULL

### Issue #5: Email Uniqueness Validation
- **Problem**: Should prevent duplicate emails but might not work
- **Test Method**:
  1. Add first member with email: `test@example.com`
  2. Try adding second member with same email
  3. Should show validation error: "Dit e-mailadres is al geregistreerd"
  4. Check database to ensure only 1 record exists

---

## 2. FUNCTIONAL TEST CASES

### TC-1: Valid Member Addition
**Precondition**: User on members page, modal form visible  
**Steps**:
1. Click "Lid toevoegen" button
2. Fill all required fields with valid data:
   - Naam: "Jan Jansen"
   - Email: "jan.jansen@example.com"
   - Telefoonnummer: "+597 123 4567"
   - Woonplaats: "Paramaribo"
   - Adres: "Straat 123"
   - Geboortedatum: "1990-05-15"
3. Click "Lid toevoegen" button
4. Verify success message appears
5. Verify modal closes
6. Verify new member appears in table on page reload

**Validation**:
- [ ] Member appears in database with correct data
- [ ] All field names match database schema (Naam, Email with capitals)
- [ ] member_id is generated (UUID format)
- [ ] betaalstatus = "Niet Betaald"
- [ ] Timestamp is recorded (lid_sinds, bijgewerkt_op)

---

### TC-2: Empty Form Validation
**Precondition**: Add member modal open  
**Steps**:
1. Leave all fields empty
2. Click "Lid toevoegen"

**Expected Result**:
- [ ] Modal stays open
- [ ] Red error container appears
- [ ] Shows error: "Naam is verplicht."
- [ ] Shows error: "Email is verplicht."
- [ ] Shows error for each required field

---

### TC-3: Invalid Email Validation
**Precondition**: Add member modal open  
**Steps**:
1. Naam: "Test User"
2. Email: "invalid-email"
3. Telefoonnummer: "+597 123 4567"
4. Woonplaats: "Paramaribo"
5. Adres: "Straat 123"
6. Geboortedatum: "1990-05-15"
7. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Voer een geldig e-mailadres in (bijv. naam@voorbeeld.com)."

---

### TC-4: Duplicate Email Prevention
**Precondition**: Member "Jan Jansen" with email "jan@example.com" already exists  
**Steps**:
1. Open add member form
2. Fill form with same email: "jan@example.com"
3. Fill other fields with different data
4. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Dit e-mailadres is al geregistreerd. Dit lid bestaat mogelijk al."
- [ ] No duplicate record created in database

---

### TC-5: Duplicate Phone Number Prevention
**Precondition**: Member with phone "+597 123 4567" exists  
**Steps**:
1. Open add member form
2. Fill form with same phone number: "+597 123 4567"
3. Fill other fields with different data
4. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Dit telefoonnummer is al geregistreerd."
- [ ] No duplicate record created

---

### TC-6: Invalid Birth Date
**Precondition**: Add member modal open  
**Steps**:
1. Fill all fields with valid data
2. Geboortedatum: "2025-12-31" (future date)
3. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Geboortedatum moet in het verleden liggen."

---

### TC-7: Cancel Button Functionality
**Precondition**: Add member modal open, form partially filled  
**Steps**:
1. Fill some fields
2. Click "Annuleren" button

**Expected Result**:
- [ ] Modal closes
- [ ] Form is cleared/reset
- [ ] Form data is not saved
- [ ] No new member in database

---

### TC-8: Modal Close Button
**Precondition**: Add member modal open  
**Steps**:
1. Click "×" close button
2. OR click on dark overlay background

**Expected Result**:
- [ ] Modal closes
- [ ] Form is cleared
- [ ] No data saved

---

### TC-9: Phone Number Format Validation
**Precondition**: Add member modal open  
**Steps**:
1. Fill form with invalid phone: "abc-def-ghi"
2. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Telefoonnummer mag alleen cijfers, +, spaties en streepjes bevatten."

---

### TC-10: Address Length Validation
**Precondition**: Add member modal open  
**Steps**:
1. Fill form with address: "A" (1 character)
2. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Adres moet minstens 5 tekens bevatten."

---

### TC-11: Name Character Validation
**Precondition**: Add member modal open  
**Steps**:
1. Naam: "Jan123" (contains numbers)
2. Fill other fields with valid data
3. Click "Lid toevoegen"

**Expected Result**:
- [ ] Error message: "Naam mag alleen letters, spaties en streepjes bevatten."

---

### TC-12: List Display After Addition
**Precondition**: New member added successfully  
**Steps**:
1. Refresh /ledenpagina page
2. Scroll through member list

**Expected Result**:
- [ ] New member appears in table
- [ ] All fields display correctly (Naam, Telefoonnummer, Adres, Email, Woonplaats)
- [ ] No NULL or missing values in table rows

---

---

## 3. DATABASE VERIFICATION TESTS

### DV-1: Record Structure Verification
```sql
SELECT * FROM leden ORDER BY lid_sinds DESC LIMIT 1;
```
**Expected columns and values**:
```
lid_id           → UUID format (e.g., "550e8400-e29b-41d4-a716-446655440000")
Naam             → Text value from form (e.g., "Jan Jansen")
Email            → Email format (e.g., "jan.jansen@example.com")
telefoonnummer   → Phone with +597 prefix (e.g., "+597 123 4567")
adres            → Street address (e.g., "Straat 123")
woonplaats       → One of the allowed cities (e.g., "Paramaribo")
geboortedatum    → Date format YYYY-MM-DD (e.g., "1990-05-15")
betaalstatus     → Either "Betaald" or "Niet Betaald" (default: "Niet Betaald")
lid_sinds        → Current timestamp
bijgewerkt_op    → Current timestamp
```

### DV-2: Field Case Sensitivity Check
```sql
DESCRIBE leden;
```
**Verify column names are exactly**:
- `Naam` (capital N) - NOT `naam`, NOT `name`
- `Email` (capital E) - NOT `email`
- All others lowercase: `telefoonnummer`, `adres`, `woonplaats`, `geboortedatum`, `betaalstatus`

### DV-3: No Orphaned Records
```sql
SELECT COUNT(*) FROM leden WHERE Naam IS NULL OR Email IS NULL OR lid_id IS NULL;
```
**Expected**: 0 records

### DV-4: Uniqueness Constraints
```sql
SELECT Email, COUNT(*) FROM leden GROUP BY Email HAVING COUNT(*) > 1;
SELECT telefoonnummer, COUNT(*) FROM leden GROUP BY telefoonnummer HAVING COUNT(*) > 1;
```
**Expected**: No duplicate rows

---

## 4. BROWSER CONSOLE TESTS

### BC-1: JavaScript Errors
1. Open browser DevTools (F12)
2. Go to Console tab
3. Submit the form
4. **Expected**: No red error messages

### BC-2: Network Request Inspection
1. Open DevTools → Network tab
2. Submit form
3. Look for POST request to `/ledenpagina/addlid`
4. **Check**:
   - [ ] Request status is 200 or 201 (success) OR 422 (validation error)
   - [ ] Response includes data fields being sent
   - [ ] Headers include `X-CSRF-TOKEN`

### BC-3: Form Data Payload
1. In Network tab, click the POST request
2. Go to "Payload" tab
3. **Verify** sent data includes:
   - [ ] `name` (from form input)
   - [ ] `email` (from form input)
   - [ ] `telefoonnummer`
   - [ ] `woonplaats`
   - [ ] `adres`
   - [ ] `geboortedatum`
   - [ ] `_token` (CSRF token)

---

## 5. PERFORMANCE TESTS

### PT-1: Form Submission Speed
- **Steps**: Submit a valid form
- **Expected**: Success toast appears within 2 seconds
- **Acceptable**: Up to 5 seconds for page reload

### PT-2: Database Query Performance
- **Steps**: With 1000 members in database, load /ledenpagina
- **Expected**: Page loads within 3 seconds
- **Check**: No timeout errors in logs

---

## 6. REGRESSION TESTS

### RT-1: Existing Members Still Visible
- **Steps**: 
  1. Go to /ledenpagina
  2. Verify previously added members are displayed
- **Expected**: All members show in table with pagination

### RT-2: Filtering Still Works
- **Steps**:
  1. Use woonplaats filter dropdown
  2. Select "Paramaribo"
- **Expected**: Table shows only members from Paramaribo

### RT-3: Chart Updates
- **Steps**:
  1. Add new member
  2. Check monthly chart on dashboard
- **Expected**: Current month bar increments by 1

### RT-4: Search Functionality
- **Steps**:
  1. Go to member list
  2. Use search box
  3. Search for a member's name
- **Expected**: Table filters to show matching member

---

## 7. DEBUGGING CHECKLIST

If tests fail, check these items:

### Backend Issues
- [ ] PostController imports `StoreLidRequest`
- [ ] PostController uses `StoreLidRequest $request` (not `Request`)
- [ ] Field transformation maps `name` → `Naam` and `email` → `Email`
- [ ] UUID generation: `Str::uuid()->toString()`
- [ ] Default betaalstatus set to "Niet Betaald"
- [ ] Lid model fillable array includes: Naam, Email, telefoonnummer, adres, woonplaats, geboortedatum, betaalstatus, lid_id
- [ ] Route `/ledenpagina/addlid` uses POST method
- [ ] Route points to `PostController@store`

### Frontend Issues
- [ ] Modal form has `data-store-url="{{ route('ledenpagina.addlid.store') }}"`
- [ ] Form inputs have correct `name` attributes: name, email, telefoonnummer, etc.
- [ ] AddLidModal.js runs on DOMContentLoaded
- [ ] FormValidator.js runs before form submit
- [ ] CSRF token meta tag exists: `<meta name="csrf-token">`
- [ ] fetch() request includes `X-CSRF-TOKEN` header

### Database Issues
- [ ] leden table exists with columns: lid_id, Naam, Email, telefoonnummer, adres, woonplaats, geboortedatum, betaalstatus, lid_sinds, bijgewerkt_op
- [ ] Column names have correct capitalization
- [ ] Primary key is `lid_id` with type CHAR(36)
- [ ] No foreign key constraints preventing inserts

---

## 8. TEST EXECUTION SUMMARY

| Test Case | Status | Notes | Pass/Fail |
|-----------|--------|-------|-----------|
| TC-1: Valid Member Addition | [ ] | | [ ] Pass [ ] Fail |
| TC-2: Empty Form Validation | [ ] | | [ ] Pass [ ] Fail |
| TC-3: Invalid Email | [ ] | | [ ] Pass [ ] Fail |
| TC-4: Duplicate Email | [ ] | | [ ] Pass [ ] Fail |
| TC-5: Duplicate Phone | [ ] | | [ ] Pass [ ] Fail |
| TC-6: Invalid Birth Date | [ ] | | [ ] Pass [ ] Fail |
| TC-7: Cancel Button | [ ] | | [ ] Pass [ ] Fail |
| TC-8: Close Button | [ ] | | [ ] Pass [ ] Fail |
| TC-9: Phone Format | [ ] | | [ ] Pass [ ] Fail |
| TC-10: Address Length | [ ] | | [ ] Pass [ ] Fail |
| TC-11: Name Characters | [ ] | | [ ] Pass [ ] Fail |
| TC-12: List Display | [ ] | | [ ] Pass [ ] Fail |
| DV-1: Record Structure | [ ] | | [ ] Pass [ ] Fail |
| DV-2: Case Sensitivity | [ ] | | [ ] Pass [ ] Fail |
| DV-3: No Orphaned Records | [ ] | | [ ] Pass [ ] Fail |
| DV-4: Uniqueness | [ ] | | [ ] Pass [ ] Fail |
| BC-1: No JS Errors | [ ] | | [ ] Pass [ ] Fail |
| BC-2: Network Request | [ ] | | [ ] Pass [ ] Fail |
| BC-3: Payload Validation | [ ] | | [ ] Pass [ ] Fail |

---

## 9. SIGN-OFF

**Tested By**: _________________  
**Date**: _________________  
**Overall Result**: [ ] PASS [ ] FAIL  
**Issues Found**: _________________  
**Recommendation**: [ ] Ready for Production [ ] Needs Fixes [ ] Blocked
