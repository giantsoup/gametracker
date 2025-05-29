# Checkbox Active Status Fix

## Issue
When creating a new event and having the active checkbox checked, the event was not being saved as active.

## Root Cause
In HTML forms, unchecked checkboxes don't submit any value. When the form was submitted, if the checkbox was unchecked, no value was sent for the 'active' field. However, even when the checkbox was checked, the value wasn't being properly submitted due to how the `flux:checkbox` component works with standard HTML forms (as opposed to Livewire forms).

## Solution
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
