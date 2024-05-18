<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;

/**
 * Class GitInfoUtil.
 *
 * A utility class for retrieving Git repository information if available.
 *
 * @package OGame\Utils
 */
class GitInfoUtil extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'gitInfoUtil';
    }

    /**
     * Get the current branch.
     *
     * @return string
     */
    public static function getCurrentBranch(): string
    {
        $return = exec('git rev-parse --abbrev-ref HEAD');
        if (!$return) {
            return '';
        } else {
            return $return;
        }
    }

    /**
     * Get the current commit hash.
     *
     * @return string
     */
    public static function getCurrentCommitHash(): string
    {
        $return = exec('git log --pretty="%h" -n1 HEAD');
        if (!$return) {
            return '';
        } else {
            return $return;
        }
    }

    /**
     * Get the date of the current commit.
     *
     * @param string $format
     * @return string
     */
    public static function getCurrentCommitDate(string $format = 'Y-m-d H:i:s'): string
    {
        // Execute the git command to get the date of the current HEAD commit in the specified format
        $date = exec("git log -1 HEAD --format=%cd");
        if (!$date) {
            return '';
        }

        $time = strtotime($date);
        if (!empty($time)) {
            return date($format, $time);
        } else {
            return '';
        }
    }

    /**
     * Get the current tag of the repository.
     *
     * @return string
     */
    public static function getCurrentTag(): string
    {
        // Attempt to get the exact tag matching the current commit
        $tag = exec('git describe --tags --exact-match 2>&1', $output, $returnVar);

        // Check if the command was successful, indicated by $returnVar being 0
        if ($returnVar === 0 && !empty($tag)) {
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
    public static function getAppVersion(): string
    {
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

    /**
     * Get app version with branch and commit hash. First try dynamically retrieving it from local Git repo if exists.
     * Otherwise fallback to the statically defined version in config/app.php
     *
     * @return string
     */
    public static function getAppVersionBranchCommit(): string
    {
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

            return '';
        });
    }
}
