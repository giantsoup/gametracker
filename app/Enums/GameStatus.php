<?php

namespace App\Enums;

enum GameStatus: string
{
    case Ready = 'ready';
    case Playing = 'playing';
    case Finished = 'finished';
    case Background = 'background';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Ready => 'Ready to Start',
            self::Playing => 'Currently Playing',
            self::Finished => 'Finished',
            self::Background => 'Background Game',
        };
    }

    /**
     * Get a color class for the status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Ready => 'yellow',
            self::Playing => 'blue',
            self::Finished => 'green',
            self::Background => 'zinc',
        };
    }

    /**
     * Check if a transition from the current status to the target status is valid.
     *
     * This enforces the linear progression: Ready → Playing → Finished
     * Background games can be set directly without progression.
     */
    public function canTransitionTo(self $targetStatus): bool
    {
        // Background games have special rules
        if ($this === self::Background || $targetStatus === self::Background) {
            return true;
        }

        return match ($this) {
            self::Ready => $targetStatus === self::Playing,
            self::Playing => $targetStatus === self::Finished,
            self::Finished => false, // Cannot transition from Finished
        };
    }

    /**
     * Get the next status in the linear progression.
     */
    public function getNextStatus(): ?self
    {
        return match ($this) {
            self::Ready => self::Playing,
            self::Playing => self::Finished,
            self::Finished, self::Background => null,
        };
    }
}
