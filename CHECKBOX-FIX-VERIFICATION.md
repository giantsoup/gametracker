# Checkbox Active Status Fix Verification

## Issue
When creating a new event and having the active checkbox checked, the event was not being saved as active.

## Root Cause
In HTML forms, unchecked checkboxes don't submit any value. When the form was submitted, if the checkbox was unchecked, no value was sent for the 'active' field. However, even when the checkbox was checked, the value wasn't being properly submitted due to how the `flux:checkbox` component works with standard HTML forms (as opposed to Livewire forms).

## Solution Implemented
Added a hidden input field before the checkbox with a value of "0". This ensures that the 'active' field is always submitted, even when the checkbox is unchecked. When the checkbox is checked, it submits a value of "1" which overrides the hidden input's value.

Also added a `value="1"` attribute to the checkbox to explicitly set its value when checked.

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
```

This is a common pattern for handling checkboxes in HTML forms and ensures that the 'active' field is always submitted with the correct value.

## Verification
I've verified that the fix works correctly by:

1. Reviewing the Event model, which casts the 'active' field to boolean
2. Checking the EventRequest class, which validates the 'active' field as boolean
3. Testing the form submission process with a simulation script

The test results confirm that:
- When the checkbox is checked, the 'active' field is set to true
- When the checkbox is unchecked, the 'active' field is set to false
- The 'active' field is always submitted, regardless of checkbox state

## Best Practices
This implementation follows best practices for handling checkboxes in HTML forms:
1. Always include a hidden input with the same name as the checkbox to ensure the field is submitted
2. Set an explicit value on the checkbox
3. Ensure the model properly casts the field to the correct type
4. Validate the field with appropriate rules

## Conclusion
The issue has been successfully resolved. The active checkbox now works correctly when creating new events.
