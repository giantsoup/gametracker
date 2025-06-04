# Event Runner: System Overview

## Purpose and Problem Space

The Event Runner is a real-time management interface for tabletop gaming events. It addresses the complex problem of organizing and tracking multiple games occurring simultaneously or sequentially during a gaming event (like a convention, tournament, or game night).

Key challenges it solves:
- Managing the lifecycle of multiple games (ready → playing → finished)
- Tracking which players are participating in which games
- Organizing the queue of upcoming games
- Recording game results and distributing points
- Providing real-time status updates across the event

## Core Domain Models

### Event
- Represents a gaming gathering with a defined timeframe
- Has start and end dates/times
- Contains multiple games and players
- Can be in different states: upcoming, ongoing, or past

### Game
- Represents a single play session of a tabletop game
- Has a status (Ready, Playing, Finished, Background)
- Has a display order (for queuing)
- Tracks duration
- Contains players and owners (game masters)
- Records points distribution

### Player
- Represents a user participating in a specific event
- Has a nickname within the event context
- Can join and leave games
- Earns points from games

### GamePoint
- Records points earned by players in games
- Tracks placement (1st, 2nd, 3rd, etc.)
- Records who assigned the points and when

## Key Relationships

- An Event has many Games and Players
- A Game belongs to an Event
- A Game has many Players (through a pivot table)
- A Game has many Owners (users who manage the game)
- A Player belongs to an Event and a User
- GamePoints connect Games and Players with point values

## Core Functionality

1. **Game Status Management**
   - Moving games between statuses (Ready → Playing → Finished)
   - Background games that run throughout the event

2. **Game Queue Management**
   - Prioritizing which games should start next
   - Reordering the queue of ready games

3. **Player Participation Tracking**
   - Adding/removing players from games
   - Tracking which players are currently playing

4. **Points Distribution**
   - Recording game results
   - Assigning points to players based on placement
   - Tracking overall player standings

5. **Real-time Updates**
   - Showing current game statuses
   - Displaying notifications when games start/end

## Current UI Organization

The interface is organized into four main sections:
1. **Currently Playing** - Games in progress (largest section)
2. **Ready to Start** - Queue of games ready to begin
3. **Finished** - Recently completed games with results
4. **Background Games** - Ongoing games that run throughout the event

Each game is displayed as a card with:
- Game title and duration
- Player count and list of participants
- Game owners/managers
- Status controls
- Points assignment (for finished games)

The UI allows event organizers to:
- Switch between different events
- Start and finish games
- Reorder the game queue
- Manage player participation
- Record game results

This interface is designed to be used by event organizers on various devices (mobile, tablet, desktop) during live gaming events.
