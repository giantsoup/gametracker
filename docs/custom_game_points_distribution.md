# Custom Game Points Distribution Requirements

## Overview
This document outlines the requirements for implementing custom game points distribution in the GameTracker application. The feature will allow game owners or admins to determine how points are distributed for each game.

## Current Implementation
Currently, the system has a hardcoded points distribution:
- 1st place: 5 points
- 2nd place: 3 points
- 3rd place: 1 point
- 4th place and below: 0 points

## Requirements

### 1. Configurable Total Points
- Game owners/admins should be able to set the total number of points that a game can distribute
- This should be configurable per game
- Default value should match the current implementation (9 points total)

### 2. Configurable Number of Players Receiving Points
- Game owners/admins should be able to set how many players will receive points
- This should be configurable per game
- Default value should match the current implementation (3 players)

### 3. Configurable Points per Placement
- Game owners/admins should be able to set how many points each player will receive based on their placement
- This should be configurable per game
- Default values should match the current implementation (5, 3, 1, 0)

### 4. User Interface
- Add UI elements to the game creation/edit forms to configure points distribution
- Provide a preview of how points will be distributed
- Allow for easy adjustment of point values

### 5. Validation
- Ensure the sum of points matches the configured total
- Validate that the number of players receiving points is valid (not more than the total players in the game)
- Prevent negative point values

### 6. Backwards Compatibility
- Existing games should continue to use the current points distribution unless explicitly changed
- Provide migration for existing games to use the new configurable system with default values

## Technical Implementation
The implementation will require:
1. Database schema changes to store the custom points distribution configuration
2. Updates to the Game model to include the new configuration fields
3. Modifications to the AssignPoints component to use the custom configuration
4. New UI components for configuring the points distribution
5. Validation logic to ensure the configuration is valid
