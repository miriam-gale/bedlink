<?php
// includes/state.php (Yaw) — which bed status / transfer stage can follow which.
// James's beds.php, shift.php and v1/beds.php use canMoveBed().

const BED_MOVES = [
    'free'     => ['occupied', 'cleaning'],
    'occupied' => ['cleaning', 'free'],
    'cleaning' => ['free', 'occupied'],
    'held'     => [],                        // people can never move a held bed
];

function canMoveBed(string $from, string $to, string $actor): bool {
    if ($actor === 'system') return true;    // only hold.php, transfer.php and runExpiry() pass 'system'
    return in_array($to, BED_MOVES[$from] ?? [], true);
}

const TRANSFER_MOVES = [
    'pending'    => ['accepted', 'rejected', 'in_transit', 'expired', 'cancelled'],
    'accepted'   => ['in_transit', 'arrived', 'expired', 'cancelled', 'turned_away'],
    'in_transit' => ['arrived', 'cancelled', 'turned_away'],
];                                           // arrived, rejected, expired, cancelled, turned_away are final

function canMoveTransfer(string $from, string $to): bool {
    return in_array($to, TRANSFER_MOVES[$from] ?? [], true);
}
