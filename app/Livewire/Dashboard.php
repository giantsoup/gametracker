<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Using traditional Blade layout instead of attribute
// #[Layout('components.layouts.public')]
class Dashboard extends Component
{
    public $activeLayout = 1; // Default layout

    public $displayType = 'default'; // Can be 'projection', 'mobile', or 'default'

    public $testCounter = 0; // Simple counter for testing

    public function mount(Request $request)
    {
        // Check if layout is specified in query parameters
        if ($request->has('layout')) {
            $layout = (int) $request->input('layout');
            if ($layout >= 1 && $layout <= 3) {
                $this->activeLayout = $layout;
            }
        }

        // Check if display type is specified in query parameters
        if ($request->has('display')) {
            $display = $request->input('display');
            if (in_array($display, ['default', 'projection', 'mobile'])) {
                $this->displayType = $display;

                // Set appropriate default layout for the display type if layout is not specified
                if (! $request->has('layout')) {
                    if ($display === 'mobile') {
                        $this->activeLayout = 3; // Card grid is more mobile-friendly
                    } elseif ($display === 'projection') {
                        $this->activeLayout = 1; // Focus layout is better for projection
                    }
                }

                // Skip auto-detection since display type is explicitly set
                goto skip_detection;
            }
        }

        // Detect device type based on user agent
        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);

        // Set display type based on detection
        if ($isMobile) {
            $this->displayType = 'mobile';
            // For mobile, use layout 3 (card grid) by default as it's more mobile-friendly
            if (! $request->has('layout')) {
                $this->activeLayout = 3;
            }
        } else {
            // For non-mobile, check if it might be a projection display (based on query param for demo)
            if ($request->has('projection')) {
                $this->displayType = 'projection';
                // For projection, use layout 1 by default as it has larger text and clearer visuals
                if (! $request->has('layout')) {
                    $this->activeLayout = 1;
                }
            }
        }

        skip_detection:

        // Log the active layout and display type for debugging
        \Log::info('Dashboard mounted', [
            'activeLayout' => $this->activeLayout,
            'displayType' => $this->displayType,
            'layout_param' => $request->input('layout'),
            'display_param' => $request->input('display'),
        ]);
    }

    public function render()
    {
        // Get the current active event
        $activeEvent = Event::active()->first();

        // Get current active game if there is an active event
        $currentGame = null;
        $gameDuration = null;
        $finishedGames = collect();
        $upcomingGames = collect();

        if ($activeEvent) {
            // Get the current active game (most recent)
            $currentGame = $activeEvent->games()->latest()->first();

            if ($currentGame && $activeEvent->started_at) {
                // Calculate how long the current game has been going on
                $gameDuration = now()->diffForHumans($activeEvent->started_at, true);
            }

            // Get finished games for the current event (excluding the current game)
            $finishedGames = $activeEvent->games()
                ->when($currentGame, function ($query) use ($currentGame) {
                    return $query->where('id', '!=', $currentGame->id);
                })
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Get upcoming games for the current event
            // Since there's no specific field to determine if a game is upcoming,
            // we'll assume games without a created_at timestamp are upcoming
            $upcomingGames = $activeEvent->games()
                ->whereNull('created_at')
                ->orderBy('id', 'asc')
                ->take(5)
                ->get();
        }

        return view('livewire.dashboard', [
            'activeEvent' => $activeEvent,
            'currentGame' => $currentGame,
            'gameDuration' => $gameDuration,
            'finishedGames' => $finishedGames,
            'upcomingGames' => $upcomingGames,
            'activeLayout' => $this->activeLayout,
            'displayType' => $this->displayType,
        ]);
    }

    public function switchLayout($layout)
    {
        // Log the current layout and the new layout for debugging
        \Log::info('Switching layout', [
            'from' => $this->activeLayout,
            'to' => $layout,
        ]);

        $this->activeLayout = $layout;
    }

    public function switchDisplayType($type)
    {
        if (in_array($type, ['default', 'projection', 'mobile'])) {
            $this->displayType = $type;

            // Set appropriate default layout for the display type
            if ($type === 'mobile') {
                $this->activeLayout = 3; // Card grid is more mobile-friendly
            } elseif ($type === 'projection') {
                $this->activeLayout = 1; // Focus layout is better for projection
            }
        }
    }

    public function incrementTestCounter()
    {
        $this->testCounter++;
        \Log::info('Test counter incremented', ['counter' => $this->testCounter]);
    }
}
