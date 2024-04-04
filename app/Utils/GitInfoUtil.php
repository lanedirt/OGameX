<?php

namespace OGame\Utils;

use Illuminate\Support\Facades\Cache;

class GitInfoUtil
{
    public static function getCurrentBranch() {
        return exec('git rev-parse --abbrev-ref HEAD');
    }

    public static function getCurrentCommitHash() {
        return exec('git log --pretty="%h" -n1 HEAD');
    }

    public static function getCurrentCommitDate($format = 'Y-m-d H:i:s') {
        // Execute the git command to get the date of the current HEAD commit in the specified format
        $date = exec("git log -1 HEAD --format=%cd");
        $time = strtotime($date);

        return date($format, $time);
    }

    public static function getCurrentTag() {
        // Attempt to get the exact tag matching the current commit
        $tag = exec('git describe --tags --exact-match 2>&1', $output, $returnVar);

        // Check if the command was successful, indicated by $returnVar being 0
        if ($returnVar === 0) {
            return $tag; // Return the tag if found
        } else {
            return ''; // Return an empty string if no exact tag match is found
        }
    }

    /**
     * Get app version. First try dynamically retrieving it from local Git repo if exists.
     * Otherwise fallback to the statically defined version in config/app.php
     *
     * @return string
     */
    public static function getAppVersion() {
        return Cache::remember('app_version', 3600, function () {
            // Try to retrieve dynamic version based on local Git repo (if exists).
            $tag = self::getCurrentTag();
            if (!empty($tag)) {
                return $tag;
            }

            $app_version_full = self::getAppVersionBranchCommit();
            if (!empty($app_version_full)) {
                return $app_version_full;
            }

            // Fallback to static version as defined in app config.
            $config_version = config('app.version');
            if (!empty($config_version)) {
                return $config_version;
            }

            return 'n/a';
        });
    }

    public static function getAppVersionBranchCommit() {
        return Cache::remember('app_version_full', 3600, function () {
            $branch = self::getCurrentBranch();
            $commit = self::getCurrentCommitHash();
            if (!empty($branch) && !empty($commit)) {
                return $branch . '/' . $commit . '/' . self::getCurrentCommitDate();
            }

            // Fallback to static version as defined in app config.
            $config_version = config('app.version');
            if (!empty($config_version)) {
                return $config_version;
            }

            return 'n/a';
        });
    }
}