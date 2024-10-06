<?php

// Function to transpose a single note
function transposeNote($note, $semitones) {
    // Convert the note into an absolute semitone number
    $absoluteSemitone = $note[0] * 12 + $note[1] + $semitones;

    // Convert back the absolute semitone number into octave and note
    $newOctave = intdiv($absoluteSemitone, 12);
    $newNote = $absoluteSemitone % 12;

    // If the newNote is negative, adjust it to fit within the correct range
    if ($newNote <= 0) {
        $newNote += 12;
        $newOctave--;
    }

    return [$newOctave, $newNote];
}

// Function to check if the note is within the valid piano range
function isNoteInRange($note) {
    $firstNote = [-3, 10];
    $lastNote = [5, 1];

    // Convert the notes to absolute semitones for comparison
    $absoluteSemitone = $note[0] * 12 + $note[1];
    $firstSemitone = $firstNote[0] * 12 + $firstNote[1];
    $lastSemitone = $lastNote[0] * 12 + $lastNote[1];

    return $absoluteSemitone >= $firstSemitone && $absoluteSemitone <= $lastSemitone;
}

// Function to transpose the entire collection of notes
function transposeNotes($notes, $semitones) {
    $transposedNotes = [];

    foreach ($notes as $note) {
        // Transpose the note
        $transposedNote = transposeNote($note, $semitones);

        // Check if the transposed note is within the valid piano range
        if (!isNoteInRange($transposedNote)) {
            throw new Exception("Error: At least one transposed note is out of the keyboard range.");
        }

        $transposedNotes[] = $transposedNote;
    }

    return $transposedNotes;
}

// Main function to handle input/output
function main($inputFile, $outputFile, $semitones) {
    // Read the input file
    $inputData = file_get_contents($inputFile);
    $notes = json_decode($inputData, true);

    if ($notes === null) {
        throw new Exception("Error: Invalid input JSON format.");
    }

    // Transpose the notes
    $transposedNotes = transposeNotes($notes, $semitones);

    // Write the transposed notes to the output file
    file_put_contents($outputFile, json_encode($transposedNotes));

    echo "Transposition completed. Check the output file: $outputFile\n";
}

// Command line execution
if ($argc < 4) {
    echo "Usage: php transpose.php <inputFile> <outputFile> <semitones>\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2];
$semitones = (int)$argv[3];

try {
    main($inputFile, $outputFile, $semitones);
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

?>
