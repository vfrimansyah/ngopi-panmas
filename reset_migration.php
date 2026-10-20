<?php
/**
 * Reset migration data to allow a clean re-run.
 * Deletes all members, children, spouses, and related data.
 * Resets staging rows to 'pending' status.
 */
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Resetting migration data...\n";

    // Disable FK checks temporarily
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Delete all family_match_candidates
    $stmt = $pdo->exec("DELETE FROM family_match_candidates");
    echo "Deleted family_match_candidates: {$stmt} rows\n";

    // Delete all member_organizations
    $stmt = $pdo->exec("DELETE FROM member_organizations");
    echo "Deleted member_organizations: {$stmt} rows\n";

    // Delete all member_program_interests
    $stmt = $pdo->exec("DELETE FROM member_program_interests");
    echo "Deleted member_program_interests: {$stmt} rows\n";

    // Delete all member_political_profiles
    $stmt = $pdo->exec("DELETE FROM member_political_profiles");
    echo "Deleted member_political_profiles: {$stmt} rows\n";

    // Delete all member_contributions
    $stmt = $pdo->exec("DELETE FROM member_contributions");
    echo "Deleted member_contributions: {$stmt} rows\n";

    // Delete all member_donation_preferences
    $stmt = $pdo->exec("DELETE FROM member_donation_preferences");
    echo "Deleted member_donation_preferences: {$stmt} rows\n";

    // Delete all members
    $stmt = $pdo->exec("DELETE FROM members");
    echo "Deleted members: {$stmt} rows\n";

    // Reset staging to pending
    $stmt = $pdo->exec("UPDATE import_staging SET import_status = 'pending', created_member_id = NULL, error_message = NULL");
    echo "Reset staging rows: {$stmt} rows\n";

    // Reset AUTO_INCREMENT
    $pdo->exec("ALTER TABLE members AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE family_match_candidates AUTO_INCREMENT = 1");

    // Re-enable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\nReset complete! You can now re-run: php spark migrate:legacy-data\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
