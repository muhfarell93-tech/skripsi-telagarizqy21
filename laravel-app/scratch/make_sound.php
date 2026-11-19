<?php

// Script to generate a 16-bit PCM WAV payment chime with coin jingles
$dir = 'c:/Users/advan/Documents/Skripsi kating/laravel-app/public/audio';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$sampleRate = 44100;
$duration = 1.6; // 1.6 seconds
$numSamples = (int)($sampleRate * $duration);

$data = '';

for ($i = 0; $i < $numSamples; $i++) {
    $t = $i / $sampleRate;
    $sample = 0.0;

    // Chime 1: E5 (659.25 Hz) starts at t = 0.0s
    if ($t >= 0.0 && $t < 0.8) {
        $decay = exp(-8 * ($t - 0.0));
        $sample += 0.35 * sin(2 * M_PI * 659.25 * $t) * $decay;
        $sample += 0.15 * sin(2 * M_PI * 1318.5 * $t) * $decay; // Harmonic
    }

    // Chime 2: G#5 (830.61 Hz) starts at t = 0.1s
    if ($t >= 0.1 && $t < 0.9) {
        $decay = exp(-8 * ($t - 0.1));
        $sample += 0.4 * sin(2 * M_PI * 830.61 * ($t - 0.1)) * $decay;
        $sample += 0.15 * sin(2 * M_PI * 1661.22 * ($t - 0.1)) * $decay;
    }

    // Chime 3: B5 (987.77 Hz) starts at t = 0.2s
    if ($t >= 0.2 && $t < 1.0) {
        $decay = exp(-7 * ($t - 0.2));
        $sample += 0.45 * sin(2 * M_PI * 987.77 * ($t - 0.2)) * $decay;
        $sample += 0.2 * sin(2 * M_PI * 1975.54 * ($t - 0.2)) * $decay;
    }

    // Chime 4: High E6 (1318.51 Hz) sparkle chord starts at t = 0.3s
    if ($t >= 0.3 && $t < 1.5) {
        $decay = exp(-4.5 * ($t - 0.3));
        $sample += 0.5 * sin(2 * M_PI * 1318.51 * ($t - 0.3)) * $decay;
        $sample += 0.25 * sin(2 * M_PI * 2637.02 * ($t - 0.3)) * $decay;
        $sample += 0.1 * sin(2 * M_PI * 3955.5 * ($t - 0.3)) * $decay; // High sparkle
    }

    // Register "Cha-ching" metallic jingle around t = 0.32s
    if ($t >= 0.32 && $t < 0.7) {
        $jingleDecay = exp(-12 * ($t - 0.32));
        // High frequency metallic clang
        $metallic = sin(2 * M_PI * 3400 * $t) + sin(2 * M_PI * 4800 * $t) + sin(2 * M_PI * 6200 * $t);
        $sample += 0.18 * $metallic * $jingleDecay;
    }

    // Clamp
    $sample = max(-1.0, min(1.0, $sample));
    $intSample = (int)($sample * 32767);
    $data .= pack('v', $intSample);
}

// WAV Header
$header = 'RIFF';
$header .= pack('V', 36 + strlen($data));
$header .= 'WAVE';
$header .= 'fmt ';
$header .= pack('V', 16); // Subchunk1Size (16 for PCM)
$header .= pack('v', 1);  // AudioFormat (1 for PCM)
$header .= pack('v', 1);  // NumChannels (1 = Mono)
$header .= pack('V', $sampleRate);
$header .= pack('V', $sampleRate * 2); // ByteRate
$header .= pack('v', 2);  // BlockAlign
$header .= pack('v', 16); // BitsPerSample
$header .= 'data';
$header .= pack('V', strlen($data));

file_put_contents("$dir/payment-success.wav", $header . $data);
echo "Successfully generated $dir/payment-success.wav (" . (strlen($header) + strlen($data)) . " bytes)\n";
