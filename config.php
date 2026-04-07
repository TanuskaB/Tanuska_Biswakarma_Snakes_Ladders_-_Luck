<?php
// config.php - Configuration file for Snakes and Ladders game

// Difficulty levels
$difficulties = [
    'beginner' => ['snakes' => 3, 'ladders' => 3],
    'standard' => ['snakes' => 6, 'ladders' => 5],
    'expert' => ['snakes' => 9, 'ladders' => 4]
];

// Board layouts for each difficulty
$boards = [
    'beginner' => [
        'snakes' => [
            16 => 6,
            47 => 26,
            49 => 11
        ],
        'ladders' => [
            1 => 38,
            4 => 14,
            9 => 31
        ]
    ],
    'standard' => [
        'snakes' => [
            16 => 6,
            47 => 26,
            49 => 11,
            56 => 53,
            62 => 19,
            64 => 60
        ],
        'ladders' => [
            1 => 38,
            4 => 14,
            9 => 31,
            21 => 42,
            28 => 84
        ]
    ],
    'expert' => [
        'snakes' => [
            16 => 6,
            47 => 26,
            49 => 11,
            56 => 53,
            62 => 19,
            64 => 60,
            87 => 24,
            93 => 73,
            95 => 75
        ],
        'ladders' => [
            1 => 38,
            4 => 14,
            9 => 31,
            21 => 42
        ]
    ]
];

// Event cells for AI enhancement
$event_cells = [
    15 => ['type' => 'bonus', 'move' => 5, 'msg' => 'Found a shortcut!'],
    42 => ['type' => 'penalty', 'move' => -3, 'msg' => 'Slipped on a banana peel!'],
    67 => ['type' => 'skip', 'move' => 0, 'msg' => 'Caught in a trap! Skip next turn.'],
    80 => ['type' => 'warp', 'move' => 20, 'msg' => 'Magical portal! Warp forward.']
];

// Bonus tiles
$bonus_tiles = [
    25 => 'extra_roll',
    50 => 'skip_turn',
    75 => 'mystery_boost'
];

// Narrator messages templates
$narrator_templates = [
    'snake' => "🐍 The serpent hisses — you're dragged back to cell {to}!",
    'ladder' => "🪜 Climb the ladder — up to cell {to}!",
    'bonus' => "🎉 {msg} Move {move} spaces!",
    'penalty' => "😞 {msg} Move back {move} spaces!",
    'skip' => "⏸️ {msg}",
    'warp' => "✨ {msg} Move to cell {to}!",
    'extra_roll' => "🎲 Lucky tile! Roll again!",
    'skip_turn' => "😴 Bonus tile! Skip opponent's turn.",
    'mystery_boost' => "❓ Mystery boost! Random effect applied."
];

// Start session if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>