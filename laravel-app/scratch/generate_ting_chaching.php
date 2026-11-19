<?php

// High-fidelity PCM WAV Generator for Modern Digital Cashier: "Ting!" + "Cha-Ching!"
$dir = 'c:/Users/advan/Documents/Skripsi kating/laravel-app/public/audio';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$sampleRate = 44100;
$duration = 1.35; // 1.35 seconds
$numSamples = (int)($sampleRate * $duration);

$data = '';

for ($i = 0; $i < $numSamples; $i++) {
    $t = $i / $sampleRate;
    $sample = 0.0;

    // =========================================================================
    // PART 1: The Modern Digital POS "Ting!" (Crystalline Glass/Bell strike)
    // Starts at t = 0.0s, high resonant pure tones
    // =========================================================================
    if ($t >= 0.0 && $t < 0.9) {
        $decay = exp(-9.0 * $t);
        // Primary Bell Note: C7 (2093 Hz) + E7 (2637 Hz) + G7 (3135 Hz) for pure luxury chime
        $sample += 0.45 * sin(2 * M_PI * 2093.00 * $t) * $decay;
        $sample += 0.25 * sin(2 * M_PI * 2637.02 * $t) * $decay;
        $sample += 0.15 * sin(2 * M_PI * 3135.96 * $t) * $decay;
        $sample += 0.10 * sin(2 * M_PI * 4186.01 * $t) * exp(-14.0 * $t); // Ultra-high shimmer
    }

    // Secondary Warm Chord Note: C6 (1046.5 Hz) for full-bodied tone
    if ($t >= 0.02 && $t < 1.0) {
        $decayWarm = exp(-6.5 * ($t - 0.02));
        $sample += 0.35 * sin(2 * M_PI * 1046.50 * ($t - 0.02)) * $decayWarm;
        $sample += 0.15 * sin(2 * M_PI * 1318.51 * ($t - 0.02)) * $decayWarm;
    }

    // =========================================================================
    // PART 2: The Iconic "Cha-Ching!" (Register drawer click + metallic coins)
    // Kicks in at t = 0.14s right after the bell "ting!"
    // =========================================================================
    // Metallic register mechanical latch / slide (t = 0.14s)
    if ($t >= 0.14 && $t < 0.35) {
        $tRel = $t - 0.14;
        $latchDecay = exp(-22.0 * $tRel);
        // Resonant metallic click
        $sample += 0.28 * sin(2 * M_PI * 780 * $tRel) * $latchDecay;
        $sample += 0.22 * sin(2 * M_PI * 1420 * $tRel) * $latchDecay;
    }

    // Brass Coin Jingle Clink 1 (t = 0.18s)
    if ($t >= 0.18 && $t < 0.65) {
        $tRel = $t - 0.18;
        $coinDecay = exp(-14.0 * $tRel);
        $sample += 0.32 * sin(2 * M_PI * 3850 * $tRel) * $coinDecay;
        $sample += 0.20 * sin(2 * M_PI * 4920 * $tRel) * $coinDecay;
        $sample += 0.15 * sin(2 * M_PI * 6200 * $tRel) * $coinDecay;
    }

    // Brass Coin Jingle Clink 2 (t = 0.25s)
    if ($t >= 0.25 && $t < 0.85) {
        $tRel = $t - 0.25;
        $coinDecay2 = exp(-12.0 * $tRel);
        $sample += 0.35 * sin(2 * M_PI * 4200 * $tRel) * $coinDecay2;
        $sample += 0.25 * sin(2 * M_PI * 5400 * $tRel) * $coinDecay2;
        $sample += 0.18 * sin(2 * M_PI * 7100 * $tRel) * $coinDecay2;
    }

    // Soft final coin settle sparkle (t = 0.35s)
    if ($t >= 0.35 && $t < 0.95) {
        $tRel = $t - 0.35;
        $coinDecay3 = exp(-10.0 * $tRel);
        $sample += 0.25 * sin(2 * M_PI * 4600 * $tRel) * $coinDecay3;
        $sample += 0.18 * sin(2 * M_PI * 5800 * $tRel) * $coinDecay3;
    }

    // Clamp and pack to 16-bit PCM integer
    $sample = max(-0.98, min(0.98, $sample));
    $intSample = (int)($sample * 32767);
    $data .= pack('v', $intSample);
}

// Format WAV header
$header = 'RIFF';
$header .= pack('V', 36 + strlen($data));
$header .= 'WAVE';
$header .= 'fmt ';
$header .= pack('V', 16); // 16 for PCM
$header .= pack('v', 1);  // 1 for PCM
$header .= pack('v', 1);  // 1 Channel (Mono)
$header .= pack('V', $sampleRate);
$header .= pack('V', $sampleRate * 2);
$header .= pack('v', 2);
$header .= pack('v', 16);
$header .= 'data';
$header .= pack('V', strlen($data));

file_put_contents("$dir/payment-success.wav", $header . $data);
echo "Generated modern digital POS 'Ting!' + 'Cha-Ching!' WAV (" . (strlen($header) + strlen($data)) . " bytes)\n";
