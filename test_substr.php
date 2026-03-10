<?php
require 'vendor/autoload.php';
$text = "240- إنّ الشَّيْطانَ يأْتِي أحَدَكُمْ فيقولُ منْ خَلَقَكَ فيقولُ اللَّهُ فيقولُ فَمَنْ خَلَقَ اللَّهَ فإذا وَجَدَ أحدُكُمْ ذلِكَ فَلْيَقُلْ آمنْتُ باللَّهِ وَرُسْلِهِ فإن ذلِكَ يَذْهَبُ عَنْهُ. [1657] (صحيح) (ابن أبي الدنيا في مكايد الشيطان) عن عائشة.";

if (preg_match('/\[1657\]/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
    $offset = $matches[0][1];
    echo "Offset: $offset\n";
    $cleanedText = substr($text, 0, $offset);
    echo "Clean text (substr): $cleanedText\n";

    // mb_substr string length
    $cleanedTextMb = mb_strcut($text, 0, $offset);
    echo "Clean text (mb_strcut): $cleanedTextMb\n";
}
