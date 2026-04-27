# Test Plan: Member Management & Dashboard Analytics

This document outlines the test strategy and test cases for the recently implemented "Add Member" form redesign and the "Monthly Join Statistics" chart.

## 1. Feature: Add New Member Form (Lid Toevoegen)
**Objective**: Ensure the redesigned form matches the UI specifications and maintains full functionality.

### Test Cases
| ID | Test Case | Description | Expected Result |
|---|---|---|---|
| TC-1.1 | Layout Verification | Check the 2-column grid layout on desktop. | Fields should be organized in two columns with proper spacing. |
| TC-1.2 | Responsive Design | Resize browser window to mobile width. | Form should collapse into a single column. |
| TC-1.3 | Woonplaats Selection | Click the Woonplaats input field. | Should display a dropdown list of cities instead of a text input. |
| TC-1.4 | Form Validation | Submit the form with empty required fields (Email*, Adres*). | Should display validation error messages in the new red error container. |
| TC-1.5 | Placeholder Check | Verify placeholders for Email and Adres. | Should see "email@address.com" and "password" as placeholders. |
| TC-1.6 | Cancel Functionality | Click the "Cancel" button or the close "×" icon. | Should redirect user back to the Ledenpagina. |
| TC-1.7 | Data Submission | Fill all fields and click "Add User". | New member should be saved in database and appear in the table. |

---

## 2. Feature: Monthly Join Statistics Chart
**Objective**: Verify that the chart correctly reflects real-time data from the database.

### Test Cases
| ID | Test Case | Description | Expected Result |
|---|---|---|---|
| TC-2.1 | Data Accuracy | Add a new member and check the chart. | The bar for the current month should increment by 1. |
| TC-2.2 | Empty State | Verify chart when no members joined in a specific month. | The bar for that month should be at 0 level (no bar visible). |
| TC-2.3 | UI Consistency | Compare chart labels with the current year. | Labels should show Jan through Dec (or Mon-Sun if day-view is preferred). |
| TC-2.4 | Hover Interaction | Hover over the bars in the chart. | Should display the exact count for that month. |
| TC-2.5 | Script Availability | Check browser console for errors. | No Chart.js or undefined variable errors should be present. |

---

## 3. Implementation Summary
- **Files Modified**: 
    - `app/Http/Controllers/LidController.php` (Data aggregation)
    - `resources/views/posts/LidToevoegen.blade.php` (UI Structure)
    - `resources/css/Toevoegen.css` (Styles)
    - `resources/views/layouts/Totalleden-Charts.blade.php` (Chart Component)
    - `resources/views/layouts/leden-overzicht.blade.php` (UI Integration)
    - `routes/web.php` (Cleanup)

## 4. Environment Requirements
- **Server**: PHP 8.x, Laravel 10.x
- **Frontend**: Vite, Chart.js (CDN), TailwindCSS (if applicable)
- **Database**: MySQL (using `lid_sinds` timestamp column)
