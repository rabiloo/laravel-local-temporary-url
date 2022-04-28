#!/usr/bin/env php
<?php

function ask(string $question, string $default = ''): string
{
    $answer = readline($question . ($default ? " ({$default})" : null) . ': ');

    if (!$answer) {
        return $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question . ' (' . ($default ? 'Y/n' : 'y/N') . ')');

    if (!$answer) {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line . PHP_EOL;
}

function run(string $command): string
{
    return trim(shell_exec($command));
}

function str_after(string $subject, string $search): string
{
    $pos = strrpos($subject, $search);

    if ($pos === false) {
        return $subject;
    }

    return substr($subject, $pos + strlen($search));
}

function slugify(string $subject, string $separator = '-'): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $separator, $subject), $separator));
}

function pascal_case(string $subject): string
{
    return str_replace(' ', '', title_case($subject));
}

function title_case(string $subject): string
{
    return ucwords(str_replace(['-', '_'], ' ', $subject));
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

$gitName = run('git config user.name');
$gitEmail = run('git config user.email');
$gitUrl = run('git config remote.origin.url');

if (!preg_match('#^(https://github.com/|git@github.com:)(.*)\/(.*)\.git$#', $gitUrl, $matches)) {
    writeln('Invalid git URL');
    exit(1);
}

$gitUsername = $matches[2];
$gitRepository = $matches[3];

$authorName = ask('Author name', $gitName);
$authorEmail = ask('Author email', $gitEmail);
$authorUsername = ask('Author username', $gitUsername);

$vendorName = ask('Vendor name', $authorName);
$vendorEmail = ask('Vendor email', $authorEmail);
$vendorSlug = ask('Vendor slug', $gitUsername);
$vendorNamespace = ask('Vendor namespace', pascal_case($vendorSlug));

$packageName = ask('Package name', title_case($gitRepository));
$packageSlug = ask('Package slug', slugify($packageName));
$packageNamespace = ask('Package namespace', pascal_case($packageName));
$packageDescription = ask('Package description', "This is my package {$packageName}");

$className = ask('Class name', $packageNamespace);

writeln('------');
writeln("Author     : {$authorName} ({$authorUsername}, {$authorEmail})");
writeln("Vendor     : {$vendorName} ({$vendorSlug}, {$vendorEmail})");
writeln("Package    : {$packageName} ({$packageSlug}, {$packageDescription})");
writeln("Namespace  : {$vendorNamespace}\\{$packageNamespace}");
writeln("Packagist  : {$vendorSlug}/{$packageSlug}");
writeln("Github     : https://github.com/{$gitUsername}/{$gitRepository}");
writeln("Class name : {$className}");
writeln('------');

writeln('This script will replace the above values in all relevant files in the project directory.');

if (!confirm('Modify files?', true)) {
    exit(1);
}

$replacements = [
    'author-name' => $authorName,
    'author-username' => $authorUsername,
    'author@domain.com' => $authorEmail,
    'vendor-name' => $vendorName,
    'vendor-slug' => $vendorSlug,
    'vendor@domain.com' => $vendorEmail,
    'package-name' => $packageName,
    'package-slug' => $packageSlug,
    'package-description' => $packageDescription,
    'github-uri' => "{$gitUsername}/{$gitRepository}",
    'VendorNamespace' => $vendorNamespace,
    'PackageNamespace' => $packageNamespace,
    'SkeletonClass' => $className,
];
$files = explode(PHP_EOL, run('grep -E -r -l -i "' . implode('|', array_keys($replacements)) . '" --exclude-dir=vendor ./* ./.github/* | grep -v ' . basename(__FILE__)));

foreach ($files as $file) {
    replace_in_file($file, $replacements);

    match (true) {
        str_contains($file, 'src/SkeletonClass.php') => rename($file, './src/' . $className . '.php'),
        default => [],
    };
}

confirm('Execute `composer install` and run tests?') && run('composer install && composer test');
confirm('Let this script delete itself?', true) && unlink(__FILE__) && unlink(rtrim(__DIR__, DIRECTORY_SEPARATOR) . '/undo-configure.php');
