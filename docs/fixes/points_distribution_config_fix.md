# Points Distribution Config Fix

## Issue
When running tests, the following error was encountered:

```
Cannot assign null to property App\Livewire\Games\PointsDistributionConfig::$totalPoints of type int
```

This occurred in the `PointsDistributionConfig.php` file at line 45, where the component was trying to assign `$game->total_points` to `$this->totalPoints`. The issue was that when a Game object was passed to the component that didn't have the `total_points` property set (which can happen in tests), it was trying to assign null to a property that's typed as `int`, which PHP doesn't allow.

## Fix
Added null coalescing operators (`??`) to the property assignments in the `mount` method of the `PointsDistributionConfig` class:

```php
// Before
$this->totalPoints = $game->total_points;
$this->pointsRecipients = $game->points_recipients;

// After
$this->totalPoints = $game->total_points ?? $this->totalPoints;
$this->pointsRecipients = $game->points_recipients ?? $this->pointsRecipients;
```

This ensures that when a Game object is passed without the required properties, we fall back to the default values instead of trying to assign null to typed properties.

## Verification
All tests now pass successfully.
