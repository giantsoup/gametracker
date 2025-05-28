# Demo Seeder Refactoring for Custom Points Distribution

## Overview
This document describes the changes made to the DemoDataSeeder to support the new custom game points distribution system. The seeder now creates games with various points distribution configurations and assigns points to players based on these configurations.

## Changes Made

### 1. Updated Game Creation
The `createDemoGamesAndPlayers` method was modified to include the new points distribution properties when creating games:

- Added a variety of points distribution configurations:
  - Default distribution (5, 3, 1)
  - Custom distribution with more recipients (7, 5, 3, 1)
  - Custom distribution with different point values (10, 5, 2)
  - Custom distribution with equal points (3, 3, 3)

- For each game, the seeder now:
  - Randomly selects one of these distributions
  - Calculates the total points and number of recipients
  - Sets these values along with the distribution when creating the game

```php
// Create different points distributions for variety
$pointsDistributions = [
    // Default distribution (5, 3, 1)
    null,
    // Custom distribution with more recipients
    [1 => 7, 2 => 5, 3 => 3, 4 => 1],
    // Custom distribution with different point values
    [1 => 10, 2 => 5, 3 => 2],
    // Custom distribution with equal points
    [1 => 3, 2 => 3, 3 => 3],
];

// Randomly select a points distribution or use null for default
$pointsDistribution = $pointsDistributions[array_rand($pointsDistributions)];

// Calculate total points and recipients based on the distribution
$totalPoints = $pointsDistribution ? array_sum($pointsDistribution) : 9; // Default is 9 (5+3+1)
$pointsRecipients = $pointsDistribution ? count($pointsDistribution) : 3; // Default is 3

$game = Game::updateOrCreate(
    [
        'name' => "Game $i for $event->name",
        'event_id' => $event->id,
    ],
    [
        'duration' => rand(30, 180), // 30 minutes to 3 hours
        'total_points' => $totalPoints,
        'points_recipients' => $pointsRecipients,
        'points_distribution' => $pointsDistribution,
    ]
);
```

### 2. Updated Points Assignment
The `createDemoGamePoints` method was modified to use the game's points distribution when assigning points to players:

- Instead of using hardcoded values (5 for 1st place, 3 for 2nd place, 1 for 3rd place), it now uses the `getPointsForPlacement` method from the Game model
- This method returns the points based on the game's custom points distribution or the default distribution if none is set

```php
// Determine points based on placement using the game's points distribution
$points = 0;
if ($placement !== null) {
    $points = $game->getPointsForPlacement($placement);
}
```

### 3. Updated Placement Assignment
The code that assigns placements to players was updated to use the game's `points_recipients` value:

- Instead of hardcoding to only assign placements to the first 3 players, it now uses the game's `points_recipients` value
- If the `points_recipients` value is not set, it defaults to 3 to maintain backward compatibility

```php
// Only assign placements to players based on the game's points_recipients
$maxRecipients = $game->points_recipients ?? 3; // Default to 3 if not set
$placement = ($index < $maxRecipients) ? $index + 1 : null;
```

## Testing
The refactored seeder was tested by running:

```bash
php artisan db:seed --class=DemoDataSeeder
```

The seeder completed successfully, creating demo data with the new points distribution system.

## Conclusion
The DemoDataSeeder now fully supports the new custom game points distribution system. It creates games with various points distribution configurations and assigns points to players based on these configurations, providing a good demonstration of the system's capabilities.
