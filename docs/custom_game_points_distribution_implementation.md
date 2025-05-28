# Custom Game Points Distribution Implementation

## Overview
This document describes the implementation of the custom game points distribution feature in the GameTracker application. This feature allows game owners or admins to determine how points are distributed for each game.

## Database Changes
A migration was created to add the following fields to the `games` table:
- `total_points` (integer) - The total number of points that a game can distribute
- `points_recipients` (integer) - The number of players who will receive points
- `points_distribution` (json) - Custom points distribution per placement

## Model Changes
The `Game` model was updated to include:
- New fillable fields for the points distribution configuration
- Proper casting of the new fields
- Helper methods for working with the points distribution:
  - `getDefaultPointsDistribution()` - Returns the default points distribution
  - `getPointsForPlacement()` - Gets the points for a specific placement
  - `generatePointsDistribution()` - Generates a points distribution based on the total points and number of recipients

## Component Changes
### AssignPoints Component
The `AssignPoints` component was updated to use the custom points distribution configuration from the Game model instead of the hardcoded values.

### PointsDistributionConfig Component
A new Livewire component `PointsDistributionConfig` was created to allow configuring the points distribution. This component:
- Allows setting the total points and number of recipients
- Supports both auto-generated and custom distributions
- Provides validation to ensure the sum of points matches the total
- Emits events when the configuration changes

### CreateGameForm Component
The `CreateGameForm` component was updated to:
- Include the PointsDistributionConfig component
- Store the points distribution configuration
- Save the configuration when creating a new game

## User Interface
The game creation form now includes a section for configuring the points distribution, with:
- Input fields for setting the total points and number of recipients
- A toggle between auto-generated and custom distributions
- A table showing the points distribution
- Validation to ensure the sum of points matches the total

## Backwards Compatibility
Existing games will continue to use the default points distribution (5, 3, 1, 0) unless explicitly changed. The migration sets default values that match the current implementation.

## Future Improvements
- Add support for editing the points distribution of existing games
- Add more validation to ensure the points distribution is valid
- Add more UI feedback when the points distribution is invalid
