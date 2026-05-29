<?php
// Groepeer matches per ronde
$matchesByRonde = [];
foreach ($playerMatches as $match) {
    $ronde = $match['ronde'];
    if (!isset($matchesByRonde[$ronde])) {
        $matchesByRonde[$ronde] = [];
    }
    $matchesByRonde[$ronde][] = $match;
}
?>

<div class="pageShell">
    <h1><?php echo htmlspecialchars($playerName); ?></h1>
    <h2>Jouw Wedstrijdschema</h2>

    <?php if (empty($playerMatches)): ?>
        <p>Geen wedstrijden gepland.</p>
    <?php else: ?>
        <!-- Desktop: Alle spelers kaarten -->
        <div class="scheduleGrid" style="display: none;">
            <?php foreach ($playerMatches as $match): ?>
                <article class="pageCard">
                    <h3><?php echo htmlspecialchars($playerName); ?></h3>
                    <div class="matchColumn">
                        <?php
                        // Bepaal tegenstanders
                        $isTeam1Speler1 = $match['team1_speler1'] == $playerId;
                        $isTeam1Speler2 = $match['team1_speler2'] == $playerId;
                        $isTeam2Speler1 = $match['team2_speler1'] == $playerId;
                        $isTeam2Speler2 = $match['team2_speler2'] == $playerId;

                        if ($isTeam1Speler1 || $isTeam1Speler2) {
                            $partner = ($isTeam1Speler1) ? $match['team1_speler2_naam'] : $match['team1_speler1_naam'];
                            $opp1 = $match['team2_speler1_naam'];
                            $opp2 = $match['team2_speler2_naam'];
                        } else {
                            $partner = ($isTeam2Speler1) ? $match['team2_speler2_naam'] : $match['team2_speler1_naam'];
                            $opp1 = $match['team1_speler1_naam'];
                            $opp2 = $match['team1_speler2_naam'];
                        }

                        $time = getRondeTime($match['ronde']);
                        ?>
                        <div class="matchCard">
                            <strong>Ronde <?php echo $match['ronde']; ?> - Baan <?php echo $match['baan']; ?></strong>
                            <span><?php echo $time; ?></span>
                            <p><small>Partner: <?php echo htmlspecialchars($partner); ?></small></p>
                            <p><small>Tegenstanders: <?php echo htmlspecialchars($opp1 . ' & ' . $opp2); ?></small></p>
                            <p class="matchStatus">Status: <?php echo ucfirst($match['status']); ?></p>
                            <?php if ($match['status'] === 'afgerond'): ?>
                                <p class="matchScore">Score: <?php echo $match['team1_score']; ?> - <?php echo $match['team2_score']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Mobile: Persoonlijk schema -->
        <div class="mobileOwnSchedule">
            <h3>Jouw Wedstrijden</h3>
            <div class="matchColumn">
                <?php foreach ($matchesByRonde as $ronde => $matches): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4>Ronde <?php echo $ronde; ?></h4>
                        <?php foreach ($matches as $match): ?>
                            <?php
                            // Bepaal team
                            $isTeam1Speler1 = $match['team1_speler1'] == $playerId;
                            $isTeam1Speler2 = $match['team1_speler2'] == $playerId;
                            
                            if ($isTeam1Speler1 || $isTeam1Speler2) {
                                $partner = ($isTeam1Speler1) ? $match['team1_speler2_naam'] : $match['team1_speler1_naam'];
                                $opp1 = $match['team2_speler1_naam'];
                                $opp2 = $match['team2_speler2_naam'];
                            } else {
                                $partner = ($match['team2_speler1'] == $playerId) ? $match['team2_speler2_naam'] : $match['team2_speler1_naam'];
                                $opp1 = $match['team1_speler1_naam'];
                                $opp2 = $match['team1_speler2_naam'];
                            }

                            $time = getRondeTime($match['ronde']);
                            ?>
                            <div class="matchCard" style="margin-top: 0.75rem;">
                                <strong>Baan <?php echo $match['baan']; ?> • <?php echo $time; ?></strong>
                                <p style="margin: 0.5rem 0 0 0;"><small>Partner: <strong><?php echo htmlspecialchars($partner); ?></strong></small></p>
                                <p style="margin: 0.25rem 0;"><small>vs <?php echo htmlspecialchars($opp1 . ' & ' . $opp2); ?></small></p>
                                <?php if ($match['status'] === 'afgerond'): ?>
                                    <p style="margin: 0.25rem 0; color: #28a745;"><small>✓ Gespeeld</small></p>
                                <?php else: ?>
                                    <p style="margin: 0.25rem 0; color: #ffc107;"><small>● Gepland</small></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php endif; ?>
</div>
