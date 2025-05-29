# Create Event Form Fix

## Issue
The create event form was missing the active checkbox, and there were potential issues with how the active field was being submitted and processed.

## Changes Made

### 1. Added Active Checkbox to Create Event Form
Added the active checkbox to the create event form with a hidden input field to ensure the 'active' field is always submitted:

```php
<!-- Hidden input to ensure 'active' is always submitted -->
<input type="hidden" name="active" value="0">
<flux:checkbox
    name="active"
    id="active"
    :label="__('Active')"
    :checked="old('active', true)"
    value="1"
/>
@error('active')
<flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
    {{ $message }}
</flux:text>
@enderror
```

This implementation ensures that:
- If the checkbox is unchecked, the hidden input's value ("0") is submitted
- If the checkbox is checked, both the hidden input and the checkbox are submitted, but the checkbox's value ("1") takes precedence
- The 'active' field is always included in the form submission data
- The checkbox is checked by default (using `:checked="old('active', true)"`)
- Error messages are displayed if there are validation errors for the 'active' field

### 2. Verified Form Submission Process
Verified that the form submission process correctly handles the 'active' field:
- The EventRequest validates the 'active' field as a boolean
- The Event model casts the 'active' field to a boolean
- The EventController uses the validated data to create a new Event model instance

### 3. Created Test Script
Created a test script to verify that the 'active' field is properly cast to a boolean during the validation process. The script simulates three scenarios:
1. Form submission with the active checkbox checked
2. Form submission with the active checkbox unchecked
3. Form submission with no active field (which shouldn't happen with our implementation)

## Best Practices
This implementation follows best practices for handling checkboxes in HTML forms:
1. Always include a hidden input with the same name as the checkbox to ensure the field is submitted
2. Set an explicit value on the checkbox
3. Ensure the model properly casts the field to the correct type
4. Validate the field with appropriate rules

## Conclusion
The create event form has been fixed by adding the active checkbox with a hidden input field to ensure the 'active' field is always submitted with the correct value. The form now works correctly and follows best practices for handling checkboxes in HTML forms.
