<?php

declare(strict_types=1);

function usage(): void
{
    echo <<<'TXT'
KABEERI Task Tracker

Usage:
  php 24_kabeeri_task_tracking/scripts/kbr-task.php list V1
  php 24_kabeeri_task_tracking/scripts/kbr-task.php next V1
  php 24_kabeeri_task_tracking/scripts/kbr-task.php status V1 T07
  php 24_kabeeri_task_tracking/scripts/kbr-task.php start V1 T07
  php 24_kabeeri_task_tracking/scripts/kbr-task.php done V1 T07 --prompt=07 --tests="php artisan test" --commit=pending --notes="summary"
  php 24_kabeeri_task_tracking/scripts/kbr-task.php verify V1 T07 --notes="reviewed"
  php 24_kabeeri_task_tracking/scripts/kbr-task.php block V1 T07 --notes="reason"
  php 24_kabeeri_task_tracking/scripts/kbr-task.php report V1

Versions examples:
  V1, V2, V3, V4, V5, V6, V7, V8, FREEMIUM, EXT_UPDATE

TXT;
    exit(1);
}

function normalizeVersion(string $version): string
{
    $v = strtolower(preg_replace('/[^A-Za-z0-9]+/', '_', $version));

    return trim($v, '_');
}

function normalizeTaskId(string $taskId): string
{
    $taskId = strtoupper(trim($taskId));
    if (preg_match('/^\d+$/', $taskId)) {
        return 'T'.str_pad($taskId, 2, '0', STR_PAD_LEFT);
    }
    if (preg_match('/^T(\d+)$/', $taskId, $m)) {
        return 'T'.str_pad($m[1], 2, '0', STR_PAD_LEFT);
    }

    return $taskId;
}

function argValue(array $argv, string $name, ?string $default = null): ?string
{
    foreach ($argv as $arg) {
        if (str_starts_with($arg, "--{$name}=")) {
            return substr($arg, strlen($name) + 3);
        }
    }

    return $default;
}

function nowIso(): string
{
    return gmdate('c');
}

function loadData(string $taskFile): array
{
    if (! file_exists($taskFile)) {
        fwrite(STDERR, "Task file not found: {$taskFile}\n");
        exit(1);
    }
    $data = json_decode((string) file_get_contents($taskFile), true);
    if (! is_array($data) || ! isset($data['tasks']) || ! is_array($data['tasks'])) {
        fwrite(STDERR, "Invalid task file format: {$taskFile}\n");
        exit(1);
    }

    return $data;
}

function saveData(string $taskFile, array $data): void
{
    file_put_contents(
        $taskFile,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL
    );
}

function appendHistory(string $root, array $history): void
{
    $historyFile = $root.'/logs/task_history.jsonl';
    if (! is_dir(dirname($historyFile))) {
        mkdir(dirname($historyFile), 0777, true);
    }
    file_put_contents($historyFile, json_encode($history, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL, FILE_APPEND);
}

if ($argc < 3) {
    usage();
}

$action = strtolower($argv[1]);
$version = strtoupper($argv[2]);
$versionFile = normalizeVersion($version);
$taskId = isset($argv[3]) ? normalizeTaskId($argv[3]) : null;

$root = dirname(__DIR__);
$taskFile = "{$root}/tasks/{$versionFile}.tasks.json";
$data = loadData($taskFile);

if ($action === 'list') {
    foreach ($data['tasks'] as $task) {
        printf("%s | Prompt %s | %-12s | %s\n", $task['task_id'] ?? '', $task['prompt_id'] ?? '', $task['status'] ?? '', $task['title'] ?? '');
    }
    exit(0);
}

if ($action === 'next') {
    foreach ($data['tasks'] as $task) {
        if (($task['status'] ?? 'pending') === 'pending') {
            printf("Next: %s | Prompt %s | %s\n", $task['task_id'], $task['prompt_id'], $task['title']);
            exit(0);
        }
    }
    echo "No pending tasks for {$version}.\n";
    exit(0);
}

if ($taskId === null && ! in_array($action, ['report'], true)) {
    usage();
}

if ($action === 'report') {
    $counts = [];
    foreach ($data['tasks'] as $task) {
        $status = $task['status'] ?? 'pending';
        $counts[$status] = ($counts[$status] ?? 0) + 1;
    }
    echo "KABEERI {$version} Task Report\n";
    foreach (['pending', 'in_progress', 'codex_done', 'verified', 'blocked'] as $status) {
        printf("%-12s %d\n", $status.':', $counts[$status] ?? 0);
    }
    exit(0);
}

$found = false;
foreach ($data['tasks'] as &$task) {
    if (($task['task_id'] ?? null) !== $taskId) {
        continue;
    }

    $found = true;

    if ($action === 'status') {
        echo json_encode($task, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
        exit(0);
    }

    if ($action === 'start') {
        $task['status'] = 'in_progress';
        $task['started_at'] = nowIso();
    } elseif ($action === 'done') {
        $task['status'] = 'codex_done';
        $task['completed_by_codex_at'] = nowIso();
        $task['prompt_id'] = argValue($argv, 'prompt', $task['prompt_id'] ?? null);
        $task['tests'] = argValue($argv, 'tests', $task['tests'] ?? null);
        $task['commit'] = argValue($argv, 'commit', $task['commit'] ?? null);
        $task['notes'] = argValue($argv, 'notes', $task['notes'] ?? '');
    } elseif ($action === 'verify') {
        $task['status'] = 'verified';
        $task['verified_by_owner_at'] = nowIso();
        $task['notes'] = argValue($argv, 'notes', $task['notes'] ?? '');
    } elseif ($action === 'block') {
        $task['status'] = 'blocked';
        $task['blocked_at'] = nowIso();
        $task['notes'] = argValue($argv, 'notes', $task['notes'] ?? '');
    } else {
        usage();
    }

    appendHistory($root, [
        'at' => nowIso(),
        'version' => $version,
        'task_id' => $taskId,
        'action' => $action,
        'status' => $task['status'],
        'prompt_id' => $task['prompt_id'] ?? null,
        'commit' => $task['commit'] ?? null,
        'tests' => $task['tests'] ?? null,
        'notes' => $task['notes'] ?? null,
    ]);
    break;
}
unset($task);

if (! $found) {
    fwrite(STDERR, "Task not found: {$taskId}\n");
    exit(1);
}

saveData($taskFile, $data);
echo "Updated {$version} {$taskId}: {$action}\n";
