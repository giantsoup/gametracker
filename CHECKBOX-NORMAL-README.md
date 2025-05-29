# Normal Checkbox Implementation for Event Active Field

## Issue
The issue was that we were ensuring the 'active' field is always submitted by using a hidden input field, which is not treating the checkbox as a normal checkbox. The requirement was to treat the checkbox as any normal checkbox and set the default to true.

## Changes Made

### 1. Removed Hidden Input Field
Removed the hidden input field that was ensuring the 'active' field is always submitted:

```php
<!-- Hidden input to ensure 'active' is always submitted -->
<input type="hidden" name="active" value="0">
```

This change allows the checkbox to behave like a normal HTML checkbox, where:
- When checked, it submits the value "1"
- When unchecked, it doesn't submit any value

### 2. Updated Database Default Value
Created a new migration to update the default value of the 'active' field to `true`:

```php
Schema::table('events', function (Blueprint $table) {
    $table->boolean('active')->default(true)->change();
});
```

This ensures that new events are active by default, even if the 'active' field is not submitted.

### 3. Kept Default Checked State
Kept the default checked state of the checkbox:

```php
:checked="old('active', true)"
```

This ensures that the checkbox is checked by default when creating a new event.

## How It Works Now
1. When the checkbox is checked, the 'active' field is submitted with a value of "1", which is cast to `true` by Laravel's validation.
2. When the checkbox is unchecked, the 'active' field is not submitted at all, and the database default value of `true` is used.
3. The checkbox is still checked by default when creating a new event.

This implementation treats the checkbox as a normal HTML checkbox while still ensuring that new events are active by default.
