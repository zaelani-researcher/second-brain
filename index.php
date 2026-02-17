<?php
// ORCID iD
$orcid_id = '0009-0002-1054-3167';
$url = "https://pub.orcid.org/v3.0/$orcid_id/record";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

// Execute and close
$response = curl_exec($ch);
curl_close($ch);

// Parse JSON
$data = json_decode($response, true);

// Extract data
$person = $data['person'] ?? null;
$activities = $data['activities-summary'] ?? null;

$name = $person['name']['credit-name']['value'] ??
        (($person['name']['given-names']['value'] ?? '') . ' ' . ($person['name']['family-name']['value'] ?? ''));
$name = trim($name);
$biography = $person['biography']['content'] ?? 'No biography available.';

$education_groups = $activities['educations']['affiliation-group'] ?? [];
$educations = [];
foreach ($education_groups as $group) {
    foreach ($group['summaries'] as $summary) {
        if (isset($summary['education-summary'])) {
            $educations[] = $summary['education-summary'];
        }
    }
}

$employment_groups = $activities['employments']['affiliation-group'] ?? [];
$employments = [];
foreach ($employment_groups as $group) {
    foreach ($group['summaries'] as $summary) {
        if (isset($summary['employment-summary'])) {
            $employments[] = $summary['employment-summary'];
        }
    }
}

$works = $activities['works']['group'] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($name); ?> - Curriculum Vitae</title>
    <style>
        /* Reset and Base Styles */
        :root {
            --bg-color: #ffffff;
            --text-color: #333333;
            --secondary-text: #666666;
            --accent-color: #000000;
            --border-color: #e0e0e0;
            --font-main: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            --font-serif: "Georgia", "Times New Roman", Times, serif;
            --max-width: 800px;
            --spacing: 1.5rem;
        }

        * {
            box_sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: var(--spacing);
        }

        .container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 2rem 0;
        }

        /* Typography */
        h1, h2, h3 {
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        h2 {
            font-size: 0.9rem;
            margin-top: 2.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--secondary-text);
        }

        h3 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            color: var(--text-color);
        }

        p {
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        a {
            color: var(--text-color);
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 3px;
        }

        a:hover {
            color: var(--accent-color);
            text-decoration-thickness: 2px;
        }

        .meta {
            font-size: 0.9rem;
            color: var(--secondary-text);
            margin-bottom: 0.5rem;
        }

        /* Section Specifics */
        .section {
            margin-bottom: 2rem;
        }

        .item {
            margin-bottom: 1.5rem;
        }

        .item:last-child {
            margin-bottom: 0;
        }

        .work-title {
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }

        .work-journal {
            font-style: italic;
        }

        .biography-content {
            white-space: pre-wrap;
        }

        /* Responsive */
        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            .container {
                padding: 1rem 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header class="section">
        <h1><?php echo htmlspecialchars($name); ?></h1>
        <p class="meta">ORCID: <a href="https://orcid.org/<?php echo $orcid_id; ?>" target="_blank"><?php echo $orcid_id; ?></a></p>
    </header>

    <section class="section">
        <h2>Biography</h2>
        <div class="content biography-content"><?php echo htmlspecialchars($biography); ?></div>
    </section>

    <?php if (!empty($educations)): ?>
    <section class="section">
        <h2>Education</h2>
        <?php foreach ($educations as $edu): ?>
            <div class="item">
                <h3><?php echo htmlspecialchars($edu['organization']['name']); ?></h3>
                <p class="meta">
                    <?php
                    echo htmlspecialchars($edu['role-title'] ?? '');
                    $start = $edu['start-date']['year']['value'] ?? '';
                    $end = $edu['end-date']['year']['value'] ?? 'Present';
                    if ($start) {
                        echo " | " . htmlspecialchars($start) . " – " . htmlspecialchars($end);
                    }
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($employments)): ?>
    <section class="section">
        <h2>Employment</h2>
        <?php foreach ($employments as $emp): ?>
            <div class="item">
                <h3><?php echo htmlspecialchars($emp['organization']['name']); ?></h3>
                <p class="meta">
                    <?php
                    echo htmlspecialchars($emp['role-title'] ?? '');
                    $start = $emp['start-date']['year']['value'] ?? '';
                    $end = $emp['end-date']['year']['value'] ?? 'Present';
                    if ($start) {
                        echo " | " . htmlspecialchars($start) . " – " . htmlspecialchars($end);
                    }
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($works)): ?>
    <section class="section">
        <h2>Works</h2>
        <?php foreach ($works as $group): ?>
            <?php
            // Usually take the first summary as the preferred one
            $work = $group['work-summary'][0];
            $title = $work['title']['title']['value'] ?? 'Untitled';
            $journal = $work['journal-title']['value'] ?? '';
            $year = $work['publication-date']['year']['value'] ?? '';
            $url = $work['url']['value'] ?? null;
            $type = $work['type'] ?? '';
            ?>
            <div class="item">
                <?php if ($url): ?>
                    <a href="<?php echo htmlspecialchars($url); ?>" class="work-title" target="_blank"><?php echo htmlspecialchars($title); ?></a>
                <?php else: ?>
                    <span class="work-title"><?php echo htmlspecialchars($title); ?></span>
                <?php endif; ?>

                <p class="meta">
                    <?php
                    $parts = [];
                    if ($journal) $parts[] = htmlspecialchars($journal);
                    if ($year) $parts[] = htmlspecialchars($year);
                    if ($type) $parts[] = ucfirst(str_replace('-', ' ', htmlspecialchars($type)));
                    echo implode(' • ', $parts);
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

</div>

</body>
</html>
